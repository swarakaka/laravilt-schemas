<?php

namespace Laravilt\Schemas;

use Laravilt\Schemas\Concerns\HasSchema;
use Laravilt\Support\Component;

class Schema extends Component
{
    use HasSchema;

    protected string $view = 'laravilt-schemas::schema';

    protected array $schema = [];

    protected array $data = [];

    protected int $gridColumns = 1;

    /**
     * The model class associated with this schema/form.
     *
     * @var class-string|null
     */
    protected ?string $model = null;

    /**
     * The resource slug associated with this schema/form.
     */
    protected ?string $resourceSlug = null;

    /**
     * The relation manager class (for forms inside relation managers).
     */
    protected ?string $relationManagerClass = null;

    /**
     * Create a new schema instance.
     * Override parent to make name optional.
     */
    public static function make(?string $name = null): static
    {
        $static = app(static::class);
        $static->name = $name ?? 'schema';
        $static->setUp();

        return $static;
    }

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
     * Propagates model, resourceSlug and relationManagerClass to all components that support them.
     */
    public function getSchema(): array
    {
        // Propagate context to components if any is set
        if ($this->model || $this->resourceSlug || $this->relationManagerClass) {
            $this->setContextOnComponentsRecursive($this->schema, $this->model, $this->resourceSlug, $this->relationManagerClass);
        }

        return $this->schema;
    }

