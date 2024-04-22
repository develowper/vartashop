<script>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import {Head, Link} from '@inertiajs/vue3';
import mitt from 'mitt'
import Alert from "@/Components/Alert.vue";
import Dialog from "@/Components/Dialog.vue";
import Toast from "@/Components/Toast.vue";
import LoadingIcon from "@/Components/LoadingIcon.vue";

export const emitter = mitt()

export default {
  // emits: ['showToast'],
  components: {
    Head, Link, ApplicationLogo, Alert, Dialog, Toast, LoadingIcon,
  },
  mounted() {
    window.tailwindElements();
    this.emitter.on('showToast', (e) => {
      if (this.$refs.toast)
        this.$refs.toast.show(e.type, e.message);
    });

    this.emitter.on('showAlert', (e) => {
      if (this.$refs.alert)
        this.$refs.alert.show(e.type, e.message);
    });

    this.emitter.on('showDialog', (e) => {

      if (this.$refs.modal)
        this.$refs.modal.show(e.type, e.message, e.button, e.action, e.items);
    });

    this.emitter.on('loading', (e) => {
      this.loading = e;
    });
  }
}

</script>

<template>
  <div>
    <Alert ref="alert"/>
    <Toast ref="toast"/>

    <div class="px-2 min-h-screen flex flex-col sm:justify-center items-center   pt-2 bg-gray-100">
      <div>
        <Link href="/">
          <ApplicationLogo type="outline-dark" class="w-25 h-20 fill-current text-gray-500"/>
          <div v-if="route().current('admin.login-form')" class="w-full   text-end">
            {{ __('admin_portal') }}
          </div>
        </Link>
      </div>

      <div
          class="w-full sm:max-w-lg z-0   mt-6 px-6 py-4 bg-white shadow-md overflow-hidden  rounded-lg"
      >
        <slot/>
      </div>
    </div>
  </div>
</template>
