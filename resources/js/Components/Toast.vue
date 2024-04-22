<template>
  <!--    Toast-->
  <div
      :class="`bg-${color}-100 text-${color}-700`"
      class=" z-[9999] fixed bottom-0 end-0 pointer-events-auto m-2   hidden w-96 max-w-full rounded-lg   bg-clip-padding text-sm   shadow-lg shadow-black/5 data-[te-toast-show]:block data-[te-toast-hide]:hidden"
      id="toast"
      role="alert"
      aria-live="assertive"
      data-te-position=""
      aria-atomic="true"
      data-te-autohide="true"
      data-te-toast-init
      data-te-class-fadeIn="animate-[fade-in_3s_both]"
  >
    <div :class="`border-b  border-${color}-300 bg-${type}-200 text-${type}`"
         class="flex items-center justify-between rounded-t-lg   bg-clip-padding px-4 py-1  ">
      <p :class="`text-${color}-700`" class="flex items-center font-bold  ">
        <ExclamationCircleIcon :class="`text-${type}`" class="h-4 w-4" v-if="type=='info'"/>

        <CheckCircleIcon :class="`text-${type}`" class="h-4 w-4" v-if="type=='success'"/>
        <ExclamationTriangleIcon :class="`text-${type}`" class="h-4 w-4" v-if="type=='warning'"/>
        <XCircleIcon :class="`text-${color}-700`" class="h-4 w-4" v-if="type=='danger'"/>

      </p>
      <div class="flex items-center">
        <p class="text-xs " :class="`text-${type}`"></p>
        <button
            type="button"
            class="ml-2 box-content rounded-none border-none opacity-80 hover:no-underline hover:opacity-75 focus:opacity-100 focus:shadow-none focus:outline-none"
            data-te-toast-dismiss
            aria-label="Close">
        <span
            class="w-[1em] focus:opacity-100 disabled:pointer-events-none disabled:select-none disabled:opacity-25 [&.disabled]:pointer-events-none [&.disabled]:select-none [&.disabled]:opacity-25">
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
        </span>
        </button>
      </div>
    </div>

    <div v-html="message"
         class="  rounded-b-lg   px-4 py-2  " :class="`text-${type} bg-${type}-200   border-t-${color}-900`">
    </div>
  </div>

</template>

<script>
import {initTE, Toast} from "tw-elements";

import {CheckCircleIcon, XCircleIcon, ExclamationCircleIcon, ExclamationTriangleIcon} from "@heroicons/vue/24/solid";

export default {

  data() {
    return {
      message: null,
      type: 'success',
      toast: null,
      color: 'primary'
    }

  },
  components: {CheckCircleIcon, XCircleIcon, ExclamationCircleIcon, ExclamationTriangleIcon},


  mounted() {
    // initTE({Toast});
    // this.toast = Toast.getInstance(document.getElementById('toast'))
  },
  // watch: {
  //     type: {
  //         handler(val) {
  //         },
  //         immediate: true,
  //     }
  // },
  methods: {
    show(type, message) {

      this.type = type || 'info';
      this.message = message;
      this.color = this.type == 'danger' ? 'danger' : this.type == 'success' ? 'success' : this.type == 'warning' ? 'orange' : 'primary';

      window.Toast.show();
      // this.toast.show();

    },
    success(text) {
      this.type = 'success';
      this.show(text);
    },
    danger(text) {
      this.type = 'danger';
      this.show(text);
    },
    warning(text) {
      this.type = 'danger';
      this.show(text);
    },
  },
}
</script>
