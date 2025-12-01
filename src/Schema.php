<?php

namespace Laravilt\Schemas;

use Laravilt\Schemas\Concerns\HasSchema;
use Laravilt\Support\Component;

class Schema extends Component
{
    use HasSchema;

    protected string $view = 'laravilt-schemas::schema';

    protected array $schema = [];

    /**
     * Set the schema components.
     */
    public function schema(array $components): static
    {
        $this->schema = $components;

        return $this;
    }

    /**
     * Get the schema components.
     */
    public function getSchema(): array
    {
        return $this->schema;
    }

    /**
     * Get all visible components in the schema.
     */
    public function getVisibleComponents(): array
    {
        return array_filter($this->schema, function ($item): bool {
            // Support both Components and Actions
            if (method_exists($item, 'isHidden')) {
                return ! $item->isHidden();
            }

            return true;
        });
    }

    /**
     * Serialize to Laravilt props with evaluation context.
     */
    public function toLaraviltProps(array &$data = [], mixed $record = null, ?string $changedField = null): array
    {
        \Log::info('[Schema] toLaraviltProps called', [
            'changedField' => $changedField,
            'hasChangedFieldInData' => $changedField ? array_key_exists($changedField, $data) : false,
            'data' => $data,
        ]);

        // Execute afterStateUpdated callbacks if a field changed
        if ($changedField && array_key_exists($changedField, $data)) {
            \Log::info('[Schema] Executing afterStateUpdated callbacks for field: '.$changedField);
            $this->executeAfterStateUpdatedCallbacks($this->schema, $changedField, $data[$changedField], $data, $record);
        }

        // Set evaluation context on all components recursively
        $this->setEvaluationContextRecursive($this->schema, $data, $record);

        return array_merge(parent::toLaraviltProps(), [
            'schema' => array_map(
                function ($item): array {
                    // Support both Components and Actions
                    if (method_exists($item, 'toLaraviltProps')) {
                        return $item->toLaraviltProps();
                    }
                    if (method_exists($item, 'toArray')) {
                        return $item->toArray();
                    }

                    return [];
                },
                $this->getVisibleComponents()
            ),
        ]);
    }

    /**
     * Recursively set evaluation context on all components.
     */
    protected function setEvaluationContextRecursive(array $components, array $data, mixed $record): void
    {
        foreach ($components as $component) {
            // Set evaluation context if component supports it
            if (method_exists($component, 'evaluationContext')) {
                $component->evaluationContext($data, $record);
            }

            // Handle nested schemas (Sections, Grids, Tabs, etc.)
            if (method_exists($component, 'getSchema')) {
                $nestedSchema = $component->getSchema();
                if (is_array($nestedSchema)) {
                    $this->setEvaluationContextRecursive($nestedSchema, $data, $record);
                }
            }

            // Handle tabs
            if (method_exists($component, 'getTabs')) {
                $tabs = $component->getTabs();
                if (is_array($tabs)) {
                    foreach ($tabs as $tab) {
                        if (method_exists($tab, 'getSchema')) {
                            $tabSchema = $tab->getSchema();
                            if (is_array($tabSchema)) {
                                $this->setEvaluationContextRecursive($tabSchema, $data, $record);
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Execute afterStateUpdated callbacks for a changed field.
     */
    protected function executeAfterStateUpdatedCallbacks(array $components, string $changedField, mixed $value, array &$data, mixed $record): void
    {
        \Log::info('[Schema] executeAfterStateUpdatedCallbacks called', [
            'changedField' => $changedField,
            'value' => $value,
            'componentCount' => count($components),
        ]);

        $set = new \Laravilt\Support\Utilities\Set($data);

        foreach ($components as $component) {
            // Check if this component is the one that changed
            if (method_exists($component, 'getName') && $component->getName() === $changedField) {
                \Log::info('[Schema] Found matching component', [
                    'componentName' => $component->getName(),
                    'hasGetAfterStateUpdated' => method_exists($component, 'getAfterStateUpdated'),
                ]);

                // Check if it has an afterStateUpdated callback
                if (method_exists($component, 'getAfterStateUpdated')) {
                    $callback = $component->getAfterStateUpdated();
                    \Log::info('[Schema] afterStateUpdated callback', [
                        'isClosure' => $callback instanceof \Closure,
                        'callbackType' => gettype($callback),
                    ]);

                    if ($callback instanceof \Closure) {
                        \Log::info('[Schema] Executing afterStateUpdated callback for field: '.$changedField);
                        // Execute the callback with dependency injection
                        $get = new \Laravilt\Support\Utilities\Get($data);
                        app()->call($callback, [
                            'value' => $value,
                            'get' => $get,
                            'set' => $set,
                            'data' => $data,
                            'record' => $record,
                        ]);
                        \Log::info('[Schema] afterStateUpdated callback executed, data after:', $data);
                    }
                }
            }

            // Handle nested schemas
            if (method_exists($component, 'getSchema')) {
                $nestedSchema = $component->getSchema();
                if (is_array($nestedSchema)) {
                    $this->executeAfterStateUpdatedCallbacks($nestedSchema, $changedField, $value, $data, $record);
                }
            }

            // Handle tabs
            if (method_exists($component, 'getTabs')) {
                $tabs = $component->getTabs();
                if (is_array($tabs)) {
                    foreach ($tabs as $tab) {
                        if (method_exists($tab, 'getSchema')) {
                            $tabSchema = $tab->getSchema();
                            if (is_array($tabSchema)) {
                                $this->executeAfterStateUpdatedCallbacks($tabSchema, $changedField, $value, $data, $record);
                            }
                        }
                    }
                }
            }
        }
    }
}
