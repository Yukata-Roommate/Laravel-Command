<?php

namespace YukataRm\Laravel\Command;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;

use YukataRm\Laravel\Command\Commands\TreeCommand;

/**
 * Command Service Provider
 *
 * @package YukataRm\Laravel\Command
 */
class ServiceProvider extends BaseServiceProvider
{
    /*----------------------------------------*
     * Boot
     *----------------------------------------*/

    /**
     * boot
     *
     * @return void
     */
    public function boot(): void
    {
        $this->bootCommands();
    }

    /**
     * boot commands
     *
     * @return void
     */
    protected function bootCommands(): void
    {
        if (!$this->app->runningInConsole()) return;

        $this->commands([
            TreeCommand::class,
        ]);
    }
}
