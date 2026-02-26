<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeService extends Command
{
    protected $signature = 'make:service {name}';
    protected $description = 'Create a new service class';

    public function handle()
    {
        $name = $this->argument('name');
        $path = app_path("Services/{$name}.php");

        if (File::exists($path)) {
            $this->error("Service {$name} already exists!");
            return;
        }

        File::ensureDirectoryExists(app_path('Services'));

        File::put($path, $this->getStub($name));

        $this->info("Service {$name} created successfully.");
    }

    private function getStub(string $name): string
    {
        return <<<PHP
<?php

namespace App\Services;

class {$name} extends BaseService
{
    public function __construct()
    {
        //
    }
}
PHP;
    }
}