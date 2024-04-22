<template>

  <Panel>
    <template v-slot:header>
      <title>{{__('edit_agency')}}</title>
    </template>


    <template v-slot:content>
      <!-- Content header -->
      <div
          class="flex items-center justify-start px-4 py-2 text-primary-500 border-b md:py-4">
        <FolderPlusIcon class="h-7 w-7 mx-3"/>

        <h1 class="text-2xl font-semibold">{{ __('edit_agency') }}</h1>

      </div>


      <div class="px-2  md:px-4">
        <div v-if="data && data.id" class="flex flex-col mt-4">
          <div class="flex text-sm">
            <div class="text-gray-500">{{ __('created_at') }}:</div>
            <div class="text-primary-700 mx-2">{{ toShamsi(data.created_at) }}</div>
          </div>
          <div class="flex text-sm">
            <div class="text-gray-500">{{ __('status') }}:</div>
            <div class="mx-2" :class="`text-${getStatus('user_statuses',data.status).color}-500`">{{
                __(data.status)
              }}
            </div>
          </div>
          <div>
            <div class="flex text-sm items-center">
              <div class="text-gray-500">{{ __('parent_debit') }}:</div>
              <div class="mx-2" :class="`text-gray-700`">{{
                  data.financial ? data.financial.parent_debit : 0
                }}

              </div>
              <TomanIcon class=" "/>
            </div>
            <div class="flex text-sm items-center">
              <div class="text-gray-500">{{ __('payment_balance') }}:</div>
              <div class="mx-2" :class="`text-gray-700`">{{
                  data.financial ? data.financial.wallet : 0
                }}

              </div>
              <TomanIcon class=" "/>
            </div>
          </div>


        </div>

        <div
            class="    mx-auto md:max-w-2xl   mt-6 px-2 md:px-4 py-4 bg-white shadow-md overflow-hidden  rounded-lg  ">


          <div
              class="flex flex-col mx-2   col-span-2 w-full     px-2"
          >
            <div class="flex-col   m-2 items-center rounded-lg max-w-xs  w-full mx-auto    ">
              <div v-if="false" class="my-2">
                <ImageUploader ref="imageCropper" :label="__('image_cover_jpg')" cropRatio="1.25" id="img"
                               height="10" class="grow "/>
                <InputError class="mt-1 " :message="form.errors.img"/>
              </div>

            </div>
            <form @submit.prevent="submit">
              <div class="my-2">

                <Selector
                    v-show="$page.props.agency_types.filter((e)=>$page.props.agency && e.level>$page.props.agency.level).length>0"
                    ref="typeSelector"
                    :data="$page.props.agency_types.filter((e)=>$page.props.agency && e.level>$page.props.agency.level)"
                    :label="__('agency_type')"
                    :error="form.errors.type_id"
                    @change="updateFilteredAgencies()"
                    id="type_id" v-model="form.type_id">
                  <template v-slot:append>
                    <div class="  p-3">
                      <Squares2X2Icon class="h-5 w-5"/>
                    </div>
                  </template>
                </Selector>
              </div>
              <div class="my-2" v-if=" form.type_id==1 ">
                <Selector
                    ref="provincesSelector" :multiple="true"
                    :data="$page.props.cities.filter((e)=>e.level==1)"
                    :label="__('supported_provinces')"
                    :error="form.errors.supported_provinces"
                    id="supported_provinces" v-model="form.supported_provinces">
                  <template v-slot:append>
                    <div class="  p-3">
                      <Squares2X2Icon class="h-5 w-5"/>
                    </div>
                  </template>
                </Selector>
              </div>
              <div class="my-2" v-if="form.type_id">

                <Selector
                    v-show="form.type_id>1"
                    ref="parentSelector"
                    :data="filteredAgencies"
                    :label="__('parent_agency')"
                    :error="form.errors.parent_id"
                    id="parent_id" v-model="form.parent_id">
                  <template v-slot:append>
                    <div class="  p-3">
                      <Squares2X2Icon class="h-5 w-5"/>
                    </div>
                  </template>
                </Selector>
              </div>
              <div class="my-2">
                <TextInput
                    id="name"
                    type="text"
                    :placeholder="__('name')"
                    classes="  "
                    v-model="form.name"
                    autocomplete="name"
                    :error="form.errors.name"
                >
                  <template v-slot:prepend>
                    <div class="p-3">
                      <Bars2Icon class="h-5 w-5"/>
                    </div>
                  </template>

                </TextInput>

              </div>
              <div class="my-2">
                <TextInput
                    id="phone"
                    type="tel"
                    :placeholder="__('phone')"
                    classes="  "
                    v-model="form.phone"
                    autocomplete="phone"
                    :error="form.errors.phone"
                >
                  <template v-slot:prepend>
                    <div class="p-3">
                      <Bars2Icon class="h-5 w-5"/>
                    </div>
                  </template>

                </TextInput>

              </div>
              <div class="my-4" v-if="hasAccess('edit_setting')">
                <TextInput
                    id="order_profit_percent"
                    step=".01"
                    type="number"
                    :placeholder="__('order_profit_percent')"
                    classes="  "
                    v-model="form.order_profit_percent"
                    autocomplete="order_profit_percent"
                    :error="form.errors.order_profit_percent"
                >
                  <template v-slot:prepend>
                    <div class=" px-3">
                      <ChartPieIcon class="h-5 w-5"/>
                    </div>
                  </template>

                </TextInput>
              </div>
              <div class="my-4">
                <TextInput
                    id="card"
                    type="number"
                    :placeholder="__('card')"
                    classes="  "
                    v-model="form.card"
                    autocomplete="card"
                    :error="form.errors.card"
                >
                  <template v-slot:append>
                    <div class="p- px-0">
                      <CreditCardIcon class="h-5 w-5"/>
                    </div>
                  </template>

                </TextInput>
              </div>
              <div class="my-4">
                <TextInput
                    id="sheba"
                    type="number"
                    :placeholder="__('sheba')"
                    classes="  "
                    v-model="form.sheba"
                    autocomplete="sheba"
                    :error="form.errors.sheba"
                >
                  <template v-slot:append>
                    <div class="p-1">
                      <strong>IR</strong>
                      <!--                      <CreditCardIcon class="h-5 w-5"/>-->
                    </div>
                  </template>
                </TextInput>
              </div>
              <div v-if="false" class="my-2">
                <UserSelector :link="route('admin.panel.admin.search')" :label="__('owner')"
                              :id="'admin'" v-model:selected="form.owner_id" :preload="null">
                  <template v-slot:selector="props">
                    <div :class="props.selectedText?'py-2':'py-2'"
                         class=" px-4 border border-gray-300 rounded hover:bg-gray-100 cursor-pointer flex items-center ">
                      <div class="grow">
                        {{ props.selectedText ?? __('select_owner') }}
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
                <AddressSelector ref="addressSelector" :editable="true" :clearable="true" class=" " type=""
                                 :label="__('address')"
                                 @change="updateAddress($event) "
                                 :error="form.errors.address ||form.errors.postal_code || form.errors.province_id || form.errors.county_id "/>


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
  LinkIcon,
  Squares2X2Icon,
  PencilSquareIcon,
  SignalIcon,
  ChatBubbleBottomCenterTextIcon,
  PencilIcon,
  XMarkIcon,
  ChartPieIcon,
  CreditCardIcon,
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
import Article from "@/Components/Article.vue";
import TextEditor from "@/Components/TextEditor.vue";
import UserSelector from "@/Components/UserSelector.vue";
import AddressSelector from "@/Components/AddressSelector.vue";
import TomanIcon from "@/Components/TomanIcon.vue";


