<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import {Head, Link, useForm} from '@inertiajs/vue3';
import {UserIcon, EyeIcon, EyeSlashIcon} from "@heroicons/vue/24/outline";
import {ref} from 'vue'

defineProps({
  canResetPassword: Boolean,
  status: String,
});
let showPassword = ref(false);

const form = useForm({
  login: '',
  password: '',
  remember: false,

});

const submit = () => {
  form.post(route().current('admin.login-form') ? route('admin.login') : route('login'), {
    onFinish: () => form.reset('password'),
  });
};
</script>

<template>

  <GuestLayout :dir="dir()"

               aria-expanded="false"
  >

    <Head :title="__('signin')"/>

    <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
      {{ status }}
    </div>

    <form @submit.prevent="submit">
      <div>
        <InputLabel for="login" :value="__('phone')"/>

        <TextInput
            id="login"
            type="text"
            classes="  "
            v-model="form.login"
            required
            autofocus
            autocomplete="login">


          <template v-slot:prepend>
            <div class="p-3">
              <UserIcon class="h-5 w-5"/>
            </div>
          </template>

        </TextInput>

        <InputError class="mt-2" :message="form.errors.login"/>
      </div>

      <div class="mt-4">
        <InputLabel for="password" :value="__('password')"/>

        <TextInput
            id="password"
            :type="showPassword?'text':'password'"
            classes=" "
            v-model="form.password"
            required
            autocomplete="password">

          <template v-slot:prepend>
            <div class="p-3" @click="showPassword=!showPassword">
              <EyeIcon v-if="!showPassword"
                       class="h-5 w-5   "/>
              <EyeSlashIcon v-else class="h-5 w-5 "/>
            </div>
          </template>
        </TextInput>

        <InputError class="mt-2" :message="form.errors.password"/>
      </div>

      <div class="flex mt-4  items-center  justify-between">
        <label class="flex items-center">
          <Checkbox name="remember" v-model:checked="form.remember"/>
          <span class="m-2 text-sm text-gray-600">{{ __('remember_me') }}</span>
        </label>
        <Link
            v-if="canResetPassword"
            :href="route().current('admin.login-form') ?route('admin.password.request'):route('password.request')"
            class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
        >
          {{ __('forgot_my_password') }}
        </Link>
      </div>

      <div class=" relative   mt-4">

        <PrimaryButton class="w-full   " :class="{ 'opacity-25': form.processing }"
                       :disabled="form.processing">
          <span class=" text-lg    ">  {{ __('signin') }}</span>
        </PrimaryButton>

      </div>
      <div v-if="!route().current('admin.login-form')" class="w-full mt-5">
        <span>{{ __('not_have_account?') }}</span>
        <Link
            v-if="canResetPassword"
            :href="route('register')"
            class="underline mx-2 text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
        >
          {{ __('signup') }}
        </Link>
      </div>
      <div v-if="route().current('login')" class="w-full mt-5 text-end">
        <Link
            :href="route('admin.login-form')"
            class=" text-primary-600  mx-2 text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
        >
          {{ __('admin_portal') }}
        </Link>
      </div>
    </form>
  </GuestLayout>
</template>
