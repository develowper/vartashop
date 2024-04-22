<script setup>
import {onMounted, ref,} from 'vue';
import {initTE, Input} from "tw-elements";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";

defineProps(['modelValue',]);


defineEmits(['update:modelValue', 'search']);

const input = ref(null);

onMounted(() => {


});

defineExpose({focus: () => input.value.focus()});

const focusNext = (elem) => {
  if (!elem || !elem.form) return;
  const currentIndex = Array.from(elem.form.elements).indexOf(elem);
  elem.form.elements.item(
      currentIndex < elem.form.elements.length - 1 ?
          currentIndex + 1 :
          0
  ).focus();
}
</script>

<template>
  <div>
    <div class="     rounded border border-neutral-300  ">
      <div class="relative    mx-auto  ">
        <div
            class="absolute top-0 bottom-0 start-4 flex items-center opacity-60  hover:cursor-pointer   ">
          <svg @click.prevent="$emit('search' )"
               class="w-4 h-4 text-gray-600  fill-gray-500   "
               xmlns="http://www.w3.org/2000/svg"
               viewBox="0 0 20 20">
            <path
                d="M12.9 14.32a8 8 0 1 1 1.41-1.41l5.35 5.33-1.42 1.42-5.33-5.34zM8 14A6 6 0 1 0 8 2a6 6 0 0 0 0 12z"></path>
          </svg>
          <span class="absolute border-gray-300  border-s top-0 bottom-0 my-2 ms-6 "></span>
        </div>
        <input id="search-toggle" :value="modelValue" type="search"
               @input="$emit('update:modelValue', $event.target.value)"
               :placeholder="__('hero_search_placeholder')"
               class="placeholder-gray-400 border-transparent   w-full py-2 ps-12 pe-4 focus:ring-primary-500   text-gray-600 bg-gray-100 rounded-lg  "
               @search="$emit('search' )">

      </div>

    </div>
  </div>

</template>
