<script setup>
import { Head } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

defineProps({
    title: String,
    message: {
        type: String,
        default: 'Página em construção.'
    }
});
</script>

<template>
    <Head :title="title" />

    <AdminLayout>
        <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
            
            <div class="sm:flex sm:justify-between sm:items-center mb-8">
                <div class="mb-4 sm:mb-0">
                    <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">{{ title }}</h1>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-sm border border-gray-200 dark:border-gray-700 p-8 text-center">
                <div class="text-4xl mb-4">🚧</div>
                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100 mb-2">{{ title }}</h2>
                <p class="text-gray-600 dark:text-gray-400">{{ message }}</p>
            </div>

        </div>
    </AdminLayout>
</template>
