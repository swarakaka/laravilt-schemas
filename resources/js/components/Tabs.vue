<template>
    <Tabs :default-value="String(activeTab || 0)" :dir="dir" class="w-full" @update:model-value="handleTabChange">
        <TabsList class="w-full justify-start">
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
            <!-- Skeleton while loading -->
            <Transition
                enter-active-class="transition-opacity duration-150 ease-out"
                enter-from-class="opacity-0"
                enter-to-class="opacity-100"
                leave-active-class="transition-opacity duration-100 ease-in"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
                mode="out-in"
            >
                <div v-if="isLoading && currentTab === String(index)" key="skeleton" class="space-y-6">
                    <div class="bg-card rounded-xl border shadow-sm p-6 space-y-4">
                        <div class="h-4 bg-muted/60 rounded w-1/4 animate-pulse"></div>
                        <div class="space-y-3">
                            <div class="h-10 bg-muted/60 rounded animate-pulse" style="animation-delay: 0ms"></div>
                            <div class="h-10 bg-muted/60 rounded animate-pulse" style="animation-delay: 75ms"></div>
                            <div class="h-10 bg-muted/60 rounded w-3/4 animate-pulse" style="animation-delay: 150ms"></div>
                        </div>
                    </div>
                </div>

                <!-- Actual content -->
                <Schema
                    v-else-if="tab.schema"
                    key="content"
                    :schema="tab.schema"
                    :model-value="modelValue"
                    :form-controller="formController"
                    :form-method="formMethod"
                    @update:model-value="(value) => emit('update:modelValue', value)"
                />
            </Transition>
        </TabsContent>
    </Tabs>
</template>

<script setup lang="ts">
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs'
import Schema from './Schema.vue'
import * as LucideIcons from 'lucide-vue-next'
import { ref, onMounted, onUnmounted, nextTick, Transition, inject } from 'vue'

// Inject parent context for reactive fields
const formController = inject<string | undefined>('formController', undefined)
const formMethod = inject<string | undefined>('formMethod', 'getSchema')

const props = defineProps<{
    tabs: Array<any>
    activeTab?: number
    persistTabInQueryString?: boolean
    modelValue?: Record<string, any>
}>()

const isLoading = ref(false)
const currentTab = ref(String(props.activeTab || 0))

const handleTabChange = async (value: string) => {
    if (value !== currentTab.value) {
        isLoading.value = true
        currentTab.value = value
        await nextTick()
        // Small delay to show skeleton
        setTimeout(() => {
            isLoading.value = false
        }, 150)
    }
}

// Reactive document direction for RTL support
const dir = ref<'ltr' | 'rtl'>('ltr')
let observer: MutationObserver | null = null

onMounted(() => {
    // Set initial direction
    dir.value = (document.documentElement.dir as 'ltr' | 'rtl') || 'ltr'

    // Watch for direction changes on the html element
    observer = new MutationObserver((mutations) => {
        for (const mutation of mutations) {
            if (mutation.attributeName === 'dir') {
                dir.value = (document.documentElement.dir as 'ltr' | 'rtl') || 'ltr'
            }
        }
    })

    observer.observe(document.documentElement, {
        attributes: true,
        attributeFilter: ['dir']
    })
})

onUnmounted(() => {
    if (observer) {
        observer.disconnect()
        observer = null
    }
})

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
