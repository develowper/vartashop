<template>

  <Panel>
    <template v-slot:header>
      <title>{{__('new_driver')}}</title>
    </template>


    <template v-slot:content>
      <!-- Content header -->
      <div
          class="flex items-center justify-start px-4 py-2 text-primary-500 border-b md:py-4">
        <FolderPlusIcon class="h-7 w-7 mx-3"/>

        <h1 class="text-2xl font-semibold">{{ __('new_driver') }}</h1>

      </div>


      <div class="px-2  md:px-4">

        <div
            class="    mx-auto md:max-w-2xl   mt-6 px-2 md:px-4 py-4 bg-white shadow-md overflow-hidden  rounded-lg  ">


          <div
              class="flex flex-col mx-2   col-span-2 w-full     px-2"
          >
            <div class="flex-col   m-2 items-center rounded-lg max-w-xs  w-full mx-auto    ">
              <div class="my-2">
                <ImageUploader ref="imageCropper" :label="__('driver_image_jpg')" :cropRatio="3/4" id="img"
                               height="10" class="grow " :crop-ratio="1"/>
                <InputError class="mt-1 " :message="form.errors.img"/>
              </div>

            </div>
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
                      <PhoneIcon class="h-5 w-5"/>
                    </div>
                  </template>
                </TextInput>
              </div>

              <div class="my-4">
                <TextInput
                    id="national_code"
                    type="text"
                    :placeholder="__('national_code')"
                    classes="  "
                    v-model="form.national_code"
                    autocomplete="card"
                    v-model:verified="form.national_code"
                    :error="form.errors.national_code"
                >
                  <template v-slot:prepend>
                    <div class="p-3">
                      <IdentificationIcon class="h-5 w-5"/>
                    </div>
                  </template>

                </TextInput>
              </div>
              <div class="my-4">
                <TextInput
                    id="card"
                    type="text"
                    :placeholder="__('card')"
                    classes="  "
                    v-model="form.card"
                    autocomplete="card"
                    v-model:verified="form.wallet_active"
                    :error="form.errors.card"
                >
                  <template v-slot:prepend>
                    <div class="p-3">
                      <CreditCardIcon class="h-5 w-5"/>
                    </div>
                  </template>

                </TextInput>
              </div>
              <div class="my-4">
                <TextInput
                    id="sheba"
                    type="text"
                    :placeholder="__('sheba')"
                    classes="  "
                    v-model="form.sheba"
                    autocomplete="sheba"
                    v-model:verified="form.sheba"
                    :error="form.errors.sheba"
                >
                  <template v-slot:prepend>
                    <div class="p-3">
                      <strong>IR</strong>
                      <!--                      <CreditCardIcon class="h-5 w-5"/>-->
                    </div>
                  </template>
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
  ChatBubbleBottomCenterTextIcon,
  Squares2X2Icon,
  SignalIcon,
  PencilIcon,
  XMarkIcon,
  PhoneIcon,
  CreditCardIcon,
  IdentificationIcon,

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


export default {

  data() {
    return {
      form: useForm({

        agency_id: null,
        fullname: null,
        national_code: null,
        phone: null,
        sheba: null,
        card: null,
        uploading: false,

      }),
      img: null,

    }
  },
  components: {
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
    PhoneIcon,
    CreditCardIcon,
    IdentificationIcon,

  },
  mounted() {
    // this.log(this.$page.props)

  },
  watch: {
    form(_new, _old) {


    }
  },
  methods: {

    submit() {
      // this.img = this.$refs.imageCropper.getCroppedData();
      this.img = this.$refs.imageCropper.getCroppedData();
      this.form.uploading = false;
      this.form.clearErrors();
      // this.isLoading(true, this.form.progress ? this.form.progress.percentage : null);

      this.form.post(route('admin.panel.shipping.driver.create'), {
        preserveScroll: false,

        onSuccess: (data) => {

          if (!this.form.uploading) {
            this.form.uploading = true;

            this.form.transform((data) => ({
              ...data,
              uploading: true,
              img: this.img,

            }))
                .post(route('admin.panel.shipping.driver.create'), {
                  preserveScroll: false,
                  onSuccess: (data) => {
                    // else {
                    if (this.$page.props.flash.status)
                      this.showAlert(this.$page.props.flash.status, this.$page.props.flash.message);
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
