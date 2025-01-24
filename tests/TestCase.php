<?php

namespace Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Tarfin\Moka\MokaServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            MokaServiceProvider::class,
        ];
    }

    protected function defineDatabaseMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }

    protected function getEnvironmentSetUp($app): void
    {
        config()->set('moka.dealer_code', 'test_dealer');
        config()->set('moka.username', 'test_user');
        config()->set('moka.password', 'test_pass');
        config()->set('moka.check_key', 'test_check_key');
        config()->set('moka.sandbox_mode', true);
        config()->set('moka.sandbox_url', 'https://service.refmoka.com');
        config()->set('moka.production_url', 'https://service.moka.com');
        config()->set('moka.store_failed_payments', false);
    }

    protected function setUp(): void
    {
        parent::setUp();
    }
}
