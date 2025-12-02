<template>
    <div v-if="schema && schema.length > 0" :class="gridClasses">
        <div
            v-for="(child, index) in schema"
            :key="child.name || child.id || index"
            :class="getColumnSpanClass(child)"
        >
            <SchemaRenderer
                :schema="[child]"
                :model-value="modelValue"
                @update:model-value="$emit('update:modelValue', $event)"
            />
        </div>
    </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import SchemaRenderer from './SchemaRenderer.vue'

const props = defineProps<{
    columns: number | Record<string, number>
    schema: Array<any>
    modelValue?: Record<string, any>
}>()

const emit = defineEmits<{
    'update:modelValue': [value: Record<string, any>]
}>()

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
