# MCP Server Integration

The Laravilt Schemas package can be integrated with MCP (Model Context Protocol) server for AI agent interaction.

## Available Generator Command

### make:schema
Generate a new schema class.

**Usage:**
```bash
php artisan make:schema ProductSchema
php artisan make:schema Admin/ProductSchema
php artisan make:schema ProductSchema --force
```

**Arguments:**
- `name` (string, required): Schema class name (StudlyCase)

**Options:**
- `--force`: Overwrite existing file

**Generated Structure:**
```php
<?php

namespace App\Schemas;

use Laravilt\Schemas\Schema;
use Laravilt\Schemas\Components\Section;
use Laravilt\Schemas\Concerns\HasSchema;

class ProductSchema extends Schema
{
    use HasSchema;

    protected function setUp(): void
    {
        parent::setUp();

        $this->schema([
            Section::make('Information')
                ->description('Add your schema components here')
                ->schema([
                    // Add form fields or entries here
                ]),
        ]);
    }
}
```

## Layout Components Reference

For MCP tools to provide component information:

- **Section**: Collapsible sections with headers, footers, and icons
- **Tabs**: Tabbed content panels with icons and badges
- **Tab**: Individual tab within Tabs component
- **Grid**: Multi-column grid layouts with responsive support
- **Fieldset**: Grouped fields with legends

## Integration Example

MCP server tools should provide:

1. **list-schemas** - List all schema classes in the application
2. **schema-info** - Get details about a specific schema class
3. **generate-schema** - Generate a new schema class with specified layout
4. **list-layout-components** - List all available layout components

## Security

The MCP server runs with the same permissions as your Laravel application. Ensure:
- Proper file permissions on the app/Schemas directory
- Secure configuration of the MCP server
- Limited access to the MCP configuration file
