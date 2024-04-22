<template>

  <Panel>
    <template v-slot:header>
      <title>{{__('edit_repository')}}</title>
    </template>


    <template v-slot:content>
      <!-- Content header -->
      <div
          class="flex items-center justify-start px-4 py-2 text-primary-500 border-b md:py-4">
        <FolderPlusIcon class="h-7 w-7 mx-3"/>

        <h1 class="text-2xl font-semibold">{{ __('edit_repository') }}</h1>

      </div>


      <div class="px-2  md:px-4">

        <div
            class="    mx-auto md:max-w-2xl   mt-6 px-2 md:px-4 py-4 bg-white shadow-md overflow-hidden  rounded-lg  ">


          <div v-if="data"
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

              <div class="my-2" v-if=" $page.props.agency && $page.props.agency.level<3">
                <UserSelector :colsData="['name','phone','level','id']"
                              :labelsData="['name','phone','type','id']"
                              :callback="{'level':getAgency}" :error="form.errors.agency_id"
                              :link="route('admin.panel.agency.search')" :label="__('agency')"
                              :id="'agency'" v-model:selected="form.agency_id" :preload=" this.$page.props.data.agency">
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
                <UserSelector :colsData="['fullname','phone','agency']" :labelsData="['name','phone','agency_id']"
                              :callback="{'agency':e=>`${e.name||''} (${e.id||''})` }"
                              :link="route('admin.panel.admin.search')+(form.agency_id?`?agency_id=${form.agency_id }`:'')"
                              :label="__('repo_owner/admin')" :error="form.errors.admin_id"
                              :id="'admin'" v-model:selected="form.admin_id" :preload=" this.$page.props.data.admin">
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
                <TextInput
                    id="name"
                    type="text"
                    :placeholder="__('repo_name')"
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
              <div class="my-4">
                <AddressSelector ref="addressSelector" :editable="true" :clearable="true"
                                 class=" " type=""
                                 :label="__('address')"
                                 @change="updateAddress($event) "
                                 :error="form.errors.address ||form.errors.postal_code || form.errors.province_id || form.errors.county_id "/>


              </div>
              <div class="my-4">
                <CitySelector :multi="true" :label="__('supported_districts')" v-model="form.cities"
                              :preload=" $page.props.data.cities"
                              :error="form.errors.cities"/>
              </div>
              <div class="my-3">
                <TextInput
                    id="is_shop"
                    type="checkbox"
                    :placeholder="__('connect_shop')"
                    classes="  "
                    v-model="form.is_shop"
                    autocomplete="is_shop"
                    :error="form.errors.is_shop"
                >
                </TextInput>
              </div>
              <div class="my-3" v-if="form.is_shop">
                <TextInput
                    id="allow_visit"
                    type="checkbox"
                    :placeholder="__('allow_visit')"
                    classes="  "
                    v-model="form.allow_visit"
                    autocomplete="allow_visit"
                    :error="form.errors.allow_visit"
                >
                </TextInput>
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
import CitySelector from "@/Components/CitySelector.vue";


export default {

  data() {
    return {
      data: this.$page.props.data || {},
      filteredAgencies: this.$page.props.agencies,
      preloadAddress: null,
      form: useForm({
        id: null,
        agency_id: null,
        admin_id: null,
        type_id: null,
        is_shop: false,
        allow_visit: false,
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
        timestamps: null,
        cities: [],


      }),
      img: null,
    }
  },
  components: {
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
    CitySelector,
  },
  created() {

  },
  mounted() {

    // console.log(this.data);


    this.form.id = this.data.id;
    this.form.name = this.data.name;
    this.form.is_shop = this.data.is_shop;
    this.form.allow_visit = this.data.allow_visit;
    this.form.type_id = this.data.type_id;
    this.form.admin = this.data.admin;
    this.form.admin_id = this.data.admin_id;
    this.form.agency = this.data.agency;
    this.form.agency_id = this.data.agency_id;
    this.form.phone = this.data.phone;
    this.form.status = this.data.status;
    this.form.cities = this.data.cities || [];


    this.form.owner = this.data.owner;
    this.form.owner_id = this.data.owner_id;

    this.preloadAddress = {
      address: this.data.address,
      postal_code: this.data.postal_code,
      province_id: this.data.province_id,
      county_id: this.data.county_id,
      district_id: this.data.district_id,
      lat: this.data.location && this.data.location.indexOf(',') > -1 ? this.data.location.split(',')[0] : null,
      lon: this.data.location && this.data.location.indexOf(',') > -1 ? this.data.location.split(',')[1] : null,

    };
    this.$nextTick(() => {
      this.$refs.addressSelector.preload(this.preloadAddress);
      this.updateAddress(this.preloadAddress);


    });

  },
  methods: {

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
      this.form.patch(route('admin.panel.repository.update'), {
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
