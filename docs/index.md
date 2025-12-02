# Laravilt Schemas Documentation

Complete schema system with sections, tabs, grids, and layout components for Laravilt.

## Table of Contents

1. [Getting Started](#getting-started)
2. [Architecture](#architecture)
3. [Schema Generation](#schema-generation)
4. [Layout Components](#layout-components)
5. [API Reference](#api-reference)
6. [MCP Server Integration](mcp-server.md)

## Overview

Laravilt Schemas provides a comprehensive layout system for organizing form fields and information displays:

- **Section**: Collapsible sections with headers, footers, and icons
- **Tabs**: Tabbed interfaces with multiple panels
- **Grid**: Multi-column grid layouts
- **Fieldset**: Grouped fields with legends
- **Reactive**: Dynamic visibility and state updates
- **Nested Schemas**: Support for deeply nested layouts
- **Inertia Integration**: Seamless Vue 3 integration

## Quick Start

```bash
# Generate a new schema class
php artisan make:schema ProductSchema

# Use in your forms or infolists
use App\Schemas\ProductSchema;
use Laravilt\Forms\Components\TextInput;

$schema = ProductSchema::make()
    ->schema([
        Section::make('Basic Information')
            ->schema([
                TextInput::make('name'),
                TextInput::make('sku'),
            ]),
    ]);
```

## Key Features

### 📦 Layout Components
- **Section**: Collapsible sections with headers/footers
- **Tabs**: Tabbed content panels
- **Grid**: Multi-column layouts
- **Fieldset**: Grouped fields with legends

### 🎨 Section Features
- Collapsible with custom state
- Icons (Lucide icons)
- Headers and descriptions
- Footers for actions
- Aside layout option
- Compact mode

### 📑 Tab Features
- Multiple tabs
- Active tab state
- Icons and badges
- Lazy loading
- URL persistence

### 📐 Grid Features
- 1-12 column layouts
- Responsive column spans
- Gap control
- Nested grids

### ⚡ Reactivity
- Conditional visibility
- Dynamic fields
- State management
- After state updated callbacks

## System Requirements

- PHP 8.3+
- Laravel 12+
- Inertia.js v2+
- Vue 3

## Installation

```bash
composer require laravilt/schemas
```

The service provider is auto-discovered and will register automatically.

## Configuration

Publish the configuration:

```bash
php artisan vendor:publish --tag=laravilt-schemas-config
```

Publish the assets:

```bash
php artisan vendor:publish --tag=laravilt-schemas-assets
```

## Basic Usage

### Section Component

```php
use Laravilt\Schemas\Components\Section;
use Laravilt\Forms\Components\TextInput;
use Laravilt\Forms\Components\Textarea;

Section::make('Product Details')
    ->description('Basic product information')
    ->icon('package')
    ->collapsible()
    ->schema([
        TextInput::make('name')
            ->label('Product Name')
            ->required(),

        TextInput::make('sku')
            ->label('SKU')
            ->required(),

        Textarea::make('description')
            ->label('Description')
            ->rows(5),
    ]);
```

### Tabs Component

```php
use Laravilt\Schemas\Components\Tabs;
use Laravilt\Schemas\Components\Tabs\Tab;

Tabs::make('product_tabs')
    ->tabs([
        Tab::make('Details')
            ->icon('info')
            ->schema([
                TextInput::make('name'),
                TextInput::make('sku'),
            ]),

        Tab::make('Pricing')
            ->icon('dollar-sign')
            ->schema([
                NumberField::make('price'),
                NumberField::make('cost'),
            ]),

        Tab::make('Inventory')
            ->icon('box')
            ->badge(fn ($record) => $record->low_stock ? 'Low Stock' : null)
            ->schema([
                NumberField::make('quantity'),
                Toggle::make('track_inventory'),
            ]),
    ]);
```

### Grid Component

```php
use Laravilt\Schemas\Components\Grid;

Grid::make()
    ->columns(2)
    ->schema([
        TextInput::make('first_name')
            ->columnSpan(1),

        TextInput::make('last_name')
            ->columnSpan(1),

        TextInput::make('email')
            ->columnSpan(2), // Full width

        TextInput::make('phone'),
        TextInput::make('company'),
    ]);
```

### Fieldset Component

```php
use Laravilt\Schemas\Components\Fieldset;

Fieldset::make('Shipping Address')
    ->schema([
        TextInput::make('shipping_address_line1')
            ->label('Address Line 1'),

        TextInput::make('shipping_city')
            ->label('City'),

        Select::make('shipping_country')
            ->label('Country')
            ->options([...]),
    ]);
```

## Advanced Features

### Collapsible Sections

```php
Section::make('Advanced Options')
    ->description('Additional configuration options')
    ->collapsible()
    ->collapsed() // Collapsed by default
    ->persistCollapsed() // Remember collapsed state
    ->schema([
        // Fields...
    ]);
```

### Aside Layout

```php
Section::make('Quick Actions')
    ->aside()
    ->description('Commonly used actions')
    ->schema([
        Action::make('save_draft'),
        Action::make('preview'),
        Action::make('publish'),
    ]);
```

### Conditional Visibility

```php
use Laravilt\Support\Utilities\Get;

Section::make('Shipping Details')
    ->visible(fn (Get $get) => $get('requires_shipping') === true)
    ->schema([
        TextInput::make('shipping_weight'),
        TextInput::make('shipping_dimensions'),
    ]);
```

### Nested Layouts

```php
Section::make('Product Information')
    ->schema([
        Grid::make()
            ->columns(2)
            ->schema([
                TextInput::make('name'),
                TextInput::make('sku'),
            ]),

        Tabs::make('details_tabs')
            ->tabs([
                Tab::make('Description')
                    ->schema([
                        RichEditor::make('description'),
                    ]),

                Tab::make('Specifications')
                    ->schema([
                        KeyValue::make('specifications'),
                    ]),
            ]),
    ]);
```

## Section Options

### Header and Footer

```php
Section::make('User Profile')
    ->description('Manage user profile information')
    ->icon('user')
    ->headerActions([
        Action::make('reset')
            ->label('Reset')
            ->icon('rotate-ccw'),
    ])
    ->footerActions([
        Action::make('save')
            ->label('Save Changes')
            ->icon('check'),
    ])
    ->schema([
        // Fields...
    ]);
```

### Compact Mode

```php
Section::make('Quick Info')
    ->compact()
    ->schema([
        TextEntry::make('status'),
        TextEntry::make('created_at'),
    ]);
```

## Tabs Options

### Tab Icons and Badges

```php
Tab::make('Orders')
    ->icon('shopping-cart')
    ->badge(fn ($record) => $record->orders_count)
    ->badgeColor('primary')
    ->schema([
        // Order fields...
    ]);
```

### Tab State Persistence

```php
Tabs::make('settings_tabs')
    ->persistTabInQueryString() // Save active tab in URL
    ->tabs([
        Tab::make('General'),
        Tab::make('Security'),
        Tab::make('Notifications'),
    ]);
```

## Grid Options

### Responsive Columns

```php
Grid::make()
    ->columns([
        'default' => 1,
        'sm' => 2,
        'lg' => 3,
        'xl' => 4,
    ])
    ->schema([
        // Fields with automatic responsive behavior
    ]);
```

### Custom Column Spans

```php
Grid::make()
    ->columns(12)
    ->schema([
        TextInput::make('title')
            ->columnSpan(8),

        Select::make('status')
            ->columnSpan(4),

        Textarea::make('content')
            ->columnSpan(12), // Full width
    ]);
```

## Generator Command

```bash
# Generate a schema class
php artisan make:schema ProductSchema

# Force overwrite existing file
php artisan make:schema ProductSchema --force
```

## Best Practices

1. **Use Sections for Organization**: Group related fields in sections
2. **Add Icons**: Use meaningful icons for better visual hierarchy
3. **Provide Descriptions**: Help users understand section purposes
4. **Use Tabs Sparingly**: Don't hide important information in tabs
5. **Responsive Grids**: Use responsive column configurations
6. **Collapsible Sections**: Make advanced options collapsible
7. **Meaningful Labels**: Use clear, descriptive section headings

## Examples

### Multi-Step Form Layout

```php
use Laravilt\Schemas\Components\Tabs;
use Laravilt\Schemas\Components\Tabs\Tab;
use Laravilt\Schemas\Components\Section;

Tabs::make('registration_steps')
    ->tabs([
        Tab::make('Personal Info')
            ->icon('user')
            ->schema([
                Section::make('Basic Information')
                    ->schema([
                        TextInput::make('first_name')->required(),
                        TextInput::make('last_name')->required(),
                        TextInput::make('email')->email()->required(),
                    ]),
            ]),

        Tab::make('Company')
            ->icon('building')
            ->schema([
                Section::make('Company Details')
                    ->schema([
                        TextInput::make('company_name')->required(),
                        TextInput::make('company_website'),
                        Select::make('industry')->required(),
                    ]),
            ]),

        Tab::make('Review')
            ->icon('check-circle')
            ->schema([
                Section::make('Summary')
                    ->description('Review your information before submitting')
                    ->schema([
                        // Display entered information
                    ]),
            ]),
    ]);
```

### Dashboard Layout

```php
Grid::make()
    ->columns(3)
    ->schema([
        Section::make('Quick Stats')
            ->columnSpan(2)
            ->schema([
                Grid::make()
                    ->columns(3)
                    ->schema([
                        StatsWidget::make('total_users'),
                        StatsWidget::make('total_orders'),
                        StatsWidget::make('revenue'),
                    ]),
            ]),

        Section::make('Recent Activity')
            ->columnSpan(1)
            ->aside()
            ->schema([
                // Activity list
            ]),

        Section::make('Sales Chart')
            ->columnSpan(2)
            ->schema([
                LineChartWidget::make('sales'),
            ]),

        Section::make('Top Products')
            ->columnSpan(1)
            ->schema([
                TableWidget::make('top_products'),
            ]),
    ]);
```

## Support

- GitHub Issues: github.com/laravilt/schemas
- Documentation: docs.laravilt.com
- Discord: discord.laravilt.com
