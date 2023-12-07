<?php

namespace JiagBrody\LaravelFacturaMx\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use JiagBrody\LaravelFacturaMx\LaravelFacturaMxServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('migrate', ['--database' => 'testbench'])->run();

        Factory::guessFactoryNamesUsing(
            fn(string $modelName) => 'JiagBrody\\LaravelFacturaMx\\Database\\Factories\\' . class_basename($modelName) . 'Factory'
        );


    }

    protected function getPackageProviders($app)
    {
        return [
            LaravelFacturaMxServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        // config()->set('database.default', 'testbench');

        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);

        $migration = include __DIR__ . '/../database/migrations/create_factura_mx_table.php.stub';
        $migration->up();
    }
}