    /**
     * Recursively set model, resourceSlug and relationManagerClass on all components that support it.
     */
    protected function setContextOnComponentsRecursive(array $components, ?string $model, ?string $resourceSlug, ?string $relationManagerClass = null): void
    {
        foreach ($components as $component) {
            // Set model if component has the formModel method
            if ($model && method_exists($component, 'formModel')) {
                $component->formModel($model);
            }

            // Set resourceSlug if component has the resourceSlug method
            if ($resourceSlug && method_exists($component, 'resourceSlug')) {
                $component->resourceSlug($resourceSlug);
            }

            // Set relationManagerClass if component has the relationManagerClass method
            if ($relationManagerClass && method_exists($component, 'relationManagerClass')) {
                $component->relationManagerClass($relationManagerClass);
            }

            // Handle nested schemas (Sections, Grids, Tabs, etc.)
            if (method_exists($component, 'getSchema')) {
                $nestedSchema = $component->getSchema();
                if (is_array($nestedSchema)) {
                    $this->setContextOnComponentsRecursive($nestedSchema, $model, $resourceSlug, $relationManagerClass);
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
                                $this->setContextOnComponentsRecursive($tabSchema, $model, $resourceSlug, $relationManagerClass);
                            }
                        }
                    }
                }
            }

            // Handle repeaters
            if (method_exists($component, 'getComponents')) {
                $repeaterComponents = $component->getComponents();
                if (is_array($repeaterComponents)) {
                    $this->setContextOnComponentsRecursive($repeaterComponents, $model, $resourceSlug, $relationManagerClass);
                }
            }
        }
    }

    /**
     * Set the number of columns for the schema grid layout.
     */
    public function columns(int $columns): static
    {
        $this->gridColumns = $columns;

        return $this;
    }

    /**
     * Get the number of grid columns.
     */
    public function getGridColumns(): int
    {
        return $this->gridColumns;
    }

    /**
     * Set the model class for this schema.
     *
     * @param  class-string  $model
     */
    public function model(string $model): static
    {
        $this->model = $model;

        return $this;
    }

    /**
     * Get the model class for this schema.
     *
     * @return class-string|null
     */
    public function getModel(): ?string
    {
        return $this->model;
    }

    /**
     * Set the resource slug for this schema.
     */
    public function resourceSlug(string $slug): static
    {
        $this->resourceSlug = $slug;

        return $this;
    }

    /**
     * Get the resource slug for this schema.
     */
    public function getResourceSlug(): ?string
    {
        return $this->resourceSlug;
    }

    /**
     * Set the relation manager class for this schema.
     */
    public function relationManagerClass(string $class): static
    {
        $this->relationManagerClass = $class;

        return $this;
    }

    /**
     * Get the relation manager class for this schema.
     */
    public function getRelationManagerClass(): ?string
    {
        return $this->relationManagerClass;
    }

    /**
     * Fill the schema with data.
     */
    public function fill(array $data): static
    {
        $this->data = $data;

        // Fill each component/entry with the data
        $this->fillComponents($this->schema, $data);

        return $this;
    }

    /**
     * Recursively fill components with data.
     */
    protected function fillComponents(array $components, array $data): void
    {
        foreach ($components as $component) {
            // If component has a fill method (like Entry), call it
            if (method_exists($component, 'getName') && method_exists($component, 'state')) {
                $name = $component->getName();
                $value = $data[$name] ?? null;

                // Set state - the component will handle null values (e.g., Entry uses placeholder)
                $component->state($value);
            }

            // Handle nested schemas (Sections, Grids, Tabs, etc.)
            if (method_exists($component, 'getSchema')) {
                $nestedSchema = $component->getSchema();
                if (is_array($nestedSchema)) {
                    $this->fillComponents($nestedSchema, $data);
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
                                $this->fillComponents($tabSchema, $data);
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Get the data.
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * Get all visible components in the schema.
     */
    public function getVisibleComponents(): array
    {
        // Use getSchema() to ensure context propagation (model, resourceSlug, relationManagerClass)
        $schema = $this->getSchema();

        // Filter hidden components and re-index with array_values to ensure
        // sequential 0-based indices (required for proper JSON array encoding)
        return array_values(array_filter($schema, function ($item): bool {
            // Support both Components and Actions
            if (method_exists($item, 'isHidden')) {
                return ! $item->isHidden();
            }

            return true;
        }));
    }

    /**
     * Serialize to Inertia props (for page rendering).
     */
    public function toInertiaProps(): array
    {
        return $this->toLaraviltProps($this->data);
    }

    /**
     * Serialize to Laravilt props with evaluation context.
     *
     * @param  array  &$data  Form data (passed by reference for afterStateUpdated modifications)
     * @param  mixed  $record  The model record (if editing)
     * @param  string|null  $changedField  The field that changed (for reactive updates)
     * @param  array|null  $repeaterContext  Context for Repeater fields ['repeaterName', 'repeaterIndex', 'fieldName']
     */
    public function toLaraviltProps(array &$data = [], mixed $record = null, ?string $changedField = null, ?array $repeaterContext = null): array
    {
        // Execute afterStateUpdated callbacks if a field changed
        if ($changedField) {
            // Handle nested field paths for Repeater (e.g., "items.0.product_id")
            if ($repeaterContext !== null) {
                $this->executeRepeaterAfterStateUpdatedCallback(
                    $this->schema,
                    $repeaterContext['repeaterName'],
                    $repeaterContext['repeaterIndex'],
                    $repeaterContext['fieldName'],
                    $data,
                    $record
                );
            } elseif (array_key_exists($changedField, $data)) {
                $this->executeAfterStateUpdatedCallbacks($this->schema, $changedField, $data[$changedField], $data, $record);
            }
        }

        // Set evaluation context on all components recursively
        $this->setEvaluationContextRecursive($this->schema, $data, $record);

        return array_merge(parent::toLaraviltProps(), [
            'gridColumns' => $this->getGridColumns(),
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

            // Set form model for relationship-based fields (like Select)
            if ($this->model && method_exists($component, 'formModel')) {
                $component->formModel($this->model);
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
     * Extract validation rules from all components in the schema.
     *
     * @return array<string, mixed> Array of field names => validation rules
     */
    public function getValidationRules(): array
    {
        return $this->extractValidationRulesFromComponents($this->schema);
    }

    /**
     * Extract validation messages from all components in the schema.
     *
     * @return array<string, string> Array of validation messages
     */
    public function getValidationMessages(): array
    {
        return $this->extractValidationMessagesFromComponents($this->schema);
    }

    /**
     * Extract validation attributes from all components in the schema.
     *
     * @return array<string, string> Array of validation attributes
     */
    public function getValidationAttributes(): array
    {
        return $this->extractValidationAttributesFromComponents($this->schema);
    }

    /**
     * Get field prefixes that should be prepended to values for validation.
     * This is used for URL fields with prefix text like "https://".
     *
     * @return array<string, string> Array of field names => prefix text
     */
    public function getFieldPrefixes(): array
    {
        return $this->extractFieldPrefixesFromComponents($this->schema);
    }

    /**
     * Recursively extract field prefixes from components.
     */
    protected function extractFieldPrefixesFromComponents(array $components): array
    {
        $prefixes = [];

        foreach ($components as $component) {
            // Skip actions
            if ($component instanceof \Laravilt\Actions\Action) {
                continue;
            }

            // Check if component has a prefix and is a URL type
            if (method_exists($component, 'getName') && method_exists($component, 'getPrefix') && method_exists($component, 'getType')) {
                $name = $component->getName();
                $prefix = $component->getPrefix();
                $type = $component->getType();

                // Only add prefix for URL fields (where prefix affects validation)
                if ($name && $prefix && $type === 'url') {
                    $prefixes[$name] = $prefix;
                }
            }

            // Handle nested schemas (Sections, Grids, etc.)
            if (method_exists($component, 'getSchema')) {
                $nestedSchema = $component->getSchema();
                if (is_array($nestedSchema)) {
                    $prefixes = array_merge($prefixes, $this->extractFieldPrefixesFromComponents($nestedSchema));
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
                                $prefixes = array_merge($prefixes, $this->extractFieldPrefixesFromComponents($tabSchema));
                            }
                        }
                    }
                }
            }
        }

        return $prefixes;
    }

    /**
     * Recursively extract validation rules from components.
     */
    protected function extractValidationRulesFromComponents(array $components): array
    {
        $rules = [];

        foreach ($components as $component) {
            // Skip actions
            if ($component instanceof \Laravilt\Actions\Action) {
                continue;
            }

            // If component has validation rules
            if (method_exists($component, 'getName') && method_exists($component, 'getValidationRules')) {
                $name = $component->getName();
                $componentRules = $component->getValidationRules();

                if ($name && $componentRules) {
                    $rules[$name] = $componentRules;
                } elseif ($name && method_exists($component, 'isRequired') && $component->isRequired()) {
                    // Add 'required' if the field is marked as required but has no explicit rules
                    $rules[$name] = isset($rules[$name])
                        ? (is_array($rules[$name]) ? array_merge(['required'], $rules[$name]) : 'required|'.$rules[$name])
                        : 'required';
                }
            }

            // Handle nested schemas (Sections, Grids, etc.)
            if (method_exists($component, 'getSchema')) {
                $nestedSchema = $component->getSchema();
                if (is_array($nestedSchema)) {
                    $rules = array_merge($rules, $this->extractValidationRulesFromComponents($nestedSchema));
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
                                $rules = array_merge($rules, $this->extractValidationRulesFromComponents($tabSchema));
                            }
                        }
                    }
                }
            }
        }

        return $rules;
    }

    /**
     * Recursively extract validation messages from components.
     */
    protected function extractValidationMessagesFromComponents(array $components): array
    {
        $messages = [];

        foreach ($components as $component) {
            // Skip actions
            if ($component instanceof \Laravilt\Actions\Action) {
                continue;
            }

            // If component has validation messages
            if (method_exists($component, 'getValidationMessages')) {
                $componentMessages = $component->getValidationMessages();
                if ($componentMessages) {
                    $messages = array_merge($messages, $componentMessages);
                }
            }

            // Handle nested schemas
            if (method_exists($component, 'getSchema')) {
                $nestedSchema = $component->getSchema();
                if (is_array($nestedSchema)) {
                    $messages = array_merge($messages, $this->extractValidationMessagesFromComponents($nestedSchema));
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
                                $messages = array_merge($messages, $this->extractValidationMessagesFromComponents($tabSchema));
                            }
                        }
                    }
                }
            }
        }

        return $messages;
    }

    /**
     * Recursively extract validation attributes from components.
     */
    protected function extractValidationAttributesFromComponents(array $components): array
    {
        $attributes = [];

        foreach ($components as $component) {
            // Skip actions
            if ($component instanceof \Laravilt\Actions\Action) {
                continue;
            }

            // If component has a name and label, use the label as the attribute
            if (method_exists($component, 'getName') && method_exists($component, 'getLabel')) {
                $name = $component->getName();
                $label = $component->getLabel();
                if ($name && $label) {
                    $attributes[$name] = $label;
                }
            }

            // Handle nested schemas
            if (method_exists($component, 'getSchema')) {
                $nestedSchema = $component->getSchema();
                if (is_array($nestedSchema)) {
                    $attributes = array_merge($attributes, $this->extractValidationAttributesFromComponents($nestedSchema));
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
                                $attributes = array_merge($attributes, $this->extractValidationAttributesFromComponents($tabSchema));
                            }
                        }
                    }
                }
            }
        }

        return $attributes;
    }

    /**
     * Execute afterStateUpdated callback for a field inside a Repeater.
     * Uses scoped Get/Set that work within the Repeater item context.
     */
    protected function executeRepeaterAfterStateUpdatedCallback(
        array $components,
        string $repeaterName,
        int $repeaterIndex,
        string $fieldName,
        array &$data,
        mixed $record
    ): void {
        \Log::info('[Schema] executeRepeaterAfterStateUpdatedCallback called', [
            'repeaterName' => $repeaterName,
            'repeaterIndex' => $repeaterIndex,
            'fieldName' => $fieldName,
        ]);

        // Find the Repeater component
        $repeaterComponent = $this->findRepeaterComponent($components, $repeaterName);

        if (! $repeaterComponent) {
            \Log::warning('[Schema] Repeater component not found', ['repeaterName' => $repeaterName]);

            return;
        }

        // Get the Repeater's schema (child fields)
        $repeaterSchema = [];
        if (method_exists($repeaterComponent, 'getSchema')) {
            $repeaterSchema = $repeaterComponent->getSchema();
        }

        // Find the field that changed within the Repeater's schema
        $changedFieldComponent = null;
        foreach ($repeaterSchema as $component) {
            if (method_exists($component, 'getName') && $component->getName() === $fieldName) {
                $changedFieldComponent = $component;
                break;
            }
        }

        if (! $changedFieldComponent) {
            \Log::warning('[Schema] Field not found in Repeater schema', [
                'fieldName' => $fieldName,
                'availableFields' => array_map(fn ($c) => method_exists($c, 'getName') ? $c->getName() : 'unknown', $repeaterSchema),
            ]);

            return;
        }

        // Check if the field has an afterStateUpdated callback
        if (! method_exists($changedFieldComponent, 'getAfterStateUpdated')) {
            \Log::info('[Schema] Field does not have getAfterStateUpdated method', ['fieldName' => $fieldName]);

            return;
        }

        $callback = $changedFieldComponent->getAfterStateUpdated();

        if (! $callback instanceof \Closure) {
            \Log::info('[Schema] Field has no afterStateUpdated callback', ['fieldName' => $fieldName]);

            return;
        }

        // Get the repeater data and the specific item
        $repeaterData = $data[$repeaterName] ?? [];

        if (! is_array($repeaterData) || ! isset($repeaterData[$repeaterIndex])) {
            \Log::warning('[Schema] Repeater item not found', [
                'repeaterName' => $repeaterName,
                'repeaterIndex' => $repeaterIndex,
            ]);

            return;
        }

        $itemData = $repeaterData[$repeaterIndex];
        $value = $itemData[$fieldName] ?? null;

        \Log::info('[Schema] Executing Repeater afterStateUpdated callback', [
            'fieldName' => $fieldName,
            'value' => $value,
            'itemData' => $itemData,
        ]);

        // Create scoped Get and Set objects that work within the Repeater item
        // The Get class expects a reference to data and uses dot notation
        // For Repeater items, we want $get('quantity') to get the quantity from the current item

        // Extract item data to work with
        $itemDataRef = &$data[$repeaterName][$repeaterIndex];

        // Create a Get object that operates on the item data
        $scopedGet = new \Laravilt\Support\Utilities\Get($itemDataRef);

        // Create a Set object that operates on the item data
        $scopedSet = new \Laravilt\Support\Utilities\Set($itemDataRef);

        \Log::info('[Schema] Executing Repeater callback with scoped Get/Set', [
            'itemData' => $itemDataRef,
        ]);

        // Execute the callback with scoped Get/Set objects
        app()->call($callback, [
            'value' => $value,
            'state' => $value,
            'get' => $scopedGet,
            'set' => $scopedSet,
            'data' => $data,
            'record' => $record,
        ]);

        \Log::info('[Schema] After callback, item data:', [
            'itemData' => $data[$repeaterName][$repeaterIndex],
        ]);

        \Log::info('[Schema] Repeater afterStateUpdated callback executed', [
            'dataAfter' => $data[$repeaterName][$repeaterIndex] ?? [],
        ]);
    }

    /**
     * Find a Repeater component by name recursively.
     */
    protected function findRepeaterComponent(array $components, string $repeaterName): mixed
    {
        foreach ($components as $component) {
            // Check if this is the Repeater we're looking for
            if ($component instanceof \Laravilt\Forms\Components\Repeater) {
                if (method_exists($component, 'getName') && $component->getName() === $repeaterName) {
                    return $component;
                }
            }

            // Also check by name for any component
            if (method_exists($component, 'getName') && $component->getName() === $repeaterName) {
                return $component;
            }

            // Handle nested schemas (Section, Grid, etc.)
            if (method_exists($component, 'getSchema')) {
                $nestedSchema = $component->getSchema();
                if (is_array($nestedSchema)) {
                    $found = $this->findRepeaterComponent($nestedSchema, $repeaterName);
                    if ($found) {
                        return $found;
                    }
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
                                $found = $this->findRepeaterComponent($tabSchema, $repeaterName);
                                if ($found) {
                                    return $found;
                                }
                            }
                        }
                    }
                }
            }
        }

        return null;
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
