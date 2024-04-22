<template>

  <Panel>
    <template v-slot:header>
      <title>{{__('new_shipping')}}</title>
    </template>


    <template v-slot:content>
      <!-- Content header -->
      <div
          class="flex items-center justify-start px-4 py-2 text-primary-500 border-b md:py-4">
        <FolderPlusIcon class="h-7 w-7 mx-3"/>

        <h1 class="text-2xl font-semibold">{{ __('new_shipping') }}</h1>

      </div>


      <div class="px-2  md:px-4">

        <div
            class="    mx-auto md:max-w-2xl lg:max-w-max   mt-6 px-2 md:px-4 py-4 bg-white shadow-md overflow-hidden  rounded-lg  ">


          <div
              class="flex flex-col mx-2   col-span-2 w-full     px-2"
          >

            <form @submit.prevent="submit">

              <div class="my-2" v-if="$page.props.agency && $page.props.agency.level<3">
                <UserSelector :colsData="['name','phone','level']" :labelsData="['name','phone','type']"
                              :callback="{'level':getAgency}" :error="form.errors.agency_id"
                              :link="route('admin.panel.agency.search')" :label="__('agency')"
                              :id="'agency'" v-model:selected="form.agency_id" :preload="null">
                  <template v-slot:selector="props">
                    <div :class="props.selectedText?'py-2':'py-2'"
                         class=" px-4 border border-gray-300 rounded hover:bg-gray-100 cursor-pointer flex items-center ">
                      <div class="grow">
                        {{ props.selectedText ?? __('select') }}
                      </div>
                      <div v-if="props.selectedText"
                           class="bg-danger rounded p-2   cursor-pointer text-white hover:bg-danger-400"
                           @click.stop="props.clear()">
                        <XMarkIcon class="w-5 h-5"/>
                      </div>
                    </div>
                  </template>
                </UserSelector>

              </div>
              <div class="my-2">
                <UserSelector v-if="form.agency_id" :colsData="['fullname','phone','agency' ]"
                              :labelsData="['name','phone','agency' ]"
                              :callback="{'level':getAgency,'agency':(e)=>`${e.name||''} (${e.id||''})`}"
                              :error="form.errors.driver_id"
                              :link="`${route('admin.panel.shipping.driver.search')}?agency_id=${form.agency_id}`"
                              :label="__('driver')"
                              :id="'driver'" v-model:selected="form.driver_id" :preload="null">
                  <template v-slot:selector="props">
                    <div :class="props.selectedText?'py-2':'py-2'"
                         class=" px-4 border border-gray-300 rounded hover:bg-gray-100 cursor-pointer flex items-center ">
                      <div class="grow">
                        {{ props.selectedText ?? __('select') }}
                      </div>
                      <div v-if="props.selectedText"
                           class="bg-danger rounded p-2   cursor-pointer text-white hover:bg-danger-400"
                           @click.stop="props.clear()">
                        <XMarkIcon class="w-5 h-5"/>

                      </div>
                    </div>
                  </template>
                </UserSelector>
              </div>
              <div class="my-2">
                <UserSelector v-if="form.driver_id" :colsData="['name', 'agency' ]"
                              :labelsData="['name', 'agency' ]"
                              :callback="{'level':getAgency,'agency':(e)=>`${e.name||''} (${e.id ||''})`}"
                              :error="form.errors.car_id"
                              :link="`${route('admin.panel.shipping.car.search')}?driver_id=${form.driver_id}`"
                              :label="__('car')"
                              :id="'car'" v-model:selected="form.car_id" :preload="null">
                  <template v-slot:selector="props">
                    <div :class="props.selectedText?'py-2':'py-2'"
                         class=" px-4 border border-gray-300 rounded hover:bg-gray-100 cursor-pointer flex items-center ">
                      <div class="grow">
                        {{ props.selectedText ?? __('select') }}
                      </div>
                      <div v-if="props.selectedText"
                           class="bg-danger rounded p-2   cursor-pointer text-white hover:bg-danger-400"
                           @click.stop="props.clear()">
                        <XMarkIcon class="w-5 h-5"/>

                      </div>
                    </div>
                  </template>
                </UserSelector>
              </div>
              <div class="my-2">
                <OrderSelector
                    :error="form.errors"
                    :link="route('admin.panel.order.merged.search')+`?agency_id=${form.agency_id}` "
                    :label="__('orders')"
                    :id="'orders'" v-model:selecteds="form.ordersData" :preload="null">

                </OrderSelector>
              </div>


              <div v-if="form.progress" class="shadow w-full bg-grey-light m-2   bg-gray-200 rounded-full">
                <div
                    class=" bg-primary rounded  text-xs leading-none py-[.1rem] text-center text-white duration-300 "
                    :class="{' animate-pulse': form.progress.percentage <100}"
                    :style="`width: ${form.progress.percentage }%`">
                  <span class="animate-bounce">{{ form.progress.percentage }}</span>
                </div>
              </div>

              <div class="    mt-4">

                <PrimaryButton @click="submit" type="button" class="w-full flex items-center justify-center"
                               :class="{ 'opacity-25': form.processing }"
                               :disabled="form.processing">
                  <LoadingIcon class="w-4 h-4 mx-3 " v-if="  form.processing"/>
                  <span class=" text-lg  ">  {{ __('register_info') }} </span>
                </PrimaryButton>

              </div>

            </form>
          </div>


        </div>
      </div>
    </template>


  </Panel>
