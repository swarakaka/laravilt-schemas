<?php

use Laravilt\Schemas\Tests\TestCase;
use Laravilt\Support\Component;

uses(TestCase::class)->in(__DIR__);

/*
|--------------------------------------------------------------------------
| Helper Functions
|--------------------------------------------------------------------------
*/

// Helper function to create test components properly initialized
function createTestComponent(string $name): Component
{
    return new class($name) extends Component
    {
        protected string $view = 'test-view';

        public function __construct(string $name)
        {
            $this->name = $name;
            $this->setUp();
        }
    };
}
