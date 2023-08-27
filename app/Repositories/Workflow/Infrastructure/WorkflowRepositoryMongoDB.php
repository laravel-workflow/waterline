<?php

namespace Waterline\Repositories\Workflow\Infrastructure;

class WorkflowRepositoryMongoDB extends WorkflowRepositoryBaseSQL
{
    public function maxWaitTimeWorkflow()
    {
        $maxWaitTimeWorkflow = $this->workflowModel::where('status', 'pending')
            ->orderBy('updated_at')
            ->first();

        if ($maxWaitTimeWorkflow && $maxWaitTimeWorkflow->_id) {
            $maxWaitTimeWorkflow->id = $maxWaitTimeWorkflow->_id;
        }

        return $maxWaitTimeWorkflow;
    }

    public function maxDurationWorkflow()
    {
        $maxDurationWorkflow = $this->workflowModel::select('*')
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

        if ($maxDurationWorkflow) {
            $maxDurationWorkflow->id = $maxDurationWorkflow->_id;
        }

        return $maxDurationWorkflow;
    }

    public function maxExceptionsWorkflow()
    {
        $maxExceptionsWorkflow =  $this->workflowExceptionModel::raw(function ($collection) {
            return $collection->aggregate([
                ['$group' => ['_id' => '$stored_workflow_id', 'count' => ['$sum' => 1]]],
                ['$sort' => ['count' => -1]],
                ['$limit' => 1]
            ]);
        })->first();

        if ($maxExceptionsWorkflow) {
            $mostExceptionWorkflowId = $maxExceptionsWorkflow['_id'];

            $maxExceptionsWorkflow = $this->workflowModel::where('_id', $mostExceptionWorkflowId)->first();

            if ($maxExceptionsWorkflow) {
                $maxExceptionsWorkflow->exceptions_count =  $this->workflowExceptionModel::where('stored_workflow_id', $mostExceptionWorkflowId)->count();
                $maxExceptionsWorkflow->id = $maxExceptionsWorkflow->_id;
            }
        }

        return $maxExceptionsWorkflow;
    }
}
