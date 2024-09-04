<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class MakeRepository extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:repository {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new repository class';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');
        $className = Str::studly($name);
        $path = app_path("Repositories/{$className}.php");

        if (file_exists($path)) {
            $this->error("Repository already exists!");
            return 1;
        }

        $stub = $this->getStub();
        $stub = str_replace('{{class}}', $className, $stub);

        file_put_contents($path, $stub);
        $this->info("Repository created successfully.");
        return 0;
    }

    protected function getStub()
    {
        return <<<EOT
        <?php

        namespace App\Repositories;

        class {{class}}
        {
            // Your repository methods here
        }
        EOT;
    }
}
