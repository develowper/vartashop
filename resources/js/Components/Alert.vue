<template>

  <div v-show="showing" id="alert"
       :class="`bg-${status}-100  text-${status}-700`"
       class="cursor-pointer flex  z-[999999]  mb-3 fixed top-0 start-0 end-0  duration-500 transition-all w-full items-center justify-around   px-6 py-5     data-[te-alert-show]:inline-flex"
       role="alert"
       data-te-alert-init
       data-te-delay="500"
       data-te-autohide="false"
       @click="hide"
  >
    <strong class="mx-1" v-html="message"> </strong>
    <button
        @click="hide"
        :class="`bg-${status}-100 text-${status} hover:text-${status}-900` "
        type="button"
        class="ms-auto box-content rounded-none border-none p-1   opacity-50  hover:no-underline hover:opacity-75 focus:opacity-100 focus:shadow-none focus:outline-none"

        aria-label="Close">
    <span
        class="w-[1em] focus:opacity-100 disabled:pointer-events-none disabled:select-none disabled:opacity-25 [&.disabled]:pointer-events-none [&.disabled]:select-none [&.disabled]:opacity-25">
      <svg
          xmlns="http://www.w3.org/2000/svg"
          viewBox="0 0 24 24"
          fill="currentColor"
          class="h-6 w-6">
        <path
            fill-rule="evenodd"
            d="M5.47 5.47a.75.75 0 011.06 0L12 10.94l5.47-5.47a.75.75 0 111.06 1.06L13.06 12l5.47 5.47a.75.75 0 11-1.06 1.06L12 13.06l-5.47 5.47a.75.75 0 01-1.06-1.06L10.94 12 5.47 6.53a.75.75 0 010-1.06z"
            clip-rule="evenodd"/>
      </svg>
    </span>
    </button>
  </div>
</template>
<script>
import {Alert, initTE,} from "tw-elements";

export default {
  data() {
    return {
      alert: null,
      status: 'primary',
      message: null,
      showing: false,
    }
  },
  props: [],
  methods: {
    show(status, message) {
      this.status = status;
      this.message = message;
      if (message && message != undefined) {
        this.showing = true;
        this.$nextTick(() => {
          window.Alert.show();
        });
      }

    },
    hide() {

      window.Alert.hide();
      this.showing = false;
    },
  },
  mounted() {


  }
}
</script>
