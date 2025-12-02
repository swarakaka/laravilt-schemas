<template>
    <div class="overflow-hidden rounded-xl border bg-card text-card-foreground shadow">
        <!-- Section Header -->
        <div
            v-if="heading || description || $slots.header"
            :class="[
                'flex justify-between border-b p-6',
                collapsible ? 'cursor-pointer hover:bg-muted/50' : ''
            ]"
            @click="collapsible && toggleCollapse()"
        >
            <div class="flex items-start gap-3 flex-1">
                <component
                    v-if="icon && getIconComponent(icon)"
                    :is="getIconComponent(icon)"
                    class="h-5 w-5 text-muted-foreground flex-shrink-0 mt-0.5"
                />
                <div class="flex-1">
                    <slot name="header">
                        <h3 class="text-lg font-semibold leading-none tracking-tight">
                            {{ heading }}
                        </h3>
                        <p v-if="description" class="mt-1.5 text-sm text-muted-foreground">
                            {{ description }}
                        </p>
                    </slot>
                </div>
            </div>
            <ChevronDown
                v-if="collapsible"
                :class="[
                    'h-5 w-5 text-muted-foreground transition-transform ml-4 mt-0.5 flex-shrink-0',
                    isCollapsed ? '-rotate-90 rtl:rotate-90' : ''
                ]"
            />
        </div>

        <!-- Section Body -->
        <div v-show="!isCollapsed">
            <div class="p-6">
                <slot name="body">
                    <SchemaRenderer
                        v-if="schema && schema.length > 0"
                        :schema="schema"
                        :model-value="modelValue"
                        @update:model-value="$emit('update:modelValue', $event)"
                    />
                </slot>
            </div>

            <!-- Section Footer -->
            <div v-if="$slots.footer" class="border-t bg-muted/50 px-6 py-4">
                <slot name="footer"></slot>
            </div>
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
