<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ClassificationController;
use App\Http\Controllers\API\DeviseController;
use App\Http\Controllers\API\DomaineInterventionController;
use App\Http\Controllers\API\EntiteAccrediteeController;
use App\Http\Controllers\API\FinancementController;
use App\Http\Controllers\API\HeroController;
use App\Http\Controllers\API\MapController;
use App\Http\Controllers\API\ProjetController;
use App\Http\Controllers\API\StatusController;
use App\Http\Controllers\API\ZoneGeographiqueController;
use App\Http\Controllers\API\ChatbotKnowledgeController;
use App\Http\Controllers\API\ChatbotSettingController;
use App\Http\Controllers\API\FaqsController;
use App\Http\Controllers\API\PartnerController;
use App\Http\Controllers\API\ContactController;
use App\Http\Controllers\API\SliderController;
use App\Http\Controllers\API\GeoController;
use App\Http\Controllers\API\DepenseController;
use App\Http\Controllers\API\EngagementController;
use App\Http\Controllers\API\DecaissementController;
use App\Http\Controllers\API\DocumentController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\StatsController;
use App\Http\Controllers\API\ActivityLogController;
use App\Http\Controllers\API\PublicSettingsController;
use App\Http\Controllers\API\Resultat_mrvController;
use App\Http\Controllers\API\Indicateur_mrvController;
use App\Http\Controllers\API\ComposanteController;
use App\Http\Controllers\API\ActiviteController;
use App\Http\Controllers\API\ContributionCategorieController;
use App\Http\Controllers\API\OrganismeContributeurController;
use App\Http\Controllers\Api\ProjectIdeaController;
use App\Http\Controllers\Api\ProjectIdeaFinancementController;
use App\Http\Controllers\Api\ProjectIdeaDocumentController;
use App\Http\Controllers\Api\ProjectIdeaDashboardController;
use App\Http\Controllers\Api\SecteurController;
use App\Http\Controllers\API\BudgetStageController;
use App\Http\Controllers\API\BudgetCycleController;
use App\Http\Controllers\API\RapportNationalController;


Route::get('/test', function () {
    return response()->json([
        'message' => 'API fonctionne',
    ]);
});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/heros', [HeroController::class, 'index']);
Route::get('/maps/{id}', [MapController::class, 'show']);
Route::get('/maps', [MapController::class, 'index']);
Route::get ('/public/stats',         [StatsController::class, 'public']);
Route::get ('/public/projects/map',  [ProjetController::class, 'mapData']);
Route::get('/sliders-public', [SliderController::class, 'active_sliders']);
Route::get('/partners-public', [PartnerController::class, 'active_partners']);
Route::get('/projets-paginated', [ProjetController::class, 'getPaginatedProjects']);
Route::get('/chatbot-settings-public', [ChatbotSettingController::class, 'publicSettings']);
Route::get('/faqs-public', [FaqsController::class, 'active_faqs']);
Route::get('/public-settings', [PublicSettingsController::class, 'index']);
// ─── LECTURE ET EXPORTS (GET) ─────────────────────────────────
        Route::get('/rapports-nationaux', [RapportNationalController::class, 'index']);
        Route::get('/rapports-nationaux/{id}', [RapportNationalController::class, 'show']);
        Route::get('/rapports-nationaux/{id}/export/pdf', [RapportNationalController::class, 'exportPdf']);
        Route::get('/rapports-nationaux/{id}/export/excel', [RapportNationalController::class, 'exportExcel']);


Route::get('/documents/{id}/download', [DocumentController::class, 'download'])
        ->middleware('signed')->name('documents.download');
