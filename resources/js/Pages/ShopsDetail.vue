<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import {Link} from "@inertiajs/inertia-vue3";
import ShopDetailNav from "@/Components/ShopDetail/ShopDetailNav.vue";

const props = defineProps(['shop', 'header', 'endpoints'])

const statusBadges = {
  'not_synced': 'bg-red-100 w-24 text-sm px-2 py-1 rounded-xl text-center',
  'synced': 'bg-green-100 w-24 text-sm px-2 py-1 rounded-xl text-center'
}

function getTimestamp(date) {
  let dateObject = new Date(date);
  return dateObject.toLocaleString();
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
                <Link class="mb-10 font-extralight text-sm flex flex-row items-center" :href="route('shops.index')">
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5">
                    <path fill-rule="evenodd"
                          d="M12.79 5.23a.75.75 0 01-.02 1.06L8.832 10l3.938 3.71a.75.75 0 11-1.04 1.08l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 011.06.02z"
                          clip-rule="evenodd"/>
                  </svg>
                  <p>Back to Shops</p>
                </Link>
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
              <ShopDetailNav :shop="shop"></ShopDetailNav>

              <div class="w-5/6 flex flex-col gap-2">
                <div class="bg-white rounded-lg p-4">
                  <h1 class="text-2xl font-bold">{{ header }}</h1>
                </div>
                <div class="bg-white p-4 rounded-lg">
                  <section v-if="route().current('shops.show')">
                    <p>{{ shop.name }}</p>
                    <p>{{ shop.id }}</p>
                    <p>{{ shop.status }}</p>
                    <p>{{ shop.url }}</p>
                  </section>

                  <section v-if="route().current('shops.endpoints.index')" class="flex flex-col">
                    <div>
                      <table class="table-auto w-full">
                        <tr class="text-gray-400">
                          <th class="pb-1">ID</th>
                          <th class="pb-1">Name</th>
                          <th class="pb-1">URL</th>
                          <th class="pb-1">Last fetched</th>
                        </tr>
                        <tr v-for="endpoint in endpoints" class="text-sm text-gray-700 text-center border-t">
                          <td class="p-2">{{ endpoint.id }}</td>
                          <td class="p-2">{{ endpoint.name }}</td>
                          <td class="p-2">{{ endpoint.url }}</td>
                          <td class="p-2">{{ getTimestamp(endpoint.updated_at) }}</td>
                        </tr>

                      </table>
                    </div>
                  </section>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
