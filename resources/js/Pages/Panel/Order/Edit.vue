<template>

  <Panel>
    <template v-slot:header>
      <title>{{__('order_details')}}</title>
    </template>


    <template v-slot:content>
      <!-- Content header -->
      <div
          class="flex items-center justify-start px-4 py-2 text-primary-500 border-b md:py-4">
        <FolderPlusIcon class="h-7 w-7 mx-3"/>

        <h1 class="text-2xl font-semibold">{{ __('order_details') }}</h1>

      </div>


      <div class="px-2  md:px-4">

        <div
            class="    mx-auto md:max-w-2xl lg:max-w-max   mt-6 px-2 md:px-4 py-4 bg-white shadow-md overflow-hidden  rounded-lg  ">

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


export default {

  data() {
    return {
      orderFrom: [this.__('internal'), this.__('external')],
      form: useForm({

        status: 'pending',
        to_repo_id: null,
        from_repo_id: null,
        from_repo: null,
        order_type: this.__('internal'),
        pay_timeout: this.$page.props.pay_timeout,
        products: [],
        shipping_method_id: null,
        total_discount: 0,
        total_shipping_price: 0,
        from_address: null,
        from_province_id: null,
        from_county_id: null,
        from_district_id: null,
        from_lat: null,
        from_lon: null,
        from_location: null,
        from_postal_code: null,
        from_fullname: null,
        from_phone: null,
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

      this.form.post(route('admin.panel.repository.order.create'), {
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
