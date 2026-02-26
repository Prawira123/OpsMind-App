<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeRepository extends Command
{
    protected $signature = 'make:repository {name}';
    protected $description = 'Create a new repository class';

    public function handle()
    {
        $name = $this->argument('name');
        $path = app_path("Repositories/{$name}.php");

        if (File::exists($path)) {
            $this->error("Repository {$name} already exists!");
            return;
        }

        File::ensureDirectoryExists(app_path('Repositories'));

        File::put($path, $this->getStub($name));

        $this->info("Repository {$name} created successfully.");
    }

    private function getStub(string $name): string
    {
        $model = str_replace('Repository', '', $name);
        return <<<PHP
<?php

namespace App\Repositories;

use App\Models\\{$model};

class {$name} extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(new {$model}());
    }
}
PHP;
    }
}