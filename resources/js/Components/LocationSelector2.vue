<template>

  <div class="flex  ">

    <!--    <template v-slot:button>-->

    <!--    </template>-->
    <div
        class="rounded   flex items-center border border-neutral-300 hover:cursor-pointer p-2 hover:bg-gray-50 text-gray-500"
        @click="preload();modal.show()"
    >
      <MapPinIcon class="h-4 w-4 mx-1"/>
      {{ selectedName }}
    </div>
    <!-- Modal -->
    <div
        data-te-modal-init
        class="fixed    left-0 top-0 backdrop-blur z-[1055] hidden h-full w-full overflow-y-auto overflow-x-hidden outline-none"
        id="locationModal"
        tabindex="-1"
        aria-labelledby="messageModalLabel"
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
                id="locationModalLabel">
              <MapPinIcon class="h-7 w-7 mx-3"/>
              {{ __('city_select') }}
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
                class="flex items-center justify-start px-4 py-2 text-primary-500 border-b md:py-4">

              <h5 class="text-2xl font-semibold"> {{
                  selecteds[currentLevel > 0 ? currentLevel - 1 : 0] ? selecteds[currentLevel > 0 ? currentLevel - 1 : 0].name : ''
                }}</h5>

            </div>

            <div class="px-2  md:px-4">
              <div
                  class="    mx-auto md:max-w-3xl   mt-6 px-2 md:px-4 py-4   overflow-hidden  rounded-lg  ">

                <div class="flex flex-col mx-2   col-span-2 w-full     px-2">
                  <ul>
                    <li v-if="selecteds.length>0"
                        @click="back()"
                        class="p-3 px-3 border-b flex text-gray-600 items-center justify-between hover:bg-gray-100 rounded  hover:cursor-pointer">
                      <span>{{ __('return') }}</span>
                      <ChevronRightIcon class="w-4 h-4"/>
                    </li>
                    <li v-for="(d,idx) in filteredCities"
                        @click="selectCity(d)"
                        class="p-2 px-3 flex items-center justify-between hover:bg-gray-100 rounded  hover:cursor-pointer">
                      <span>{{ d.name }}</span>
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
import {

  MapPinIcon,
  Bars2Icon,
  ChevronLeftIcon,
  ChevronRightIcon,
} from "@heroicons/vue/24/outline";

export default {
  data() {
    return {
      currentLevel: 1,
      selecteds: [],
      selectedName: null,
      loading: false,
      cities: this.$page.props.cities,
      filteredCities: [],

    }
  },
  props: ['id', 'label', 'data', 'modelValue'],
  emits: ['change'],
  components: {
    InputLabel,
    MapPinIcon,
    Bars2Icon,
    TextInput,
    LoadingIcon,
    ChevronLeftIcon,
    ChevronRightIcon,
  },
  mounted() {
    const modalEl = document.getElementById('locationModal');
    this.modal = new Modal(modalEl);

    this.preload();

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
    updateLocation(city_id) {
      window.axios.post(route('user.update_location'), {city_id: city_id});
    },
    preload() {
      this.selecteds = this.$page.props.user_location || [];
      this.currentLevel = this.selecteds.length + 1;
      this.selectedName = (this.selecteds.length > 1 ? this.selecteds[1]['name'] : '') + (this.selecteds.length > 2 ? (' _ ' + this.selecteds[2]['name']) : '');
      this.selectedName == '' ? this.__('select_city') : this.selectedName;
      this.getCities(this.currentLevel, this.selecteds.length - 2 >= 0 ? this.selecteds[this.selecteds.length - 2].id : 0);
      if (this.selecteds.length == 0)
        this.modal.show();
    },
    back() {
      const last = this.selecteds[this.selecteds.length - 1];
      this.selecteds.pop();
      this.currentLevel = this.selecteds.length;

      this.getCities(this.currentLevel + 1, this.selecteds.length > 0 ? this.selecteds[this.selecteds.length - 1].id : 0);

    },
    selectCity(d) {
      this.selecteds.push({id: d.id, name: d.name});

      this.currentLevel++;
      this.getCities(this.currentLevel + 1, d.id);
      if (this.filteredCities.length == 0) {
        this.selectedName = (this.selecteds.length > 1 ? this.selecteds[1]['name'] : '') + (this.selecteds.length > 2 ? (' _ ' + this.selecteds[2]['name']) : '');
        this.$page.props.user_location = this.selecteds;
        this.updateLocation(this.selecteds[this.selecteds.length - 1].id);
        this.$emit('change', {
          province_id: this.selecteds[0].id,
          city_id: this.selecteds[this.selecteds.length - 1].id
        });
        this.modal.hide();
      }
    },
    getCities(level, parent) {
      // this.log(level + "," + parent)
      this.filteredCities = [];
      for (const idx in this.cities) {
        if (this.cities[idx].level == level && this.cities[idx].parent_id == parent)
          this.filteredCities.push(this.cities[idx]);
      }
      return this.filteredCities;
    }
  },
}
</script>

<style scoped>


</style>
