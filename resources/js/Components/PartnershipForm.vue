<template>
  <div class=" grow flex" @click="reset();  "

       data-te-toggle="modal"
       data-te-class-backdrop="z-[99999]"
       :data-te-target="`#partnershipModal${type}`">
    <slot name="partnershipForm" :selectedText="selectedText">
    </slot>
  </div>
  <div
      data-te-modal-init

      class="fixed left-0 top-0 z-[1045] backdrop-blur hidden h-full w-full overflow-y-auto overflow-x-hidden outline-none"
      :id="`partnershipModal${type}`"
      tabindex="-1"
      :aria-labelledby="`partnershipModal${type}Label`"
      aria-hidden="true">
    <div
        data-te-modal-dialog-ref
        class="pointer-events-none relative  w-auto translate-y-[-50px] opacity-0 transition-all duration-300 ease-in-out min-[576px]:mx-auto min-[576px]:mt-7 px-2 sm:px-4 md:px8 min-[576px]:max-w-5xl">
      <div
          class="min-[576px]:shadow-[0_0.5rem_1rem_rgba(#000, 0.15)] pointer-events-auto relative flex w-full flex-col rounded-md border-none bg-white bg-clip-padding text-current shadow-lg outline-none">
        <div
            class="flex flex-shrink-0 items-center justify-between rounded-t-md border-b-2 border-neutral-100 border-opacity-100 p-4">
          <!--Modal title-->
          <h5
              class="text-xl font-medium leading-normal text-neutral-800"
              :id="`partnershipModal${type}Label`">

          </h5>
          <!--Close button-->
          <button
              :class="`text-danger`"
              type="button"
              class="box-content rounded-none cursor-pointer border-none hover:no-underline hover:opacity-75 focus:opacity-100 focus:shadow-none focus:outline-none"
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
        <div class="relative flex-auto text-start p-1 md:p-4" data-te-modal-body-ref>
          <div
              class="flex items-center justify-start px-4 py-2 text-primary-500 border-b md:py-4">
            <FolderPlusIcon class="h-7 w-7 mx-3"/>

            <h1 class="text-2xl font-semibold">{{ __(type) }}</h1>

          </div>


          <div class="px-2  md:px-4">

            <div
                class="    mx-auto md:max-w-3xl   mt-6 px-2 md:px-4 py-4   overflow-hidden  rounded-lg  ">


              <div
                  class="flex flex-col mx-2   col-span-2 w-full     px-2"
              >

                <form @submit.prevent="send">
                  <input type="hidden" name="_token" :value="csrf()">


                  <div class="my-2">
                    <TextInput
                        id="fullname"
                        type="text"
                        :placeholder="__('fullname')"
                        classes="  "
                        v-model="params.fullname"
                        autocomplete="key"
                        :error="params.errors.fullname"
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
                        v-model:phone="params.phone"
                        v-model:phone-verify="params.phone_verify"
                        :phone-error="params.errors.phone"
                        :phone-verify-error="params.errors.phone_verify"
                        type="forget"
                        for="users"
                        :verified="null"
                        :activeButtonText="__('send_code')"
                        :disable="null"
                        :disableEdit="null"
                    />

                  </div>
                  <div class="my-2" v-if="type=='gardener'|| type=='farmer'">
                    <TextInput
                        id="meterage"
                        type="text"
                        :placeholder="__('meterage')"
                        classes="  "
                        v-model="params.meterage"
                        autocomplete="key"
                        :error="params.errors.meterage"
                    >
                      <template v-slot:prepend>
                        <div class="p-3">
                          <Bars2Icon class="h-5 w-5"/>
                        </div>
                      </template>

                    </TextInput>
                  </div>
                  <div class="my-4" v-if="false && (type=='gardener'|| type=='farmer')">
                    <InputLabel :value="__('farm_type')"/>

                    <div class="my-2 flex">
                      <TextInput
                          id="is_bush"
                          type="checkbox"
                          :placeholder="__('bush')"
                          classes="  "
                          v-model="params.is_bush "
                          autocomplete="is_bush"
                          :error="params.errors.is_bush"
                      >
                      </TextInput>
                      <TextInput
                          id="greenhouse"
                          type="checkbox"
                          :placeholder="__('greenhouse')"
                          classes="  "
                          v-model="params.is_greenhouse "
                          autocomplete="greenhouse"
                          :error="params.errors.is_greenhouse"
                      >
                      </TextInput>
                    </div>
                  </div>
                  <div class="my-4  border rounded p-2" v-if="type=='gardener'|| type=='farmer'">
                    <InputLabel :value="__('product_name_and_weight')"/>
                    <div class="flex flex-col w-full  ">
                      <div v-for="(product,idx) in params.products"
                           class=" flex py-1 sm:grid-cols-2 gap-2 items-center  "
                           :class="{'border-b-2':idx<params.products.length-1}" :key="idx">
                        <div class="grid xs:flex     w-full  items-center sm:items-end  ">
                          <div class=" flex sm:flex-row   flex-col   grow  gap-1  ">
                            <TextInput class="grow"
                                       :id="`product${idx}`"
                                       type="text"
                                       :placeholder="__('product')"
                                       classes="  "
                                       v-model="product.name"
                                       :autocomplete="`product${idx}`"
                                       :error="params.errors[`products.${idx}.name`]"
                            >
                              <template v-slot:prepend>
                                <div class="p-3">
                                  <Bars2Icon class="h-5 w-5"/>
                                </div>
                              </template>
                            </TextInput>
                            <TextInput class="grow"
                                       :id="`weight${idx}`"
                                       type="number"
                                       :placeholder="`${__('weight')} (${__('kg')})`"
                                       classes="   "
                                       v-model="product.weight"
                                       :autocomplete="`weight${idx}`"
                                       :error="params.errors[`products.${idx}.weight`] "
                            >
                              <template v-slot:prepend>
                                <div class="p-3">
                                  <Bars2Icon class="h-5 w-5"/>
                                </div>
                              </template>
                            </TextInput>
                          </div>
                          <div
                              class="p-3  xs:mx-1 my-1 xs:my-0    text-center   cursor-pointer   bg-danger-500 hover:bg-danger-400 rounded   "
                              @click="params.products.splice(idx,1)">
                            <MinusIcon class="w-6 h-6 mx-auto"/>
                          </div>
                        </div>
                      </div>
                      <div
                          class="p-3 my-1    text-center   cursor-pointer   bg-success-500 hover:bg-success-400 rounded   "
                          @click="params.products.push({name:null,weight:null})">
                        <PlusIcon class="w-6 h-6 mx-auto"/>
                      </div>


                    </div>
                  </div>
                  <div class="my-2">
                    <ProvinceCounty
                        ref="provinceCounty"
                        v-model:province-data="params.province_id"
                        v-model:county-data="params.county_id"
                        :provinces-data="$page.props.cities.filter((e)=>e.level==1)"
                        :counties-data="$page.props.cities.filter((e)=>e.level==2)"
                        :provinceError="params.errors.province_id"
                        :countyError="params.errors.county_id"
                    />
                  </div>
                  <div class="my-2">
                    <TextInput
                        :multiline="true"
                        id="address"
                        type="text"
                        :placeholder="__('address')"
                        classes="  "
                        v-model="params.address"
                        autocomplete="address"
                        :error="params.errors.address"
                    >
                      <template v-slot:prepend>
                        <div class="p-3">
                          <MapIcon class="h-5 w-5"/>
                        </div>
                      </template>
                    </TextInput>
                  </div>
                  <div class="my-2">
                    <TextInput
                        :multiline="true"
                        id="description"
                        type="text"
                        :placeholder="__('description')"
                        classes="  "
                        v-model="params.description"
                        autocomplete="description"
                        :error="params.errors.description"
                    >
                      <template v-slot:prepend>
                        <div class="p-3">
                          <ChatBubbleBottomCenterTextIcon class="h-5 w-5"/>
                        </div>
                      </template>

                    </TextInput>

                  </div>

                  <div class="my2">

                    <!--                    <vue-recaptcha v-show="showRecaptcha" :sitekey="siteKey"-->
                    <!--                                   size="normal" class="w-full"-->
                    <!--                                   theme="light"-->
                    <!--                                   hl="fa"-->
                    <!--                                   :loading-timeout="loadingTimeout"-->
                    <!--                                   @verify="recaptchaVerified"-->
                    <!--                                   @expire="recaptchaExpired"-->
                    <!--                                   @fail="recaptchaFailed"-->
                    <!--                                   @error="recaptchaError"-->
                    <!--                                   ref="vueRecaptcha">-->
                    <!--                    </vue-recaptcha>-->
                    <!--                    <InputError class="mt-1" :message="params.errors.recaptcha"/>-->
                  </div>
                  <div v-if="loading" class="shadow w-full bg-grey-light m-2   bg-gray-200 rounded-full">
                    <div
                        class=" bg-primary rounded  text-xs leading-none py-[.1rem] text-center text-white duration-300 "
                        :class="{' animate-pulse': loading}"
                        :style="`width: 100%`">
                    </div>
                  </div>

                  <div class="    mt-4">

                    <PrimaryButton class="w-full  "
                                   :class="{ 'opacity-25': loading}"
                                   :disabled="loading">
                      <LoadingIcon class="w-4 h-4 mx-3 " v-if="  loading"/>
                      <span class=" text-lg  ">  {{ __('register_info') }}</span>
                    </PrimaryButton>

                  </div>

                </form>
              </div>


            </div>
          </div>
        </div>


      </div>
    </div>
  </div>
