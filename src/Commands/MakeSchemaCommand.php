<?php

namespace Laravilt\Schemas\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;

class MakeSchemaCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:schema {name : The name of the schema}
                            {--component : Generate a schema component instead of a standalone schema}
                            {--force : Overwrite existing file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new schema class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Schema';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->option('component')) {
            $this->type = 'Schema Component';
        }

        parent::handle();

        $this->components->info("{$this->type} [{$this->argument('name')}] created successfully.");

        // Show usage example
        $this->newLine();

        if ($this->option('component')) {
            $this->components->bulletList([
                'Import: use App\Schemas\Components\\'.str_replace('/', '\\', $this->argument('name')).';',
                'Usage: '.class_basename($this->argument('name')).'::make()->schema([...])',
            ]);
        } else {
            $this->components->bulletList([
                'Import: use App\Schemas\\'.str_replace('/', '\\', $this->argument('name')).';',
                'Usage: '.class_basename($this->argument('name')).'::make()->schema([...])',
            ]);
        }
    }

    /**
     * Get the stub file for the generator.
     */
    protected function getStub(): string
    {
        if ($this->option('component')) {
            return __DIR__.'/../../stubs/schema-component.stub';
        }

        return __DIR__.'/../../stubs/schema.stub';
    }

    /**
     * Get the default namespace for the class.
     */
    protected function getDefaultNamespace($rootNamespace): string
    {
        if ($this->option('component')) {
            return $rootNamespace.'\\Schemas\\Components';
        }

        return $rootNamespace.'\\Schemas';
    }

    /**
     * Build the class with the given name.
     */
    protected function buildClass($name): string
    {
        $stub = parent::buildClass($name);

        return $this->replaceSchemaName($stub);
    }

    /**
     * Replace the schema name in the stub.
     */
    protected function replaceSchemaName(string $stub): string
    {
        $name = class_basename($this->argument('name'));
        $kebabName = Str::kebab($name);
        $snakeName = Str::snake($name);

        $stub = str_replace('{{ schemaKebab }}', $kebabName, $stub);
        $stub = str_replace('{{ schemaSnake }}', $snakeName, $stub);

        return $stub;
    }

    /**
     * Get the destination class path.
     */
    protected function getPath($name): string
    {
        $name = Str::replaceFirst($this->rootNamespace(), '', $name);

        return $this->laravel['path'].'/'.str_replace('\\', '/', $name).'.php';
    }
}
