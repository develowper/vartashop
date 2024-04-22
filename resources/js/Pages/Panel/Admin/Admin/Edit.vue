<template>

  <Panel>
    <template v-slot:header>
      <title>{{__('edit_admin')}}</title>
    </template>


    <template v-slot:content>
      <!-- Content header -->
      <div
          class="flex items-center justify-start px-4 py-2 text-primary-500 border-b md:py-4">
        <PencilSquareIcon class="h-7 w-7 mx-3"/>

        <h1 class="text-2xl font-semibold">{{ __('edit_admin') }}</h1>

      </div>

      <!-- Content -->
      <div class="px-2  md:px-4">


        <div
            class="lg:grid      lg:grid-cols-3  mx-auto md:max-w-5xl   my-6 px-2 md:px-4 py-4 bg-white shadow-md overflow-hidden  rounded-lg  ">
          <div
              class="lg:flex-col  flex flex-wrap   self-center  md:m-2  lg:mx-2 md:items-center lg:items-stretch rounded-lg    ">
            <!--            <InputLabel class="m-2 w-full md:text-start lg:text-center"-->
            <!--                        :value="__('profile_images_max_%s_item').replace('%s',$page.props.max_images_limit)"/>-->

            <div class="flex-col   m-2 items-center rounded-lg max-w-[8rem]  w-full mx-auto lg:mx-2   ">
              <div class="my-2">
                <ImageUploader :replace="true"
                               :preload="route('storage.admins')+`/${$page.props.data.id}.jpg`"
                               mode="edit" :for-id="$page.props.data.id"
                               :link="route('admin.panel.admin.update')"
                               ref="imageCropper" :label="__('driver_image_jpg')" :cropRatio="3/4" id="img"
                               height="10" class="grow "/>
                <InputError class="mt-1 " :message="form.errors.img"/>
              </div>

            </div>

          </div>
          <div
              class="flex flex-col mx-2   col-span-2 w-full   lg:border-s px-2"
          >
            <form @submit.prevent="submit">

              <div class="flex flex-wrap items-center justify-center ">

                <RadioGroup ref="statusSelector" class=" grow mx-2" name="status" v-model="form.status"
                            :items="$page.props.statuses" :before-selected=" $page.props.data.status "/>
                <RadioGroup ref="roleSelector" class=" grow mx-2" name="role" v-model="form.role"
                            :before-selected=" $page.props.data.role "
                            :items="$page.props.roles"/>
              </div>
              <div class="my-2" v-if="$page.props.agency && $page.props.agency.level<3">
                <UserSelector :colsData="['name','phone','level']" :labelsData="['name','phone','type']"
                              :callback="{'level':getAgency}" :error="form.errors.agency_id"
                              :link="route('admin.panel.agency.search')" :label="__('agency')"
                              :id="'agency'" v-model:selected="form.agency_id" :preload="$page.props.data.agency">
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
              <div class="my-4">
                <TextInput
                    id="fullname"
                    type="text"
                    :placeholder="__('fullname')"
                    classes="  "
                    v-model="form.fullname"
                    autocomplete="fullname"
                    :error="form.errors.fullname"
                >
                  <template v-slot:prepend>
                    <div class="p-3">
                      <Bars2Icon class="h-5 w-5"/>
                    </div>
                  </template>

                </TextInput>
              </div>


              <div class="my-4">
                <PhoneFields
                    v-model:phone="form.phone"
                    v-model:phone-verify="form.phone_verify"
                    :phone-error="form.errors.phone"
                    type="edit"
                    for="admins"
                    :verified="null"
                    :activeButtonText="__('send_code')"
                    :disable="null"
                    :disableEdit="null"
                    :phone-verify-error="form.errors.phone_verify"
                />
              </div>
              <div class="my-4">
                <TextInput
                    id="national_code"
                    type="text"
                    :placeholder="__('national_code')"
                    classes="  "
                    v-model="form.national_code"
                    autocomplete="card"
                    :error="form.errors.national_code"
                >
                  <template v-slot:append>
                    <div class="">
                      <IdentificationIcon class="h-5 w-5"/>
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
              <div class="my-4">
                <TextInput
                    id="wallet"
                    type="number"
                    :placeholder="__('wallet')"
                    classes="  "
                    v-model="form.wallet"
                    autocomplete="wallet"
                    :error="form.errors.wallet"
                >
                  <template v-slot:append>
                    <div class="p- px-0">
                      <BanknotesIcon class="h-5 w-5"/>
                    </div>
                  </template>

                </TextInput>
              </div>
              <div class="my-4">
                <TextInput
                    id="password"
                    type="text"
                    :placeholder="__('password')"
                    classes="  "
                    v-model="form.password"
                    autocomplete="password"
                    :error="form.errors.password"
                >
                  <template v-slot:prepend>
                    <div class="p-3">
                      <KeyIcon class="h-5 w-5"/>
                    </div>
                  </template>

                </TextInput>

              </div>
              <div class="my-4">
                <TextInput
                    id="password_verify"
                    type="text"
                    :placeholder="__('password_verify')"
                    classes="  "
                    v-model="form.password_confirmation"
                    autocomplete="password"
                    :error="form.errors.password_confirmation"
                >
                  <template v-slot:prepend>
                    <div class="p-3">
                      <KeyIcon class="h-5 w-5"/>
                    </div>
                  </template>

                </TextInput>

              </div>


              <div class="py-4"></div>
              <div v-if="form.progress" class="shadow w-full bg-grey-light m-2   bg-gray-200 rounded-full">
                <div
                    class=" bg-primary rounded  text-xs leading-none py-[.1rem] text-center text-white duration-300 "
                    :class="{' animate-pulse': form.progress.percentage <100}"
                    :style="`width: ${form.progress.percentage }%`">
                  <span class="animate-bounce">{{ form.progress.percentage }}</span>
                </div>
              </div>
              <div class="    mt-4">

                <PrimaryButton class="w-full flex items-center justify-center "
                               :class="{ 'opacity-25': form.processing }"
                               :disabled="form.processing">
                  <LoadingIcon class="w-4 h-4 mx-3 " v-if="  form.processing"/>
                  <span class=" text-lg  ">  {{ __('register_info') }}</span>
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
  PencilSquareIcon,
  PaintBrushIcon,
  CreditCardIcon,
  AtSymbolIcon,
  PhoneIcon,
  KeyIcon,
  IdentificationIcon,
  XMarkIcon,
  BanknotesIcon,

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
import EmailFields from "@/Components/EmailFields.vue";
import UserSelector from "@/Components/UserSelector.vue";

