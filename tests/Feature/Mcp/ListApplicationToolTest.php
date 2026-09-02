<?php


use App\Enums\ApplicationStatus;
use App\Mcp\Servers\ApplicationTrackerServer;
use App\Mcp\Tools\ListApplicationsTool;
use App\Models\Application;
use Illuminate\Support\Collection;

describe('list job applications through the mcp server', function () {
    test('list applications, filtered by status', function () {
        /** @var Collection<Application> $applications */
        $applications = Application::factory(count: 3)
            ->sequence(
                ['status' => ApplicationStatus::LEAD],
                ['status' => ApplicationStatus::APPLIED],
                ['status' => ApplicationStatus::REJECTED],
            )
            ->create();

        /** @var Application $rejected */
        $rejected = $applications->last();

        $response = ApplicationTrackerServer::tool(ListApplicationsTool::class, [
            'status' => 'rejected',
        ]);

        $response
            ->assertOk()
            ->assertSee($rejected->role_title)
            ->assertSee($rejected->company->name)
            ->assertSee('(rejected)');
    });
});
