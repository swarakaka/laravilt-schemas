<?php

use Laravilt\Schemas\Components\Split;

beforeEach(function () {
    $this->split = Split::make('test-split');
});

it('can be instantiated with make method', function () {
    $split = Split::make('test');

    expect($split)->toBeInstanceOf(Split::class)
        ->and($split->getName())->toBe('test');
});

it('can set and get start schema', function () {
    $component = createTestComponent('child');

    $this->split->startSchema([$component]);

    expect($this->split->getStartSchema())->toHaveCount(1)
        ->and($this->split->getStartSchema()[0])->toBe($component);
});

it('can set and get end schema', function () {
    $component = createTestComponent('child');

    $this->split->endSchema([$component]);

    expect($this->split->getEndSchema())->toHaveCount(1)
        ->and($this->split->getEndSchema()[0])->toBe($component);
});

it('supports leftSchema alias for startSchema', function () {
    $component = createTestComponent('child');

    $this->split->leftSchema([$component]);

    expect($this->split->getLeftSchema())->toHaveCount(1)
        ->and($this->split->getStartSchema())->toHaveCount(1);
});

it('supports rightSchema alias for endSchema', function () {
    $component = createTestComponent('child');

    $this->split->rightSchema([$component]);

    expect($this->split->getRightSchema())->toHaveCount(1)
        ->and($this->split->getEndSchema())->toHaveCount(1);
});

it('can set from breakpoint', function () {
    $this->split->fromBreakpoint('lg');

    expect($this->split->getFromBreakpoint())->toBe('lg');
});

it('has default breakpoint as md', function () {
    expect($this->split->getFromBreakpoint())->toBe('md');
});

it('can set start column span', function () {
    $this->split->startColumnSpan(4);

    $props = $this->split->toLaraviltProps();
    expect($props['startColumnSpan'])->toBe(4);
});

it('can set end column span', function () {
    $this->split->endColumnSpan(8);

    $props = $this->split->toLaraviltProps();
    expect($props['endColumnSpan'])->toBe(8);
});

it('has default column spans', function () {
    $props = $this->split->toLaraviltProps();

    expect($props['startColumnSpan'])->toBe('md:col-span-6')
        ->and($props['endColumnSpan'])->toBe('md:col-span-6');
});

it('serializes all schemas to laravilt props', function () {
    $start = createTestComponent('start');
    $start->label('Start Component');

    $end = createTestComponent('end');
    $end->label('End Component');

    $this->split
        ->startSchema([$start])
        ->endSchema([$end])
        ->fromBreakpoint('lg');

    $props = $this->split->toLaraviltProps();

    expect($props)->toHaveKey('startSchema')
        ->and($props['startSchema'])->toHaveCount(1)
        ->and($props['startSchema'][0]['label'])->toBe('Start Component')
        ->and($props)->toHaveKey('endSchema')
        ->and($props['endSchema'])->toHaveCount(1)
        ->and($props['endSchema'][0]['label'])->toBe('End Component')
        ->and($props)->toHaveKey('leftSchema')
        ->and($props['leftSchema'])->toHaveCount(1)
        ->and($props)->toHaveKey('rightSchema')
        ->and($props['rightSchema'])->toHaveCount(1)
        ->and($props)->toHaveKey('fromBreakpoint')
        ->and($props['fromBreakpoint'])->toBe('lg');
});

it('supports method chaining', function () {
    $result = $this->split
        ->startSchema([])
        ->endSchema([])
        ->fromBreakpoint('lg')
        ->startColumnSpan(4)
        ->endColumnSpan(8);

    expect($result)->toBe($this->split);
});
