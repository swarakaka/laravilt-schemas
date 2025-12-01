<template>
    <div v-if="visibleSchema.length > 0" :class="gridClasses">
        <div
            v-for="(child, index) in visibleSchema"
            :key="child.name || child.id || index"
            :class="getColumnSpanClass(child)"
        >
            <component
                :is="getComponent(child)"
                v-bind="getComponentProps(child)"
                :value="modelValue?.[child.name]"
                @update:model-value="(value) => updateValue(child.name, value)"
            />
        </div>
    </div>
</template>

<script setup lang="ts">
import { computed, defineAsyncComponent } from 'vue'

const props = defineProps<{
    columns: number | Record<string, number>
    schema: Array<any>
    modelValue?: Record<string, any>
}>()

const emit = defineEmits<{
    'update:modelValue': [value: Record<string, any>]
}>()

// Filter out hidden fields from schema
const visibleSchema = computed(() => {
    return props.schema.filter(child => child && child.hidden !== true)
})

const updateValue = (name: string, value: any) => {
    const newValue = {
        ...(props.modelValue || {}),
        [name]: value
    }
    emit('update:modelValue', newValue)
}

// Get component props, excluding value and modelValue since we set them explicitly
const getComponentProps = (component: any) => {
    const { value, modelValue, ...props } = component
    return props
}

const componentMap: Record<string, any> = {
    // Schema layout components
    section: defineAsyncComponent(() => import('./Section.vue')),
    grid: defineAsyncComponent(() => import('./Grid.vue')),

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

const gridClasses = computed(() => {
    const classes = ['grid', 'gap-6']

    if (typeof props.columns === 'number') {
        classes.push(`grid-cols-1`)
        if (props.columns > 1) {
            classes.push(`md:grid-cols-${props.columns}`)
        }
    } else if (typeof props.columns === 'object') {
        if (props.columns.default) classes.push(`grid-cols-${props.columns.default}`)
        if (props.columns.sm) classes.push(`sm:grid-cols-${props.columns.sm}`)
        if (props.columns.md) classes.push(`md:grid-cols-${props.columns.md}`)
        if (props.columns.lg) classes.push(`lg:grid-cols-${props.columns.lg}`)
        if (props.columns.xl) classes.push(`xl:grid-cols-${props.columns.xl}`)
    }

    return classes.join(' ')
})

const getColumnSpanClass = (child: any): string => {
    if (!child.columnSpan) return ''

    if (typeof child.columnSpan === 'number') {
        return `col-span-${child.columnSpan}`
    }

    if (typeof child.columnSpan === 'object') {
        const classes: string[] = []
        if (child.columnSpan.default) classes.push(`col-span-${child.columnSpan.default}`)
        if (child.columnSpan.sm) classes.push(`sm:col-span-${child.columnSpan.sm}`)
        if (child.columnSpan.md) classes.push(`md:col-span-${child.columnSpan.md}`)
        if (child.columnSpan.lg) classes.push(`lg:col-span-${child.columnSpan.lg}`)
        return classes.join(' ')
    }

    return ''
}
</script>
