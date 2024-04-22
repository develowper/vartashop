<template>
  <!-- Modal -->
  <div v-if="user"
       data-te-modal-init
       class="fixed left-0 top-0 backdrop-blur z-[1055] hidden h-full w-full overflow-y-auto overflow-x-hidden outline-none"
       id="walletChargeModal"
       tabindex="-1"
       aria-labelledby="walletChargeModal"
       aria-hidden="true">
    <div
        data-te-modal-dialog-ref
        class="pointer-events-none relative w-auto translate-y-[-50px] opacity-0 transition-all duration-300 ease-in-out min-[576px]:mx-auto min-[576px]:mt-7 px-2 sm:px-4 md:px8 min-[576px]:max-w-5xl">
      <div
          class="min-[576px]:shadow-[0_0.5rem_1rem_rgba(#000, 0.15)] pointer-events-auto relative flex w-full flex-col rounded-md border-none bg-white bg-clip-padding text-current shadow-lg outline-none dark:bg-neutral-600">
        <div
            class="flex flex-shrink-0 items-center justify-between rounded-t-md border-b-2 border-neutral-100 border-opacity-100 p-4 dark:border-opacity-50">
          <!--Modal title-->
          <h5
              class="text-xl font-medium leading-normal text-primary-800 dark:text-neutral-200"
              id="walletChargeModalLabel">
            {{ __('wallet_charge') }}
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
        <div class="relative flex-auto p-4" data-te-modal-body-ref>
          <div class="flex items-center p-4">

            <span class="text-xs text-gray-500"> {{ __('current_balance') }} : </span>
            <strong class="mx-2 text-primary-700 rounded px-2  ">{{ asPrice(user.wallet) }} </strong>
            <span class="text-xs text-gray-500"> {{ `(${__('currency')})` }}</span>
          </div>
          <div class="flex flex-col space-y-2 w-fit  items-center mx-auto">
            <div class="flex w-full justify-center items-center">
              <TextInput
                  id="amount"
                  type="text"
                  classes="  "
                  v-model="amount"
                  @input="viewAsPrice"
                  @keyup.enter="pay"
                  autocomplete="amount"
                  :error="params.errors.amount"
              >
                <template v-slot:prepend>
                  <div class="p-4 text-sm flex justify-center items-center ">

                    <span>{{ __('charge_amount') }}</span>
                    <span class="text-xs mx-1">{{ `(${__('currency')})` }}</span>
                  </div>
                </template>
              </TextInput>
            </div>
            <button @click="pay"
                    class=" flex items-center justify-center p-2 w-full text-center bg-green-500 hover:bg-green-400 text-white rounded cursor-pointer"
                    :class="{ 'opacity-75': params.processing }"
                    :disabled="params.processing"
            >
              <LoadingIcon class="w-4 h-4 mx-3 " v-if="  params.processing"/>
              {{ __('pay') }}
            </button>
          </div>
        </div>


      </div>
    </div>
  </div>
</template>

<script>

import {Modal} from "tw-elements";
import TextInput from "@/Components/TextInput.vue";
import LoadingIcon from "@/Components/LoadingIcon.vue";
import {useForm} from "@inertiajs/vue3";

export default {
  components: {
    TextInput,
    LoadingIcon,
  },
  data() {
    return {
      modal: null,
      amount: '',
      params: {
        id: null,
        processing: false,

        errors: {},
      },

    }
  },
  mounted() {
    this.$nextTick(() => {
      // this.amount = '200';
      // this.showWalletChargeDialog();

    });
  },
  watch: {
    // amount: function () {
    //   return this.asPrice(this.amount)
    // }
  },
  methods: {
    viewAsPrice() {
      this.amount = this.asPrice(this.replaceAll(this.amount, ',', ''));
    },
    show() {
      const modalEl = document.getElementById('walletChargeModal');
      this.modal = new Modal(modalEl);
      this.modal.show();
    },
    pay() {
      this.params.processing = true;
      this.params.amount = this.replaceAll(this.amount, ',', '');
      window.axios.post(route('payment.url'), this.params,
          {})
          .then((response) => {
            if (response.data && response.data.message) {
              this.showToast('success', response.data.message);
            }

            if (response.data && response.data.url)
              window.location = response.data.url;


          })

          .catch((error) => {
            this.error = this.getErrors(error);
            this.showToast('danger', this.error);
          })
          .finally(() => {
            // always executed
            this.params.processing = false;
          });
    }
  }
}
</script>