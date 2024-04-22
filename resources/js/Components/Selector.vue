<template>
  <div>
    <InputLabel class="my-1  " :for="id" :value="label"/>
    <div class="flex " :id="`${id}-wrapper`">
         <span v-if="$slots.append"
               class=" flex bg-gray-100  text-gray-500 items-center whitespace-nowrap rounded-s border border-e-0 border-solid border-neutral-300  text-center text-base font-normal leading-[1.6]   dark:border-neutral-600 dark:text-neutral-200 dark:placeholder:text-neutral-200"
               :id="`${id}-addon`">
            <slot name="append"></slot>
        </span>

      <span class="grow " dir="ltr">
            <select :multiple="multiple" :id="id" class=" " :value="modelValue" v-model="selecteds"
                    @change=" set ( $event.target.value)"
                    data-te-select-init
                    :name="id"
                    :data-te-select-all="false"
                    data-te-select-filter="true"
                    data-te-select-search-placeholder="..."
                    data-te-select-clear-button="true"
                    data-te-select-size="lg"
                    data-te-class-select-input="border rounded-s border-neutral-300 text-gray-700 text-center block min-h-[auto] w-full bg-transparent outline-none transition-all duration-200 ease-linear focus:placeholder:opacity-100 data-[te-input-state-active]:placeholder:opacity-100 motion-reduce:transition-none dark:text-gray-200 dark:placeholder:text-gray-200 [&:not([data-te-input-placeholder-active])]:placeholder:opacity-0 cursor-pointer data-[te-input-disabled]:bg-[#e9ecef] data-[te-input-disabled]:cursor-default group-data-[te-was-validated]/validation:mb-4 dark:data-[te-input-disabled]:bg-zinc-600"
                    data-te-class-select-dropdown-container="z-[1070]"
                    data-te-class-select-filter-input="relative m-0 text-end block w-full min-w-0 flex-auto rounded border border-solid border-gray-300 bg-transparent bg-clip-padding px-3 py-1.5 text-base font-normal text-gray-700 transition duration-300 ease-in-out motion-reduce:transition-none focus:border-blue-600 focus:text-gray-700 focus:shadow-te-blue focus:outline-none dark:text-gray-200 dark:placeholder:text-gray-200"
                    data-te-class-select-wrapper=""
                    data-te-class-no-result="text-center px-4"
                    :data-te-select-no-result-text="__('no_results')"
                    :data-te-select-placeholder="placeholder"
                    data-te-class-select-input-placeholder="__('no_results')"
                    data-te-class-select-form-outline="rounded-lg"
                    data-te-class-select-input-notch="pointer-events-none box-border bg-transparent transition-all duration-200 ease-linear motion-reduce:transition-none left-0 top-0 h-full w-2 border-0 rounded-l-[0.25rem] group-data-[te-input-focused]:border-r-0 group-data-[te-input-state-active]:border-r-0 border-neutral-300 dark:border-neutral-600 group-data-[te-input-focused]:shadow-[-1px_0_0_#3b71ca,_0_1px_0_0_#3b71ca,_0_-1px_0_0_#3b71ca] group-data-[te-input-focused]:border-none"
                    data-te-class-select-input-group="flex items-center whitespace-nowrap p-3 text-center text-base font-normal leading-[1.6] text-gray-700 dark:bg-zinc-800 dark:text-gray-200 dark:placeholder:text-gray-200"
                    data-te-class-select-clear-btn="hidden absolute opacity-0 top-2 end-0 text-gray-50 outline-none"
                    data-te-class-select-arrow="absolute end-3 text-[0.8rem] cursor-pointer peer-focus:text-primary peer-data-[te-input-focused]:text-primary group-data-[te-was-validated]/validation:peer-valid:text-green-600 group-data-[te-was-validated]/validation:peer-invalid:text-[rgb(220,76,100)] w-5 h-5"
                    data-te-class-select-option="flex flex-row-reverse text-sm items-center justify-center w-full px-4 truncate text-gray-700 bg-transparent select-none cursor-pointer data-[te-input-multiple-active]:bg-black/5 hover:[&:not([data-te-select-option-disabled])]:bg-black/5 data-[te-input-state-active]:bg-black/5 data-[te-select-option-selected]:data-[te-input-state-active]:bg-black/5 data-[te-select-selected]:data-[te-select-option-disabled]:cursor-default data-[te-select-selected]:data-[te-select-option-disabled]:text-gray-400 data-[te-select-selected]:data-[te-select-option-disabled]:bg-transparent data-[te-select-option-selected]:bg-black/[0.02] data-[te-select-option-disabled]:text-gray-400 data-[te-select-option-disabled]:cursor-default group-data-[te-select-option-group-ref]/opt:pl-7 dark:text-gray-200 dark:hover:[&:not([data-te-select-option-disabled])]:bg-white/30 dark:data-[te-input-state-active]:bg-white/30 dark:data-[te-select-option-selected]:data-[te-input-state-active]:bg-white/30 dark:data-[te-select-option-disabled]:text-gray-400 dark:data-[te-input-multiple-active]:bg-white/30"

                    :data-te-select-all-label="__('select_all')"
                    :data-te-select-options-selected-label="__('item')"
                    :data-te-select-search-placeholder="__('search')"
                    data-te-select-class="text-center"
            >
                <option value="" hidden></option>
                <option class="text-start" v-for="d in data" :value="d.id"> {{
                    __(d.name)
                  }}</option>

            </select>

        <!--            <label data-te-select-label-ref> {{ label }}</label>-->
</span>
      <div
          @click.stop="selecteds=( multiple ? []:null);input.value=null; set ( null); "
          class="bg-transparent border border-gray-300 border-s-0 rounded-lg-e  cursor-pointer text-gray-400 align-middle rounded-e hover:bg-gray-200">
        <XMarkIcon class="w-8 h-11    p-1 "/>
      </div>
    </div>
    <InputError class="mt-1" :message="error"/>
  </div>
</template>


<script>
import {Select, initTE} from "tw-elements";
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import {XMarkIcon} from "@heroicons/vue/24/outline"

export default {
  data() {
    return {
      selecteds: this.multiple ? [] : this.modelValue,
      selected: null,
      closeButton: null,
    }
  },
  props: ['id', 'label', 'data', 'modelValue', 'error', 'multiple', 'placeholder'],
  emits: ['update:modelValue'],
  components: {InputLabel, InputError, XMarkIcon},
  watch: {
    modelValue(_new, _old) {
      this.closeButton.classList.add('opacity-0');
      // if (!_new && !this.closeButton.classList.contains('opacity-0'))
      //   this.closeButton.classList.add('opacity-0');
      // else
      //   this.closeButton.classList.remove('opacity-0');
    }
  },
  created() {


  },
  mounted() {
    // this.log(this.data);
    new Select(document.getElementById(this.id));

    this.closeButton = document.querySelector(`#${this.id + '-wrapper'} span[data-te-select-clear-btn-ref]`);
    this.input = document.querySelector(`#${this.id + '-wrapper'} input[data-te-select-input-ref]`);
    if (this.input)
      this.input.setAttribute('dir', 'rtl');
    if (this.closeButton && this.modelValue == null)
      this.closeButton.classList.add('opacity-0');

    // if (!window.Select) {
    //   this.$forceUpdate();
    //   this.$nextTick(function () {
    //     initTE({Select})
    //     window.Select = Select;
    //   });
    // }

  }, methods: {
    set(val) {
      if (!this.multiple)
        this.$emit('update:modelValue', val);
      else {
        this.$emit('update:modelValue', this.selecteds);

      }


    }
  }
}
</script>

<style scoped>


</style>
