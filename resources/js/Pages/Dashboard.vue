<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Welcome from '@/Components/Welcome.vue';
import {usePage} from "@inertiajs/inertia-vue3";
const props = defineProps(['shops'])
</script>

<template>
    <AppLayout title="Server Overview">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Shops
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class=" overflow-hidden sm:rounded-lg">
                  <div class="grid grid-cols-4 gap-6 w-full ">
                    <div v-for="shop in shops.data" class="bg-gray-200 rounded-xl h-40 w-60">
                      {{shop.url}}
                    </div>
                  </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
