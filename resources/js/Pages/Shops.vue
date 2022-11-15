<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Welcome from '@/Components/Welcome.vue';
import {usePage} from "@inertiajs/inertia-vue3";

const props = defineProps(['shops'])

const statusBadges = {
  'not_synced': 'bg-red-100 w-24 text-sm px-2 py-1 rounded-xl text-center',
  'synced': 'bg-green-100 w-24 text-sm px-2 py-1 rounded-xl text-center'
}


function getStatus(shopStatus) {
  switch (shopStatus) {
    case 'not_synced':
      return {
        status: 'Not synced',
        class: statusBadges.not_synced
      }
    case 'synced':
      return {
        status: 'Synced',
        class: statusBadges.synced
      }
  }
}
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
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 w-full">
            <div v-for="shop in shops.data"
                 class="flex flex-col bg-white rounded-xl h-52 w-72 p-4 hover:shadow transition gap-2 justify-between">
              <div class="flex flex-row items-center">
                <div>
                  <svg class="h-7 w-7 text-gray-800 mr-3" viewBox="0 0 34 34" version="1.1" xmlns="http://www.w3.org/2000/svg"
                       xmlns:xlink="http://www.w3.org/1999/xlink">
                    <g id="icon-shopware-5" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                      <path
                          d="M17,0 C7.6075,0 0,7.6075 0,17 C0,26.3925 7.6075,34 17,34 C26.3925,34 34,26.3925 34,17 C34,7.6075 26.3925,0 17,0 Z M10.0895,6.46 L25.5,6.46 L23.953,10.88 L8.5425,10.88 L10.0895,6.46 Z M17.9945,18.02 C17.0935,18.02 9.911,18.02 9.911,18.02 L8.534,14.195 C8.534,14.195 14.756,14.195 17.7565,14.195 C22.134,14.195 23.766,15.572 24.3695,16.83 C25.585,19.771 22.304,23.375 17.0425,23.3581194 C16.881,23.3581194 19.635,23.3325 21.0545,21.2755 C22.1765,19.1675 20.978,18.02 17.9945,18.02 Z M8.5255,11.05 L13.5405,11.05 L13.5405,14.025 L8.5255,14.025 L8.5255,11.05 L8.5255,11.05 Z M24.939,23.749 C22.984,27.6165 18.683,27.3785 16.6515,27.3785 C14.3225,27.3785 8.5425,27.3785 8.5425,27.3785 L9.9195,23.5535 C9.928,23.5535 10.1575,23.5535 17.068,23.5535 C22.627,23.5535 25.6955,19.7625 24.5735,16.915 C24.565,16.915 26.401,20.3745 24.939,23.749 Z"
                          id="Shape-Copy-17" fill="currentColor" fill-rule="nonzero"></path>
                    </g>
                  </svg>
                </div>
                <p class="">{{ shop.name }}</p>
              </div>
              <div class="ml-10 text-xs text-gray-700 font-extralight h-full flex flex-col gap-2">
                <p class="truncate">
                  {{ shop.url }}
                </p>
                <p>
                  {{ shop.created_at }}
                </p>
              </div>
              <div class="" :var="response = getStatus(shop.status)">
                <div :class="response.class">
                  {{ response.status }}
                </div>

              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
