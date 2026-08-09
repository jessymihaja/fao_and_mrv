<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProjectIdeaDocument;
use Illuminate\Support\Facades\Storage;

class ProjectIdeaDocumentController extends Controller
{
    public function index($ideaId)
    {
        return response()->json(ProjectIdeaDocument::where('project_idea_id', $ideaId)->get());
    }

    public function store(Request $request, $ideaId)
    {
        $request->validate([
            'file' => 'required|file|max:20480', // Max 20Mo
            'type' => 'required|in:concept_note,etude_faisabilite,budget,carte,images,autre',
        ]);

        $file = $request->file('file');
        $path = $file->store("project_ideas/{$ideaId}/documents", 'private');

        $doc = ProjectIdeaDocument::create([
            'project_idea_id' => $ideaId,
            'type' => $request->type,
            'libelle' => $request->libelle ?? $file->getClientOriginalName(),
            'file_path' => $path,
            'file_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getClientMimeType(),
            'size' => $file->getSize(),
        ]);

        return response()->json($doc, 201);
    }

    public function destroy($id)
    {
        $doc = ProjectIdeaDocument::findOrFail($id);
        Storage::disk('private')->delete($doc->file_path);
        $doc->delete();

        return response()->json(null, 204);
    }

    public function download($id)
    {
        $doc = ProjectIdeaDocument::findOrFail($id);

        if (!Storage::disk('private')->exists($doc->file_path)) {
            return response()->json(['message' => 'Fichier introuvable'], 404);
        }

        return Storage::disk('private')->download($doc->file_path, $doc->file_name);
    }
}
