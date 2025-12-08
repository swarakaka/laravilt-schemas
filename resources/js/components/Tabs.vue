<template>
    <Tabs :default-value="String(activeTab || 0)" class="w-full">
        <TabsList class="w-full justify-start rtl:flex-row-reverse">
            <TabsTrigger
                v-for="(tab, index) in tabs"
                :key="index"
                :value="String(index)"
                class="gap-2"
            >
                <component
                    v-if="tab.icon && getIconComponent(tab.icon)"
                    :is="getIconComponent(tab.icon)"
                    class="h-4 w-4"
                />
                <span>{{ tab.label }}</span>
                <span
                    v-if="tab.badge"
                    class="ms-1 inline-flex items-center justify-center px-2 py-0.5 text-xs font-medium rounded-full bg-primary/10 text-primary"
                >
                    {{ tab.badge }}
                </span>
            </TabsTrigger>
        </TabsList>

        <TabsContent
            v-for="(tab, index) in tabs"
            :key="index"
            :value="String(index)"
            class="space-y-6 mt-6"
        >
            <SchemaRenderer
                v-if="tab.schema"
                :schema="tab.schema"
                :model-value="modelValue"
                @update:model-value="(value) => emit('update:modelValue', value)"
            />
        </TabsContent>
    </Tabs>
</template>

<script setup lang="ts">
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs'
import SchemaRenderer from './SchemaRenderer.vue'
import * as LucideIcons from 'lucide-vue-next'

defineProps<{
    tabs: Array<any>
    activeTab?: number
    persistTabInQueryString?: boolean
    modelValue?: Record<string, any>
}>()

const emit = defineEmits<{
    'update:modelValue': [value: Record<string, any>]
}>()

// Convert kebab-case or snake_case icon names to PascalCase for lucide-vue-next
const getIconComponent = (iconName: string) => {
    if (!iconName) return null

    // Convert formats like 'user', 'file-text', 'user-circle' to PascalCase
    const pascalCase = iconName
        .split(/[-_]/)
        .map(word => word.charAt(0).toUpperCase() + word.slice(1))
        .join('')

    return (LucideIcons as any)[pascalCase] || null
}
</script>
