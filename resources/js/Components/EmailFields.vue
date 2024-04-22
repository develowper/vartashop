<template>
  <div class="grid grid-cols-1   gap-2">
    <div>
      <div class="flex items-center">
        <InputLabel class="my-2" for="email" :value="__('email')"/>
        <span v-if="verified==null" class="text-danger text-xs mx-1">({{ __('not_verified') }})</span>
        <span v-else class="text-success text-xs mx-1">({{ __('verified') }})</span>

      </div>
      <div class="relative mb-2 mt-2 flex flex-wrap items-stretch">

      <span
          class="flex bg-gray-100  text-gray-500 items-center whitespace-nowrap rounded-s border border-e-0 border-solid border-neutral-300  text-center text-base font-normal leading-[1.6]   dark:border-neutral-600 dark:text-neutral-200 dark:placeholder:text-neutral-200"
          id="basic-addon1">
             <div class="p-3">
                 <AtSymbolIcon class="h-5 w-5"/>
            </div>
        </span>
        <input
            :value="email"
            @input="$emit('update:email', $event.target.value); "
            class="  flex-auto rounded-0  border border-solid border-neutral-300    px-3   text-neutral-700   transition duration-200 ease-in-out focus:z-[3] focus:border-primary focus:text-neutral-700   dark:border-neutral-600 dark:text-neutral-200 dark:placeholder:text-neutral-200 dark:focus:border-primary"
            @visibility.window="$el.type =  'text'  "
            ref="input_email"/>
        <span @click="!isDisabled ?sendVerificationCode(email):null"
              class="flex  cursor-pointer hover:bg-primary-400 bg-primary-500 items-center whitespace-nowrap rounded-e border border-s-0 border-solid border-neutral-300 px-3 py-[0.25rem] focus:border-primary text-center text-base font-normal leading-[1.6] text-neutral-700 dark:border-neutral-600 dark:text-neutral-200 dark:placeholder:text-neutral-200"
              :class="{ 'opacity-25':isDisabled}"
              :disabled="isDisabled"
        >
          <LoadingIcon v-if="loading" class="w-4 h-4 mx-3 "/>
          <span v-if="timer<60 && timer>0" class="text-white">{{ timer }}</span>
          <span v-else class="text-white">{{ __('activation') }}</span>
          <!--            <PaperAirplaneIcon class="h-5 w-5 text-white font-bold "/>-->
        </span>
      </div>
      <InputError class="mt-1" :message="emailError"/>
    </div>

  </div>

</template>

<script>
import Scaffold from "@/Layouts/Scaffold.vue";
import Image from "@/Components/Image.vue";
import {Link} from "@inertiajs/vue3";
import {CurrencyDollarIcon, EyeIcon} from "@heroicons/vue/24/outline";
import LoadingIcon from "@/Components/LoadingIcon.vue";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import {getCurrentInstance} from 'vue'
import {
  TagIcon,
  CheckIcon,
  PlusIcon,
  XMarkIcon,
  PaperAirplaneIcon,
  AtSymbolIcon,


} from "@heroicons/vue/24/outline";

export default {
  props: ['email', 'for', 'verified', 'type', 'emailVerify', 'emailError', 'emailVerifyError', 'admin'],
  emits: ['update:email', 'update:emailVerify'],
  data() {
    return {
      loading: false,
      timer: 60,
      oldEmail: null,
    }
  },
  components: {
    LoadingIcon,
    AtSymbolIcon,
    InputLabel,
    InputError,
  },
  computed: {
    isDisabled: function () {
      return this.loading || (this.timer < 60 && this.timer > 0) || (this.oldEmail == this.email)

    }
  },
  created() {
    // this.isLoading(true);

  },
  mounted() {
    this.$nextTick(() => {
      this.oldEmail = this.email;
    });
    // this.log('this.emailVerified')
    // this.log(this.emailVerified)
  },
  methods: {
    sendVerificationCode(email) {
      this.loading = true;
      window.axios.post(route('verification.send'), {
        email: email,
        type: this.type || 'verification',
        table: this.for
      },)

          .then((response) => {

            if (response && response.status == 202) {
              this.showToast('success', this.__('email_verification_sent'));
              this.startTimer();
            }
            if (response && response.status == 204) {
              this.showToast('success', this.__('email_verified_before'));
            }
          })
          .catch((error) => {
            console.log(error);
            error = this.getErrors(error);

            this.showToast('danger', error);
          })
          .finally(() => {
            // always executed
            this.loading = false;
          });
    },
    startTimer() {
      this.timer = 60;
      const intervalId = setInterval(function () {
        this.timer--;
        if (this.timer <= 0) {
          clearInterval(intervalId);
          this.timer = 60
        }
      }.bind(this), 1000);
    }
  },
}
</script>