// Lecture et Téléchargement
Route::get('/documents/{id}/signed-url', [DocumentController::class, 'signedUrl']);
Route::get('/documents/{id}/file', [DocumentController::class, 'downloadFile'])->name('documents.download-file');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // lecture
    Route::get('/classifications', [ClassificationController::class, 'index']);
    Route::get('/statuses', [StatusController::class, 'index']);
    Route::get('/zone-geographiques', [ZoneGeographiqueController::class, 'index']);
    Route::get('/entite-accreditees', [EntiteAccrediteeController::class, 'index']);
    Route::get('/domaine-interventions', [DomaineInterventionController::class, 'index']);
    Route::get('/welcome-messages', [WelcomeMessageController::class, 'index']);
    Route::get('/chatbot-knowledges', [ChatbotKnowledgeController::class, 'index']);

    Route::get('/projets', [ProjetController::class, 'index']);
    Route::get('/projets/number', [ProjetController::class, 'projectsNumber']);
    Route::get('/projets/number-active', [ProjetController::class, 'projectsNumberActive']);
    Route::get('/projets/filter', [ProjetController::class, 'projectsfilter']);
    Route::get('/projets/{id}', [ProjetController::class, 'show']);
    Route::get('/projects/{id}/depenses', [DepenseController::class, 'projectDepenses']);

    Route::get('/projets/{id}/financements', [FinancementController::class, 'byProject']);
    Route::get('/financements', [FinancementController::class, 'index']);
    Route::get('/fundings', [FinancementController::class, 'index']);
    Route::get('/financements/number', [FinancementController::class, 'financementsNumber']);
    Route::get('/financements/{id}', [FinancementController::class, 'show']);
    Route::get('/financements-totaux', [FinancementController::class, 'financementsTotauxMGA']);

    Route::get('/financements/{id}/engagements', [EngagementController::class, 'engagements']);
    Route::get('/financements/{id}/decaissements', [DecaissementController::class, 'decaissements']);

    Route::get('/documents',                  [DocumentController::class, 'index']);
    Route::get('/documents/{id}',             [DocumentController::class, 'show']);
    Route::get('/documents/{id}/signed-url',  [DocumentController::class, 'signedUrl']);
    Route::get('/projects/{projectId}/documents', [DocumentController::class, 'listByProject']);
    Route::get('/composantes/{composanteId}/documents', [DocumentController::class, 'listByComposante']);
    

    Route::get('/depenses',      [DepenseController::class, 'index']);
    Route::get('/depenses/{id}', [DepenseController::class, 'show']);
    
    // --- SECTEURS ---
    Route::get('/secteurs', [SecteurController::class, 'index']);

    // --- PROJECT IDEA ---
    // --- DASHBOARD ---
    Route::get('/project-ideas/dashboard', [ProjectIdeaDashboardController::class, 'index']);
    Route::get('/project-ideas/export-data', [ProjectIdeaController::class, 'exportData']);
    Route::apiResource('/project-ideas', ProjectIdeaController::class);
    Route::get('/project-ideas/{ideaId}/financements', [ProjectIdeaFinancementController::class, 'index']);
    Route::get('/project-ideas/{ideaId}/documents', [ProjectIdeaDocumentController::class, 'index']);
    Route::get('/project-idea-documents/{id}/download', [ProjectIdeaDocumentController::class, 'download']);

    // --- CONTRIBUTION  ---
    Route::get('/contribution-categories', [ContributionCategorieController::class, 'index']);
    Route::get('/organismes-contributeurs', [OrganismeContributeurController::class, 'index']);

    // --- SECTEURS ---
    Route::get('/secteurs', [SecteurController::class, 'index']);

    // ─── 1. Pledges (budgetPledgeApi) ───
    Route::get('/financements/{financementId}/pledges', [BudgetStageController::class, 'listPledges']);
    Route::get('/pledges/{id}/download', [BudgetStageController::class, 'downloadPledge']);

    // ─── 2. Mobilisations (budgetMobilisationApi) ───
    Route::get('/financements/{financementId}/mobilisations', [BudgetStageController::class, 'listMobilisations']);
    Route::get('/mobilisations/{id}/download', [BudgetStageController::class, 'downloadMobilisation']);

    // ─── 3. Approbations (budgetApprobationApi) ───
    Route::get('/financements/{financementId}/approbations', [BudgetStageController::class, 'listApprobations']);
    Route::get('/approbations/{id}/download', [BudgetStageController::class, 'downloadApprobation']);

    // ─── 4. Engagements (suiviApi) ───
    Route::get('/financements/{financementId}/engagements', [BudgetStageController::class, 'listEngagements']);
    Route::get('/engagements/{id}/download', [BudgetStageController::class, 'downloadEngagement']);

    // ─── 5. Plans de décaissement / Programmations (suiviApi) ───
    Route::get('/financements/{financementId}/decaissement-plans', [BudgetStageController::class, 'listPlans']);
    Route::get('/decaissement-plans/{id}/download', [BudgetStageController::class, 'downloadPlan']);

    // ─── 6. Décaissements réels (suiviApi) ───
    Route::get('/financements/{financementId}/decaissements', [BudgetStageController::class, 'listDecaissements']);
    Route::get('/decaissements/{id}/download', [BudgetStageController::class, 'downloadDecaissement']);

    // ─── 7. Dépenses & Audits (depenseApi) ───
    Route::get('/depenses', [BudgetStageController::class, 'listDepenses']);
    Route::get('/depenses/{id}', [BudgetStageController::class, 'showDepense']);
    Route::get('/depenses/{id}/rapport-audit/download', [BudgetStageController::class, 'downloadDepenseRapportAudit']);
    Route::get('/depenses/{id}/download', [BudgetStageController::class, 'downloadDepenseJustification']);

    // ─── 8. Vue consolidée / Tableaux de bord (budgetCycleApi) ───
    Route::get('/projects/{projectId}/budget-cycle', [BudgetCycleController::class, 'forProject']);
    Route::get('/financements/{financementId}/budget-cycle', [BudgetCycleController::class, 'forFinancement']);

    //public page


        Route::get('/devises', [DeviseController::class, 'index']);
            // --- HERO ---
        Route::post('/heros', [HeroController::class, 'store']);
        Route::put('/heros/{id}', [HeroController::class, 'update']);
        Route::delete('/heros/{id}', [HeroController::class, 'destroy']);

        // --- MAPS ---
        Route::post('/maps', [MapController::class, 'store']);
        Route::put('/maps/{id}', [MapController::class, 'update']);
        Route::delete('/maps/{id}', [MapController::class, 'destroy']);

        // --- CHATBOT KNOWLEDGE ---
        Route::post('/chatbot-knowledges', [ChatbotKnowledgeController::class, 'storeKnowledge']);
        Route::put('/chatbot-knowledges/{id}', [ChatbotKnowledgeController::class, 'updateKnowledge']);
        Route::delete('/chatbot-knowledges/{id}', [ChatbotKnowledgeController::class, 'destroyKnowledge']);
        Route::get('/chatbot-knowledges', [ChatbotKnowledgeController::class, 'knowledge']);

        // --- CHATBOT SETTINGS ---
        
        Route::put('/chatbot-settings', [ChatbotSettingController::class, 'updateSettings']);
        Route::get('/chatbot-settings-admin', [ChatbotSettingController::class, 'settings']);
        Route::post ('/chatbot-message', [ChatbotSettingController::class, 'message']);

        // --- FAQS ---
        Route::post('/faqs', [FaqsController::class, 'store']);
        Route::put('/faqs/{id}', [FaqsController::class, 'update']);
        Route::delete('/faqs/{id}', [FaqsController::class, 'destroy']);
        Route::get('/faqs', [FaqsController::class, 'index']);
        

        // --- PARTNERS ---
        Route::post('/partners', [PartnerController::class, 'store']);
        Route::put('/partners/{id}', [PartnerController::class, 'update']);
        Route::delete('/partners/{id}', [PartnerController::class, 'destroy']);
        Route::get('/partners', [PartnerController::class, 'index']);
        

        // --- CONTACTS ---
        Route::post('/contacts', [ContactController::class, 'store']);
        Route::put('/contacts/{id}', [ContactController::class, 'update']);
        Route::delete('/contacts/{id}', [ContactController::class, 'destroy']);
        Route::get('/contacts', [ContactController::class, 'index']);

        // --- SLIDERS ---
        Route::post('/sliders', [SliderController::class, 'store']);
        Route::put('/sliders/{id}', [SliderController::class, 'update']);
        Route::delete('/sliders/{id}', [SliderController::class, 'destroy']);
        Route::get('/sliders', [SliderController::class, 'index']);

        // --- Statistiques ---
        Route::get('/stats/global',             [StatsController::class, 'global']);
        Route::get('/stats/projects-by-status', [StatsController::class, 'projectsByStatus']);
        Route::get('/stats/budget-by-year',     [StatsController::class, 'budgetByYear']);
        Route::get('/stats/projects-by-region', [StatsController::class, 'projectsByRegion']);

        // --- ACTIVITY LOGS ---
        Route::get('/activity-logs', [ActivityLogController::class, 'index']);

        // ---  MRV ---
        Route::get('/resultat-mrvs', [Resultat_mrvController::class, 'index']);
        Route::get('/resultat-mrvs/composante/{id}', [Resultat_mrvController::class, 'getByComposite']);
        Route::get('/resultat-mrvs/activite/{id}', [Resultat_mrvController::class, 'getByActivite']);
        Route::get('/resultat-mrvs/projet/{id}', [Resultat_mrvController::class, 'getByProjet']);
        Route::get('/resultat-mrvs/{id}', [Resultat_mrvController::class, 'show']);
        Route::get('/indicateur-mrvs', [Indicateur_mrvController::class, 'index']);
        Route::get('/indicateur-mrvs/{id}', [Indicateur_mrvController::class, 'show']);
        Route::get('/indicateur-kpis', [Resultat_mrvController::class, 'getKpis']);


        // --- COMPOSANTES ---
        Route::get('/composantes', [ComposanteController::class, 'index']);
        Route::get('/composantes/{id}', [ComposanteController::class, 'show']);
        Route::get('/composantes/projet/{id}', [ComposanteController::class, 'getByProjet']);

        // --- ACTIVITES ---
        Route::get('/activites', [ActiviteController::class, 'index']);
        Route::get('/activites/{id}', [ActiviteController::class, 'show']);
        Route::get('/activites/projet/{id}', [ActiviteController::class, 'getByProjet']);
        Route::get('/activites/composante/{id}', [ActiviteController::class, 'getByComposante']);

        

    
        
    // admin + gestionnaire
    Route::middleware('role:admin,gestionnaire')->group(function () {
        // --- USERS ---
        Route::get('/users', [AuthController::class, 'getUsersPaginated']);
        // --- CLASSIFICATIONS ---
        Route::post('/classifications', [ClassificationController::class, 'store']);
        Route::put('/classifications/{id}', [ClassificationController::class, 'update']);
        Route::delete('/classifications/{id}', [ClassificationController::class, 'destroy']);

        // --- STATUSES ---
        Route::post('/statuses', [StatusController::class, 'store']);
        Route::put('/statuses/{id}', [StatusController::class, 'update']);
        Route::delete('/statuses/{id}', [StatusController::class, 'destroy']);

        // --- ZONES GEOGRAPHIQUES ---
        Route::post('/zone-geographiques', [ZoneGeographiqueController::class, 'store']);
        Route::put('/zone-geographiques/{id}', [ZoneGeographiqueController::class, 'update']);
        Route::delete('/zone-geographiques/{id}', [ZoneGeographiqueController::class, 'destroy']);

        // --- ENTITES ACCREDITEES ---
        Route::post('/entite-accreditees', [EntiteAccrediteeController::class, 'store']);
        Route::put('/entite-accreditees/{id}', [EntiteAccrediteeController::class, 'update']);
        Route::delete('/entite-accreditees/{id}', [EntiteAccrediteeController::class, 'destroy']);

        // --- DOMAINES D'INTERVENTION ---
        Route::post('/domaine-interventions', [DomaineInterventionController::class, 'store']);
        Route::put('/domaine-interventions/{id}', [DomaineInterventionController::class, 'update']);
        Route::delete('/domaine-interventions/{id}', [DomaineInterventionController::class, 'destroy']);

        // --- PROJETS ---
        Route::post('/projets', [ProjetController::class, 'store']);
        Route::put('/projets/{id}', [ProjetController::class, 'update']);
        Route::delete('/projets/{id}', [ProjetController::class, 'destroy']);
        Route::put('/projets/{id}/step', [ProjetController::class, 'advanceStep']);
        Route::put('/projets/{id}/geo', [ProjetController::class, 'updateGeo']);

        // --- DEVISES ---
        Route::post('/devises', [DeviseController::class, 'store']);
        Route::put('/devises/{id}', [DeviseController::class, 'update']);
        Route::delete('/devises/{id}', [DeviseController::class, 'destroy']);

        // --- FINANCEMENTS ---
        Route::post('/financements', [FinancementController::class, 'store']);
        Route::put('/financements/{id}', [FinancementController::class, 'update']);
        Route::delete('/financements/{id}', [FinancementController::class, 'destroy']);

        // --- GEOS ---
        Route::get('/geo/provinces',               [GeoController::class, 'provinces']);
        Route::get('/geo/regions/{province_id?}',  [GeoController::class, 'regions']);
        Route::get('/geo/districts/{region_id?}',  [GeoController::class, 'districts']);
        Route::get('/geo/communes/{district_id?}', [GeoController::class, 'communes']);
        Route::get('/geo/fokontany/{commune_id?}', [GeoController::class, 'fokontany']);

        // --- DEPENSES ---
        Route::post  ('/depenses',                [DepenseController::class, 'store']);
        Route::put   ('/depenses/{id}',           [DepenseController::class, 'update']);
        Route::delete('/depenses/{id}',           [DepenseController::class, 'destroy']);

        // --- ENGAGEMENTS ---
        Route::post  ('/financements/{id}/engagements',  [EngagementController::class, 'storeEngagement']);
        Route::put   ('/engagements/{id}',        [EngagementController::class, 'updateEngagement']);
        Route::delete('/engagements/{id}',        [EngagementController::class, 'destroyEngagement']);

        // --- DECAISSEMENTS ---
        Route::post  ('/financements/{id}/decaissements',           [DecaissementController::class, 'storeDecaissement']);
        Route::put   ('/decaissements/{id}',      [DecaissementController::class, 'updateDecaissement']);
        Route::delete('/decaissements/{id}',      [DecaissementController::class, 'destroyDecaissement']);

        // --- DOCUMENTS ---
        Route::post  ('/documents',      [DocumentController::class, 'store']);
        Route::delete('/documents/{id}', [DocumentController::class, 'destroy']);

        

        // --- USERS ---
        Route::put   ('/users/{id}/role',  [AuthController::class, 'updateRole']);
        Route::put   ('/users/{id}/status', [AuthController::class, 'toggle']);
        Route::put   ('/users/{id}/toggle',[AuthController::class, 'toggle']);
        Route::put   ('/users/{id}',       [AuthController::class, 'update']);

        // --- settings ---
         Route::get ('/settings',       [PublicSettingsController::class, 'adminIndex']);
        Route::put ('/settings/{key}', [PublicSettingsController::class, 'update']);
        Route::post('/settings/stats ', [StatsController::class, 'updateManual']);

        // --- Indicateur MRV ---
        
        Route::post('/indicateur-mrvs', [Indicateur_mrvController::class, 'store']);
        Route::put('/indicateur-mrvs/{id}', [Indicateur_mrvController::class, 'update']);
        Route::delete('/indicateur-mrvs/{id}', [Indicateur_mrvController::class, 'destroy']);

        // --- Resultat MRV ---
        
        Route::post('/resultat-mrvs', [Resultat_mrvController::class, 'store']);
        Route::put('/resultat-mrvs/{id}', [Resultat_mrvController::class, 'update']);
        Route::delete('/resultat-mrvs/{id}', [Resultat_mrvController::class, 'destroy']);

        // --- Composantes ---
        Route::post('/composantes/{projet_id}', [ComposanteController::class, 'store']);
        Route::put('/composantes/{id}', [ComposanteController::class, 'update']);
        Route::delete('/composantes/{id}', [ComposanteController::class, 'destroy']);

        // --- Activites ---
        Route::post('/activites', [ActiviteController::class, 'store']);
        Route::put('/activites/{id}', [ActiviteController::class, 'update']);
        Route::delete('/activites/{id}', [ActiviteController::class, 'destroy']);
        Route::post('/activites/composante/{composante}', [ActiviteController::class, 'store']);
        Route::post('/activites/projet/{project}', [ActiviteController::class, 'store']);

        // --- Secteurs ---
        Route::post('/secteurs', [SecteurController::class, 'store']);
        Route::put('/secteurs/{id}', [SecteurController::class, 'update']);
        Route::delete('/secteurs/{id}', [SecteurController::class, 'destroy']);

        // --- Project Idea ---
    Route::put('/project-ideas/{id}/status', [ProjectIdeaController::class, 'changeStatus']);
    Route::post('/project-ideas/{id}/convert', [ProjectIdeaController::class, 'convert']);

    Route::post('/project-ideas/{ideaId}/financements', [ProjectIdeaFinancementController::class, 'store']);
    Route::put('/project-idea-financements/{id}', [ProjectIdeaFinancementController::class, 'update']);
    Route::delete('/project-idea-financements/{id}', [ProjectIdeaFinancementController::class, 'destroy']);
    Route::post('/project-ideas/{ideaId}/documents', [ProjectIdeaDocumentController::class, 'store']);
    Route::delete('/project-idea-documents/{id}', [ProjectIdeaDocumentController::class, 'destroy']);
    
    // --- CONTRIBUTION  ---
    Route::post('/contribution-categories', [ContributionCategorieController::class, 'store']);
    Route::post('/organismes-contributeurs', [OrganismeContributeurController::class, 'store']);

    // --- SECTEURS ---
    Route::post('/secteurs', [SecteurController::class, 'store']);

    // ─── 1. Pledges (budgetPledgeApi) ───
    Route::post('/financements/{financementId}/pledges', [BudgetStageController::class, 'storePledge']);
    Route::post('/pledges/{id}', [BudgetStageController::class, 'updatePledge']);
    Route::delete('/pledges/{id}', [BudgetStageController::class, 'destroyPledge']);

    // ─── 2. Mobilisations (budgetMobilisationApi) ───
    Route::post('/financements/{financementId}/mobilisations', [BudgetStageController::class, 'storeMobilisation']);
    Route::post('/mobilisations/{id}', [BudgetStageController::class, 'updateMobilisation']);
    Route::delete('/mobilisations/{id}', [BudgetStageController::class, 'destroyMobilisation']);

    // ─── 3. Approbations (budgetApprobationApi) ───
    Route::post('/financements/{financementId}/approbations', [BudgetStageController::class, 'storeApprobation']);
    Route::post('/approbations/{id}', [BudgetStageController::class, 'updateApprobation']);
    Route::delete('/approbations/{id}', [BudgetStageController::class, 'destroyApprobation']);

    // ─── 4. Engagements (suiviApi) ───
    Route::post('/financements/{financementId}/engagements', [BudgetStageController::class, 'storeEngagement']);
    Route::put('/engagements/{id}', [BudgetStageController::class, 'updateEngagement']);
    Route::post('/engagements/{id}', [BudgetStageController::class, 'updateEngagement']); // Soumission FormData
    Route::delete('/engagements/{id}', [BudgetStageController::class, 'destroyEngagement']);

    // ─── 5. Plans de décaissement / Programmations (suiviApi) ───
    Route::post('/financements/{financementId}/decaissement-plans', [BudgetStageController::class, 'storePlan']);
    Route::put('/decaissement-plans/{id}', [BudgetStageController::class, 'updatePlan']);
    Route::post('/decaissement-plans/{id}', [BudgetStageController::class, 'updatePlan']); // Soumission FormData
    Route::delete('/decaissement-plans/{id}', [BudgetStageController::class, 'destroyPlan']);

    // ─── 6. Décaissements réels (suiviApi) ───
    Route::post('/financements/{financementId}/decaissements', [BudgetStageController::class, 'storeDecaissement']);
    Route::put('/decaissements/{id}', [BudgetStageController::class, 'updateDecaissement']);
    Route::post('/decaissements/{id}', [BudgetStageController::class, 'updateDecaissement']); // Soumission FormData
    Route::delete('/decaissements/{id}', [BudgetStageController::class, 'destroyDecaissement']);

    // ─── 7. Dépenses & Audits (depenseApi) ───
    Route::post('/depenses', [BudgetStageController::class, 'storeDepense']);
    Route::put('/depenses/{id}', [BudgetStageController::class, 'updateDepense']);
    Route::delete('/depenses/{id}', [BudgetStageController::class, 'destroyDepense']);
    Route::post('/depenses/{id}/audit', [BudgetStageController::class, 'auditDepense']);

    // ─── 8. Rapports nationaux (rapportNationalApi) ───
    Route::post('/rapports-nationaux', [RapportNationalController::class, 'store']);
    Route::post('/rapports-nationaux/{id}/generate', [RapportNationalController::class, 'generate']);
    Route::delete('/rapports-nationaux/{id}', [RapportNationalController::class, 'destroy']);

    });

});
