<script setup lang="ts">
import { computed } from "vue";
import { usePage } from "@inertiajs/vue3";

const page = usePage();
const flash = computed(() => page.props.flash);
</script>
<template>
  <div v-if="flash.status === 'success'" class="bg-blue-300 text-white p-4">
    {{ flash.message }}
  </div>
  <div v-if="flash.status === 'danger'" class="bg-red-300 text-white p-4">
    {{ flash.message }}
  </div>
</template>