</template>

<script>
import Scaffold from "@/Layouts/Scaffold.vue";
import Panel from "@/Layouts/Panel.vue";
import {Head, Link, useForm} from "@inertiajs/vue3";
import {
  ChevronDownIcon,
  HomeIcon,
  UserIcon,
  EyeIcon,
  FolderPlusIcon,
  Bars2Icon,
  ChatBubbleBottomCenterTextIcon,
  Squares2X2Icon,
  SignalIcon,
  PencilIcon,
  XMarkIcon,
  CurrencyDollarIcon,
  ClockIcon,
  TrashIcon,
  PlusIcon,

} from "@heroicons/vue/24/outline";
import {QuestionMarkCircleIcon,} from "@heroicons/vue/24/solid";
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import RadioGroup from '@/Components/RadioGroup.vue'
import LoadingIcon from "@/Components/LoadingIcon.vue";
import Popover from "@/Components/Popover.vue";
import Tooltip from "@/Components/Tooltip.vue";
import TagInput from "@/Components/TagInput.vue";
import ImageUploader from "@/Components/ImageUploader.vue";
import Selector from "@/Components/Selector.vue";
import ProvinceCounty from "@/Components/ProvinceCounty.vue";
import PhoneFields from "@/Components/PhoneFields.vue";
import SocialFields from "@/Components/SocialFields.vue";
import TextEditor from "@/Components/TextEditor.vue";
import UserSelector from "@/Components/UserSelector.vue";
import AddressSelector from "@/Components/AddressSelector.vue";
import CitySelector from "@/Components/CitySelector.vue";
import ProductSelector from "@/Components/ProductSelector.vue";
import OrderSelector from "@/Components/OrderSelector.vue";


export default {

  data() {
    return {
      orderFrom: [this.__('internal'), this.__('external')],
      form: useForm({

        agency_id: this.$page.props.agency.level == 3 ? this.$page.props.agency.id : null,
        driver_id: null,
        car_id: null,
        ordersData: [],
        orders: [],
      }),

    }
  },
  components: {
    AddressSelector,
    UserSelector,
    ImageUploader,
    LoadingIcon,
    Head,
    Link,
    HomeIcon,
    ChevronDownIcon,
    Panel,
    InputLabel,
    TextInput,
    InputError,
    PrimaryButton,
    RadioGroup,
    UserIcon,
    EyeIcon,
    Checkbox,
    Popover,
    Tooltip,
    FolderPlusIcon,
    Bars2Icon,
    ChatBubbleBottomCenterTextIcon,
    TagInput,
    QuestionMarkCircleIcon,
    Selector,
    Squares2X2Icon,
    ProvinceCounty,
    PhoneFields,
    SocialFields,
    SignalIcon,
    TextEditor,
    PencilIcon,
    XMarkIcon,
    CitySelector,
    CurrencyDollarIcon,
    ClockIcon,
    ProductSelector,
    TrashIcon,
    PlusIcon,
    OrderSelector,

  },
  mounted() {
    // this.log(this.$page.props)


  },
  watch: {
    form(_new, _old) {


    }
  },
  methods: {
    updateAddress(address) {
      address = address || {};
      this.$nextTick(() => {
        this.form.from_address = address.address;
        this.form.from_province_id = address.province_id;
        this.form.from_county_id = address.county_id;
        this.form.from_district_id = address.district_id;
        this.form.from_lat = address.lat;
        this.form.from_lon = address.lon;
        this.form.from_location = address.lat && address.lon ? `${address.lat},${address.lon}` : null;
        this.form.from_postal_code = this.f2e(address.postal_code);
        this.form.from_fullname = address.receiver_fullname;
        this.form.from_phone = address.receiver_phone;

        this.$refs.addressSelector.preload(address);
      })

    },
    submit() {
      // this.img = this.$refs.imageCropper.getCroppedData();

      this.form.clearErrors();
      // this.isLoading(true, this.form.progress ? this.form.progress.percentage : null);
      this.form.orders = this.myMap(this.form.ordersData, e => {
        return {id: e.id, type: e.type, from_agency_id: e.from_agency_id, status: e.status,}
      })
      this.form.post(route('admin.panel.shipping.create'), {
        preserveScroll: false,

        onSuccess: (data) => {

          if (this.$page.props.flash.status)
            this.showAlert(this.$page.props.flash.status, this.$page.props.flash.message);

          // else {
          //   this.showAlert(this.$page.props.flash.status, this.$page.props.flash.message);
          //   this.form.reset();
          // }
        },
        onError: () => {
          this.showToast('danger', Object.values(this.form.errors).join("<br/>"));
        },
        onFinish: (data) => {
          // this.isLoading(false,);
        },
      });
    }
  },

}
</script>
