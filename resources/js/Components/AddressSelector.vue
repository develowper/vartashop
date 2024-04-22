<template>

  <div class="   ">
    <InputLabel :value="label"/>

    <!--    <template v-slot:button>-->

    <!--    </template>-->
    <div :class="{'bg-danger-100':error}"
         class="rounded   flex flex-col items-start border border-neutral-300 hover:cursor-pointer p-2 hover:bg-gray-100 text-gray-500"
         @click="clicked"
    >

      <div v-if="selectedAddress" class="  w-full">
        <div class="  end-0 top-0  flex">
          <PrimaryButton v-if="clearable" type="button"
                         @click.stop=" selectedAddress=null;mapAddress={};addAddressToCart(null) "
                         class="bg-red-500 hover:bg-red-400 text-sm  ms-auto">
            <TrashIcon class="w-4 h-4 "/>
          </PrimaryButton>
        </div>
        <div class="flex items-center py-3 text-sm">
          <MapIcon class="w-4 h-4  text-primary-600"/>
          <div class="mx-1 text-neutral-700"> {{ selectedAddress.address }}</div>
        </div>
        <div class="flex items-center py-1 ">
          <MapPinIcon class="w-4 h-4  text-primary-600"/>
          <div class="mx-1 text-neutral-700"> {{ getCityName(selectedAddress.province_id) }}</div>
          <div v-if="getCityName(selectedAddress.county_id)" class="mx-1 text-neutral-700"> -
            {{ getCityName(selectedAddress.county_id) }}
          </div>
          <div v-if="getCityName(selectedAddress.district_id)" class="mx-1 text-neutral-700"> -
            {{ getCityName(selectedAddress.district_id) }}
          </div>
        </div>
        <div v-if="type=='cart' || type=='repo'" class="flex items-center py-1 ">
          <UserIcon class="w-4 h-4  text-primary-600"/>
          <div class="mx-1 text-neutral-700"> {{ selectedAddress.receiver_fullname }}</div>
        </div>
        <div v-if="type=='cart' || type=='repo'" class="flex items-center py-1">
          <PhoneIcon class="w-4 h-4 text-primary-600"/>
          <div class="mx-1 text-neutral-700"> {{ selectedAddress.receiver_phone }}</div>
        </div>
        <div class="flex items-center py-1">
          <HomeIcon class="w-4 h-4 text-primary-600"/>
          <div class="mx-1 text-neutral-700"> {{ selectedAddress.postal_code }}</div>
        </div>
        <div v-if="editable" class="text-primary-500 text-end">{{ __('edit_address') }}</div>
      </div>
      <div v-else>
        <MapPinIcon class="h-4 w-4 mx-1"/>
        {{ __('select_address') }}
      </div>
      <div v-if="error" class="text-red-500 font-bold">
        {{ error }}
      </div>
    </div>


    <!-- Modal -->
    <div
        data-te-modal-init
        class="fixed    left-0 top-0 backdrop-blur z-[1055] hidden h-full w-full overflow-y-auto overflow-x-hidden outline-none"
        id="locationModal"
        tabindex="-1"
        aria-labelledby="addressModalLabel"
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
              {{ __('select_address') }}
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
          <div class="relative   p-2" data-te-modal-body-ref>
            <div
                class="flex items-center justify-start px-4 py-2 text-primary-500 border-b  ">

              <h5 class="text-2xl font-semibold"></h5>

            </div>

            <div class="px-2  md:px-1">
              <div
                  class="    mx-auto md:max-w-3xl   mt-2 px-2 md:px-2 py-2   overflow-hidden  rounded-lg  ">

                <div v-if="show=='addresses'" class="flex flex-col mx-1   col-span-2 w-full     px-2">
                  <ul>
                    <li
                        @click="show='create_address'; "
                        class="py-8 px-3 border-b flex text-gray-800 items-center justify-start hover:bg-gray-100 rounded  hover:cursor-pointer">
                      <MagnifyingGlassPlusIcon class="w-6 h-6"/>
                      <span class="mx-2 font-bold">{{ __('add_new_address') }}</span>
                    </li>

                    <li class=" " v-for="(address,idx) in addresses" @click="addAddressToCart(idx)">
                      <div class="flex flex-col hover:bg-gray-100 cursor-pointer border rounded p-2">
                        <div class="w-full ">
                          <PrimaryButton
                              @click.stop="showDialog('danger',__('remove_item?'), __('ok') , edit,{cmnd:'remove-address',idx:idx} )"
                              class="bg-red-500 hover:bg-red-400 text-sm">
                            <TrashIcon class="w-4 h-4 mx-4"/>
                          </PrimaryButton>
                        </div>
                        <div class="flex items-center py-3 text-sm">
                          <MapIcon class="w-4 h-4  text-primary-600"/>
                          <div class="mx-1 text-neutral-700"> {{ address.address }}</div>
                        </div>
                        <div class="flex items-center py-1 ">
                          <MapPinIcon class="w-4 h-4  text-primary-600"/>
                          <div class="mx-1 text-neutral-700"> {{ getCityName(address.province_id) }}</div>
                          <div v-if="getCityName(address.county_id)" class="mx-1 text-neutral-700"> -
                            {{ getCityName(address.county_id) }}
                          </div>
                          <div v-if="getCityName(address.district_id)" class="mx-1 text-neutral-700"> -
                            {{ getCityName(address.district_id) }}
                          </div>
                        </div>
                        <div class="flex items-center py-1 ">
                          <UserIcon class="w-4 h-4  text-primary-600"/>
                          <div class="mx-1 text-neutral-700"> {{ address.receiver_fullname }}</div>
                        </div>
                        <div class="flex items-center py-1">
                          <PhoneIcon class="w-4 h-4 text-primary-600"/>
                          <div class="mx-1 text-neutral-700"> {{ address.receiver_phone }}</div>
                        </div>
                        <div class="flex items-center py-1">
                          <HomeIcon class="w-4 h-4 text-primary-600"/>
                          <div class="mx-1 text-neutral-700"> {{ address.postal_code }}</div>
                        </div>

                      </div>
                    </li>
                  </ul>
                </div>

                <div v-if="show=='create_address'">
                  <Map ref="mapSelector" mode="edit" @change=" mapAddress=$event "
                       :preload="selectedAddress && selectedAddress.lat?{lat:selectedAddress.lon,lon:selectedAddress.lat}:null"/>
                  <PrimaryButton @click="show='create_address2'" classes="w-full" class="my-2" v-if="mapAddress">
                    {{ __('accept_location') }}
                  </PrimaryButton>
                </div>

                <div v-if="show=='create_address2'" class="flex-col ">

                  <div @click="show='create_address'"
                       class="text-primary-500 hover:text-white hover:bg-primary-500 cursor-pointer text-center p-2 rounded border border-primary-500">
                    {{ __('edit_map_location') }}
                  </div>
                  <div class="my-2">
                    <TextInput
                        id="address"
                        :multiline="true"
                        :placeholder="`${__('address')} *`"
                        classes="  "
                        v-model="mapAddress.address"
                        autocomplete="address"
                        :error=" errors.address?errors.address[0]:null">
                      <template v-slot:prepend>
                        <div class="p-3">
                          <MapIcon class="h-5 w-5"/>
                        </div>
                      </template>
                    </TextInput>

                  </div>

                  <Selector ref="provinceSelector" :data="$page.props.cities.filter(e=>e.parent_id==0)"
                            :label="`${__('province')} *`"
                            @change="mapAddress.county_id=null;mapAddress.district_id=null"
                            :error=" errors.province_id?errors.province_id[0]:null"
                            id="province_id" v-model=" mapAddress.province_id">
                    <template v-slot:append>
                      <div class="  p-3">
                        <MapPinIcon class="h-5 w-5"/>
                      </div>
                    </template>
                  </Selector>

                  <Selector ref="countySelector"
                            :data="$page.props.cities.filter(e=>e.parent_id==mapAddress.province_id)"
                            :label="`${__('county')} *`"
                            @change="mapAddress.district_id=null"
                            :error=" errors.county_id?errors.county_id[0]:null"
                            id="county_id" v-model=" mapAddress.county_id">
                    <template v-slot:append>
                      <div class="  p-3">
                        <MapPinIcon class="h-5 w-5"/>
                      </div>
                    </template>
                  </Selector>
                  <Selector v-if="$page.props.cities.filter(e=>e.parent_id==mapAddress.county_id).length>0"
                            ref="districtSelector"
                            :error=" errors.district_id?errors.district_id[0]:null"
                            :data="$page.props.cities.filter(e=>e.parent_id==mapAddress.county_id)"
                            :label="`${__('district/city')} *`"
                            id="category_id" v-model=" mapAddress.district_id">
                    <template v-slot:append>
                      <div class="  p-3">
                        <MapPinIcon class="h-5 w-5"/>
                      </div>
                    </template>
                  </Selector>

                  <div class="my-2">
                    <TextInput
                        id="postal_code"
                        type="text"
                        :placeholder="`${__('postal_code')} *`"
                        classes="  "
                        v-model="mapAddress.postal_code"
                        autocomplete="postal_code"
                        :error=" errors.postal_code?errors.postal_code[0]:null">
                      <template v-slot:prepend>
                        <div class="p-3">
                          <HomeIcon class="h-5 w-5"/>
                        </div>
                      </template>
                    </TextInput>

                  </div>
                  <div v-if="type=='cart' || type=='repo'" class=" ">
                    <div class="p-2 border-b my-2 ">{{ type == 'repo' ? __('owner') : __('receiver') }}</div>
                    <div class="grid gap-2 grid-cols-1 lg:grid-cols-2 my-4">
                      <TextInput
                          id="fullname"
                          type="text"
                          :placeholder="`${__('fullname')} *`"
                          classes="  "
                          v-model="mapAddress.receiver_fullname"
                          autocomplete="fullname"
                          :error=" errors.receiver_fullname?errors.receiver_fullname[0]:null">
                        <template v-slot:prepend>
                          <div class="p-3">
                            <UserIcon class="h-5 w-5"/>
                          </div>
                        </template>
                      </TextInput>

                      <TextInput
                          id="phone"
                          type="text"
                          :placeholder="`${__('phone')} *`"
                          classes="  "
                          v-model="mapAddress.receiver_phone"
                          autocomplete="phone"
                          :error=" errors.receiver_phone?errors.receiver_phone[0]:null">
                        <template v-slot:prepend>
                          <div class="p-3">
                            <PhoneIcon class="h-5 w-5"/>
                          </div>
                        </template>
                      </TextInput>
                    </div>
                  </div>
                  <PrimaryButton @click="mapAddress.cmnd='add-address'; !loading? edit( mapAddress ):null"
                                 classes="w-full"
                                 class="my-2"
                                 type="button"
                                 v-if="mapAddress">
                    <LoadingIcon class="w-6 mx-auto  mx-3  " type="line-dot" v-if="loading"/>
                    <div v-else> {{ __('reg_address') }}</div>

                  </PrimaryButton>
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
import Map from '@/Components/Map.vue';
import Selector from '@/Components/Selector.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import LoadingIcon from '@/Components/LoadingIcon.vue';
import {

  MapPinIcon,
  Bars2Icon,
  ChevronLeftIcon,
  ChevronRightIcon,
  MagnifyingGlassIcon,
  MagnifyingGlassPlusIcon,
  UserIcon,
  PhoneIcon,
  Squares2X2Icon,
  HomeIcon,
  MapIcon,
  TrashIcon,
} from "@heroicons/vue/24/outline";
import PrimaryButton from "@/Components/PrimaryButton.vue";

