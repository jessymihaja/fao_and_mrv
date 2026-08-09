<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{BudgetPledge, BudgetMobilisation, BudgetApprobation, BudgetEngagement, BudgetProgrammation, BudgetDecaissement, BudgetDepense};
use Illuminate\Support\Facades\Storage;

class BudgetStageController extends Controller
{
    // Helper réutilisable pour l'upload de justificatifs
    private function uploadFile(Request $request, string $field = 'justificatif', string $dir = 'budgets/justificatifs')
    {
        if ($request->hasFile($field)) {
            $file = $request->file($field);
            $path = $file->store($dir, 'public');
            return ['path' => $path, 'name' => $file->getClientOriginalName()];
        }
        return null;
    }

    private function downloadFile(?string $path, ?string $filename)
    {
        if (!$path || !Storage::disk('public')->exists($path)) {
            return response()->json(['message' => 'Fichier inexistant ou introuvable.'], 404);
        }
        return Storage::disk('public')->download($path, $filename ?? basename($path));
    }

    // ─── 1. PLEDGES ───
    public function listPledges($financementId) {
        return response()->json(BudgetPledge::where('financement_id', $financementId)->get());
    }
    public function storePledge(Request $request, $financementId) {
        $data = $request->all();
        $data['financement_id'] = $financementId;
        if ($file = $this->uploadFile($request)) {
            $data['justificatif_path'] = $file['path'];
            $data['justificatif_name'] = $file['name'];
        }
        return response()->json(BudgetPledge::create($data), 201);
    }
    public function updatePledge(Request $request, $id) {
        $item = BudgetPledge::findOrFail($id);
        $data = $request->all();
        if ($file = $this->uploadFile($request)) {
            $data['justificatif_path'] = $file['path'];
            $data['justificatif_name'] = $file['name'];
        }
        $item->update($data);
        return response()->json($item);
    }
    public function destroyPledge($id) {
        BudgetPledge::findOrFail($id)->delete();
        return response()->json(null, 204);
    }
    public function downloadPledge($id) {
        $item = BudgetPledge::findOrFail($id);
        return $this->downloadFile($item->justificatif_path, $item->justificatif_name);
    }

    // ─── 2. MOBILISATIONS ───
    public function listMobilisations($financementId) {
        return response()->json(BudgetMobilisation::where('financement_id', $financementId)->get());
    }
    public function storeMobilisation(Request $request, $financementId) {
        $data = $request->all();
        $data['financement_id'] = $financementId;
        if ($file = $this->uploadFile($request)) {
            $data['justificatif_path'] = $file['path'];
            $data['justificatif_name'] = $file['name'];
        }
        return response()->json(BudgetMobilisation::create($data), 201);
    }
    public function updateMobilisation(Request $request, $id) {
        $item = BudgetMobilisation::findOrFail($id);
        $data = $request->all();
        if ($file = $this->uploadFile($request)) {
            $data['justificatif_path'] = $file['path'];
            $data['justificatif_name'] = $file['name'];
        }
        $item->update($data);
        return response()->json($item);
    }
    public function destroyMobilisation($id) {
        BudgetMobilisation::findOrFail($id)->delete();
        return response()->json(null, 204);
    }
    public function downloadMobilisation($id) {
        $item = BudgetMobilisation::findOrFail($id);
        return $this->downloadFile($item->justificatif_path, $item->justificatif_name);
    }

    // ─── 3. ENGAGEMENTS ───
    public function listEngagements($financementId) {
        return response()->json(BudgetEngagement::where('financement_id', $financementId)->get());
    }
    public function storeEngagement(Request $request, $financementId) {
        $data = $request->all();
        $data['financement_id'] = $financementId;
        if ($file = $this->uploadFile($request)) {
            $data['justificatif_path'] = $file['path'];
            $data['justificatif_name'] = $file['name'];
        }
        return response()->json(BudgetEngagement::create($data), 201);
    }
    public function updateEngagement(Request $request, $id) {
        $item = BudgetEngagement::findOrFail($id);
        $data = $request->all();
        if ($file = $this->uploadFile($request)) {
            $data['justificatif_path'] = $file['path'];
            $data['justificatif_name'] = $file['name'];
        }
        $item->update($data);
        return response()->json($item);
    }
    public function destroyEngagement($id) {
        BudgetEngagement::findOrFail($id)->delete();
        return response()->json(null, 204);
    }
    public function downloadEngagement($id) {
        $item = BudgetEngagement::findOrFail($id);
        return $this->downloadFile($item->justificatif_path, $item->justificatif_name);
    }

    // ─── 4. APPROBATIONS ───
    public function listApprobations($financementId) {
        return response()->json(BudgetApprobation::where('financement_id', $financementId)->get());
    }
    public function storeApprobation(Request $request, $financementId) {
        $data = $request->all();
        $data['financement_id'] = $financementId;
        if ($file = $this->uploadFile($request)) {
            $data['justificatif_path'] = $file['path'];
            $data['justificatif_name'] = $file['name'];
        }
        return response()->json(BudgetApprobation::create($data), 201);
    }
    public function updateApprobation(Request $request, $id) {
        $item = BudgetApprobation::findOrFail($id);
        $data = $request->all();
        if ($file = $this->uploadFile($request)) {
            $data['justificatif_path'] = $file['path'];
            $data['justificatif_name'] = $file['name'];
        }
        $item->update($data);
        return response()->json($item);
    }
    public function destroyApprobation($id) {
        BudgetApprobation::findOrFail($id)->delete();
        return response()->json(null, 204);
    }
    public function downloadApprobation($id) {
        $item = BudgetApprobation::findOrFail($id);
        return $this->downloadFile($item->justificatif_path, $item->justificatif_name);
    }

