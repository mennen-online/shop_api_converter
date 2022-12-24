<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import {Link} from "@inertiajs/inertia-vue3";
import DialogModal from "@/Components/DialogModal.vue";
import {reactive, ref, watch} from 'vue';
import {Inertia} from "@inertiajs/inertia";


let showModal = ref(false);

let search = ref('');

const form = reactive({
  name: null,
  url: null,
  type: '',
  credentials: {
    username: null,
    password: null,
    client_id: null,
    client_secret: null,
  },
});

const props = defineProps(['shops'])

const currentPage = props.shops['current_page'];
const lastPage = props.shops['last_page'];

const statusBadges = {
  'not_synced': 'bg-slate-100 w-24 text-sm px-2 py-1.5 rounded-lg text-center font-bold',
  'failed': 'bg-red-500 text-white w-24 text-sm px-2 py-1.5 rounded-lg text-center font-bold',
  'finished': 'bg-green-500 text-white  w-24 text-sm px-2 py-1.5 rounded-lg text-center font-bold',
  'enqueued': 'bg-yellow-300 font-bold w-24 text-sm px-2 py-1.5 rounded-lg text-center font-bold',
  'running': 'bg-green-300 font-bold w-24 text-sm px-2 py-1.5 rounded-lg text-center font-bold',
  'unknown': 'bg-orange-200 font-bold w-32 text-sm px-2 py-1.5 rounded-lg text-center font-bold',
}

watch(search, async (newSearch, oldSearch) => {
  if (oldSearch !== newSearch) {
    if (newSearch === '') {
      Inertia.visit(route('shops.index'), {preserveState: true})
      return;
    }

    Inertia.visit(route('shops.index', {
      _query: {
        search: newSearch
      }
    }), {preserveState: true})

  }
})

function submitForm() {
  showModal = false;
  Inertia.post(route('shops.store'), form);
}

function nextPage() {
  if (currentPage !== lastPage) {
    Inertia.visit(route('shops.index', {
      _query: {
        page: (currentPage + 1)
      }
    }));
  }
}

function prevPage() {
  if (currentPage > 1) {
    if ((currentPage - 1) === 1) {
      Inertia.visit(route('shops.index'))
      return;
    }

    Inertia.visit(route('shops.index', {
      _query: {
        page: (currentPage - 1)
      }
    }));
  }
}

function getStatus(shopStatus) {
  switch (shopStatus) {
    case 'not_synced':
      return {
        status: 'Not synced',
        resync: true,
        class: statusBadges.not_synced
      }
    case 'finished':
      return {
        status: 'Synced',
        resync: true,
        class: statusBadges.finished
      }
    case 'queued': {
      return {
        status: 'Enqueued',
        resync: false,
        class: statusBadges.enqueued
      }
    }
    case 'running': {
      return {
        status: 'Syncing',
        resync: false,
        class: statusBadges.running
      }
    }
    case 'failed': {
      return {
        status: 'Sync failed',
        resync: true,
        class: statusBadges.failed
      }
    }
    default: {
      return {
        status: 'Unknown Status',
        resync: true,
        class: statusBadges.unknown
      }
    }
  }
}

function syncShop(id) {
  Inertia.get(route('shop.sync', id))
}
</script>

