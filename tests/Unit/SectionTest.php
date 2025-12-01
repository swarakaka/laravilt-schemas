<?php

use Laravilt\Schemas\Components\Section;

beforeEach(function () {
    $this->section = Section::make('test-section');
});

it('can be instantiated with make method', function () {
    $section = Section::make('test');

    expect($section)->toBeInstanceOf(Section::class)
        ->and($section->getName())->toBe('test');
});

it('can set and get heading', function () {
    $this->section->heading('Test Heading');

    expect($this->section->getHeading())->toBe('Test Heading');
});

it('can set and get description', function () {
    $this->section->description('Test Description');

    expect($this->section->getDescription())->toBe('Test Description');
});

it('can set and get icon', function () {
    $this->section->icon('heroicon-o-user');

    expect($this->section->getIcon())->toBe('heroicon-o-user');
});

it('supports closures for heading', function () {
    $this->section->heading(fn () => 'Dynamic Heading');

    expect($this->section->getHeading())->toBe('Dynamic Heading');
});

it('supports closures for description', function () {
    $this->section->description(fn () => 'Dynamic Description');

    expect($this->section->getDescription())->toBe('Dynamic Description');
});

it('can be collapsible', function () {
    $this->section->collapsible();

    expect($this->section->isCollapsible())->toBeTrue();
});

it('can be collapsed by default', function () {
    $this->section->collapsible()->collapsed();

    expect($this->section->isCollapsed())->toBeTrue();
});

it('is not collapsible by default', function () {
    expect($this->section->isCollapsible())->toBeFalse()
        ->and($this->section->isCollapsed())->toBeFalse();
});

it('can set and get schema', function () {
    $component = createTestComponent('child');

    $this->section->schema([$component]);

    expect($this->section->getSchema())->toHaveCount(1)
        ->and($this->section->getSchema()[0])->toBe($component);
});

it('serializes all properties to laravilt props', function () {
    $component = createTestComponent('child');
    $component->label('Child Component');

    $this->section
        ->heading('Section Heading')
        ->description('Section Description')
        ->icon('heroicon-o-information-circle')
        ->collapsible()
        ->collapsed()
        ->schema([$component]);

    $props = $this->section->toLaraviltProps();

    expect($props)->toHaveKey('heading')
        ->and($props['heading'])->toBe('Section Heading')
        ->and($props)->toHaveKey('description')
        ->and($props['description'])->toBe('Section Description')
        ->and($props)->toHaveKey('icon')
        ->and($props['icon'])->toBe('heroicon-o-information-circle')
        ->and($props)->toHaveKey('collapsible')
        ->and($props['collapsible'])->toBeTrue()
        ->and($props)->toHaveKey('collapsed')
        ->and($props['collapsed'])->toBeTrue()
        ->and($props)->toHaveKey('schema')
        ->and($props['schema'])->toHaveCount(1);
});

it('supports method chaining', function () {
    $result = $this->section
        ->heading('Test')
        ->description('Description')
        ->collapsible()
        ->schema([]);

    expect($result)->toBe($this->section);
});
