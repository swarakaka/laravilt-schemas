<template>
    <div
        class="grid gap-4"
        :class="gridClasses"
        :dir="rtl ? 'rtl' : 'ltr'"
    >
        <div
            v-for="(child, index) in schema"
            :key="index"
            :class="[getColumnSpanClass(child), child.hidden ? 'hidden' : '']"
        >
            <component
                :is="child.component || 'div'"
                v-bind="child"
            />
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    columns: {
        type: [Number, Object],
        default: 1
    },
    schema: {
        type: Array,
        default: () => []
    },
    rtl: {
        type: Boolean,
        default: false
    },
    theme: {
        type: String,
        default: 'light'
    }
});

const gridClasses = computed(() => {
    if (typeof props.columns === 'number') {
        return `grid-cols-${props.columns}`;
    }

    // Responsive grid
    const classes = [];
    if (props.columns.default) classes.push(`grid-cols-${props.columns.default}`);
    if (props.columns.sm) classes.push(`sm:grid-cols-${props.columns.sm}`);
    if (props.columns.md) classes.push(`md:grid-cols-${props.columns.md}`);
    if (props.columns.lg) classes.push(`lg:grid-cols-${props.columns.lg}`);
    if (props.columns.xl) classes.push(`xl:grid-cols-${props.columns.xl}`);

    return classes.join(' ');
});

const getColumnSpanClass = (child) => {
    if (!child.columnSpan) return '';

    if (typeof child.columnSpan === 'number') {
        return `col-span-${child.columnSpan}`;
    }

    // Responsive column span
    const classes = [];
    if (child.columnSpan.default) classes.push(`col-span-${child.columnSpan.default}`);
    if (child.columnSpan.sm) classes.push(`sm:col-span-${child.columnSpan.sm}`);
    if (child.columnSpan.md) classes.push(`md:col-span-${child.columnSpan.md}`);
    if (child.columnSpan.lg) classes.push(`lg:col-span-${child.columnSpan.lg}`);

    return classes.join(' ');
};
</script>
