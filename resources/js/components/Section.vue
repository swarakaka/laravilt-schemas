<template>
    <div class="rounded-lg border p-6 space-y-6">
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
                        'h-4 w-4 text-muted-foreground transition-transform ml-auto rtl:ml-0 rtl:mr-auto',
                        isCollapsed ? '-rotate-90 rtl:rotate-90' : ''
                    ]"
                />
            </div>
            <p v-if="description" class="text-sm text-muted-foreground">
                {{ description }}
            </p>
        </header>

        <!-- Section Content -->
        <div v-show="!isCollapsed" class="space-y-6">
            <SchemaRenderer
                v-if="schema && schema.length > 0"
                :schema="schema"
                :model-value="modelValue"
                @update:model-value="$emit('update:modelValue', $event)"
            />
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { ChevronDown } from 'lucide-vue-next'
import * as LucideIcons from 'lucide-vue-next'
import SchemaRenderer from './SchemaRenderer.vue'

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
</script>
