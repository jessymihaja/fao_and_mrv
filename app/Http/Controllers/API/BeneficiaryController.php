<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Beneficiary;

class BeneficiaryController extends Controller
{
    public function listByProject(Request $request, $projectId)
    {
        $query = Beneficiary::with([
            'beneficiaryType',
            'beneficiaryCategory',
            'region:id,nom',
            'district:id,nom',
            'commune:id,nom',
            'fokontany:id,nom',
        ])->where('project_id', $projectId);

        $beneficiaries = $query->get();

        // Calcul global des statistiques
        $totalPrevu = $beneficiaries->sum('planned_count');
        $totalAtteint = $beneficiaries->sum('achieved_count');

        $stats = [
            'total_prevu' => $totalPrevu,
            'total_atteint' => $totalAtteint,
            'femmes' => $beneficiaries->sum('women_count'),
            'hommes' => $beneficiaries->sum('men_count'),
            'jeunes' => $beneficiaries->sum('youth_count'),
            'vulnerables' => $beneficiaries->sum('vulnerable_count'),
            'taux_atteinte' => $totalPrevu > 0 ? round(($totalAtteint / $totalPrevu) * 100, 2) : 0,
        ];

        return response()->json([
            'data' => $beneficiaries,
            'stats' => $stats,
        ]);
    }

    public function store(Request $request, $projectId)
    {
        $validated = $request->validate([
            'beneficiary_type_id' => 'required|exists:beneficiary_types,id',
            'beneficiary_category_id' => 'required|exists:beneficiary_categories,id',
            'region_id' => 'nullable|exists:regions,id',
            'district_id' => 'nullable|exists:districts,id',
            'commune_id' => 'nullable|exists:communes,id',
            'fokontany_id' => 'nullable|exists:fokontanies,id',
            'description' => 'nullable|string',
            'planned_count' => 'required|integer|min:0',
            'achieved_count' => 'required|integer|min:0',
            'women_count' => 'nullable|integer|min:0',
            'men_count' => 'nullable|integer|min:0',
            'youth_count' => 'nullable|integer|min:0',
            'vulnerable_count' => 'nullable|integer|min:0',
            'reference_year' => 'required|integer',
            'monitoring_year' => 'nullable|integer',
            'source' => 'nullable|string|max:255',
            'observations' => 'nullable|string',
        ]);

        $validated['project_id'] = $projectId;

        $beneficiary = Beneficiary::create($validated);
        $beneficiary->load([
            'beneficiaryType',
            'beneficiaryCategory',
            'region:id,nom',
            'district:id,nom',
            'commune:id,nom',
            'fokontany:id,nom',
        ]);

        return response()->json($beneficiary, 201);
    }

    public function show($id)
    {
        $beneficiary = Beneficiary::with([
            'beneficiaryType',
            'beneficiaryCategory',
            'region:id,nom',
            'district:id,nom',
            'commune:id,nom',
            'fokontany:id,nom',
        ])->findOrFail($id);

        return response()->json($beneficiary);
    }

    public function update(Request $request, $id)
    {
        $beneficiary = Beneficiary::findOrFail($id);

        $validated = $request->validate([
            'beneficiary_type_id' => 'sometimes|exists:beneficiary_types,id',
            'beneficiary_category_id' => 'sometimes|exists:beneficiary_categories,id',
            'region_id' => 'nullable|exists:regions,id',
            'district_id' => 'nullable|exists:districts,id',
            'commune_id' => 'nullable|exists:communes,id',
            'fokontany_id' => 'nullable|exists:fokontanies,id',
            'description' => 'nullable|string',
            'planned_count' => 'sometimes|integer|min:0',
            'achieved_count' => 'sometimes|integer|min:0',
            'women_count' => 'nullable|integer|min:0',
            'men_count' => 'nullable|integer|min:0',
            'youth_count' => 'nullable|integer|min:0',
            'vulnerable_count' => 'nullable|integer|min:0',
            'reference_year' => 'sometimes|integer',
            'monitoring_year' => 'nullable|integer',
            'source' => 'nullable|string|max:255',
            'observations' => 'nullable|string',
        ]);

        $beneficiary->update($validated);
        $beneficiary->load([
            'beneficiaryType',
            'beneficiaryCategory',
            'region:id,nom',
            'district:id,nom',
            'commune:id,nom',
            'fokontany:id,nom',
        ]);

        return response()->json($beneficiary);
    }

    public function destroy($id)
    {
        $beneficiary = Beneficiary::findOrFail($id);
        $beneficiary->delete();

        return response()->json(null, 204);
    }
}
