<?php

namespace Laravilt\Schemas\Mcp\Tools;

use Illuminate\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

class GenerateSchemaTool extends Tool
{
    protected string $description = 'Generate a new schema class for layout organization';

    public function handle(Request $request): Response
    {
        $name = $request->string('name');

        $command = 'php '.base_path('artisan').' make:schema "'.$name.'" --no-interaction';

        if ($request->boolean('force', false)) {
            $command .= ' --force';
        }

        exec($command, $output, $returnCode);

        if ($returnCode === 0) {
            $response = "✅ Schema '{$name}' created successfully!\n\n";
            $response .= "📖 Location: app/Schemas/{$name}.php\n\n";
            $response .= "📦 Available layout components: Section, Tabs, Tab, Grid, Fieldset\n";

            return Response::text($response);
        } else {
            return Response::text('❌ Failed to create schema: '.implode("\n", $output));
        }
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'name' => $schema->string()
                ->description('Schema class name in StudlyCase (e.g., "ProductSchema")')
                ->required(),
            'force' => $schema->boolean()
                ->description('Overwrite existing file')
                ->default(false),
        ];
    }
}
