<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Financement;
use App\Services\ActivityLogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FinancementController extends Controller
{
    public function __construct(
        private readonly ActivityLogService $logService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $financements = Financement::with(['project', 'documents', 'contributions.organismeContributeur', 'contributions.categorieContribution', 'categorieContribution'])
            ->when($request->filled('project_id'), fn ($q) => $q->where('project_id', $request->integer('project_id')))
            ->when($request->filled('source_financement'), fn ($q) => $q->where('source_financement', 'ilike', "%{$request->source_financement}%"))
            ->orderByDesc('date_approbation')
            ->paginate($request->integer('per_page', 15));

        return response()->json($financements);
    }

    public function show($id): JsonResponse
    {
        $financement = Financement::with(['project', 'documents', 'contributions', 'categorieContribution'])->findOrFail($id);

        return response()->json($financement);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'project_id'                 => 'required|integer',
            'type_financement'           => 'nullable|string',
            'mode_contribution'          => 'nullable|string',
            'source_financement'         => 'required|string',
            'budget_approuve'            => 'required|numeric',
            'devise'                     => 'required|string',
            'montant_mga'                => 'required|numeric',
            'date_approbation'           => 'required|date',
            'description'                => 'nullable|string',
            'categorie_contribution_id'  => 'nullable|integer',
            'contributions'              => 'nullable|array',
            'contributions.*.organisme_contributeur_id' => 'required|integer',
            'contributions.*.mode_contribution'         => 'required|string',
            'contributions.*.montant'                   => 'required|numeric',
            'contributions.*.devise'                    => 'required|string',
            'contributions.*.montant_mga'               => 'required|numeric',
            'contributions.*.date_contribution'          => 'required|date',
            'contributions.*.categorie_contribution_id' => 'nullable|integer',
            'contributions.*.description'               => 'nullable|string',
        ]);

        $financement = DB::transaction(function () use ($validated) {
            $financement = Financement::create($validated);

            if (!empty($validated['contributions'])) {
                $financement->contributions()->createMany($validated['contributions']);
            }

            return $financement->load(['contributions', 'project']);
        });

        $this->logService->log(
            'create', 'financement',
            "Financement ajouté : {$financement->source_financement} — {$financement->budget_approuve} {$financement->devise}",
            $financement->project_id
        );

        return response()->json($financement, 201);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $financement = Financement::findOrFail($id);

        $validated = $request->validate([
            'project_id'                 => 'required|integer',
            'type_financement'           => 'nullable|string',
            'mode_contribution'          => 'nullable|string',
            'source_financement'         => 'required|string',
            'budget_approuve'            => 'required|numeric',
            'devise'                     => 'required|string',
            'montant_mga'                => 'required|numeric',
            'date_approbation'           => 'required|date',
            'description'                => 'nullable|string',
            'categorie_contribution_id'  => 'nullable|integer',
            'contributions'              => 'nullable|array',
            'contributions.*.organisme_contributeur_id' => 'required|integer',
            'contributions.*.mode_contribution'         => 'required|string',
            'contributions.*.montant'                   => 'required|numeric',
            'contributions.*.devise'                    => 'required|string',
            'contributions.*.montant_mga'               => 'required|numeric',
            'contributions.*.date_contribution'          => 'required|date',
            'contributions.*.categorie_contribution_id' => 'nullable|integer',
            'contributions.*.description'               => 'nullable|string',
        ]);

        DB::transaction(function () use ($financement, $validated) {
            $financement->update($validated);

            if (isset($validated['contributions'])) {
                // Remplacement synchrone des lignes de contribution
                $financement->contributions()->delete();
                $financement->contributions()->createMany($validated['contributions']);
            }
        });

        return response()->json([
            'message' => 'Mis à jour avec succès',
            'data'    => $financement->fresh(['contributions', 'project']),
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $financement = Financement::findOrFail($id);
        $financement->delete();

        return response()->json([
            'message' => 'Supprimé avec succès',
        ]);
    }

    public function financementsNumber(): JsonResponse
    {
        return response()->json([
            'count' => Financement::count(),
        ]);
    }

    // Répond à l'appel `financementApi.totaux()`
    public function financementsTotauxMGA(Request $request): JsonResponse
    {
        $query = Financement::query();

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->integer('project_id'));
        }

        $totalUSD = (float) (clone $query)->where('devise', 'USD')->sum('budget_approuve');
        $totalEUR = (float) (clone $query)->where('devise', 'EUR')->sum('budget_approuve');
        $totalAR  = (float) (clone $query)->where('devise', 'AR')->sum('budget_approuve');
        $totalMGA = (float) (clone $query)->sum('montant_mga');

        return response()->json([
            'total_count' => (clone $query)->count(),
            'totaux_MGA'  => $totalMGA,
            'totaux'      => [
                'USD' => $totalUSD,
                'EUR' => $totalEUR,
                'AR'  => $totalAR,
            ],
        ]);
    }

    public function byProject(int $projectId): JsonResponse
    {
        $financements = Financement::with(['contributions', 'documents'])
            ->where('project_id', $projectId)
            ->orderByDesc('date_approbation')
            ->get();

        return response()->json([
            'data'   => $financements,
            'totaux' => [
                'AR'  => (float) $financements->where('devise', 'AR')->sum('budget_approuve'),
                'USD' => (float) $financements->where('devise', 'USD')->sum('budget_approuve'),
                'EUR' => (float) $financements->where('devise', 'EUR')->sum('budget_approuve'),
                'MGA' => (float) $financements->sum('montant_mga'),
            ],
        ]);
    }
}