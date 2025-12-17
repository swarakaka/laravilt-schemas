![schemas](https://raw.githubusercontent.com/laravilt/schemas/master/arts/screenshot.jpg)

# Laravilt Schemas

[![Latest Stable Version](https://poser.pugx.org/laravilt/schemas/version.svg)](https://packagist.org/packages/laravilt/schemas)
[![License](https://poser.pugx.org/laravilt/schemas/license.svg)](https://packagist.org/packages/laravilt/schemas)
[![Downloads](https://poser.pugx.org/laravilt/schemas/d/total.svg)](https://packagist.org/packages/laravilt/schemas)
[![Dependabot Updates](https://github.com/laravilt/schemas/actions/workflows/dependabot/dependabot-updates/badge.svg)](https://github.com/laravilt/schemas/actions/workflows/dependabot/dependabot-updates)
[![PHP Code Styling](https://github.com/laravilt/schemas/actions/workflows/fix-php-code-styling.yml/badge.svg)](https://github.com/laravilt/schemas/actions/workflows/fix-php-code-styling.yml)
[![Tests](https://github.com/laravilt/schemas/actions/workflows/tests.yml/badge.svg)](https://github.com/laravilt/schemas/actions/workflows/tests.yml)



Complete schema system with sections, tabs, grids, and layout components for Laravilt. Organize form fields and information displays with powerful layout components.

## Features

- 📦 **8 Layout Components** - Section, Tabs, Grid, Fieldset, Split, Wizard, Step, Columns
- 🎨 **Section Features** - Collapsible, icons, headers, footers, aside layout
- 📑 **Tab Features** - Multiple tabs, icons, badges, lazy loading
- 📐 **Grid Features** - 1-12 column layouts, responsive spans
- ⚡ **Reactivity** - Conditional visibility, dynamic fields, state management
- 🔄 **Nested Schemas** - Support for deeply nested layouts

## Layout Components

| Component | Description |
|-----------|-------------|
| `Section` | Collapsible sections with headings and icons |
| `Tabs` | Tabbed interfaces with badges |
| `Grid` | Responsive multi-column layouts |
| `Fieldset` | HTML fieldset grouping |
| `Split` | Two-column responsive layouts |
| `Wizard` | Multi-step form workflows |
| `Step` | Individual wizard steps |
| `Columns` | Simple two-column wrapper |

## Quick Examples

### Section with Columns

```php
use Laravilt\Schemas\Components\Section;
use Laravilt\Forms\Components\TextInput;

Section::make('Product Information')
    ->description('Basic product details')
    ->icon('Package')
    ->columns(2)
    ->collapsible()
    ->schema([
        TextInput::make('name')->required(),
        TextInput::make('sku')->required(),
        TextInput::make('price')->numeric()->prefix('$'),
        TextInput::make('stock')->numeric(),
    ]);
```

### Tabs

```php
use Laravilt\Schemas\Components\Tabs;
use Laravilt\Schemas\Components\Tab;

Tabs::make('product_tabs')
    ->tabs([
        Tab::make('details')
            ->label('Details')
            ->icon('FileText')
            ->schema([...]),

        Tab::make('pricing')
            ->label('Pricing')
            ->icon('DollarSign')
            ->badge(fn ($record) => $record?->has_discount ? 'Sale' : null)
            ->schema([...]),
    ]);
```

### Wizard

```php
use Laravilt\Schemas\Components\Wizard;
use Laravilt\Schemas\Components\Step;

Wizard::make()
    ->steps([
        Step::make('account')
            ->label('Account')
            ->icon('User')
            ->schema([...]),

        Step::make('profile')
            ->label('Profile')
            ->icon('Settings')
            ->schema([...]),
    ])
    ->skippable();
```

## Installation

```bash
composer require laravilt/schemas
```

## Generator Command

```bash
php artisan make:schema ProductSchema
```

## Documentation

- **[Complete Documentation](docs/index.md)** - All layout components and features
- **[MCP Server Guide](docs/mcp-server.md)** - AI agent integration

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
