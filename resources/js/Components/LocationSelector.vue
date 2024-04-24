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

            <Selector v-if="loaded" ref="provinceSelector"
                      :data="cities.filter(e=>e.level==1 || e.id==0)"
                      :label="`${__('province')} *`"
                      @change="$e=>select('province_id',$e )"
                      id="province_id" v-model=" selected.province_id">
              <template v-slot:append>
                <div class="  p-3">
                  <MapPinIcon class="h-5 w-5"/>
                </div>
              </template>
            </Selector>

            <Selector v-if="loaded && selected.province_id!=0" ref="countySelector"
                      :data="cities.filter(e=>e.parent_id==selected.province_id || e.id==0) "
                      :label="`${__('county')} *`"
                      @change="$e=>select('county_id',$e ) "
                      id="county_id" v-model=" selected.county_id">
              <template v-slot:append>
                <div class="  p-3">
                  <MapPinIcon class="h-5 w-5"/>
                </div>
              </template>
            </Selector>
            <Selector
                v-if="loaded &&  selected.county_id   && cities.filter(e=>e.parent_id==selected.county_id && e.parent_id!=0&& e.id!=0).length>0"
                ref="districtSelector"
                :data="cities.filter(e=>(e.level==3 && e.parent_id==selected.county_id) || e.id==0)"
                @change="$e=>select('district_id',$e )"
                :label="`${__('district/city')} *`"
                id="district_id" v-model=" selected.district_id">
              <template v-slot:append>
                <div class="  p-3">
                  <MapPinIcon class="h-5 w-5"/>
                </div>
              </template>
            </Selector>

            <PrimaryButton @click="modal.hide();$emit('change',selected)" classes="w-full" class="my-2">
              {{ __('accept') }}
            </PrimaryButton>
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
import Selector from '@/Components/Selector.vue';
import {

  MapPinIcon,
  Bars2Icon,
  ChevronLeftIcon,
  ChevronRightIcon,
} from "@heroicons/vue/24/outline";
import PrimaryButton from "@/Components/PrimaryButton.vue";

export default {
  data() {
    return {
      loaded: false,
      currentLevel: 1,
      selected: {},
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
    PrimaryButton,
    InputLabel,
    MapPinIcon,
    Bars2Icon,
    TextInput,
    LoadingIcon,
    ChevronLeftIcon,
    ChevronRightIcon,
    Selector,
  },
  mounted() {
    const modalEl = document.getElementById('locationModal');
    this.modal = new Modal(modalEl);
    this.cities = [{id: 0, name: this.__('all')}];
    for (let idx in this.$page.props.cities)
      this.cities.push(this.$page.props.cities[idx]);

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
      window.axios.post(route('user.update_location'), {city_id: city_id}).then((response) => {
        if (response.data && response.data.location) {
          this.$page.props.user_location = response.data.location;
        }


      });

    },
    preload() {
      let preload = this.$page.props.user_location || {};

      this.selected.province_id = preload.length > 0 ? preload[0]['id'] : 0;
      this.selected.county_id = preload.length > 1 ? preload[1]['id'] : 0;
      this.selected.district_id = preload.length > 2 ? preload[2]['id'] : 0;

      if (this.selected.district_id && this.selected.county_id && this.selected.province_id)
        this.selectedName = `${this.cities.filter(e => e.id == this.selected.county_id)[0]['name']}-${this.cities.filter(e => e.id == this.selected.district_id)[0]['name']}`;
      else if (this.selected.county_id && this.selected.province_id)
        this.selectedName = `${this.cities.filter(e => e.id == this.selected.province_id)[0]['name']}-${this.cities.filter(e => e.id == this.selected.county_id)[0]['name']}`;
      else if (this.selected.province_id)
        this.selectedName = `${this.cities.filter(e => e.id == this.selected.province_id)[0]['name']}`;
      else
        this.selectedName = this.__('select_city');
      // if (!this.selected.province_id)
      //   this.modal.show();
      // else
        this.$emit('change', this.selected);
      this.$emit('update:modelValue', this.selected);
      this.loaded = true;
    },

    select(_type, id) {
      if (id.target && id.target.value)
        id = id.target.value;
      id = id || 0;
      if (_type == 'province_id') {
        this.selected = {
          province_id: id,
          county_id: 0,
          district_id: 0,
        };
        this.selectedName = `${this.cities.filter(e => e.id == this.selected.province_id)[0]['name']}`;


        this.updateLocation(id == 0 ? null : id);

      } else if (_type == 'county_id') {
        this.selected = {
          province_id: this.selected.province_id,
          county_id: id,
          district_id: 0,
        };
        this.selectedName = `${this.cities.filter(e => e.id == this.selected.province_id)[0]['name']}-${this.cities.filter(e => e.id == this.selected.county_id)[0]['name']}`;

        this.updateLocation(id == 0 ? (this.selected.province_id == 0 ? null : this.selected.province_id) : id);
      } else if (_type == 'district_id') {
        this.selected = {
          province_id: this.selected.province_id,
          county_id: this.selected.county_id,
          district_id: id,
        };
        this.selectedName = `${this.cities.filter(e => e.id == this.selected.county_id)[0]['name']}-${this.cities.filter(e => e.id == this.selected.district_id)[0]['name']}`;

        this.updateLocation(id == 0 ? (this.selected.county_id == 0 ? null : this.selected.county_id) : id);

      }

      this.$emit('change', this.selected);
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
