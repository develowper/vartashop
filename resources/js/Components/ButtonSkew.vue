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
  <button
      :type="type"
      class=" relative z-[30]  group     p-2  ">
    <div :class="classes"
         class="absolute    bg-primary-500 start-0 top-0 w-full h-full rounded-lg  -skew-x-12  duration-300 group-hover:bg-primary-400 z-[-1] ">
    </div>
    <slot class="  "/>
    <!--    <div-->
    <!--        class="absolute bg-primary start-0 top-0 w-full h-full rounded-lg start-[8px] duration-300 group-hover:bg-primary-700 z-0 "></div>-->
    <!--      class="  skew inline-flex items-center cursor-pointer   justify-center px-4 py-2 bg-primary-500 border border-transparent rounded-md font-semibold  transition-all duration-300 text-white     hover:bg-primary-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">-->
  </button>
</template>
<style lang="scss">

</style>