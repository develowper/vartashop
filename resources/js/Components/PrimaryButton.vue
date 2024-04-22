<script setup>
defineProps({
  type: {
    type: String,
    default: 'submit',
  }, classes: {
    type: String,
    default: 'submit',
  },
});
</script>

<template>
  <button @keydown.enter.self
          :type="type"
          class=" relative bg-primary-500 text-white  rounded-lg   duration-300 hover:bg-primary-400   p-2  "
          :class="classes">
    <slot class="  "/>

  </button>
</template>
<style lang="scss">

</style>