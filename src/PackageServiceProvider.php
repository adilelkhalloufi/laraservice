<?php

namespace adev\laraservice;

use Illuminate\Support\ServiceProvider;
use App\Console\Commands\CommandConstants;
use App\Console\Commands\CommandServiceLayer;
  

class PackageServiceProvider extends ServiceProvider
{
    public function boot()
    {

        
        // Override the default model stub
        $this->app->extend('command.make.model', function ($command, $app) {
            // Bind the custom MakeModelCommand with the custom stub
            $command->setStubPath(__DIR__.'/../stubs/model.stub');
            return $command;
        });

        // Optionally, publish the stub file when the package is installed
        $this->publishes([
            __DIR__.'/../stubs/model.stub' => resource_path('stubs/model.stub'),
        ], 'stubs');

        if ($this->app->runningInConsole()) {
            $this->commands([
                CommandConstants::class,
                CommandServiceLayer::class,
            ]);
        }
    }

    public function register()
    {
        //
    }
}