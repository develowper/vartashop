<script setup>
import {onMounted, ref, reactive} from 'vue';
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import Tooltip from "@/Components/Tooltip.vue";
import {
  TagIcon,
  CheckIcon,
  PlusIcon,
  XMarkIcon,

} from "@heroicons/vue/24/outline";
import {QuestionMarkCircleIcon,} from "@heroicons/vue/24/solid";

const props = defineProps(['modelValue', 'type', 'id', 'classes', 'placeholder', 'error']);

const emit = defineEmits(['update:modelValue']);

let tmpValue = ref(null);
let input = ref(null);
const items = reactive([]);


onMounted(() => {
  if (input.value.hasAttribute('autofocus')) {
    input.value.focus();
  }


});
const set = (data) => {

  if (data) {
    const tmp = data.split(",");
    for (let i in tmp)
      items.push(tmp[i]);
    emit('update:modelValue', items.join(","))
  }
};
defineExpose({focus: () => input.value.focus(), set});


const add = () => {
  if (!tmpValue.value) return;
  items.push(tmpValue.value);
  tmpValue.value = null;
  emit('update:modelValue', items.join(","))
};
const remove = (idx) => {
  items.splice(idx, 1);
  emit('update:modelValue', items.join(","))
}
</script>

<template>
  <div>
    <div class="flex items-center">
      <Tooltip class="  " :content="__('help_tags')">
        <QuestionMarkCircleIcon class="text-gray-500 hover:bg-gray-50 w-4 h-4"/>
      </Tooltip>
      <InputLabel :for="id" :value="placeholder"/>
    </div>
    <div class="relative mb-2  flex flex-wrap items-stretch">
        <span
            class="flex bg-gray-100  text-gray-500 items-center whitespace-nowrap rounded-s border border-e-0 border-solid border-neutral-300  text-center text-base font-normal leading-[1.6]   dark:border-neutral-600 dark:text-neutral-200 dark:placeholder:text-neutral-200"
            id="basic-addon1">
             <div class="p-3">
                 <TagIcon class="h-5 w-5"/>
            </div>
        </span>
      <input
          v-model="tmpValue"
          @keydown.enter.prevent="add"
          class="  flex-auto rounded-0  border border-solid border-neutral-300    px-3   text-neutral-700   transition duration-200 ease-in-out focus:z-[3] focus:border-primary-500 focus:text-neutral-700   dark:border-neutral-600 dark:text-neutral-200 dark:placeholder:text-neutral-200 dark:focus:border-primary"
          @visibility.window="$el.type =  'text'  "
          ref="input"/>
      <input :id="id" type="hidden" :value="modelValue" @click="$emit('update:modelValue', $event.target.value)">
      <span @click="add"
            class="flex  cursor-pointer hover:bg-primary-400 bg-primary-500 items-center whitespace-nowrap rounded-e border border-s-0 border-solid border-neutral-300 px-3 py-[0.25rem] text-center text-base font-normal leading-[1.6] text-neutral-700 dark:border-neutral-600 dark:text-neutral-200 dark:placeholder:text-neutral-200"
            id="basic-addon2">
            <PlusIcon class="h-5 w-5 text-white font-bold"/>
        </span>
    </div>
    <InputError class="mt-1" :message="error"/>
    <div class="flex   items-center  ">
            <span v-for="(item,idx) in items">
                <button @click.prevent="remove(idx)"
                        class=" flex mx-[1px] items-center bg-primary-500 hover:bg-primary-600 rounded p-1 text-sm text-white  ">
                    <XMarkIcon class="h-5 w-5 font-bold"/>
                    <span class="px-2">{{ item }}</span>
                </button>
            </span>
    </div>
  </div>

</template>
