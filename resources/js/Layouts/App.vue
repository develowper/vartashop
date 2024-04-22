<template>
  <slot/>
</template>

<script setup>
import {Dropdown, initTE} from "tw-elements";
import {onMounted} from "vue";

onMounted(() => {
  window.tailwindElements();

});

</script>

<style scoped>

</style>