export default {
  data() {
    return {

      errors: {},
      show: 'addresses',
      addresses: [],
      currentLevel: 1,
      selectedAddress: null,
      selectedName: null,
      loading: false,
      cities: this.$page.props.cities,
      filteredCities: [],
      mapAddress: {},

    }
  },
  props: ['id', 'label', 'type', 'data', 'preloadData', 'modelValue', 'editable', 'clearable', 'error'],
  emits: ['change', 'updateCart'],
  components: {
    PrimaryButton,
    InputLabel,
    MapPinIcon,
    Bars2Icon,
    TextInput,
    LoadingIcon,
    ChevronLeftIcon,
    ChevronRightIcon,
    MagnifyingGlassIcon,
    MagnifyingGlassPlusIcon,
    UserIcon,
    PhoneIcon,
    Map,
    Selector,
    Squares2X2Icon,
    HomeIcon,
    MapIcon,
    TrashIcon,
  },
  mounted() {
    const modalEl = document.getElementById('locationModal');
    this.modal = new Modal(modalEl);

    if (this.$page.props.auth.user) {
      this.addresses = this.$page.props.auth.user.addresses;
    }
    if (this.preloadData) {
      this.preload(this.preloadData);
    }
    this.emitter.on('updateCart', (cart) => {

      if (cart && this.type == 'cart')
        this.selectedAddress = cart.address;
    });

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
    preload(address) {
      this.show = 'create_address';
      this.$nextTick(() => {
        // if (address.lat && address.lon)
        //   this.$refs.mapSelector.setLocation({y: address.lon, x: address.lat});
        // this.show = 'create_address2';
        this.selectedAddress = address;
        this.mapAddress = address;
      });


    },
    clicked() {
      if (this.editable) {
        if (this.type == 'cart')
          this.show = 'addresses';
        else if (this.selectedAddress)
          this.show = 'create_address2';
        else
          this.show = 'addresses';

        this.modal.show();
      }
    }
    ,
    addAddressToCart(idx) {
      this.modal.hide();
      this.$emit('change', idx);
    },

    edit(params = {}) {

      this.errors = {};
      this.loading = true;
      if (params.postal_code)
        params.postal_code = this.f2e(params.postal_code);
      if (!params.address)
        params.address = null;

      if (this.type != 'cart') {
        this.loading = false;
        this.modal.hide();
        this.selectedAddress = params;

        this.$emit('change', params);

        return;
      }

      if (params.receiver_phone)
        params.receiver_phone = this.f2e(params.receiver_phone);
      window.axios.patch(route(`${this.isAdmin() ? 'admin' : 'user'}.panel.profile.update`), params,
          {})
          .then((response) => {
            if (response.data && response.data.message) {
              this.showToast('success', response.data.message);
            }

            if (response.data.addresses) {
              if (this.$page.props.auth.user)
                this.$page.props.auth.user.addresses = response.data.addresses;
              this.addresses = response.data.addresses;
              this.show = 'addresses';

            }
            this.$emit('change', null);

          })

          .catch((error) => {
            let e = this.getErrors(error);
            if (error.response && error.response.data) {
              this.errors = error.response.data.errors || {};
            }
            this.showToast('danger', e);

          })
          .finally(() => {
            // always executed
            this.loading = false;

          });
    }
    ,
  },
}
</script>

<style scoped>


</style>
