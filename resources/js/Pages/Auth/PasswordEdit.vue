<template>
  <GuestLayout class="" :dir="dir()">
    <template v-slot:header>
      <title>{{__('edit_password')}}</title>
    </template>


    <!-- Content header -->
    <div
        class="flex items-center justify-start px-4 py-2 text-primary-500 border-b md:py-4">
      <PencilSquareIcon class="h-7 w-7 mx-3"/>

      <h1 class="text-2xl font-semibold">{{ __('edit_password') }}</h1>

    </div>

    <!-- Content -->
    <div class="px-2   ">


      <div
          class="lg:grid      lg:grid-cols-3  mx-auto md:max-w-5xl   mt-6 px-2 md:px-4 py-4   overflow-hidden  rounded-lg  ">
        <div
            class="lg:flex-col  flex flex-wrap   self-center  md:m-2  lg:mx-2 md:items-center lg:items-stretch rounded-lg    ">
          <!--            <InputLabel class="m-2 w-full md:text-start lg:text-center"-->
          <!--                        :value="__('profile_images_max_%s_item').replace('%s',$page.props.max_images_limit)"/>-->


        </div>
        <div
            class="flex flex-col mx-2   col-span-2 w-full   lg:border-s px-2"
        >
          <form @submit.prevent="submit">


            <div class="my-2">
              <PhoneFields
                  v-model:phone="form.phone"
                  v-model:phone-verify="form.phone_verify"
                  :phone-error="form.errors.phone"
                  type="forget"
                  :for="form.is_admin?'admins':'users'"
                  :verified="null"
                  :activeButtonText="__('send_code')"
                  :disable="null"
                  :disableEdit="null"
                  :phone-verify-error="form.errors.phone_verify"
              />
            </div>


            <div class="my-2">
              <TextInput
                  id="new_password"
                  type="password"
                  :placeholder="__('new_password')"
                  classes="  "
                  v-model="form.new_password"
                  :autocomplete="form.new_password"
                  :error="form.errors.new_password"
              >
                <template v-slot:prepend>
                  <div class="p-3">
                    <KeyIcon class="h-5 w-5"/>
                  </div>
                </template>

              </TextInput>

            </div>
            <div class="my-2">
              <TextInput
                  id="new_password_confirmation"
                  type="password"
                  :placeholder="__('confirm_password')"
                  classes="  "
                  v-model="form.new_password_confirmation"
                  :autocomplete="form.new_password_confirmation"
                  :error="form.errors.new_password_confirmation"
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


  </GuestLayout>
</template>
<script>
import Panel from "@/Layouts/Panel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import LoadingIcon from "@/Components/LoadingIcon.vue";
import PhoneFields from "@/Components/PhoneFields.vue";
import TextInput from "@/Components/TextInput.vue";
import {
  PencilSquareIcon,
  KeyIcon,
} from "@heroicons/vue/24/outline";
import {useForm} from "@inertiajs/vue3";
import GuestLayout from "@/Layouts/GuestLayout.vue";

export default {
  data() {
    return {
      data: this.data || {},
      form: useForm({
        cmnd: 'password-reset',
        new_password: null,
        new_password_confirmation: null,
        phone: null,
        phone_verify: null,
        is_admin: route().current('admin.password.request')
      }),
    }
  },
  components: {
    GuestLayout,
    Panel,
    PencilSquareIcon,
    PrimaryButton,
    LoadingIcon,
    PhoneFields,
    TextInput,
    KeyIcon,
  },
  created() {
    this.data = this.$page.props.auth.user;
  },
  mounted() {

    // console.log(this.data);

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
      this.form.post(route(`${this.form.is_admin ? 'admin.' : ''}password.update`), {
        preserveScroll: false,

        onSuccess: (data) => {
          if (this.$page.props.flash.status)
            this.showAlert(this.$page.props.flash.status, this.$page.props.flash.message);

          this.form.new_password = null;
          this.form.new_password_confirmation = null;
          this.form.phone_verify = null;
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