<script>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import {Head, Link, useForm} from '@inertiajs/vue3';
import {
  UserIcon,
  EyeIcon,
  EyeSlashIcon,
  KeyIcon,
  Bars2Icon,
  ChatBubbleBottomCenterTextIcon,

} from "@heroicons/vue/24/outline";
import {ref,} from 'vue'
import PhoneFields from "@/Components/PhoneFields.vue";
import LoadingIcon from "@/Components/LoadingIcon.vue";

import {usePage,} from "@inertiajs/vue3";


export default {
  data() {
    return {
      form: useForm({
        fullname: this.$page.props.auth.user ? this.$page.props.auth.user.fullname : null,
        title: null,
        description: null,
        phone: usePage().props.auth.user ? usePage().props.auth.user.phone : null,
        phone_verify: null,
        cmnd: 'create-order',
        items: [],
      })
    }
  },
  components: {
    LoadingIcon,
    PhoneFields,
    TextInput,
    PrimaryButton,
    InputLabel,
    InputError,
    GuestLayout,
    Checkbox,
    Bars2Icon,
    Head,
    ChatBubbleBottomCenterTextIcon,
  },
  methods: {
    submit() {

      this.form.clearErrors();

      this.form.post(route('project.create'), {
        onError: () => {
          this.showToast('danger', Object.values(this.form.errors).join("<br/>"));
        },
        onFinish: () => null,
      });
    },
  }
}
</script>

<template>
  <GuestLayout :dir="dir()"

               aria-expanded="false"
  >
    <Head :title="__('signin')"/>

    <div class="mb-4 font-medium text-sm text-primary-500">
      {{ __('new_project_order_help') }}
    </div>

    <form @submit.prevent="submit">

      <div v-if="user" class="border rounded p-2">
        <div class="flex flex-col">
          <div class="flex items-center">
            <span class="mx-2 text-primary"> {{ user.fullname }}</span>
            <span class="mx-2 text-primary"> {{ user.phone }}</span>
          </div>
        </div>
      </div>
      <div v-else class="border rounded p-2">
        <div class="my-2">
          <TextInput
              id="fullname"
              type="text"
              :disabled="null"
              :placeholder="__('fullname')"
              classes="grow  "
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
          <div class="my-2">
            <PhoneFields
                v-model:phone="form.phone"
                v-model:phone-verify="form.phone_verify"
                :phone-error="form.errors.phone"
                for="users"
                :disable="null"
                :disableEdit="null"
                :phone-verify-error="form.errors.phone_verify"
            />
          </div>
        </div>
      </div>

      <div class="my-2">
        <TextInput
            id="title"
            type="text"
            :placeholder="__('article_title')"
            classes="grow  "
            v-model="form.title"
            autocomplete="title"
            :error="form.errors.title"
        >
          <template v-slot:prepend>
            <div class="p-3">
              <Bars2Icon class="h-5 w-5"/>
            </div>
          </template>

        </TextInput>
        <div class="my-2">
          <TextInput
              :multiline="true"
              id="description"
              type="text"
              :placeholder="__('description')"
              classes=""
              v-model="form.description"
              autocomplete="description"
              :error="form.errors.description"
          >
            <template v-slot:prepend>
              <div class="p-3">
                <ChatBubbleBottomCenterTextIcon class="h-5 w-5"/>
              </div>
            </template>

          </TextInput>

        </div>
      </div>


      <div class="my-4 border rounded-lg">
        <div class="p-2 border-b">{{ __('select_items_need_in_article') }}</div>
        <div v-for="(item,idx) in $page.props.items" class="inline-flex items-center py-3">
          <label
              class="relative flex items-center    pe-1 ps-3 rounded-full cursor-pointer "
              :for="`access${idx}`"
              data-ripple-dark="true"
          >
            <input :value="item" v-model="form.items"
                   :checked="form.items.indexOf(item)>-1"
                   :id="`access${idx}`"
                   type="checkbox"
                   class="before:content[''] peer relative h-5 w-5 cursor-pointer appearance-none rounded-md border border-blue-gray-200 transition-all before:absolute before:top-2/4 before:left-2/4 before:block before:h-12 before:w-12 before:-translate-y-2/4 before:-translate-x-2/4 before:rounded-full before:bg-blue-gray-500 before:opacity-0 before:transition-opacity checked:border-primary-500 checked:bg-primary-500 checked:before:bg-primary-500 hover:before:opacity-10"
            />

          </label>
          <label
              class="mt-px font-light text-sm text-gray-500 cursor-pointer select-none"
              :for="`access${idx}`"
          >
            {{ __(item) }}
          </label>
        </div>
        <InputError class="mt-1" :message="form.errors.accesses"/>
      </div>


      <div class="    mt-4">

        <PrimaryButton class="w-full    " :class="{ 'opacity-25': form.processing }"
                       :disabled="form.processing">
          <LoadingIcon class="w-4 h-4 mx-3 " v-if="  form.processing"/>
          <span class=" text-lg  ">  {{ __('register_info') }}</span>
        </PrimaryButton>

      </div>

    </form>
  </GuestLayout>
</template>
