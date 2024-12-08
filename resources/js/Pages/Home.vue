<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ref, onMounted } from "vue";
import axios from "axios";
import { Head, Link } from '@inertiajs/vue3';

const categories = ref([]);
const loading = ref(true);

const fetchCategories = async () => {
    try {
        const response = await axios.get("https://the-trivia-api.com/api/categories");
        categories.value = Object.keys(response.data);
        loading.value = false;
    } catch (error) {
        console.error("Failed to fetch categories:", error);
        loading.value = false;
    }
};

onMounted(fetchCategories);
</script>

<template>

    <Head title="home" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Home
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                <div v-if="loading">Loading categories...</div>
                <div v-else class="grid lg:grid-cols-4 gap-4 mt-5 mx-5 md:grid-cols-3 sm:grid-cols-2">
                    <div v-for="category in categories" :key="category" class="bg-white px-10 py-10 text-center rounded-md shadow cursor-pointer hover:bg-slate-200">
                        <Link :href="route('quiz', category)">
                            <h3>{{ category }}</h3>
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
