<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUpdatePerspectiveRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
{
    return [
        'type_id' => 'required|exists:perspective_types,id',
        'titre' => 'required|string|max:255',
        'description' => 'nullable|string',
        'zone_extension_envisagee' => 'nullable|string|max:255',
        'objectif_moyen_terme' => 'nullable|string',
        'objectif_long_terme' => 'nullable|string',
        'impact_futur_attendu' => 'nullable|string',
        'statut' => [
            'required', 
            Rule::in(['a_l_etude', 'planifie', 'en_cours', 'realise'])
        ],
    ];
}
}