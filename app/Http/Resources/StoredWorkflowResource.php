<?php
declare(strict_types=1);

namespace Waterline\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Waterline\Transformer\WorkflowToChartDataTransformer;
use Workflow\Models\StoredWorkflow;
use Workflow\Serializers\Y;

/**
 * @mixin StoredWorkflow
 */
class StoredWorkflowResource extends JsonResource
{
    public static $wrap = null;

    public function toArray(Request $request)
    {
        return [
            "id" => $this->id,
            "class" => $this->class,
            "arguments" => serialize(Y::unserialize($this->arguments)),
            "output" => $this->output === null ? serialize(null) : serialize(Y::unserialize($this->output)),
            "status" => $this->status,
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
            "logs" => StoredWorkflowLogResource::collection($this->logs),
            "exceptions" => StoredWorkflowExceptionResource::collection($this->exceptions),
            "chartData" => app(WorkflowToChartDataTransformer::class)->transform($this->resource),
        ];
    }
}
