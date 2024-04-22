<template>

  <!-- Button trigger modal -->
  <!--  <button-->
  <!--      type="button"-->
  <!--      class="inline-block rounded bg-primary px-6 pb-2 pt-2.5 text-xs font-medium uppercase leading-normal text-white shadow-[0_4px_9px_-4px_#3b71ca] transition duration-150 ease-in-out hover:bg-primary-600 hover:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.3),0_4px_18px_0_rgba(59,113,202,0.2)] focus:bg-primary-600 focus:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.3),0_4px_18px_0_rgba(59,113,202,0.2)] focus:outline-none focus:ring-0 active:bg-primary-700 active:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.3),0_4px_18px_0_rgba(59,113,202,0.2)] dark:shadow-[0_4px_9px_-4px_rgba(59,113,202,0.5)] dark:hover:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.2),0_4px_18px_0_rgba(59,113,202,0.1)] dark:focus:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.2),0_4px_18px_0_rgba(59,113,202,0.1)] dark:active:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.2),0_4px_18px_0_rgba(59,113,202,0.1)]"-->
  <!--      data-te-toggle="modal"-->
  <!--      data-te-target="#exampleModal"-->
  <!--      data-te-ripple-init-->
  <!--      data-te-ripple-color="light">-->
  <!--    Launch demo modal-->
  <!--  </button>-->

  <!-- Modal -->
  <div
      data-te-modal-init
      class="fixed left-0 top-0 backdrop-blur z-[10550] hidden h-full w-full overflow-y-auto overflow-x-hidden outline-none"
      id="modal"
      tabindex="-1"
      aria-labelledby="exampleModalLabel"
      aria-hidden="true">
    <div
        data-te-modal-dialog-ref
        class="pointer-events-none relative w-auto translate-y-[-50px] opacity-0 transition-all duration-300 ease-in-out min-[576px]:mx-auto min-[576px]:mt-7 min-[576px]:max-w-[500px]">
      <div
          class="min-[576px]:shadow-[0_0.5rem_1rem_rgba(#000, 0.15)] pointer-events-auto relative flex w-full flex-col rounded-md border-none bg-white bg-clip-padding text-current shadow-lg outline-none dark:bg-neutral-600">
        <div
            class="flex flex-shrink-0 items-center justify-between rounded-t-md border-b-2 border-neutral-100 border-opacity-100 p-4 dark:border-opacity-50">
          <!--Modal title-->
          <h5
              class="text-xl font-medium leading-normal text-neutral-800 dark:text-neutral-200"
              id="exampleModalLabel">

          </h5>
          <!--Close button-->
          <button
              :class="`text-${status}`"
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
        <div class="relative flex-auto p-4" v-html="message" :class="`text-${status}`" data-te-modal-body-ref>

        </div>

        <!--Modal footer-->
        <div
            class="flex flex-shrink-0 flex-wrap items-center justify-end rounded-b-md border-t-2 border-neutral-100 border-opacity-100 p-4 dark:border-opacity-50">
          <button
              :class="`text-gray  bg-gray-100 hover:bg-gray-200`"
              type="button"
              class="inline-block rounded-s   px-6 pb-2 pt-2.5 text-xs font-medium   leading-normal   transition duration-150 ease-in-out    focus:outline-none focus:ring-0  "
              data-te-modal-dismiss
              data-te-ripple-init
              data-te-ripple-color="light">
            {{ __('cancel') }}
          </button>
          <button
              :class="`bg-${status} hover:bg-${status}-600`"
              type="button"
              class="ml-1 rounded-e inline-block    px-6 pb-2 pt-2.5 text-xs font-medium uppercase leading-normal text-white shadow-[0_4px_9px_-4px_#3b71ca] transition duration-150 ease-in-out   hover:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.3),0_4px_18px_0_rgba(59,113,202,0.2)]   focus:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.3),0_4px_18px_0_rgba(59,113,202,0.2)] focus:outline-none focus:ring-0  active:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.3),0_4px_18px_0_rgba(59,113,202,0.2)] dark:shadow-[0_4px_9px_-4px_rgba(59,113,202,0.5)] dark:hover:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.2),0_4px_18px_0_rgba(59,113,202,0.1)] dark:focus:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.2),0_4px_18px_0_rgba(59,113,202,0.1)] dark:active:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.2),0_4px_18px_0_rgba(59,113,202,0.1)]"
              data-te-ripple-init
              data-te-modal-dismiss
              @click="items ? action(items):action()"
              data-te-ripple-color="light">
            {{ button }}

          </button>
        </div>
      </div>
    </div>
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
      button: null,
      action: null,
      items: null,
    }
  },
  props: [],
  methods: {
    show(status, message, button, action, items = null) {

      this.status = status;
      this.message = message;
      this.button = button;
      this.action = action;
      this.items = items;

      if (message && message != undefined)
        this.$nextTick((e) => {
          window.Modal.show();
        });

    },
    hide() {
      window.Modal.hide();
    },
  },
  mounted() {


  }
}
</script>
