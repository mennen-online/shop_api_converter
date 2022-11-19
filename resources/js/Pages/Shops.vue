<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import {Link} from "@inertiajs/inertia-vue3";
import DialogModal from "@/Components/DialogModal.vue";
import {ref} from 'vue';

const showModal = ref(false);

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
    <DialogModal :show="showModal">
      <template #title>
        <div class="flex flex-row justify-between items-center">
          <h1 class="font-bold text-xl">
            Create Shop
          </h1>
          <button @click="showModal = false" class="text-gray-600 hover:text-gray-900">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
              <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>

          </button>
        </div>
      </template>
      <template #content>
        Content
      </template>
      <template #footer>
        <div class="flex flex-row justify-end">
          <button class="transition bg-white px-4 py-2 rounded-lg border bg-violet-500 hover:bg-violet-600 font-bold text-white" @click="">
            Create Shop
          </button>
        </div>
      </template>

    </DialogModal>
    <div class="py-12">

      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class=" overflow-hidden sm:rounded-lg">
          <div class="flex flex-row mb-6 justify-between">
            <div
                class="flex flex-row border rounded-xl overflow-hidden items-center bg-white text-gray-400 focus-within:text-gray-600 transition">
              <span class="ml-3">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                  <path fill-rule="evenodd"
                        d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z"
                        clip-rule="evenodd"/>
                </svg>

              </span>
              <input type="text" class="appearance-none border-none py-4 px-2 focus:ring-0 h-6 text-sm">
            </div>
            <div>

              <button @click="showModal = true" @close="showModal = false" class="bg-white flex flex-row font-semibold py-2 px-4 rounded-lg border">
                  <span class="mr-2">
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                       class="w-5 h-5">
                    <path
                        d="M2.879 7.121A3 3 0 007.5 6.66a2.997 2.997 0 002.5 1.34 2.997 2.997 0 002.5-1.34 3 3 0 104.622-3.78l-.293-.293A2 2 0 0015.415 2H4.585a2 2 0 00-1.414.586l-.292.292a3 3 0 000 4.243zM3 9.032a4.507 4.507 0 004.5-.29A4.48 4.48 0 0010 9.5a4.48 4.48 0 002.5-.758 4.507 4.507 0 004.5.29V16.5h.25a.75.75 0 010 1.5h-4.5a.75.75 0 01-.75-.75v-3.5a.75.75 0 00-.75-.75h-2.5a.75.75 0 00-.75.75v3.5a.75.75 0 01-.75.75h-4.5a.75.75 0 010-1.5H3V9.032z"/>
                  </svg>
                  </span>

                <p>Create Shop</p>
              </button>

            </div>
          </div>
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 w-full">

            <div v-for="shop in shops.data"
                 class="flex flex-col bg-white rounded-xl border h-52 w-72 p-4 hover:shadow transition gap-2 justify-between">
              <div class="flex flex-row items-center">
                <div>
                  <svg class="h-7 w-7 text-gray-800 mr-3" viewBox="0 0 34 34" version="1.1"
                       xmlns="http://www.w3.org/2000/svg"
                       xmlns:xlink="http://www.w3.org/1999/xlink">
                    <g id="icon-shopware-5" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                      <path
                          d="M17,0 C7.6075,0 0,7.6075 0,17 C0,26.3925 7.6075,34 17,34 C26.3925,34 34,26.3925 34,17 C34,7.6075 26.3925,0 17,0 Z M10.0895,6.46 L25.5,6.46 L23.953,10.88 L8.5425,10.88 L10.0895,6.46 Z M17.9945,18.02 C17.0935,18.02 9.911,18.02 9.911,18.02 L8.534,14.195 C8.534,14.195 14.756,14.195 17.7565,14.195 C22.134,14.195 23.766,15.572 24.3695,16.83 C25.585,19.771 22.304,23.375 17.0425,23.3581194 C16.881,23.3581194 19.635,23.3325 21.0545,21.2755 C22.1765,19.1675 20.978,18.02 17.9945,18.02 Z M8.5255,11.05 L13.5405,11.05 L13.5405,14.025 L8.5255,14.025 L8.5255,11.05 L8.5255,11.05 Z M24.939,23.749 C22.984,27.6165 18.683,27.3785 16.6515,27.3785 C14.3225,27.3785 8.5425,27.3785 8.5425,27.3785 L9.9195,23.5535 C9.928,23.5535 10.1575,23.5535 17.068,23.5535 C22.627,23.5535 25.6955,19.7625 24.5735,16.915 C24.565,16.915 26.401,20.3745 24.939,23.749 Z"
                          id="Shape-Copy-17" fill="currentColor" fill-rule="nonzero"></path>
                    </g>
                  </svg>
                </div>
                <Link class="" :href="route('shops.show', shop.id)"> {{ shop.name }}</Link>
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
