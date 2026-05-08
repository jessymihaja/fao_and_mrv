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

use Illuminate\Support\Facades\Route;

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
    Route::get('/projets-paginated', [ProjetController::class, 'getPaginatedProjects']);
    Route::get('/projets/number', [ProjetController::class, 'projectsNumber']);
    Route::get('/projets/number-active', [ProjetController::class, 'projectsNumberActive']);
    Route::get('/projets/filter', [ProjetController::class, 'projectsfilter']);
    Route::get('/projets/{id}', [ProjetController::class, 'show']);

    Route::get('/financements', [FinancementController::class, 'getFinancements']);
    Route::get('/fundings', [FinancementController::class, 'index']);
    Route::get('/financements/number', [FinancementController::class, 'financementsNumber']);
    Route::get('/financements/{id}', [FinancementController::class, 'show']);
    Route::get('/financements-totaux', [FinancementController::class, 'financementsTotauxMGA']);
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
        Route::get('/chatbot-settings-public', [ChatbotSettingController::class, 'publicSettings']);
        Route::put('/chatbot-settings', [ChatbotSettingController::class, 'updateSettings']);
        Route::get('/chatbot-settings-admin', [ChatbotSettingController::class, 'settings']);
        Route::post ('/chatbot-message', [ChatbotSettingController::class, 'message']);

        // --- FAQS ---
        Route::post('/faqs', [FaqsController::class, 'store']);
        Route::put('/faqs/{id}', [FaqsController::class, 'update']);
        Route::delete('/faqs/{id}', [FaqsController::class, 'destroy']);
        Route::get('/faqs', [FaqsController::class, 'index']);
        Route::get('/faqs-public', [FaqsController::class, 'active_faqs']);

        // --- PARTNERS ---
        Route::post('/partners', [PartnerController::class, 'store']);
        Route::put('/partners/{id}', [PartnerController::class, 'update']);
        Route::delete('/partners/{id}', [PartnerController::class, 'destroy']);
        Route::get('/partners', [PartnerController::class, 'index']);
        Route::get('/partners-public', [PartnerController::class, 'active_partners']);

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
        Route::get('/sliders-public', [SliderController::class, 'active_sliders']);

        
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

        // --- DEVISES ---
        Route::post('/devises', [DeviseController::class, 'store']);
        Route::put('/devises/{id}', [DeviseController::class, 'update']);
        Route::delete('/devises/{id}', [DeviseController::class, 'destroy']);

        // --- FINANCEMENTS ---
        Route::post('/financements', [FinancementController::class, 'store']);
        Route::put('/financements/{id}', [FinancementController::class, 'update']);
        Route::delete('/financements/{id}', [FinancementController::class, 'destroy']);



    });

});
