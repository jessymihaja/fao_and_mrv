<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;


class DocumentController extends Controller
{

    public function index(Request $request): JsonResponse
    {
        $documents = Document::with(['projet', 'financement'])
            ->when($request->filled('project_id'), fn ($q) => $q->where('project_id', $request->integer('project_id')))
            ->when($request->filled('type'),        fn ($q) => $q->where('type', $request->type))
            ->orderByDesc('created_at')
            ->paginate($request->integer('per_page', 20));

        return response()->json($documents);
    }

    public function byProject(int $projectId): JsonResponse
    {
        $documents = Document::with('financement')
            ->where('project_id', $projectId)
            ->orderByDesc('created_at')
            ->get();

        return response()->json($documents);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'titre'          => ['nullable', 'string', 'max:255'],
            'type'           => ['required', 'in:rapport,contrat,accord,plan,etude,photo,autre'],
            'fichier'        => ['required', 'file', 'max:51200', 'mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,zip'],
            'project_id'     => ['required', 'exists:projets,id_projet'],
            'financement_id' => ['nullable', 'exists:financements,id'],
            'description'    => ['nullable', 'string'],
        ]);

        $file     = $request->file('fichier');
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $path     = $file->storeAs('documents', $filename, 'public');

        $document = Document::create([
            'titre'            => $request->filled('titre') ? $request->titre : $file->getClientOriginalName(),
            'type'             => $request->type,
            'fichier'          => $path,
            'fichier_original' => $file->getClientOriginalName(),
            'taille'           => $file->getSize(),
            'mime_type'        => $file->getMimeType(),
            'project_id'       => $request->integer('project_id'),
            'financement_id'   => $request->integer('financement_id') ?: null,
            'description'      => $request->description,
            'uploaded_by'      => auth()->id(),
        ]);
        


        return response()->json($document->load(['project', 'financement']));
    }

    public function show(int $id): JsonResponse
    {
        return response()->json(
            Document::with(['project', 'financement'])->findOrFail($id)
        );
    }

    public function destroy(int $id): JsonResponse
    {
        $document = Document::findOrFail($id);

        Storage::disk('public')->delete($document->fichier);
        $document->delete();

        return response()->json(['message' => 'Document supprimé.']);
    }

    /**
     * Téléchargement via URL signée temporaire (15 min).
     * Accessible sans header Authorization — le token est dans la signature URL.
     * Appelé via GET /documents/{id}/download?signature=...&expires=...
     */
    public function download(int $id)
    {
        $document = Document::findOrFail($id);

        if (! Storage::disk('public')->exists($document->fichier)) {
            return response()->json(['message' => 'Fichier introuvable.'], 404);
        }

        return Storage::disk('public')->download(
            $document->fichier,
            $document->fichier_original ?? basename($document->fichier)
        );
    }

    /**
     * Génère une URL signée temporaire (15 min) pour le téléchargement.
     * Le frontend appelle d'abord cet endpoint (avec Bearer token),
     * puis redirige/ouvre l'URL signée retournée.
     *
     * GET /documents/{id}/signed-url
     */
    public function signedUrl(int $id): JsonResponse
    {
        // Vérifie que le document existe
        Document::findOrFail($id);

        $url = URL::temporarySignedRoute(
            'documents.download',
            now()->addMinutes(15),
            ['id' => $id]
        );

        return response()->json(['url' => $url]);
    }
}