<template>
    <div class="space-y-6">
        <!-- Section Header -->
        <header v-if="heading">
            <div
                :class="[
                    'flex items-center gap-2',
                    collapsible ? 'cursor-pointer' : ''
                ]"
                @click="collapsible && toggleCollapse()"
            >
                <component
                    v-if="icon && getIconComponent(icon)"
                    :is="getIconComponent(icon)"
                    class="h-4 w-4 text-muted-foreground flex-shrink-0"
                />
                <h3 class="mb-0.5 text-base font-medium">
                    {{ heading }}
                </h3>
                <ChevronDown
                    v-if="collapsible"
                    :class="[
                        'h-4 w-4 text-muted-foreground transition-transform ml-auto',
                        isCollapsed ? '-rotate-90' : ''
                    ]"
                />
            </div>
            <p v-if="description" class="text-sm text-muted-foreground">
                {{ description }}
            </p>
        </header>

        <!-- Section Content -->
        <div v-show="!isCollapsed" class="space-y-6">
            <template v-for="(component, index) in schema" :key="component.name || component.id || index">
                <component
                    :is="getComponent(component)"
                    v-bind="getComponentProps(component)"
                    :value="isSchemaComponent(component) ? undefined : modelValue?.[component.name]"
                    :modelValue="isSchemaComponent(component) ? modelValue : undefined"
                    @update:model-value="(value) => handleComponentUpdate(component, value)"
                />
            </template>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, defineAsyncComponent } from 'vue'
import { ChevronDown } from 'lucide-vue-next'
import * as LucideIcons from 'lucide-vue-next'

const props = defineProps<{
    heading?: string
    description?: string
    icon?: string
    collapsible?: boolean
    collapsed?: boolean
    schema?: Array<any>
    modelValue?: Record<string, any>
}>()

const emit = defineEmits<{
    'update:modelValue': [value: Record<string, any>]
}>()

const isCollapsed = ref(props.collapsed || false)

const toggleCollapse = () => {
    if (props.collapsible) {
        isCollapsed.value = !isCollapsed.value
    }
}

// Convert kebab-case or snake_case icon names to PascalCase for lucide-vue-next
const getIconComponent = (iconName: string) => {
    if (!iconName) return null

    // Convert formats like 'user', 'id-card', 'map-pin' to PascalCase
    const pascalCase = iconName
        .split(/[-_]/)
        .map(word => word.charAt(0).toUpperCase() + word.slice(1))
        .join('')

    return (LucideIcons as any)[pascalCase] || null
}

// Map component types to their Vue components
const componentMap: Record<string, any> = {
    // Schema layout components
    grid: defineAsyncComponent(() => import('./Grid.vue')),
    section: defineAsyncComponent(() => import('./Section.vue')),

    // Form field components
    text_input: defineAsyncComponent(() => import('@laravilt/forms/components/fields/TextInput.vue')),
    textarea: defineAsyncComponent(() => import('@laravilt/forms/components/fields/Textarea.vue')),
    select: defineAsyncComponent(() => import('@laravilt/forms/components/fields/Select.vue')),
    checkbox: defineAsyncComponent(() => import('@laravilt/forms/components/fields/Checkbox.vue')),
    radio: defineAsyncComponent(() => import('@laravilt/forms/components/fields/Radio.vue')),
    toggle: defineAsyncComponent(() => import('@laravilt/forms/components/fields/Toggle.vue')),
    toggle_buttons: defineAsyncComponent(() => import('@laravilt/forms/components/fields/ToggleButtons.vue')),
    hidden: defineAsyncComponent(() => import('@laravilt/forms/components/fields/Hidden.vue')),
    date_picker: defineAsyncComponent(() => import('@laravilt/forms/components/fields/DatePicker.vue')),
    time_picker: defineAsyncComponent(() => import('@laravilt/forms/components/fields/TimePicker.vue')),
    date_time_picker: defineAsyncComponent(() => import('@laravilt/forms/components/fields/DateTimePicker.vue')),
    date_range_picker: defineAsyncComponent(() => import('@laravilt/forms/components/fields/DateRangePicker.vue')),
    color_picker: defineAsyncComponent(() => import('@laravilt/forms/components/fields/ColorPicker.vue')),
    file_upload: defineAsyncComponent(() => import('@laravilt/forms/components/fields/FileUpload.vue')),
    rich_editor: defineAsyncComponent(() => import('@laravilt/forms/components/fields/RichEditor.vue')),
    markdown_editor: defineAsyncComponent(() => import('@laravilt/forms/components/fields/MarkdownEditor.vue')),
    tags_input: defineAsyncComponent(() => import('@laravilt/forms/components/fields/TagsInput.vue')),
    key_value: defineAsyncComponent(() => import('@laravilt/forms/components/fields/KeyValue.vue')),
    repeater: defineAsyncComponent(() => import('@laravilt/forms/components/fields/Repeater.vue')),
    builder: defineAsyncComponent(() => import('@laravilt/forms/components/fields/Builder.vue')),
    icon_picker: defineAsyncComponent(() => import('@laravilt/forms/components/fields/IconPicker.vue')),
    number_field: defineAsyncComponent(() => import('@laravilt/forms/components/fields/NumberField.vue')),
    pin_input: defineAsyncComponent(() => import('@laravilt/forms/components/fields/PinInput.vue')),
    rate_input: defineAsyncComponent(() => import('@laravilt/forms/components/fields/RateInput.vue')),
    checkbox_list: defineAsyncComponent(() => import('@laravilt/forms/components/fields/CheckboxList.vue')),
    slider: defineAsyncComponent(() => import('@laravilt/forms/components/fields/Slider.vue')),

    // InfoList Entry components
    text_entry: defineAsyncComponent(() => import('@laravilt/infolists/components/entries/TextEntry.vue')),
    badge_entry: defineAsyncComponent(() => import('@laravilt/infolists/components/entries/BadgeEntry.vue')),
    icon_entry: defineAsyncComponent(() => import('@laravilt/infolists/components/entries/IconEntry.vue')),
    image_entry: defineAsyncComponent(() => import('@laravilt/infolists/components/entries/ImageEntry.vue')),
    color_entry: defineAsyncComponent(() => import('@laravilt/infolists/components/entries/ColorEntry.vue')),
    code_entry: defineAsyncComponent(() => import('@laravilt/infolists/components/entries/CodeEntry.vue')),
    key_value_entry: defineAsyncComponent(() => import('@laravilt/infolists/components/entries/KeyValueEntry.vue')),
    repeatable_entry: defineAsyncComponent(() => import('@laravilt/infolists/components/entries/RepeatableEntry.vue')),
}

const getComponent = (component: any) => {
    const type = component.component || 'div'
    return componentMap[type] || 'div'
}

const getComponentProps = (component: any) => {
    const { value, modelValue, ...props } = component
    return props
}

const isSchemaComponent = (component: any) => {
    const schemaComponents = ['grid', 'section']
    return schemaComponents.includes(component.component)
}

const handleComponentUpdate = (component: any, value: any) => {
    if (isSchemaComponent(component)) {
        // For schema components, merge the entire value object
        emit('update:modelValue', { ...props.modelValue, ...value })
    } else {
        // For regular fields, update the specific field
        emit('update:modelValue', { ...props.modelValue, [component.name]: value })
    }
}
</script>
