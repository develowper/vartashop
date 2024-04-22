<template>
  <div class="">
    <InputLabel class="my-2" for="phone" :value="__('socials')"/>
    <div class="rounded border p-2 my-1   flex flex-col">
      <div v-for="(data,idx) in socials"
           class="flex  grow   border-b my-1  " :key="idx">
        <div class="grid  grow xs:grid-cols-1 md:grid-cols-1 gap-1   ">
          <div class=" my-1   flex  ">
      <span
          class="  bg-gray-100  text-gray-500 items-center whitespace-nowrap rounded-s border border-e-0 border-solid border-neutral-300  text-center text-base font-normal leading-[1.6]   dark:border-neutral-600 dark:text-neutral-200 dark:placeholder:text-neutral-200"
          id="basic-addon1">
             <div class="p-3">
                 <Bars2Icon class="h-5 w-5"/>
            </div>
        </span>
            <input :placeholder="__('name_example')"
                   v-model="data.name"
                   class=" grow  placeholder:text-xs placeholder:text-gray-400 rounded-e  border border-solid border-neutral-300    px-3   text-neutral-700   transition duration-200 ease-in-out focus:z-[3]  focus:border-primary focus:text-neutral-700   dark:border-neutral-600 dark:text-neutral-200 dark:placeholder:text-neutral-200 dark:focus:border-primary"
            />
          </div>

          <div class="  my-1  flex  ">
      <span
          class=" bg-gray-100  text-gray-500 items-center whitespace-nowrap rounded-s border border-e-0 border-solid border-neutral-300  text-center text-base font-normal leading-[1.6]   dark:border-neutral-600 dark:text-neutral-200 dark:placeholder:text-neutral-200"
          id="basic-addon1">
             <div class="p-3">
                 <LinkIcon class="h-5 w-5"/>
            </div>
        </span>
            <input
                v-model="data.value" :placeholder="__('link_example')"
                class=" grow placeholder:text-xs placeholder:text-gray-400 rounded-e  border border-solid border-neutral-300    px-3   text-neutral-700   transition duration-200 ease-in-out focus:z-[3] focus:border-primary focus:text-neutral-700   dark:border-neutral-600 dark:text-neutral-200 dark:placeholder:text-neutral-200 dark:focus:border-primary"
            />
          </div>
        </div>
        <span @click="socials.length>1? socials.splice(idx,1): socials=[{name: null, value: null}]"
              class="rounded flex  my-1   cursor-pointer hover:bg-danger-400 bg-danger-500 items-center whitespace-nowrap rounded-e border     px-3 py-[0.25rem] text-center text-base font-normal leading-[1.6] text-neutral-700 dark:border-neutral-600 dark:text-neutral-200 dark:placeholder:text-neutral-200"
        >
            <MinusIcon class="h-5 w-5 text-white font-bold"/>
        </span>
      </div>
      <span @click="socials.push({name: null, value: null})"
            class="rounded flex w-fit self-end       cursor-pointer hover:bg-primary-400 bg-primary-500 items-center whitespace-nowrap rounded-e border     px-3 py-[0.25rem] text-center text-base font-normal leading-[1.6] text-neutral-700 dark:border-neutral-600 dark:text-neutral-200 dark:placeholder:text-neutral-200"
      >
            <PlusIcon class="h-8 w-5 text-white font-bold"/>
        </span>
      <InputError class="mt-1" :message="error"/>
    </div>
  </div>

</template>

<script>
import Scaffold from "@/Layouts/Scaffold.vue";
import Image from "@/Components/Image.vue";
import {Link} from "@inertiajs/vue3";
import {CurrencyDollarIcon, EyeIcon} from "@heroicons/vue/24/outline";
import LoadingIcon from "@/Components/LoadingIcon.vue";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import {getCurrentInstance} from 'vue'
import {
  LinkIcon,
  Bars2Icon,
  PlusIcon,
  MinusIcon,
} from "@heroicons/vue/24/outline";

export default {
  props: ['modelValue', 'error',],
  emits: ['update:modelValue',],
  data() {
    return {
      socials: [{name: null, value: null}]
    }
  },
  components: {
    LinkIcon,
    Bars2Icon,
    InputLabel,
    PlusIcon,
    MinusIcon,
    InputError,

  },
  created() {
    // this.isLoading(true);
  },
  mounted() {

  }, updated() {

  },
  methods: {
    get() {
      for (let i in this.socials) {
        if (this.socials[i].name == null && this.socials[i].value == null)
          this.socials.splice(i, 1);
      }
      return JSON.stringify(this.socials);
    },
    set(data) {
      if (data)
        this.socials = JSON.parse(data);

    }
  },
}
</script>