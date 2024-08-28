<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str; 


class MakeHelper extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:helper {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new helper class';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');
        $className = Str::studly($name);
        $path = app_path("Helpers/{$className}.php");

        if (file_exists($path)) {
            $this->error("Helper already exists!");
            return 1;
        }

        $stub = $this->getStub();
        $stub = str_replace('{{class}}', $className, $stub);

        file_put_contents($path, $stub);
        $this->info("Helper created successfully.");
        return 0;
    }

    protected function getStub()
    {
        return <<<EOT
        <?php

        namespace App\Helpers;

        class {{class}}
        {
            // Your helper methods here
        }
        EOT;
    }
}
