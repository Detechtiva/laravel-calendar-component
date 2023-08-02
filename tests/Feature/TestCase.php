<?php

namespace Detechtiva\VueCalendarForLaravel\Tests\Feature;

use CreateEventsTable;
use CreateUsersTable;
use CreateWorkOrdersTable;
use Detechtiva\VueCalendarForLaravel\VueCalendarForLaravelServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->withFactories(__DIR__ . '/../database/factories');
        $this->withFactories(__DIR__ . '/database/factories');
    }

    protected function getPackageProviders($app): array
    {
        return [
            VueCalendarForLaravelServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        include_once __DIR__ . '/../../database/migrations/create_events_table.php.stub';

        include_once __DIR__ . '/database/migrations/create_work_orders_table.php';
        include_once __DIR__ . '/database/migrations/create_users_table.php';

        (new CreateEventsTable())->up();
        (new CreateWorkOrdersTable())->up();
        (new CreateUsersTable())->up();
    }
}