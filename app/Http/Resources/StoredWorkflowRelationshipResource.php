<?php
declare(strict_types=1);

namespace Waterline\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \Illuminate\Database\Eloquent\Model
 */
class StoredWorkflowRelationshipResource extends JsonResource
{
    public static $wrap = null;

    public function toArray($request)
    {
        return [
            "id" => $this->id,
            "parent_workflow_id" => $this->pivot->parent_workflow_id,
            "parent_index" => $this->pivot->parent_index,
            "parent_now" => $this->pivot->parent_now,
            "child_workflow_id" => $this->pivot->child_workflow_id,
        ];
    }
}
