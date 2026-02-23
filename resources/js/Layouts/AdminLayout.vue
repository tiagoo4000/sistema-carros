<script setup>
import { ref } from 'vue'
import Sidebar from '../Components/Mosaic/Partials/Sidebar.vue'
import Header from '../Components/Mosaic/Partials/Header.vue'

const sidebarOpen = ref(false)
</script>

<template>
  <div class="flex h-[100dvh] overflow-hidden">

    <!-- Sidebar -->
    <Sidebar :sidebarOpen="sidebarOpen" @close-sidebar="sidebarOpen = false" />

    <!-- Content area -->
    <div class="relative flex flex-col flex-1 overflow-y-auto overflow-x-hidden">
      
      <!-- Site header -->
      <Header :sidebarOpen="sidebarOpen" @toggle-sidebar="sidebarOpen = !sidebarOpen" />

      <main>
        <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
          <slot />
        </div>
      </main>

    </div> 

  </div>
</template>