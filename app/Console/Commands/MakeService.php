<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class MakeService extends Command
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
    protected $description = 'Create a new service class';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');
        $className = Str::studly($name);
        $path = app_path("Services/{$className}.php");

        if (file_exists($path)) {
            $this->error("Service already exists!");
            return 1;
        }

        $stub = $this->getStub();
        $stub = str_replace('{{class}}', $className, $stub);

        file_put_contents($path, $stub);
        $this->info("Service created successfully.");
        return 0;
    }

    protected function getStub()
    {
        return <<<EOT
        <?php

        namespace App\Services;

        class {{class}}
        {
            // Your service methods here
        }
        EOT;
    }
}
