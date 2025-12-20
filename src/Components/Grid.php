<?php

namespace Laravilt\Schemas\Components;

use Laravilt\Schemas\Concerns\HasSchema;
use Laravilt\Support\Component;

class Grid extends Component
{
    use HasSchema;

    protected string $view = 'laravilt-schemas::components.grid';

    protected int|array $columns = 1;

    protected array $schema = [];

    /**
     * Create a new Grid instance.
     * Supports both Grid::make('name') and Grid::make(2) or Grid::make(['default' => 1]).
     *
     * @param  string|int|array  $nameOrColumns  Either a name string or columns configuration
     */
    public static function make(string|int|array $nameOrColumns = 'grid'): static
    {
        // If columns are passed directly (int or array), use a default name
        if (is_int($nameOrColumns) || is_array($nameOrColumns)) {
            $static = app(static::class);
            $static->name = 'grid';
            $static->columns = $nameOrColumns;
            $static->setUp();

            return $static;
        }

        // Otherwise, use the parent make() with the string name
        return parent::make($nameOrColumns);
    }

    /**
     * Set number of columns.
     *
     * @param  int|array  $columns  Number of columns or responsive array [default => 1, sm => 2, md => 3, lg => 4]
     */
    public function columns(int|array $columns): static
    {
        $this->columns = $columns;

        return $this;
    }

    /**
     * Get columns configuration.
     */
    public function getColumns(): int|array
    {
        return $this->columns;
    }

    /**
     * Set the grid schema.
     *
     * @param  array<Component>  $components
     */
    public function schema(array $components): static
    {
        $this->schema = $components;

        return $this;
    }

    /**
     * Get the schema.
     */
    public function getSchema(): array
    {
        return $this->schema;
    }

    /**
     * Serialize to Laravilt props.
     */
    public function toLaraviltProps(): array
    {
        return array_merge(parent::toLaraviltProps(), [
            'columns' => $this->getColumns(),
            'schema' => array_map(
                fn (Component $component): array => $component->toLaraviltProps(),
                $this->getSchema()
            ),
        ]);
    }
}
