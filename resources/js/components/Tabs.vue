<template>
    <div :dir="rtl ? 'rtl' : 'ltr'">
        <!-- Tab Headers - Filament Style -->
        <div class="border-b border-gray-200 dark:border-gray-700" role="tablist">
            <nav class="flex flex-wrap gap-x-4 px-1" aria-label="Tabs">
                <button
                    v-for="(tab, index) in tabs"
                    :key="index"
                    type="button"
                    role="tab"
                    :aria-selected="currentTab === index"
                    :class="[
                        'group inline-flex items-center gap-x-2 px-3 py-2.5 text-sm font-medium transition-colors border-b-2 -mb-px whitespace-nowrap',
                        currentTab === index
                            ? 'border-primary-600 text-primary-600 dark:border-primary-500 dark:text-primary-400'
                            : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300 dark:hover:border-gray-600'
                    ]"
                    @click="selectTab(index)"
                >
                    <component
                        v-if="tab.icon && getIconComponent(tab.icon)"
                        :is="getIconComponent(tab.icon)"
                        class="h-5 w-5"
                    />
                    <span>{{ tab.label }}</span>
                    <span
                        v-if="tab.badge"
                        :class="[
                            'inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset',
                            currentTab === index
                                ? 'bg-primary-50 text-primary-700 ring-primary-600/20 dark:bg-primary-400/10 dark:text-primary-400 dark:ring-primary-400/30'
                                : 'bg-gray-50 text-gray-600 ring-gray-500/10 dark:bg-gray-400/10 dark:text-gray-400 dark:ring-gray-400/20'
                        ]"
                    >
                        {{ tab.badge }}
                    </span>
                </button>
            </nav>
        </div>

        <!-- Tab Panels -->
        <div class="mt-6">
            <div
                v-for="(tab, index) in tabs"
                :key="index"
                v-show="currentTab === index"
                role="tabpanel"
                class="focus:outline-none"
            >
                <SchemaRenderer
                    v-if="tab.schema && tab.schema.length > 0"
                    :schema="tab.schema"
                    :model-value="modelValue"
                    @update:model-value="$emit('update:model-value', $event)"
                />
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import SchemaRenderer from './SchemaRenderer.vue';
import * as LucideIcons from 'lucide-vue-next';

const props = defineProps({
    tabs: {
        type: Array,
        default: () => []
    },
    activeTab: {
        type: Number,
        default: 0
    },
    persistTabInQueryString: {
        type: Boolean,
        default: false
    },
    rtl: {
        type: Boolean,
        default: false
    },
    theme: {
        type: String,
        default: 'light'
    },
    modelValue: {
        type: Object,
        default: () => ({})
    }
});

defineEmits(['update:model-value']);

const currentTab = ref(props.activeTab);

const selectTab = (index) => {
    currentTab.value = index;

    if (props.persistTabInQueryString) {
        const url = new URL(window.location);
        url.searchParams.set('tab', index);
        history.pushState({}, '', url);
    }
};

// Convert kebab-case or snake_case icon names to PascalCase for lucide-vue-next
const getIconComponent = (iconName) => {
    if (!iconName) return null;

    // Convert formats like 'user', 'file-text', 'dollar-sign' to PascalCase
    const pascalCase = iconName
        .split(/[-_]/)
        .map(word => word.charAt(0).toUpperCase() + word.slice(1).toLowerCase())
        .join('');

    return LucideIcons[pascalCase] || null;
};

onMounted(() => {
    if (props.persistTabInQueryString) {
        const url = new URL(window.location);
        const tabFromQuery = url.searchParams.get('tab');
        if (tabFromQuery !== null) {
            currentTab.value = parseInt(tabFromQuery);
        }
    }
});
</script>
