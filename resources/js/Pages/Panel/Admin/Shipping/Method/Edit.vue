<template>

  <Panel>
    <template v-slot:header>
      <title>{{__('edit_shipping_method')}}</title>
    </template>


    <template v-slot:content>
      <!-- Content header -->
      <div
          class="flex items-center justify-start px-4 py-2 text-primary-500 border-b md:py-4">
        <FolderPlusIcon class="h-7 w-7 mx-3"/>

        <h1 class="text-2xl font-semibold">{{ __('edit_shipping_method') }}</h1>

      </div>


      <div v-if=" $page.props.data" class="px-2  md:px-4">

        <div
            class="    mx-auto md:max-w-2xl   mt-6 px-2 md:px-4 py-4 bg-white shadow-md overflow-hidden  rounded-lg  ">


          <div
              class="flex flex-col mx-2   col-span-2 w-full     px-2"
          >
            <div v-if="$page.props.help" class="flex flex-col text-md bg-primary-50 rounded-md p-2">
              <span v-for="(item,idx) in $page.props.help" class="flex my-1">
                 <LightBulbIcon v-if="idx==0" class="w-5 h-5"/>
                <span class="mx-1 text-sm " :class="{'font-bold':idx==0}">
                    {{ item }}
                </span>
              </span>
            </div>

            <form @submit.prevent="submit">

              <div class="my-2" v-if="hasAccess('edit_setting')">
                <UserSelector :colsData="['name','phone','level']" :labelsData="['name','phone','type']"
                              :callback="{'level':getAgency}" :error="form.errors.shipping_agency_id"
                              :link="route('admin.panel.agency.search')" :label="__('shipping_owner')"
                              :id="'shipping_agency'" v-model:selected="form.shipping_agency_id"
                              :preload="$page.props.data.shipping_agency">
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
                <UserSelector :colsData="['name','phone','agency_id']" :labelsData="['name','phone','agency_id']"
                              :callback="{'level':getAgency}" :error="form.errors.repo_id"
                              :link="route('admin.panel.repository.search')+(`?status=active&is_shop=1` )"
                              :label="__('repository')"
                              :id="'repository'" v-model:selected="form.repo_id" :preload="$page.props.data.repository">
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
                    id="base_price"
                    type="number"
                    :placeholder="`${__('base_price')} (${__('currency')})`"
                    classes="  "
                    v-model="form.base_price"
                    autocomplete="base_price"
                    :error="form.errors.base_price"
                >
                  <template v-slot:prepend>
                    <div class="p-3">
                      <CurrencyDollarIcon class="h-5 w-5"/>
                    </div>
                  </template>

                </TextInput>

              </div>
              <div class="my-2">
                <TextInput
                    id="per_weight_price"
                    type="number"
                    :placeholder="`${__('per_weight_price')} (${__('currency')})`"
                    classes="  "
                    v-model="form.per_weight_price"
                    autocomplete="per_weight_price"
                    :error="form.errors.per_weight_price"
                >
                  <template v-slot:prepend>
                    <div class="p-3">
                      <CurrencyDollarIcon class="h-5 w-5"/>
                    </div>
                  </template>

                </TextInput>

              </div>
              <div class="my-2">
                <TextInput
                    id="per_distance_price"
                    type="number"
                    :placeholder="`${__('per_distance_price')} (${__('currency')})`"
                    classes="  "
                    v-model="form.per_distance_price"
                    autocomplete="per_distance_price"
                    :error="form.errors.per_distance_price"
                >
                  <template v-slot:prepend>
                    <div class="p-3">
                      <CurrencyDollarIcon class="h-5 w-5"/>
                    </div>
                  </template>

                </TextInput>

              </div>
              <div class="my-2">
                <TextInput
                    id="min_order_weight"
                    type="number"
                    :placeholder="`${__('min_order_weight')} (${__('kg')})`"
                    classes="  "
                    v-model="form.min_order_weight"
                    autocomplete="min_order_weight"
                    :error="form.errors.min_order_weight"
                >
                  <template v-slot:prepend>
                    <div class="p-3">
                      <CurrencyDollarIcon class="h-5 w-5"/>
                    </div>
                  </template>

                </TextInput>
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
                    id="description"
                    type="text"
                    :placeholder="__('description')"
                    classes="  "
                    :multiline="true"
                    v-model="form.description"
                    autocomplete="description"
                    :error="form.errors.description"
                >
                  <template v-slot:prepend>
                    <div class="p-3">
                      <ChatBubbleBottomCenterTextIcon class="h-5 w-5"/>
                    </div>
                  </template>

                </TextInput>
              </div>

              <div class="my-4">
                <CitySelector :multi="true" :label="__('supported_districts')" v-model="form.cities"
                              :preload=" $page.props.data.cities"
                              :error="form.errors.cities"/>
              </div>

              <div class="my-4" v-if="form.repo_id">
                <ProductSelector :link="route('admin.panel.product.tree')+`?repo_id=${form.repo_id}`" :multi="true"
                                 :label="__('supported_products')" v-model="form.products"
                                 :preload=" $page.props.data.products"
                                 :error="form.errors.products"/>
              </div>
              <div class="my-2">
                <Timestamp ref="timestampSelector" mode="edit" :label="__('delivery_hours')"
                           :errors="form.errors || []" v-model="form.timestamps"/>
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
  LightBulbIcon,
  CurrencyDollarIcon,
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
import ProductSelector from "@/Components/ProductSelector.vue";
import Timestamp from "@/Components/Timestamp.vue";


export default {

  data() {
    return {
      data: this.$page.props.data || {},
      form: useForm({
        id: null,
        name: null,
        description: null,
        base_price: null,
        per_weight_price: null,
        per_distance_price: null,
        min_order_weight: null,
        repo: null,
        repo_id: null,

        cities: [],
        products: [],
        timestamps: null,
        shipping_agency_id: null,


      }),
      img: null,
    }
  },
  components: {
    Timestamp,
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
    ProductSelector,
    CurrencyDollarIcon,
    LightBulbIcon,
  },
  created() {

  },
  mounted() {

    // console.log(this.data);


    this.form.id = this.data.id;
    this.form.shipping_agency_id = this.data.shipping_agency_id;
    this.form.name = this.data.name;
    this.form.description = this.data.description;
    this.form.shipping_agency = this.data.shipping_agency;
    this.form.repository = this.data.repository;
    this.form.base_price = this.data.base_price;
    this.form.per_weight_price = this.data.per_weight_price;
    this.form.per_distance_price = this.data.per_distance_price;
    this.form.min_order_weight = this.data.min_order_weight;
    this.form.products = this.data.products;
    this.form.cities = this.data.cities;
    this.form.timestamps = this.data.timestamps || [];
    this.$refs.timestampSelector.data = this.form.timestamps;

    this.$nextTick(() => {


    });

  },
  methods: {


    submit() {


      // this.form.category_id = this.$refs.categorySelector.selected;
      this.form.clearErrors();

      // this.isLoading(true, this.form.progress ? this.form.progress.percentage : null);
      // this.images = [];
      // for (let i = 0; i < this.$page.props.max_images_limit; i++) {
      //   let tmp = this.$refs.imageCropper[i].getCroppedData();
      //   if (tmp) this.images.push(tmp);
      // }
      this.form.patch(route('admin.panel.shipping-method.update'), {
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
