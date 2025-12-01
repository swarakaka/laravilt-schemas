<?php

use Laravilt\Schemas\Components\Grid;

beforeEach(function () {
    $this->grid = Grid::make('test-grid');
});

it('can be instantiated with make method', function () {
    $grid = Grid::make('test');

    expect($grid)->toBeInstanceOf(Grid::class)
        ->and($grid->getName())->toBe('test');
});

it('has default single column', function () {
    expect($this->grid->getColumns())->toBe(1);
});

it('can set columns as integer', function () {
    $this->grid->columns(3);

    expect($this->grid->getColumns())->toBe(3);
});

it('can set responsive columns as array', function () {
    $columns = [
        'default' => 1,
        'sm' => 2,
        'md' => 3,
        'lg' => 4,
    ];

    $this->grid->columns($columns);

    expect($this->grid->getColumns())->toBe($columns)
        ->and($this->grid->getColumns()['sm'])->toBe(2)
        ->and($this->grid->getColumns()['lg'])->toBe(4);
});

it('can set and get schema', function () {
    $component = createTestComponent('child');

    $this->grid->schema([$component]);

    expect($this->grid->getSchema())->toHaveCount(1)
        ->and($this->grid->getSchema()[0])->toBe($component);
});

it('serializes columns and schema to laravilt props', function () {
    $component = createTestComponent('child');
    $component->label('Child Component');

    $this->grid
        ->columns(2)
        ->schema([$component]);

    $props = $this->grid->toLaraviltProps();

    expect($props)->toHaveKey('columns')
        ->and($props['columns'])->toBe(2)
        ->and($props)->toHaveKey('schema')
        ->and($props['schema'])->toHaveCount(1)
        ->and($props['schema'][0]['label'])->toBe('Child Component');
});

it('serializes responsive columns to laravilt props', function () {
    $columns = [
        'default' => 1,
        'md' => 3,
    ];

    $this->grid->columns($columns);
    $props = $this->grid->toLaraviltProps();

    expect($props['columns'])->toBe($columns)
        ->and($props['columns']['default'])->toBe(1)
        ->and($props['columns']['md'])->toBe(3);
});

it('supports method chaining', function () {
    $result = $this->grid
        ->columns(3)
        ->label('Test Grid')
        ->schema([]);

    expect($result)->toBe($this->grid);
});
