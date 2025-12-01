<?php

use Laravilt\Schemas\Components\Fieldset;

beforeEach(function () {
    $this->fieldset = Fieldset::make('test-fieldset');
});

it('can be instantiated with make method', function () {
    $fieldset = Fieldset::make('test');

    expect($fieldset)->toBeInstanceOf(Fieldset::class)
        ->and($fieldset->getName())->toBe('test');
});

it('can set and get label', function () {
    $this->fieldset->label('Test Label');

    expect($this->fieldset->getLabel())->toBe('Test Label');
});

it('can set and get legend', function () {
    $this->fieldset->legend('Test Legend');

    expect($this->fieldset->getLegend())->toBe('Test Legend');
});

it('legend is an alias for label', function () {
    $this->fieldset->legend('Test Legend');

    expect($this->fieldset->getLabel())->toBe('Test Legend')
        ->and($this->fieldset->getLegend())->toBe('Test Legend');
});

it('supports closures for label', function () {
    $this->fieldset->label(fn () => 'Dynamic Label');

    expect($this->fieldset->getLabel())->toBe('Dynamic Label');
});

it('supports closures for legend', function () {
    $this->fieldset->legend(fn () => 'Dynamic Legend');

    expect($this->fieldset->getLegend())->toBe('Dynamic Legend');
});

it('can set and get schema', function () {
    $component = createTestComponent('child');

    $this->fieldset->schema([$component]);

    expect($this->fieldset->getSchema())->toHaveCount(1)
        ->and($this->fieldset->getSchema()[0])->toBe($component);
});

it('serializes all properties to laravilt props', function () {
    $component = createTestComponent('child');
    $component->label('Child Component');

    $this->fieldset
        ->legend('Fieldset Legend')
        ->schema([$component]);

    $props = $this->fieldset->toLaraviltProps();

    expect($props)->toHaveKey('label')
        ->and($props['label'])->toBe('Fieldset Legend')
        ->and($props)->toHaveKey('legend')
        ->and($props['legend'])->toBe('Fieldset Legend')
        ->and($props)->toHaveKey('schema')
        ->and($props['schema'])->toHaveCount(1)
        ->and($props['schema'][0]['label'])->toBe('Child Component');
});

it('supports method chaining', function () {
    $result = $this->fieldset
        ->legend('Test')
        ->schema([]);

    expect($result)->toBe($this->fieldset);
});