export default {

  data() {
    return {
      data: this.data || {},
      form: useForm({
        id: null,
        fullname: null,
        phone: null,
        phone_verify: null,
        agency_id: null,
        card: null,
        sheba: null,
        national_code: null,
        wallet: 0,
        password: null,
        password_confirmation: null,
        status: null,
        role: null,
        img: null,

      }),
      profile: null,
    }
  },
  components: {
    EmailFields,
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
    PencilSquareIcon,
    PaintBrushIcon,
    CreditCardIcon,
    AtSymbolIcon,
    PhoneIcon,
    KeyIcon,
    IdentificationIcon,
    XMarkIcon,
    UserSelector,
    BanknotesIcon,

  },
  created() {
    this.data = this.$page.props.data;
  },
  mounted() {
    // this.$nextTick(() => {
    //
    // });

    // console.log(this.data);
    this.form.id = this.data.id;
    this.form.status = this.data.status;
    this.form.role = this.data.role;
    this.form.fullname = this.data.fullname;
    this.form.phone = this.data.phone;
    this.form.national_code = this.data.national_code;
    if (this.data.financial) {
      this.form.card = this.data.financial.card;
      this.form.sheba = this.data.financial.sheba;
      this.form.wallet = this.data.financial.wallet;
    }

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
      this.form.patch(route('admin.panel.admin.update'), {
        preserveScroll: false,

        onSuccess: (data) => {
          if (this.$page.props.flash.status)
            this.showAlert(this.$page.props.flash.status, this.$page.props.flash.message);

          if (this.$page.props.extra && this.$page.props.extra.wallet_active != null)
            this.user.wallet_active = this.$page.props.extra.wallet_active;

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
    }
  },

}
</script>
