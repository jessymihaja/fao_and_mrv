<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class DocumentController extends Controller
{
    /**
     * Liste paginée des documents
     */
    public function index(Request $request)
    {
        $query = Document::with(['project', 'financement']);

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }
        if ($request->filled('composante_id')) {
            $query->where('composante_id', $request->composante_id);
        }

        return response()->json($query->latest()->paginate($request->get('per_page', 15)));
    }

    /**
     * Liste des documents par projet
     */
    public function listByProject($projectId)
    {
        $documents = Document::where('project_id', $projectId)->latest()->get();
        return response()->json($documents);
    }

    /**
     * Liste des documents par composante
     */
    public function listByComposante($composanteId)
    {
        $documents = Document::where('composante_id', $composanteId)->latest()->get();
        return response()->json($documents);
    }

    /**
     * Upload d'un document (FormData)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'titre' => 'required|string|max:255',
            'type' => 'required|string',
            'fichier' => 'required|file|max:20480', // Max 20 Mo
            'project_id' => 'required|exists:projets,id_projet',
            'composante_id' => 'nullable|exists:composantes,id',
            'financement_id' => 'nullable|exists:financements,id',
            'description' => 'nullable|string',
        ]);

        $file = $request->file('fichier');
        $originalName = $file->getClientOriginalName();
        $mimeType = $file->getClientMimeType();
        $size = $file->getSize();

        // Enregistrement sur le disk public (storage/app/public/documents)
        $path = $file->store('documents', 'public');

        $document = Document::create([
            'titre' => $validated['titre'],
            'type' => $validated['type'],
            'fichier' => $path,
            'fichier_original' => $originalName,
            'taille' => $size,
            'mime_type' => $mimeType,
            'project_id' => $validated['project_id'],
            'composante_id' => $validated['composante_id'] ?? null,
            'financement_id' => $validated['financement_id'] ?? null,
            'description' => $validated['description'] ?? null,
            'uploaded_by' => auth()->id(),
        ]);

        return response()->json($document->load(['project', 'financement']), 201);
    }

    /**
     * Affichage d'un document
     */
    public function show($id)
    {
        $document = Document::with(['project', 'financement'])->findOrFail($id);
        return response()->json($document);
    }

    /**
     * Génération de l'URL signée ou directe pour le téléchargement
     */
    public function signedUrl($id)
{
    $document = Document::findOrFail($id);

    return response()->json([
        'url' => route('documents.download-file', ['id' => $document->id])
    ]);
}

/**
 * Lit et sert le fichier au navigateur sans passer par un symlink
 */
public function downloadFile($id)
{
    $document = Document::findOrFail($id);

    if (!Storage::disk('public')->exists($document->fichier)) {
        return response()->json(['message' => 'Fichier introuvable sur le disque'], 404);
    }

    $fullPath = Storage::disk('public')->path($document->fichier);

    return response()->file($fullPath, [
        'Content-Type' => $document->mime_type ?? mime_content_type($fullPath),
        'Content-Disposition' => 'inline; filename="' . ($document->fichier_original ?? basename($document->fichier)) . '"'
    ]);
}

    /**
     * Suppression d'un document et de son fichier physiquement
     */
    public function destroy($id)
    {
        $document = Document::findOrFail($id);

        if (Storage::disk('public')->exists($document->fichier)) {
            Storage::disk('public')->delete($document->fichier);
        }

        $document->delete();

        return response()->json(['message' => 'Document supprimé avec succès']);
    }
}