<?php

namespace Tarfin\Moka;

use Illuminate\Support\ServiceProvider;

class MokaServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(MokaClient::class, function () {
            return new MokaClient;
        });

        $this->mergeConfigFrom(__DIR__.'/../config/moka.php', 'moka');
    }

    public function provides(): array
    {
        return [
            MokaClient::class,
            'moka',
        ];
    }

    public function boot(): void
    {
        $this->app->alias(MokaClient::class, 'moka');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/moka.php' => config_path('moka.php'),
            ], 'moka-config');

            $this->publishes([
                __DIR__.'/../database/migrations/2024_01_15_122436_create_moka_payments_table.php' => database_path('migrations/'.date('Y_m_d_His', time()).'_create_moka_payments_table.php'),
            ], 'moka-migrations');
        }

        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
    }
}
