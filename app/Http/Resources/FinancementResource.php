<?php
// app/Http/Resources/FinancementResource.php
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FinancementResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                 => $this->id,
            'project_id'         => $this->project_id,
            'source_financement' => $this->source_financement,
            'budget_approuve'    => (float) $this->budget_approuve,
            'devise'             => $this->devise,
            'montant_mga'        => (float) $this->montant_mga,
            'date_approbation'   => $this->date_approbation?->toDateString(),
            'project'            => new ProjectResource($this->whenLoaded('project')),
            'documents'          => DocumentResource::collection($this->whenLoaded('documents')),
            'created_at'         => $this->created_at?->toISOString(),
        ];
    }
}