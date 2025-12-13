<template>
    <div class="bg-card text-card-foreground rounded-xl border shadow-sm">
        <!-- Section Header -->
        <header v-if="heading" :class="['px-6 py-4 transition-all duration-200', isCollapsed ? '' : 'border-b']">
            <div
                :class="[
                    'flex items-center gap-3',
                    collapsible ? 'cursor-pointer select-none' : ''
                ]"
                @click="collapsible && toggleCollapse()"
            >
                <div
                    v-if="icon && getIconComponent(icon)"
                    class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary/10 text-primary flex-shrink-0"
                >
                    <component
                        :is="getIconComponent(icon)"
                        class="h-5 w-5"
                    />
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="leading-none font-semibold">
                        {{ heading }}
                    </h3>
                    <p v-if="description" class="mt-1 text-sm text-muted-foreground">
                        {{ description }}
                    </p>
                </div>
                <ChevronDown
                    v-if="collapsible"
                    :class="[
                        'h-4 w-4 text-muted-foreground transition-transform duration-200 ease-out flex-shrink-0',
                        isCollapsed ? '-rotate-90 rtl:rotate-90' : ''
                    ]"
                />
            </div>
        </header>

        <!-- Section Content with smooth collapse -->
        <div
            class="grid transition-all duration-200 ease-out"
            :style="{ gridTemplateRows: isCollapsed ? '0fr' : '1fr' }"
        >
            <div class="overflow-hidden">
                <div class="p-6 space-y-6">
                    <Schema
                        v-if="schema && schema.length > 0"
                        :schema="schema"
                        :model-value="modelValue"
                        :form-controller="formController"
                        :form-method="formMethod"
                        @update:model-value="$emit('update:modelValue', $event)"
                    />
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, inject } from 'vue'
import { ChevronDown } from 'lucide-vue-next'
import * as LucideIcons from 'lucide-vue-next'
import Schema from './Schema.vue'

// Inject parent context for reactive fields
const formController = inject<string | undefined>('formController', undefined)
const formMethod = inject<string | undefined>('formMethod', 'getSchema')

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