    // ─── 5. PROGRAMMATIONS (DECISSEMENT PLANS) ───
    public function listPlans($financementId) {
        return response()->json(BudgetProgrammation::where('financement_id', $financementId)->get());
    }
    public function storePlan(Request $request, $financementId) {
        $data = $request->all();
        $data['financement_id'] = $financementId;
        if ($file = $this->uploadFile($request)) {
            $data['justificatif_path'] = $file['path'];
            $data['justificatif_name'] = $file['name'];
        }
        return response()->json(BudgetProgrammation::create($data), 201);
    }
    public function updatePlan(Request $request, $id) {
        $item = BudgetProgrammation::findOrFail($id);
        $data = $request->all();
        if ($file = $this->uploadFile($request)) {
            $data['justificatif_path'] = $file['path'];
            $data['justificatif_name'] = $file['name'];
        }
        $item->update($data);
        return response()->json($item);
    }
    public function destroyPlan($id) {
        BudgetProgrammation::findOrFail($id)->delete();
        return response()->json(null, 204);
    }
    public function downloadPlan($id) {
        $item = BudgetProgrammation::findOrFail($id);
        return $this->downloadFile($item->justificatif_path, $item->justificatif_name);
    }

    // ─── 6. DECAISSEMENTS ───
    public function listDecaissements($financementId) {
        return response()->json(BudgetDecaissement::where('financement_id', $financementId)->get());
    }
    public function storeDecaissement(Request $request, $financementId) {
        $data = $request->all();
        $data['financement_id'] = $financementId;
        if ($file = $this->uploadFile($request)) {
            $data['justificatif_path'] = $file['path'];
            $data['justificatif_name'] = $file['name'];
        }
        return response()->json(BudgetDecaissement::create($data), 201);
    }
    public function updateDecaissement(Request $request, $id) {
        $item = BudgetDecaissement::findOrFail($id);
        $data = $request->all();
        if ($file = $this->uploadFile($request)) {
            $data['justificatif_path'] = $file['path'];
            $data['justificatif_name'] = $file['name'];
        }
        $item->update($data);
        return response()->json($item);
    }
    public function destroyDecaissement($id) {
        BudgetDecaissement::findOrFail($id)->delete();
        return response()->json(null, 204);
    }
    public function downloadDecaissement($id) {
        $item = BudgetDecaissement::findOrFail($id);
        return $this->downloadFile($item->justificatif_path, $item->justificatif_name);
    }

    // ─── 7. DEPENSES & AUDIT ───
    public function listDepenses(Request $request) {
        $query = BudgetDepense::query();
        if ($request->has('project_id')) $query->where('project_id', $request->project_id);
        if ($request->has('financement_id')) $query->where('financement_id', $request->financement_id);
        return response()->json($query->paginate($request->get('per_page', 15)));
    }
    public function storeDepense(Request $request) {
        $data = $request->all();
        if ($file = $this->uploadFile($request, 'justification', 'depenses/justifications')) {
            $data['justification_path'] = $file['path'];
            $data['justification_name'] = $file['name'];
        }
        $data['statut'] = 'depense';
        return response()->json(BudgetDepense::create($data), 201);
    }
    public function showDepense($id) {
        return response()->json(BudgetDepense::findOrFail($id));
    }
    public function updateDepense(Request $request, $id) {
        $item = BudgetDepense::findOrFail($id);
        $data = $request->all();
        if ($file = $this->uploadFile($request, 'justification', 'depenses/justifications')) {
            $data['justification_path'] = $file['path'];
            $data['justification_name'] = $file['name'];
        }
        $item->update($data);
        return response()->json($item);
    }
    public function destroyDepense($id) {
        BudgetDepense::findOrFail($id)->delete();
        return response()->json(null, 204);
    }
    public function auditDepense(Request $request, $id) {
        $depense = BudgetDepense::findOrFail($id);
        $data = $request->all();
        if ($file = $this->uploadFile($request, 'rapport_audit', 'depenses/audits')) {
            $data['rapport_audit_path'] = $file['path'];
            $data['rapport_audit_name'] = $file['name'];
        }
        $data['statut'] = 'audite';
        $depense->update($data);
        return response()->json($depense);
    }
    public function downloadDepenseRapportAudit($id) {
        $item = BudgetDepense::findOrFail($id);
        return $this->downloadFile($item->rapport_audit_path, $item->rapport_audit_name);
    }
    public function downloadDepenseJustification($id) {
        $item = BudgetDepense::findOrFail($id);
        return $this->downloadFile($item->justification_path, $item->justification_name);
    }
}