export default {

  data() {
    return {
      data: this.$page.props.data || {},
      filteredAgencies: this.$page.props.parent_agencies,
      preloadAddress: null,
      form: useForm({
        id: null,
        parent_id: null,
        type_id: null,
        owner_id: null,
        name: null,
        address: null,
        lat: null,
        lon: null,
        location: null,
        province_id: null,
        county_id: null,
        district_id: null,
        postal_code: null,
        phone: null,
        supported_provinces: null,
        order_profit_percent: null,
        card: null,
        sheba: null,
      }),
      img: null,
    }
  },
  components: {
    TomanIcon,
    TextEditor,
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
    LinkIcon,
    TagInput,
    QuestionMarkCircleIcon,
    Selector,
    Squares2X2Icon,
    ProvinceCounty,
    PhoneFields,
    SocialFields,
    PencilSquareIcon,
    Article,
    SignalIcon,
    ChatBubbleBottomCenterTextIcon,
    PencilIcon,
    UserSelector,
    XMarkIcon,
    AddressSelector,
    ChartPieIcon,
    CreditCardIcon,
  },
  created() {

  },
  mounted() {

    // console.log(this.data.content);


    this.form.id = this.data.id;
    this.form.name = this.data.name;
    this.form.type_id = this.data.type_id;
    this.form.parent_id = this.data.parent_id;
    this.form.phone = this.data.phone;
    this.form.status = this.data.status;
    this.form.order_profit_percent = this.data.order_profit_percent;
    if (this.data.financial) {
      this.form.card = this.data.financial.card;
      this.form.sheba = this.data.financial.sheba;
    }

    this.form.owner = this.data.owner;
    this.form.owner_id = this.data.owner_id;

    this.preloadAddress = {
      address: this.data.address,
      postal_code: this.data.postal_code,
      province_id: this.data.province_id,
      county_id: this.data.county_id,
      district_id: this.data.district_id,
      lat: this.data.location && this.data.location.indexOf(',') > -1 ? this.data.location.split(',')[1] : null,
      lon: this.data.location && this.data.location.indexOf(',') > -1 ? this.data.location.split(',')[0] : null,

    };

    this.$nextTick(() => {
      this.$refs.addressSelector.preload(this.preloadAddress);
      this.$refs.typeSelector.selecteds = this.form.type_id;
      this.updateAddress(this.preloadAddress);
      if (this.form.type_id)
        this.$refs.parentSelector.selecteds = this.form.parent_id;

      if (this.form.type_id == 1) {
        this.$refs.provincesSelector.selecteds = this.data.access;
        this.form.supported_provinces = this.data.access;
      }
    });

  },
  methods: {
    updateFilteredAgencies() {

      if (!this.form.type_id && !this.$page.props.agency) return;
      this.filteredAgencies = [];
      let myLevel = this.$page.props.agency.level;

      for (let idx in this.$page.props.parent_agencies) {
        //find  level-1 parents
        if (this.$page.props.parent_agencies[idx].level == this.form.type_id - 1) {
          let type = this.$page.props.agency_types.filter((e) => e.level == this.form.type_id - 1)[0].name
          this.filteredAgencies.push({
            id: this.$page.props.parent_agencies[idx].id,
            name: `${this.$page.props.parent_agencies[idx].name} | ${this.__(type)}`
          });
        }
      }
    },
    updateAddress(address) {
      address = address || {};
      this.form.address = address.address;
      this.form.province_id = address.province_id;
      this.form.county_id = address.county_id;
      this.form.district_id = address.district_id;
      this.form.lat = address.lat;
      this.form.lon = address.lon;
      this.form.location = `${address.lat},${address.lon}`;
      this.form.postal_code = this.f2e(address.postal_code);
      this.updateFilteredAgencies();
    },
    submit() {


      // this.form.category_id = this.$refs.categorySelector.selected;
      this.form.clearErrors();

      // this.isLoading(true, this.form.progress ? this.form.progress.percentage : null);
      // this.images = [];
      // for (let i = 0; i < this.$page.props.max_images_limit; i++) {
      //   let tmp = this.$refs.imageCropper[i].getCroppedData();
      //   if (tmp) this.images.push(tmp);
      // }
      this.form.patch(route('admin.panel.agency.update'), {
        preserveScroll: false,

        onSuccess: (data) => {
          if (this.$page.props.flash.status)
            this.showAlert(this.$page.props.flash.status, this.$page.props.flash.message);

        },
        onError: () => {
          this.showToast('danger', Object.values(this.form.errors).join("<br/>"));
        },
        onFinish: (data) => {
          // this.isLoading(false,);
          if (this.$page.props.flash.status)
            this.showAlert(this.$page.props.flash.status, this.$page.props.flash.message);
        },
      });
    },

  },

}
</script>
