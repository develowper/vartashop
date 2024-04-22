<template>

  <Panel>
    <template v-slot:header>
      <title>{{__('edit_profile')}}</title>
    </template>


    <template v-slot:content>
      <!-- Content header -->
      <div
          class="flex items-center justify-start px-4 py-2 text-primary-500 border-b md:py-4">
        <PencilSquareIcon class="h-7 w-7 mx-3"/>

        <h1 class="text-2xl font-semibold">{{ __('edit_profile') }}</h1>

      </div>

      <!-- Content -->
      <div class="px-2  md:px-4">

        <div v-if="data && data.id" class="flex flex-col mt-4">
          <div class="flex text-sm">
            <div class="text-gray-500">{{ __('register_date') }}:</div>
            <div class="text-primary-700 mx-2">{{ toShamsi(data.created_at) }}</div>
          </div>
          <div class="flex text-sm">
            <div class="text-gray-500">{{ __('status') }}:</div>
            <div class="mx-2" :class="`text-${getStatus('user_statuses',data.status).color}-500`">{{
                __(data.status)
              }}
            </div>
          </div>
          <div class="flex text-sm items-center">
            <div class="text-gray-500">{{ __('wallet') }}:</div>
            <div class="mx-2" :class="`text-gray-700`">{{
                data.financial ? data.financial.wallet : 0
              }}

            </div>
            <TomanIcon class=" "/>
          </div>


        </div>
        <div v-if="data && data.id"
             class="lg:grid      lg:grid-cols-3  mx-auto md:max-w-5xl   mt-6 px-2 md:px-4 py-4 bg-white shadow-md overflow-hidden  rounded-lg  ">
          <div
              class="lg:flex-col  flex flex-wrap   self-center  md:m-2  lg:mx-2 md:items-center lg:items-stretch rounded-lg    ">
            <!--            <InputLabel class="m-2 w-full md:text-start lg:text-center"-->
            <!--                        :value="__('profile_images_max_%s_item').replace('%s',$page.props.max_images_limit)"/>-->

            <div class="flex-col   m-2 items-center rounded-lg max-w-[8rem]  w-full mx-auto lg:mx-2   ">
              <div class="my-2">
                <ImageUploader :replace="true"
                               :preload="route(`storage.${isAdmin()?'admins':'users'}`)+`/${data.id}.jpg`"
                               mode="edit" :for-id="data.id"
                               :link="route(`${ isAdmin() ? 'admin' : 'user'}.panel.profile.update`)"
                               ref="imageCropper" :label="__('image_cover_jpg')" cropRatio="1" id="img"
                               height="10" class="grow "/>
                <InputError class="mt-1 " :message="form.errors.img"/>
              </div>

            </div>

          </div>
          <div
              class="flex flex-col mx-2   col-span-2 w-full   lg:border-s px-2"
          >
            <form @submit.prevent="submit">


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
                <PhoneFields
                    v-model:phone="form.phone"
                    v-model:phone-verify="form.phone_verify"
                    :phone-error="form.errors.phone"
                    for="users"
                    :verified="data.phone_verified"
                    :phone-verify-error="form.errors.phone_verify"
                />
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

              <div v-if="false" class="my-4 text-gray-700">
                <p class="text-sm my-1">{{ __('ref_link') }}</p>
                <div @click="copyToClipboard(route('/') + `?ref=${data.ref_id}`)"
                     class="text-left cursor-pointer block w-full rounded bg-primary-100 hover:bg-primary-200 text-primary p-2"
                >{{
                    route('/') + `?ref=${data.ref_id}`
                  }}
                </div>

              </div>
              <div class="my-4 text-gray-700">
                <p class="text-sm my-1">{{ __('connect_telegram') }}</p>
                <div class="flex  flex-col bg-gray-100 rounded m-1 p-1 sm:p-2 text-sm">
                  <div v-for="(row,idx) in __('help.connect_telegram')">
                    <div v-for="(col,ix) in row">
                      <a class=" text-primary cursor-pointer text-left  block" v-if="col.indexOf('http')!==-1"
                         target="new"
                         :href="col">
                        <span>{{ col }}</span>
                      </a>
                      <div v-else :class="{'font-bold my-1 pt-2 pb-1 border-b ':ix==0}" class="text-gray-500">{{
                          col
                        }}
                      </div>

                    </div>
                  </div>
                </div>
                <div
                    @click="data.telegram_id?showDialog('danger',__('disconnect_connection?'),__('accept'),edit,{cmnd:'disconnect-telegram'}) :telegramLink?copyToClipboard(telegramLink): edit({cmnd:'connect-telegram'})"
                    class="flex px-1 justify-center cursor-pointer block w-full rounded bg-primary-100 hover:bg-primary-200 text-primary-600 p-2"
                >
                  <div @click="" class="text-left" v-if="telegramLink">{{
                      telegramLink
                    }}
                  </div>
                  <div v-else-if="data.telegram_id" class=" ">{{ __('connected') }}</div>
                  <div v-else class=" ">{{ __('connect') }}</div>
                </div>
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

                <PrimaryButton class="w-full  flex items-center justify-center"
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
import TomanIcon from "@/Components/TomanIcon.vue";

export default {

  data() {
    return {
      data: this.data || {},
      telegramLink: null,
      form: useForm({
        id: null,
        fullname: null,
        phone: null,
        phone_verify: null,
        wallet: null,
        sheba: null,
        card: null,

      }),
      img: null,
      profile: null,
    }
  },
  components: {
    TomanIcon,
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
    BanknotesIcon,

  },
  created() {
    this.data = this.$page.props.data;
  },
  mounted() {

    // console.log(this.data);
    this.form.fullname = this.data.fullname;
    this.form.phone = this.data.phone;
    this.form.email = this.data.email;
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
      this.form.patch(route(`${this.isAdmin() ? 'admin' : 'user'}.panel.profile.update`), {
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
    },
    edit(params) {
      this.isLoading(true);
      window.axios.patch(route(`${this.isAdmin() ? 'admin' : 'user'}.panel.profile.update`), params,
          {})
          .then((response) => {
            if (response.data && response.data.message) {
              this.showToast('success', response.data.message);

            }
            if (response.data.url) {
              this.telegramLink = response.data.url == 'disconnect' ? null : response.data.url;
              this.data.telegram_id = response.data.telegram_id;
            }


          })

          .catch((error) => {
            this.error = this.getErrors(error);
            if (error.response && error.response.data) {
              if (error.response.data.charge) {
                this.data[params.idx].charge = error.response.data.charge;
              }
              if (error.response.data.view_fee) {
                this.data[params.idx].view_fee = error.response.data.view_fee;
              }
              if (error.response.data.meta) {
                this.data[params.idx].meta = error.response.data.meta;
              }
            }
            this.showToast('danger', this.error);
          })
          .finally(() => {
            // always executed
            this.isLoading(false);
          });
    },
  },

}
</script>
