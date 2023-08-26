<?php

namespace Waterline\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Workflow\Models\StoredWorkflow;
use Workflow\Models\StoredWorkflowException;

class DashboardStatsController extends Controller
{
    public function index() {

        $flowsPastHour = config('workflows.stored_workflow_model', StoredWorkflow::class)::where('updated_at', '>=', now()->subHour())
            ->count();

        $exceptionsPastHour = config('workflows.stored_workflow_exception_model', StoredWorkflowException::class)::where('created_at', '>=', now()->subHour())
            ->count();

        $failedFlowsPastWeek = config('workflows.stored_workflow_model', StoredWorkflow::class)::where('status', 'failed')
            ->where('updated_at', '>=', now()->subDays(7))
            ->count();

        $maxWaitTimeWorkflow = config('workflows.stored_workflow_model', StoredWorkflow::class)::where('status', 'pending')
            ->orderBy('updated_at')
            ->first();

        $dbDriverName = DB::connection()->getDriverName();

        if ($dbDriverName === 'mongodb' && $maxWaitTimeWorkflow && $maxWaitTimeWorkflow->_id) {
            $maxWaitTimeWorkflow->id = $maxWaitTimeWorkflow->_id;
        }

        if ($dbDriverName === 'mongodb') {
            $maxDurationWorkflow = config('workflows.stored_workflow_model', StoredWorkflow::class)::select('*')
                ->raw(function ($collection) {
                    return $collection->aggregate([
                        [
                            '$match' => [
                                'status' => [ '$ne' => 'pending' ]
                            ]
                        ],
                        [
                            '$addFields' => [
                                'duration' => [
                                    '$subtract' => [
                                        ['$toDate' => '$updated_at'],
                                        ['$toDate' => '$created_at']
                                    ]
                                ]
                            ]
                        ],
                        [
                            '$sort' => ['duration' => -1]
                        ],
                        [
                            '$limit' => 1
                        ]
                    ]);
                })
                ->first();
                $maxDurationWorkflow->id = $maxDurationWorkflow->_id;
        } else {
            $maxDurationWorkflow = config('workflows.stored_workflow_model', StoredWorkflow::class)::select('*')
                ->when($dbDriverName === 'sqlite', function ($q) {
                    return $q->addSelect(DB::raw('julianday(created_at) - julianday(updated_at) as duration'));
                })
                ->when($dbDriverName === 'mysql', function ($q) {
                    return $q->addSelect(DB::raw('TIMEDIFF(created_at, updated_at) as duration'));
                })
                ->when($dbDriverName === 'pgsql', function ($q) {
                    return $q->addSelect(DB::raw('(EXTRACT(EPOCH FROM created_at - updated_at)) as duration'));
                })
                ->when($dbDriverName === 'sqlsrv', function ($q) {
                    return $q->addSelect(DB::raw('DATEDIFF(SECOND, created_at, updated_at) as duration'));
                })
                ->where('status', '!=', 'pending')
                ->orderBy('duration')
                ->first();
        }

        if ($dbDriverName === 'mongodb') {
            $maxExceptionsWorkflow = null;

            $mostExceptionWorkflowId = StoredWorkflowException::raw(function ($collection) {
                return $collection->aggregate([
                    ['$group' => ['_id' => '$stored_workflow_id', 'count' => ['$sum' => 1]]],
                    ['$sort' => ['count' => -1]],
                    ['$limit' => 1]
                ]);
            })->first()['_id'];

            $maxExceptionsWorkflow = StoredWorkflow::where('_id', $mostExceptionWorkflowId)->first();

            $maxExceptionsWorkflow->exceptions_count = StoredWorkflowException::where('stored_workflow_id', $mostExceptionWorkflowId)->count();

            $maxExceptionsWorkflow->id = $maxExceptionsWorkflow->_id;
        } else {
            $maxExceptionsWorkflow = config('workflows.stored_workflow_model', StoredWorkflow::class)::withCount('exceptions')
                ->has('exceptions')
                ->orderByDesc('exceptions_count')
                ->orderByDesc('updated_at')
                ->first();
        }

        return response()->json([
            'flows' => config('workflows.stored_workflow_model', StoredWorkflow::class)::count(),
            'flows_per_minute' => $flowsPastHour / 60,
            'flows_past_hour' => $flowsPastHour,
            'exceptions_past_hour' => $exceptionsPastHour,
            'failed_flows_past_week' => $failedFlowsPastWeek,
            'max_wait_time_workflow' => $maxWaitTimeWorkflow,
            'max_duration_workflow' => $maxDurationWorkflow,
            'max_exceptions_workflow' => $maxExceptionsWorkflow,
        ]);
    }
}
