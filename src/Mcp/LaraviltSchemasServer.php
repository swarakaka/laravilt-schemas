<?php

namespace Laravilt\Schemas\Mcp;

use Laravel\Mcp\Server;
use Laravilt\Schemas\Mcp\Tools\GenerateSchemaTool;
use Laravilt\Schemas\Mcp\Tools\SearchDocsTool;

class LaraviltSchemasServer extends Server
{
    protected string $name = 'Laravilt Schemas';

    protected string $version = '1.0.0';

    protected string $instructions = <<<'MARKDOWN'
        This server provides schema layout capabilities for Laravilt projects.

        You can:
        - Generate new schema classes
        - Search schemas documentation
        - Access information about layout components (sections, tabs, grids)

        Schemas organize form fields and information displays with sections, tabs, and grids.
    MARKDOWN;

    protected array $tools = [
        GenerateSchemaTool::class,
        SearchDocsTool::class,
    ];
}
