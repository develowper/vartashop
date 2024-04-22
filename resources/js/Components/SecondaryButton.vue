<script setup>
defineProps({
  type: {
    type: String,
    default: 'button',
  }, classes: {
    type: String,
    default: 'button',
  },
});
</script>

<template>
  <button
      :type="type"
      class=" bg-white hover:bg-gray-100 rounded-lg  group text-primary-500  z-10">
    <div :class="classes"
         class="p-2 rounded-lg  start-0 top-0 w-full h-full     duration-300   ">

      <slot/>
    </div>

  </button>
</template>
