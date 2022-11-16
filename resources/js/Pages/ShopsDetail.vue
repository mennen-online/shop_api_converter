<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import {usePage} from "@inertiajs/inertia-vue3";
import {reactive} from "vue";

const props = defineProps(['shop', 'header'])

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
        <div class="overflow-hidden sm:rounded-lg">
          <div class="flex flex-col w-full">
            <div class="">
              <div class="flex flex-col text-2xl font-bold">
                <a class="mb-10 font-extralight text-sm flex flex-row items-center" :href="route('shops.index')">
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5">
                    <path fill-rule="evenodd"
                          d="M12.79 5.23a.75.75 0 01-.02 1.06L8.832 10l3.938 3.71a.75.75 0 11-1.04 1.08l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 011.06.02z"
                          clip-rule="evenodd"/>
                  </svg>
                  <p>Back to Shops</p>
                </a>
                <div class="flex flex-row items-center">
                  <svg class="h-8 w-8 text-gray-800 mr-4" viewBox="0 0 34 34" version="1.1"
                       xmlns="http://www.w3.org/2000/svg"
                       xmlns:xlink="http://www.w3.org/1999/xlink">
                    <g id="icon-shopware-5" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                      <path
                          d="M17,0 C7.6075,0 0,7.6075 0,17 C0,26.3925 7.6075,34 17,34 C26.3925,34 34,26.3925 34,17 C34,7.6075 26.3925,0 17,0 Z M10.0895,6.46 L25.5,6.46 L23.953,10.88 L8.5425,10.88 L10.0895,6.46 Z M17.9945,18.02 C17.0935,18.02 9.911,18.02 9.911,18.02 L8.534,14.195 C8.534,14.195 14.756,14.195 17.7565,14.195 C22.134,14.195 23.766,15.572 24.3695,16.83 C25.585,19.771 22.304,23.375 17.0425,23.3581194 C16.881,23.3581194 19.635,23.3325 21.0545,21.2755 C22.1765,19.1675 20.978,18.02 17.9945,18.02 Z M8.5255,11.05 L13.5405,11.05 L13.5405,14.025 L8.5255,14.025 L8.5255,11.05 L8.5255,11.05 Z M24.939,23.749 C22.984,27.6165 18.683,27.3785 16.6515,27.3785 C14.3225,27.3785 8.5425,27.3785 8.5425,27.3785 L9.9195,23.5535 C9.928,23.5535 10.1575,23.5535 17.068,23.5535 C22.627,23.5535 25.6955,19.7625 24.5735,16.915 C24.565,16.915 26.401,20.3745 24.939,23.749 Z"
                          id="Shape-Copy-17" fill="currentColor" fill-rule="nonzero"></path>
                    </g>
                  </svg>
                  <h1>{{ shop.name }}</h1>
                </div>
              </div>
            </div>
            <div class="flex flex-row w-full mt-8 gap-2">
              <nav class="w-1/6">
                <a class="flex flex-row items-center rounded px-2 py-1 mb-3 w-full bg-gray-200 shadow">
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                       stroke="currentColor" class="w-5 h-5 mr-3">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/>
                  </svg>

                  <p>Info</p>
                </a>
                <a class="flex flex-row items-center bg-gray-100 rounded px-2 py-1 w-full">
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                       stroke="currentColor" class="w-5 h-5 mr-3">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M9.348 14.651a3.75 3.75 0 010-5.303m5.304 0a3.75 3.75 0 010 5.303m-7.425 2.122a6.75 6.75 0 010-9.546m9.546 0a6.75 6.75 0 010 9.546M5.106 18.894c-3.808-3.808-3.808-9.98 0-13.789m13.788 0c3.808 3.808 3.808 9.981 0 13.79M12 12h.008v.007H12V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/>
                  </svg>

                  <p>Endpoints</p>
                </a>
              </nav>

              <div class="w-5/6 flex flex-col gap-2">
                <div class="bg-white rounded-lg p-4">
                  <h1 class="text-2xl font-bold">{{ header }}</h1>
                </div>
                <div class="bg-white p-4 rounded-lg">
                  <p>{{ shop.name }}</p>
                  <p>{{ shop.id }}</p>
                  <p>{{ shop.status }}</p>
                  <p>{{ shop.url }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