</template>

<script>
import {

  PhoneIcon,
  ChatBubbleBottomCenterTextIcon,
  FolderPlusIcon,
  Bars2Icon,
  MapIcon,
  PlusIcon,
  MinusIcon,

} from "@heroicons/vue/24/outline";
import LoadingIcon from "@/Components/LoadingIcon.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import vueRecaptcha from 'vue3-recaptcha2';
import {Modal} from "tw-elements";
import PhoneFields from "@/Components/PhoneFields.vue";
import ProvinceCounty from "@/Components/ProvinceCounty.vue";
import InputLabel from "@/Components/InputLabel.vue";

export default {
  name: "PartnershipForm",
  props: ['type', 'clear', 'classes'],
  data() {
    return {
      loading: false,
      selectedText: false,
      siteKey: import.meta.env.VITE_RECAPTCHA_SITE_KEY,
      showRecaptcha: true,
      loadingTimeout: 30000, // 30 seconds
      params: {
        errors: {},
        fullname: null,
        phone: null,
        description: null,
        recaptcha: null,
        products: [{name: null, weight: null}],
        type: this.type,
      },
    }

  },
  components: {
    PhoneFields,
    ChatBubbleBottomCenterTextIcon,
    MapIcon,
    LoadingIcon,
    PhoneIcon,
    vueRecaptcha,
    FolderPlusIcon,
    Bars2Icon,
    TextInput,
    InputError,
    PrimaryButton,
    ProvinceCounty,
    InputLabel,
    PlusIcon,
    MinusIcon,
  },
  mounted() {
    const modalEl = document.getElementById('partnershipModal' + this.type);
    this.modal = new Modal(modalEl);
  },
  methods: {
    reset() {
      // this.modal.show();
      // this.recaptchaExpired();
    },
    send() {
      this.loading = true;
      this.params.phone = f2e(this.params.phone);
      window.axios.post(route('partnership.create'), this.params,
          {})
          .then((response) => {
            if (response.data && response.data.message) {
              this.modal.hide();
              this.showToast('success', response.data.message);
              this.params = {errors: {}, fullname: null, phone: null, description: null, recaptcha: null};
              // this.recaptchaExpired();

            }


          })

          .catch((error) => {
            this.error = this.getErrors(error);
            if (error.response && error.response.data) {
              // this.log(error.response)
              this.params.errors = error.response.data.errors;

            }
            this.showToast('danger', this.error);
          })
          .finally(() => {
            // always executed
            this.loading = false;
          });
    },
    recaptchaVerified(response) {
      this.params.recaptcha = response;
    },
    recaptchaExpired() {
      this.$refs.vueRecaptcha.reset();
    },
    recaptchaFailed() {
    },
    recaptchaError(reason) {
    },
  }
}
</script>

<style scoped>

</style>