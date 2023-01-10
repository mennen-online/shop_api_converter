<template>
  <nav class="w-1/6">
    <Link class="flex flex-row items-center rounded px-2 py-1 mb-2 w-full" :href="route('shops.show', shop.id)" :class="isActive(route().current('shops.show', shop.id))">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
           stroke="currentColor" class="w-5 h-5 mr-3">
        <path stroke-linecap="round" stroke-linejoin="round"
              d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/>
      </svg>

      <p>Info</p>
    </Link>
    <Link class="flex flex-row items-center bg-gray-100 rounded px-2 py-1 w-full" :class="isActive(route().current('shops.endpoints.index', shop.id))"  :href="route('shops.endpoints.index', shop.id)">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
           stroke="currentColor" class="w-5 h-5 mr-3">
        <path stroke-linecap="round" stroke-linejoin="round"
              d="M9.348 14.651a3.75 3.75 0 010-5.303m5.304 0a3.75 3.75 0 010 5.303m-7.425 2.122a6.75 6.75 0 010-9.546m9.546 0a6.75 6.75 0 010 9.546M5.106 18.894c-3.808-3.808-3.808-9.98 0-13.789m13.788 0c3.808 3.808 3.808 9.981 0 13.79M12 12h.008v.007H12V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/>
      </svg>

      <p>Endpoints</p>
    </Link>
  </nav>
</template>

<script>
import { Link } from '@inertiajs/inertia-vue3'
export default {

  name: "ShopDetailNav",
  components: {
    Link
  },
  props: [
      'shop',
      'active'
  ],
  methods: {
    isActive(bool) {
      if (bool) {
        return 'bg-gray-200 shadow';
      }
    }
  }
}
</script>

<style scoped>

</style>