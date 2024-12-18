<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CommandServiceLayer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:service {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new service layer class';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $name = $this->argument('name');
        $serviceName = $name.'Service';

        $servicePath = app_path('Services/'.$serviceName.'.php');

        if (File::exists($servicePath)) {
            $this->error("Service layer $serviceName already exists!");

            return;
        }

        $serviceTemplate = "<?php

        namespace App\\Services;

        class $serviceName
        {
            // Your service methods go here
             public function __construct() {}
        }
        ";

        File::ensureDirectoryExists(app_path('Services'));
        File::put($servicePath, $serviceTemplate);

        $this->info("$serviceName created successfully!");
    }
}