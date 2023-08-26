<?php

namespace Waterline\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Waterline\Tests\TestCase;
use Workflow\Models\StoredWorkflow;

class DashboardControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testIndex()
    {
        $response = $this
            ->get('/waterline/api/stats');

        $response
            ->assertStatus(200);
    }
}
