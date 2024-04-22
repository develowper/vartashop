<template>

  <div class="   ">
    <InputLabel :for="id" :value="label"/>
    <!--    <template v-slot:button>-->

    <!--    </template>-->
    <div
        class="rounded text-primary-500     items-center border border-neutral-300 hover:cursor-pointer p-2 hover:bg-gray-50 text-gray-500"
        @click="reset();modal.show()">

      <CursorArrowRaysIcon class="h-4 w-4 mx-1 "/>
      <div v-if="selecteds.length==0">{{ `0 ${__('product')}` }}</div>
      <div v-else>
        <span>{{ `${selecteds.length} ${__('product')}` }}</span>
        <div class="flex flex-wrap  ">
          <div @click.stop=" remove(idx)" v-for="(s,idx) in selecteds"
               class="flex ms-1 mt-1   bg-primary-500 text-white rounded px-1">
            <span>{{ s.name }}</span>
            <span
                class="   p-1     rounded-full">
            <XMarkIcon class="  h-4 w-4   "/>
            </span>
          </div>
        </div>
      </div>
    </div>
    <InputError class="mt-1" :message="error"/>

    <!-- Modal -->
    <div
        data-te-modal-init
        class="fixed    left-0 top-0 backdrop-blur z-[1055] hidden h-full w-full overflow-y-auto overflow-x-hidden outline-none"
        id="productSelectorModal"
        tabindex="-1"
        aria-labelledby="productSelectorModalLabel"
        aria-hidden="true">
      <div
          data-te-modal-dialog-ref
          class="max-w-2xl pointer-events-none relative w-auto translate-y-[-50px] opacity-0 transition-all duration-300 ease-in-out min-[576px]:mx-auto min-[576px]:mt-7 px-2 sm:px-4 md:px8 min-[576px]:max-w-5xl">
        <div
            class="min-[576px]:shadow-[0_0.5rem_1rem_rgba(#000, 0.15)] pointer-events-auto relative flex w-full flex-col rounded-md border-none bg-white bg-clip-padding text-current shadow-lg outline-none max-w-xl mx-auto">
          <div
              class="flex flex-shrink-0 items-center justify-between rounded-t-md border-b-2 border-neutral-100 border-opacity-100 p-4">
            <!--Modal title-->
            <h5
                class="text-lg text-primary-500 flex items-center font-medium leading-normal text-neutral-600"
                id="productSelectorModalLabel">
              <MapPinIcon class="h-7 w-7 mx-3"/>
              {{ __('product_select') }}
            </h5>
            <!--Close button-->
            <button
                :class="`text-danger`"
                type="button"
                class="box-content rounded-none border-none hover:no-underline hover:opacity-75 focus:opacity-100 focus:shadow-none focus:outline-none"
                data-te-modal-dismiss
                aria-label="Close">
              <svg
                  xmlns="http://www.w3.org/2000/svg"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke-width="1.5"
                  stroke="currentColor"
                  class="h-6 w-6">
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>

          <!--Modal body-->
          <div class="relative   p-4" data-te-modal-body-ref>
            <div
                class="  sticky items-center justify-start px-4 py-2 text-primary-500 border-b md:py-4">

              <div class="   text-xs ">
                <span>{{ `${selecteds.length} ${__('product')}` }}</span>
                <div class="flex flex-wrap   ">
                  <div @click.stop="remove(idx)"
                       v-for="(s,idx) in selecteds"
                       class="flex items-center ms-1 mt-1 hover:bg-primary-400 cursor-pointer   bg-primary-500 text-white rounded px-1">
                    <span>{{ s.name }}</span>
                    <XMarkIcon class=" my-1 h-4 w-4   "/>
                  </div>
                </div>
              </div>

            </div>

            <div class="px-2  md:px-4">
              <div
                  class="    mx-auto md:max-w-3xl   mt-6 px-2 md:px-4 py-4   overflow-hidden  rounded-lg  ">

                <div class="flex flex-col mx-2   col-span-2 w-full     px-2">
                  <ul>
                    <li v-if="currentData.has_child "
                        @click="back()"
                        class="p-3 px-3 border-b flex text-gray-600 items-center justify-between hover:bg-gray-100 rounded  hover:cursor-pointer">
                      <span>{{ __('return') }}</span>
                      <ChevronRightIcon class="w-4 h-4"/>
                    </li>
                    <li v-for="(d,idx) in data"
                        @click="selectData(d)" v-show="  d.parent_id==currentData.id"
                        class="p-2 px-3 flex items-center justify-between hover:bg-gray-100 rounded  hover:cursor-pointer">
                      <div class="flex items-center">
                        <div class="mb-[0.125rem] block min-h-[1.5rem] pl-[1.5rem]" dir="ltr">

                          <label @click.stop
                                 class="inline-block select-none mx-2 hover:cursor-pointer"
                                 :for="`checkProduct${d.id}`">
                            <div v-if="d.has_child">
                              {{ d.name }}
                            </div>
                            <div v-else>
                              {{
                                createName(d)
                              }}
                            </div>

                          </label>
                          <input v-if="!d.has_child"
                                 class="relative float-end   h-[1.125rem] w-[1.125rem] appearance-none rounded-[0.25rem] border-[0.125rem] border-solid border-neutral-300 outline-none before:pointer-events-none before:absolute before:h-[0.875rem] before:w-[0.875rem] before:scale-0 before:rounded-full before:bg-transparent before:opacity-0 before:shadow-[0px_0px_0px_13px_transparent] before:content-[''] checked:border-primary-500 checked:bg-primary-500 checked:before:opacity-[0.16] checked:after:absolute checked:after:-mt-px checked:after:ml-[0.25rem] checked:after:block checked:after:h-[0.8125rem] checked:after:w-[0.375rem] checked:after:rotate-45 checked:after:border-[0.125rem] checked:after:border-l-0 checked:after:border-t-0 checked:after:border-solid checked:after:border-white hover:checked:bg-primary-500 checked:after:bg-transparent checked:after:content-[''] hover:cursor-pointer hover:before:opacity-[0.04] hover:before:shadow-[0px_0px_0px_13px_rgba(0,0,0,0.6)] focus:shadow-none focus:transition-[border-color_0.2s] focus:before:scale-100 focus:before:opacity-[0.12] focus:before:shadow-[0px_0px_0px_13px_rgba(0,0,0,0.6)] focus:before:transition-[box-shadow_0.2s,transform_0.2s] focus:after:absolute focus:after:z-[1] focus:after:block focus:after:h-[0.875rem] focus:after:w-[0.875rem] focus:after:rounded-[0.125rem] focus:after:content-[''] checked:focus:before:scale-100 checked:focus:before:shadow-[0px_0px_0px_13px_#3b71ca] checked:focus:before:transition-[box-shadow_0.2s,transform_0.2s] checked:focus:after:-mt-px checked:focus:after:ml-[0.25rem] checked:focus:after:h-[0.8125rem] checked:focus:after:w-[0.375rem] checked:focus:after:rotate-45 checked:focus:after:rounded-none checked:focus:after:border-[0.125rem] checked:focus:after:border-l-0 checked:focus:after:border-t-0 checked:focus:after:border-solid checked:focus:after:border-white checked:focus:after:bg-transparent dark:border-neutral-600 dark:checked:border-primary-500 dark:checked:bg-primary-500 dark:focus:before:shadow-[0px_0px_0px_13px_rgba(255,255,255,0.4)] dark:checked:focus:before:shadow-[0px_0px_0px_13px_#3b71ca]"
                                 type="checkbox"
                                 value=""
                                 :id="`checkProduct${d.id}`"
                                 :checked="selecteds.filter((e)=>e.id==d.id).length>0"/>

                        </div>
                      </div>
                      <ChevronLeftIcon v-if="d.has_child" class="w-4 h-4"/>
                    </li>
                  </ul>
                </div>


              </div>
            </div>
          </div>


        </div>
      </div>
    </div>
  </div>