<template>
  <AppLayout title="Server Overview">
    <template #header>
      <h2 class="semibold text-xl text-gray-800 leading-tight">
        Shops
      </h2>
    </template>
    <DialogModal :show="showModal">
      <template #title>
        <div class="flex flex-row justify-between items-center">
          <h1 class="font-bold text-2xl">
            Create Shop
          </h1>
          <button class="text-gray-600 hover:text-gray-900" @click="showModal = false">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5"
                 viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path d="M6 18L18 6M6 6l12 12" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>

          </button>
        </div>
      </template>
      <template #content>
        <form id="createShopForm" class="flex flex-col" @submit.prevent="submitForm">
          <label class="mb-2" for="shop_name">Shop name</label>
          <input id="shop_name" v-model="form.name" class="mb-4 rounded-lg focus:ring-0 transition" required
                 type="text">
          <label class="mb-2" for="shop_url">Shop URL</label>
          <input id="shop_url" v-model="form.url" class="mb-4 rounded-lg focus:ring-0 transition" required
                 type="url">
          <label class="mb-1" for="shop_type">Select Shop Type</label>
          <div class="flex flex-col mb-4">
            <div>
              <input id="shopware6" v-model="form.type" class="mr-2" name="shop_type" required type="radio"
                     value="shopware6">
              <label for="shopware6">Shopware 6</label>
            </div>
            <div>
              <input id="shopware5" v-model="form.type" class="mr-2" name="shop_type" required type="radio"
                     value="shopware5">
              <label for="shopware5">Shopware 5</label>

            </div>
          </div>
          <h1 v-if="form.type" class="font-bold text-2xl mb-2">Credentials</h1>
          <div v-if="form.type === 'shopware5'" class="flex flex-col">
            <label class="mb-1" for="s5_username">Username</label>
            <input id="s5_username" v-model="form.credentials.username" class="mb-4 rounded-lg focus:ring-0 transition"
                   required
                   type="text">
            <label class="mb-1" for="s5_apikey">API Key</label>
            <input id="s5_apikey" v-model="form.credentials.password" class="mb-4 rounded-lg focus:ring-0 transition"
                   required
                   type="text">
          </div>
          <div v-if="form.type === 'shopware6'" class="flex flex-col">
            <label class="mb-1" for="s6_id">Client ID</label>
            <input id="s6_id" v-model="form.credentials.client_id" class="mb-4 rounded-lg focus:ring-0 transition"
                   required
                   type="text">
            <label class="mb-1" for="s6_secret">Client Secret</label>
            <input id="s6_secret" v-model="form.credentials.client_secret"
                   class="mb-4 rounded-lg focus:ring-0 transition"
                   required type="text">
          </div>
        </form>
      </template>
      <template #footer>
        <div class="flex flex-row justify-end">
          <button
              class="transition bg-white px-4 py-2 rounded-lg border bg-violet-500 hover:bg-violet-600 font-bold text-white"
              form="createShopForm"
              type="submit">Create Shop
          </button>
        </div>
      </template>

    </DialogModal>
    <div class="py-12">

      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class=" overflow-hidden sm:rounded-lg">
          <div class="flex flex-row mb-6 justify-between px-2">
            <div
                class="flex flex-row border rounded-xl overflow-hidden items-center bg-white text-gray-400 focus-within:text-gray-600 transition">
              <span class="ml-3">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                  <path clip-rule="evenodd"
                        d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z"
                        fill-rule="evenodd"/>
                </svg>

              </span>
              <input v-model="search" class="appearance-none border-none py-4 px-2 focus:ring-0 h-6 text-sm"
                     type="text">
            </div>
            <div class="flex flex-row justify-center items-center">
              <button @click="prevPage">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5"
                     viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                  <path d="M15.75 19.5L8.25 12l7.5-7.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </button>
              <p class="px-1 text-lg">{{ currentPage }}</p>
              <button @click="nextPage">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5"
                     viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                  <path d="M8.25 4.5l7.5 7.5-7.5 7.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </button>

            </div>
            <div>

              <button class="bg-white flex flex-row font-semibold py-2 px-4 rounded-lg border" @click="showModal = true"
                      @close="showModal = false">
                  <span class="mr-2">
                  <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"
                       xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M2.879 7.121A3 3 0 007.5 6.66a2.997 2.997 0 002.5 1.34 2.997 2.997 0 002.5-1.34 3 3 0 104.622-3.78l-.293-.293A2 2 0 0015.415 2H4.585a2 2 0 00-1.414.586l-.292.292a3 3 0 000 4.243zM3 9.032a4.507 4.507 0 004.5-.29A4.48 4.48 0 0010 9.5a4.48 4.48 0 002.5-.758 4.507 4.507 0 004.5.29V16.5h.25a.75.75 0 010 1.5h-4.5a.75.75 0 01-.75-.75v-3.5a.75.75 0 00-.75-.75h-2.5a.75.75 0 00-.75.75v3.5a.75.75 0 01-.75.75h-4.5a.75.75 0 010-1.5H3V9.032z"/>
                  </svg>
                  </span>

                Create Shop
              </button>


            </div>

          </div>
          <div v-if="$page.props.flash.message"
               :class="[$page.props.flash.message.type === 'success' ? 'bg-green-200' : 'bg-red-200']"
               class="w-full h-full rounded-xl mb-6 p-4 flex flex-row justify-between">
            <div>
              <div v-if="$page.props.flash.message.type === 'success'" class="flex flex-row items-center gap-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5"
                     viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                  <path d="M4.5 12.75l6 6 9-13.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <p>{{ $page.props.flash.message.text }}</p>
              </div>
              <div v-if="$page.props.flash.message.type === 'error'" class="flex flex-row items-center gap-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5"
                     viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                  <path d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"
                        stroke-linecap="round"
                        stroke-linejoin="round"/>
                </svg>

                <p>{{ $page.props.flash.message.text }}</p>
              </div>

            </div>

            <button @click="$page.props.flash.message = null">
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                <path
                    d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z"/>
              </svg>
            </button>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 w-full px-2 pb-2">

            <div v-for="shop in shops.data"
                 class="flex flex-col bg-white rounded-xl transition hover:scale-105 border h-52 w-72 p-4 hover:shadow transition gap-2 justify-between">
              <div class="flex flex-row items-center">
                <div class="h-7 w-7 text-gray-700 mr-3">
                  <svg v-if="shop.type === 'shopware6'" viewBox="0 0 34 34" xmlns="http://www.w3.org/2000/svg"
                  >
                    <g id="icon-shopware-6" fill="none" fill-rule="evenodd" stroke="none" stroke-width="1">
                      <path
                          id="Shape"
                          d="M29.6634615,21.6586538 C29.6634615,28.9326923 23.4927885,34 16.8317308,34 C10.0889423,34 4,28.9326923 4,21.5769231 C4,17.6947115 5.67548077,15.0384615 8.53605769,11.1971154 L16.8725962,0 L27.1298077,0 L19.7331731,10.0528846 C24.71875,10.7475962 29.6634615,15.1201923 29.6634615,21.6586538 Z M21.0817308,21.6177885 C21.0817308,19.0841346 19.1610577,17.3269231 16.8317308,17.3269231 C14.4615385,17.3269231 12.5817308,19.1658654 12.5817308,21.6177885 C12.5817308,24.1105769 14.4615385,26.03125 16.8317308,26.03125 C19.1610577,25.9903846 21.0817308,24.1514423 21.0817308,21.6177885 Z"
                          fill="currentColor" fill-rule="nonzero"></path>
                    </g>
                  </svg>
                  <svg v-if="shop.type === 'shopware5'" viewBox="0 0 34 34"
                       xmlns="http://www.w3.org/2000/svg"
                  >
                    <g id="icon-shopware-5" fill="none" fill-rule="evenodd" stroke="none" stroke-width="1">
                      <path
                          id="Shape-Copy-17"
                          d="M17,0 C7.6075,0 0,7.6075 0,17 C0,26.3925 7.6075,34 17,34 C26.3925,34 34,26.3925 34,17 C34,7.6075 26.3925,0 17,0 Z M10.0895,6.46 L25.5,6.46 L23.953,10.88 L8.5425,10.88 L10.0895,6.46 Z M17.9945,18.02 C17.0935,18.02 9.911,18.02 9.911,18.02 L8.534,14.195 C8.534,14.195 14.756,14.195 17.7565,14.195 C22.134,14.195 23.766,15.572 24.3695,16.83 C25.585,19.771 22.304,23.375 17.0425,23.3581194 C16.881,23.3581194 19.635,23.3325 21.0545,21.2755 C22.1765,19.1675 20.978,18.02 17.9945,18.02 Z M8.5255,11.05 L13.5405,11.05 L13.5405,14.025 L8.5255,14.025 L8.5255,11.05 L8.5255,11.05 Z M24.939,23.749 C22.984,27.6165 18.683,27.3785 16.6515,27.3785 C14.3225,27.3785 8.5425,27.3785 8.5425,27.3785 L9.9195,23.5535 C9.928,23.5535 10.1575,23.5535 17.068,23.5535 C22.627,23.5535 25.6955,19.7625 24.5735,16.915 C24.565,16.915 26.401,20.3745 24.939,23.749 Z"
                          fill="currentColor" fill-rule="nonzero"></path>
                    </g>
                  </svg>
                </div>
                <Link :href="route('shops.show', shop.id)" class=""> {{ shop.name }}</Link>
              </div>
              <div class="ml-10 text-xs text-gray-700 font-extralight h-full flex flex-col gap-2">
                <p class="truncate">
                  {{ shop.url }}
                </p>
                <p>
                  {{ shop.created_at }}
                </p>

              </div>
              <div :var="response = getStatus(shop.status)" class="flex flex-row justify-between items-center">
                <div :class="response.class">
                  {{ response.status }}
                </div>
                <div v-if="response.resync" class="flex flex-row items-center cursor-pointer hover:bg-emerald-500 transition bg-emerald-400 text-white pl-2 pr-3 py-1 rounded-lg font-bold"
                     @click="syncShop(shop.id)">
                  <svg class="w-5 h-5 mr-1.5" fill="currentColor" viewBox="0 0 20 20"
                       xmlns="http://www.w3.org/2000/svg">
                    <path clip-rule="evenodd"
                          d="M15.312 11.424a5.5 5.5 0 01-9.201 2.466l-.312-.311h2.433a.75.75 0 000-1.5H3.989a.75.75 0 00-.75.75v4.242a.75.75 0 001.5 0v-2.43l.31.31a7 7 0 0011.712-3.138.75.75 0 00-1.449-.39zm1.23-3.723a.75.75 0 00.219-.53V2.929a.75.75 0 00-1.5 0V5.36l-.31-.31A7 7 0 003.239 8.188a.75.75 0 101.448.389A5.5 5.5 0 0113.89 6.11l.311.31h-2.432a.75.75 0 000 1.5h4.243a.75.75 0 00.53-.219z"
                          fill-rule="evenodd"/>
                  </svg>
                  <p>Sync</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
