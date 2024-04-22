<script setup>
import {onMounted, ref,} from 'vue';
import {initTE, Input} from "tw-elements";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";

defineProps(['modelValue', 'type', 'id', 'classes', 'verified', 'placeholder', 'error', 'multiline', 'admin', 'disabled', 'step']);


defineEmits(['update:modelValue', 'update:verified',]);

const input = ref(null);

onMounted(() => {
  if (input.value && input.value.hasAttribute('autofocus')) {
    input.value.focus();
  }

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
  <div v-if="type=='checkbox'" class="relative flex justify-end items-center" dir="ltr">
    <label
        class="inline-block mx-2   hover:cursor-pointer text-gray-500"
        :for="id"
    > {{ placeholder }}
    </label>
    <input :value="modelValue"
           @change="$emit('update:modelValue', $event.target.checked  ) ; "
           class="   h-3.5 w-8 appearance-none rounded-[0.4375rem]  bg-neutral-300 before:pointer-events-none before:absolute before:h-3.5 before:w-3.5 before:rounded-full before:bg-transparent before:content-[''] after:absolute after:z-[2] after:-mt-[0.1975rem] after:h-5 after:w-5 after:rounded-full after:border-none after:bg-neutral-100 after:shadow-[0_0px_3px_0_rgb(0_0_0_/_7%),_0_2px_2px_0_rgb(0_0_0_/_4%)] after:transition-[background-color_0.2s,transform_0.2s] after:content-[''] checked:bg-success checked:after:absolute checked:after:z-[2] checked:after:-mt-[3px] checked:after:ml-[1.0625rem] checked:after:h-5 checked:after:w-5 checked:after:rounded-full checked:after:border-none checked:after:bg-success checked:after:shadow-[0_3px_1px_-2px_rgba(0,0,0,0.2),_0_2px_2px_0_rgba(0,0,0,0.14),_0_1px_5px_0_rgba(0,0,0,0.12)] checked:after:transition-[background-color_0.2s,transform_0.2s] checked:after:content-[''] hover:cursor-pointer focus:outline-none focus:ring-0 focus:before:scale-100 focus:before:opacity-[0.12] focus:before:shadow-[3px_-1px_0px_13px_rgba(0,0,0,0.6)] focus:before:transition-[box-shadow_0.2s,transform_0.2s] focus:after:absolute focus:after:z-[1] focus:after:block focus:after:h-5 focus:after:w-5 focus:after:rounded-full focus:after:content-[''] checked:focus:border-success checked:focus:bg-success checked:focus:before:ml-[1.0625rem] checked:focus:before:scale-100 checked:focus:before:shadow-[3px_-1px_0px_13px_#3b71ca] checked:focus:before:transition-[box-shadow_0.2s,transform_0.2s] dark:bg-neutral-600 dark:after:bg-success-400 dark:checked:bg-primary dark:checked:after:bg-success dark:focus:before:shadow-[3px_-1px_0px_13px_rgba(255,255,255,0.4)] dark:checked:focus:before:shadow-[3px_-1px_0px_13px_#3b71ca]"
           type="checkbox"
           :checked="modelValue  || null"
           role="switch"
           :id="id"
    />


  </div>
  <div v-else>
    <div class="flex items-center ">
      <InputLabel :for="id" :value="placeholder"/>
      <span v-if="verified==0" class="text-danger text-xs mx-1">({{ __('not_verified') }})</span>
      <span v-else-if="verified==1" class="text-success text-xs mx-1">({{ __('verified') }})</span>
      <span v-if="admin" class="flex items-center">
          <input id="checkbox-verify" type="checkbox" :checked="verified==1 || null"
                 @change="$emit('update:verified', $event.target.checked ? 1:0) "
                 class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 dark:focus:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
          <label for="checkbox-verify" class="sr-only"> </label>
        </span>
    </div>
    <div class="    flex flex-wra items-stretch ">



        <span v-if="$slots.prepend"
              class="flex bg-gray-100  text-gray-500 items-center whitespace-nowrap rounded-s border border-e-0 border-solid border-neutral-300  text-center text-base font-normal leading-[1.6]   dark:border-neutral-600 dark:text-neutral-200 dark:placeholder:text-neutral-200"
              id="basic-addon1">
            <slot name="prepend"></slot>
        </span>


      <input v-if="!multiline"
             @keydown.enter.prevent="focusNext($event.target)"
             :id="id"
             :disabled="disabled"
             :type="type"
             :step="step"
             :class="classes + (disabled?' opacity-50 ':'')+( $slots.append && $slots.prepend ? ' rounded-0 ':$slots.append? ' rounded-s ':$slots.prepend?' rounded-e ':' rounded ')"
             class="  flex grow w-full border border-solid border-neutral-300     px-3   text-neutral-700   transition duration-200 ease-in-out focus:z-[3] focus:border-primary focus:text-neutral-700   dark:border-neutral-600 dark:text-neutral-200 dark:placeholder:text-neutral-200 dark:focus:border-primary"
             :value="modelValue"
             @input="$emit('update:modelValue', $event.target.value)"
             @visibility.window="$el.type = ($el.type == 'password') ? 'text' : 'password' "
             ref="input"/>
      <textarea v-else
                rows="5"
                :id="id"
                :class="classes +( $slots.append && $slots.prepend ? ' rounded-0 ':$slots.append? ' rounded-s ':$slots.prepend?' rounded-e ':' rounded ')"
                class="  flex-auto border border-solid border-neutral-300    px-3   text-neutral-700   transition duration-200 ease-in-out focus:z-[3] focus:border-primary focus:text-neutral-700   dark:border-neutral-600 dark:text-neutral-200 dark:placeholder:text-neutral-200 dark:focus:border-primary"
                :value="modelValue"
                @input="$emit('update:modelValue', $event.target.value)"
                ref="input"></textarea>

      <span v-if="$slots.append"
            class="flex bg-gray-100 items-center whitespace-nowrap rounded-e border border-s-0 border-solid border-neutral-300 px-3 py-[0.25rem] text-center text-base font-normal leading-[1.6] text-neutral-700 dark:border-neutral-600 dark:text-neutral-200 dark:placeholder:text-neutral-200"
            id="basic-addon2">
            <slot name="append"></slot>
        </span>
    </div>
    <InputError class="mt-1" :message="error"/>
  </div>

</template>
