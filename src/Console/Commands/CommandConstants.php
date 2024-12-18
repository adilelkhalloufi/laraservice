<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;

class CommandConstants extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:constants {model}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate column constants for a given model based on its table schema';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $modelName = $this->argument('model');

        // Handle "all" argument to process all models
        if ($modelName === 'all') {
            $modelDirectory = app_path('Models');
            $modelFiles = scandir($modelDirectory);

            foreach ($modelFiles as $file) {
                if (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                    $this->processModel(pathinfo($file, PATHINFO_FILENAME));
                }
            }

            $this->info('Column constants and TABLE_NAME generated for all models.');

            return;
        }

        // Process a single model
        $this->processModel($modelName);
    }

    private function processModel(string $modelName)
    {
        $modelClass = "App\\Models\\{$modelName}";

        if (! class_exists($modelClass)) {
            $this->error("Model {$modelName} does not exist.");

            return;
        }

        $model = new $modelClass;
        $table = $model->getTable();
        $columns = Schema::getColumnListing($table);

        // Generate TABLE_NAME constant
        $constants = "    public const TABLE_NAME = '{$table}';\n\n";

        // Generate column constants
        foreach ($columns as $column) {
            $constantName = strtoupper('COL_'.$column);
            $constants .= "    public const {$constantName} = '{$column}';\n";
        }

        $modelPath = app_path("Models/{$modelName}.php");
        $content = file_get_contents($modelPath);

        $placeholder = '/* PLACEHOLDER_FOR_CONSTANTS */';
        if (strpos($content, $placeholder) === false) {
            $this->error("The model {$modelName} does not have the placeholder {$placeholder}. Add it manually.");

            return;
        }

        $content = str_replace($placeholder, $constants, $content);
        file_put_contents($modelPath, $content);

        $this->info("Column constants and TABLE_NAME generated successfully for {$modelName}.");
    }
}