<?php
declare(strict_types=1);

namespace Waterline\Transformer;

use Carbon\CarbonInterface;
use DateTimeInterface;
use Illuminate\Support\Arr;
use Waterline\Dto\ChartDataPoint;
use Workflow\Models\StoredWorkflow;
use Workflow\Models\StoredWorkflowLog;
use Workflow\Workflow;

class WorkflowToChartDataTransformer
{
    /**
     * @return ChartDataPoint[]
     */
    public function transform(StoredWorkflow $storedWorkflow) : array {
        $data = $this->transformWorkflow($storedWorkflow);

        foreach ($storedWorkflow->children as $childWorkflow) {
            $data = array_merge(
                $data,
                $this->transform($childWorkflow),
            );
        }

        return $data;
    }

    /**
     * @return ChartDataPoint
     */
    private function transformWorkflow(StoredWorkflow $storedWorkflow) : array {
        $data = [
            app(ChartDataPoint::class, [
                'x' => $storedWorkflow->class,
                'yMin' => (float) $storedWorkflow->created_at->isoFormat('XSSS'),
                'yMax' => (float) $storedWorkflow->updated_at->isoFormat('XSSS'),
                'type' => 'Workflow'
            ])
        ];

        $prevLogCreated = $storedWorkflow->created_at;
        foreach ($storedWorkflow->logs as $log) {
            if (is_subclass_of($log->class, Workflow::class)) {
                continue;
            }

            $data[] = $this->transformLog($log, $prevLogCreated);
            $prevLogCreated = $log->created_at;
        }

        return $data;
    }

    /**
     * @return ChartDataPoint
     */
    private function transformLog(StoredWorkflowLog $storedWorkflowLog, CarbonInterface $prevLogCreated) : ChartDataPoint {

        return app(ChartDataPoint::class, [
            'x' => $storedWorkflowLog->class,
            'yMin' => (float) $prevLogCreated->isoFormat('XSSS'),
            'yMax' => (float) $storedWorkflowLog->created_at->isoFormat('XSSS'),
            'type' => 'Activity',
        ]);
    }
}
