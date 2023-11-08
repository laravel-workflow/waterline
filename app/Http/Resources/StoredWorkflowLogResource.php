<?php
declare(strict_types=1);

namespace Waterline\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Waterline\Transformer\WorkflowToChartDataTransformer;
use Workflow\Models\StoredWorkflow;
use Workflow\Models\StoredWorkflowLog;
use Workflow\Serializers\Y;

/**
 * @mixin StoredWorkflowLog
 */
class StoredWorkflowLogResource extends JsonResource
{
    public static $wrap = null;

    public function toArray(Request $request)
    {
        return [
            "id" => $this->id,
            "index" => $this->index,
            "now" => $this->now,
            "class" => $this->class,
            "result" => serialize(Y::unserialize($this->result)),
            "created_at" => $this->created_at,
        ];
    }

    private function mapExceptions()
    {

    }
}