</template>


<script>
import {Select, initTE, Modal} from "tw-elements";
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import LoadingIcon from '@/Components/LoadingIcon.vue';
import InputError from '@/Components/InputError.vue';
import {

  MapPinIcon,
  Bars2Icon,
  ChevronLeftIcon,
  ChevronRightIcon,
  XMarkIcon,
  CursorArrowRaysIcon,
} from "@heroicons/vue/24/outline";

export default {
  data() {
    return {
      params: {},
      currentLevel: 0,
      currentData: {id: 0, parent_id: 0, level: 0},
      selecteds: [],
      loading: false,
      data: [],

    }
  },
  props: ['id', 'link', 'label', 'preload', 'modelValue', 'multi', 'error'],
  emits: ['change', 'update:modelValue', 'change'],
  components: {
    InputLabel,
    MapPinIcon,
    Bars2Icon,
    TextInput,
    LoadingIcon,
    ChevronLeftIcon,
    ChevronRightIcon,
    XMarkIcon,
    InputError,
    CursorArrowRaysIcon,
  },
  mounted() {
    const modalEl = document.getElementById('productSelectorModal');
    this.modal = new Modal(modalEl);
    if (this.preload) {

      this.selecteds = [];
      for (let idx in this.preload)
        this.selecteds.push({
          id: this.preload[idx].id,
          name: this.createName(this.preload[idx])
        });
      this.$emit('update:modelValue', this.myMap(this.selecteds, (e) => e.id));
      // this.$emit('change', this.data.filter((e) => e.parent_id != 0 && this.selecteds.map((el) => el.variation_id || el.id).indexOf(e.id) > -1));


    }

    // for (let idx in this.data)
    //   this.data[idx].has_child = this.data.filter((e) => e.parent_id == this.data[idx].id).length > 0;
    // this.preload();

    // initTE({Select})

    // if (!window.Select) {
    // this.$forceUpdate();
    // this.$nextTick(function () {
    //     initTE({Select})
    //     window.Select = Select;
    // });
    // }

  },
  methods: {

    createName(d) {
      if (!d) return '';
      return `${d.name} | ${this.getPack(d.pack_id)} | ${this.__('grade')} ${d.grade} | ${parseFloat(d.weight)} ${this.__('kg')} `
    },
    remove(idx) {
      this.selecteds.splice(idx, 1);
      this.$emit('update:modelValue', this.myMap(this.selecteds, (e) => e.id));
      this.$emit('change', this.data.filter((e) => e.parent_id != 0 && this.selecteds.map((el) => el.id).indexOf(e.id) > -1));


    },
    reset() {
      this.getData();
      this.currentLevel = 1;
      this.currentData = {id: 0, parent_id: 0};
    },


    back() {

      if (this.currentData.parent_id > 0) {
        this.currentData = this.data.filter((el) => el.id == this.currentData.parent_id)[0];
        this.currentLevel = this.currentData.level;

      }
      // this.currentLevel = parent.level;
      else this.currentData = {id: 0, parent_id: 0, level: 0};


    },
    selectData(d) {


      if (this.data.filter((e) => d.parent_id == 0 && d.id == e.parent_id).length == 0) {
        if (this.selecteds.filter((e) => d.parent_id != 0 && d.id == e.id).length == 0)
          this.selecteds.push({id: d.id, name: this.createName(d)});
        else
          this.selecteds = this.selecteds.filter((e) => d.id != e.id);
      } else {
        this.currentData = d;
        this.currentLevel++;
      }
      this.$emit('update:modelValue', this.myMap(this.selecteds, (e) => e.id));
      this.$emit('change', this.data.filter((e) => e.parent_id != 0 && this.selecteds.map((el) => el.id).indexOf(e.id) > -1));

    },
    getData() {

      this.loading = true;
      this.data = [];
      window.axios.get(this.link, {params: this.params}, {})

          .then((response) => {

            this.data = response.data;

            delete response.data;
            // this.pagination = response.data;
          })

          .catch((error) => {
            if (error.response) {
              // console.log(error.response.data);
              // console.log(error.response.status);
              // console.log(error.response.headers);
              this.errors = error.response.data;

            } else if (error.request) {
              // console.log(error.request);
              this.errors = error.request;
            } else {
              // console.log('Error', error.message);
              this.errors = error.message;
            }
            // console.log(error.config);
            // this.showToast('danger', error)
          })
          .finally(() => {
            this.loading = false;
          });
    },
  }
  ,
}
</script>

<style scoped>


</style>
