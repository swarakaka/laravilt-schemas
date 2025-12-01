<?php

use Laravilt\Schemas\Components\Columns;

beforeEach(function () {
    $this->columns = Columns::make('test-columns');
});

it('can be instantiated with make method', function () {
    $columns = Columns::make('test');

    expect($columns)->toBeInstanceOf(Columns::class)
        ->and($columns->getName())->toBe('test');
});

it('can set and get schema', function () {
    $component1 = createTestComponent('child1');
    $component2 = createTestComponent('child2');

    $this->columns->schema([$component1, $component2]);

    expect($this->columns->getSchema())->toHaveCount(2)
        ->and($this->columns->getSchema()[0])->toBe($component1)
        ->and($this->columns->getSchema()[1])->toBe($component2);
});

it('serializes schema to laravilt props', function () {
    $component1 = createTestComponent('child1');
    $component1->label('First Component');

    $component2 = createTestComponent('child2');
    $component2->label('Second Component');

    $this->columns->schema([$component1, $component2]);

    $props = $this->columns->toLaraviltProps();

    expect($props)->toHaveKey('schema')
        ->and($props['schema'])->toHaveCount(2)
        ->and($props['schema'][0]['label'])->toBe('First Component')
        ->and($props['schema'][1]['label'])->toBe('Second Component');
});

it('inherits Support Component traits', function () {
    $this->columns
        ->label('Test Columns')
        ->helperText('Helper text')
        ->disabled();

    expect($this->columns->getLabel())->toBe('Test Columns')
        ->and($this->columns->getHelperText())->toBe('Helper text')
        ->and($this->columns->isDisabled())->toBeTrue();
});

it('supports method chaining', function () {
    $result = $this->columns
        ->label('Test')
        ->schema([]);

    expect($result)->toBe($this->columns);
});

it('converts to array', function () {
    $array = $this->columns->toArray();

    expect($array)->toBeArray()
        ->and($array)->toHaveKey('component')
        ->and($array)->toHaveKey('schema');
});
