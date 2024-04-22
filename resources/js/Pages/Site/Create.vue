<template>

  <Scaffold navbar-theme="light">
    <template v-slot:header>
      <title>{{__('new_site')}}</title>
    </template>


    <!-- Content header -->
    <div
        class="flex items-center justify-start sm:mx-2 md:mx-8  text-primary-500 border-b  pt-24 pb-4">
      <FolderPlusIcon class="h-7 w-7 mx-3"/>

      <h1 class="text-2xl font-semibold">{{ __('new_site') }}</h1>

    </div>

    <!-- Content -->
    <div class="px-2  md:px-4">

      <div
          class="lg:grid      lg:grid-cols-3  mx-auto md:max-w-5xl   mt-6 px-2 md:px-4 py-4 bg-white shadow-md overflow-hidden  rounded-lg  ">
        <div class="flex-col self-center  m-2 items-center rounded-lg max-w-xs   mx-auto lg:mx-2   ">
          <ImageUploader ref="imageCropper" :label="__('image_jpg')" cropRatio="1.25" id="img"
                         height="10" class="grow"/>
          <InputError class="mt-1" :message="form.errors.img"/>
        </div>
        <div
            class="flex flex-col mx-auto   col-span-2 w-full md:max-w-xl lg:border-s px-2"
        >
          <form @submit.prevent="submit">

            <div class="flex items-center">
              <Tooltip class="p-2 " :content="__('help_lang')">
                <QuestionMarkCircleIcon class="text-gray-500 hover:bg-gray-50 w-4 h-4"/>
              </Tooltip>
              <RadioGroup ref="langSelector" class="grow" name="lang" :items="$page.props.langs"/>
            </div>
            <div class="my-2">
              <TextInput
                  id="fullname"
                  type="text"
                  :placeholder="__('fullname')"
                  classes="  "
                  v-model="form.fullname"
                  autocomplete="fullname"
                  :error="form.errors.fullname"
              >
                <template v-slot:prepend>
                  <div class="p-3">
                    <UserIcon class="h-5 w-5"/>
                  </div>
                </template>

              </TextInput>

            </div>
            <div class="my-2">
              <TextInput
                  id="password"
                  type="password"
                  :placeholder="__('password')"
                  classes="  "
                  v-model="form.password"
                  autocomplete="password"
                  :error="form.errors.password"
              >
                <template v-slot:prepend>
                  <div class="p-3">
                    <KeyIcon class="h-5 w-5"/>
                  </div>
                </template>

              </TextInput>

            </div>
            <div class="my-2">
              <PhoneFields
                  v-model:phone="form.phone"
                  v-model:phone-verify="form.phone_verify"
                  for="users"
                  :phone-error="form.errors.phone"
                  :phone-verify-error="form.errors.phone_verify"
              />
            </div>

            <div class="my-2">
              <TextInput
                  id="name"
                  type="text"
                  :placeholder="__('title')"
                  classes="  "
                  v-model="form.name"
                  autocomplete="name"
                  :error="form.errors.name"
              >
                <template v-slot:prepend>
                  <div class="p-3">
                    <Bars2Icon class="h-5 w-5"/>
                  </div>
                </template>

              </TextInput>

            </div>
            <div class="my-2">
              <TextInput
                  id="link"
                  type="text"
                  :placeholder="__('link')"
                  classes="  "
                  v-model="form.link"
                  autocomplete="link"
                  :error="form.errors.link"
              >
                <template v-slot:prepend>
                  <div class="p-3">
                    <LinkIcon class="h-5 w-5"/>
                  </div>
                </template>

              </TextInput>

            </div>
            <div class="my-2">
              <Selector ref="categorySelector" v-model="form.category_id" :data="$page.props.categories"
                        :label="__('category')"
                        id="category_id">
                <template v-slot:append>
                  <div class="  p-3">
                    <Squares2X2Icon class="h-5 w-5"/>
                  </div>
                </template>
              </Selector>
            </div>
            <div class="my-2">
              <TagInput
                  id="tags"
                  :placeholder="__('tags')"
                  classes="  "
                  v-model="form.tags"
                  autocomplete="tags"
                  :error="form.errors.tags"
              >
              </TagInput>
            </div>
            <div class="my-2">
              <TextInput
                  :multiline="true"
                  id="description"
                  type="text"
                  :placeholder="__('description')"
                  classes="  "
                  v-model="form.description"
                  autocomplete="description"
                  :error="form.errors.description"
              >
                <template v-slot:prepend>
                  <div class="p-3">
                    <LinkIcon class="h-5 w-5"/>
                  </div>
                </template>

              </TextInput>

            </div>

            <div class="    mt-4">

              <PrimaryButton class="w-full  "
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


  </Scaffold>
</template>

<script>
import Scaffold from "@/Layouts/Scaffold.vue";
import Panel from "@/Layouts/Panel.vue";
import {Head, Link, useForm} from "@inertiajs/vue3";
import {
  ChevronDownIcon,
  HomeIcon,
  UserIcon,
  EyeIcon,
  FolderPlusIcon,
  Bars2Icon,
  LinkIcon,
  Squares2X2Icon,
  KeyIcon,

} from "@heroicons/vue/24/outline";
import {QuestionMarkCircleIcon,} from "@heroicons/vue/24/solid";
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import RadioGroup from '@/Components/RadioGroup.vue'
import LoadingIcon from "@/Components/LoadingIcon.vue";
import Popover from "@/Components/Popover.vue";
import Tooltip from "@/Components/Tooltip.vue";
import TagInput from "@/Components/TagInput.vue";
import ImageUploader from "@/Components/ImageUploader.vue";
import Selector from "@/Components/Selector.vue";
import PhoneFields from "@/Components/PhoneFields.vue";

export default {

  data() {
    return {

      form: useForm({
        img: '',
        lang: null,
        name: null,
        link: null,
        category_id: null,
        tags: '',
        description: '',
        fullname: '',
        phone: '',
        phone_verify: '',
        password: '',

      }),
    }
  },
  components: {
    Scaffold,
    ImageUploader,
    LoadingIcon,
    Head,
    Link,
    HomeIcon,
    ChevronDownIcon,
    Panel,
    InputLabel,
    TextInput,
    InputError,
    PrimaryButton,
    RadioGroup,
    UserIcon,
    EyeIcon,
    Checkbox,
    Popover,
    Tooltip,
    FolderPlusIcon,
    Bars2Icon,
    LinkIcon,
    TagInput,
    QuestionMarkCircleIcon,
    Selector,
    Squares2X2Icon,
    PhoneFields,
    KeyIcon,

  },
  mounted() {

  },
  methods: {
    submit() {
      this.form.img = this.$refs.imageCropper.getCroppedData();
      this.form.lang = this.$refs.langSelector.selected;
      this.form.clearErrors();

      this.form.post(route('site.create'), {
        preserveScroll: false,
        onFinish: (data) => {
        },
        onSuccess: (data) => {
          this.showAlert(this.$page.props.flash.status, this.$page.props.flash.message);
          this.form.reset();

        },
        onError: () => this.showToast('danger', Object.values(this.form.errors).join("<br/>"))
      });
    }
  },

}
</script>
