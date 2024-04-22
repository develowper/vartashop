<template>

  <Panel>
    <template v-slot:header>
      <title>{{__('new_video')}}</title>
    </template>


    <template v-slot:content>
      <!-- Content header -->
      <div
          class="flex items-center justify-start px-4 py-2 text-primary-500 border-b md:py-4">
        <FolderPlusIcon class="h-7 w-7 mx-3"/>

        <h1 class="text-2xl font-semibold">{{ __('new_video') }}</h1>

      </div>

      <!-- Content -->
      <div class="px-2  md:px-4">

        <div
            class="lg:grid      lg:grid-cols-3  mx-auto md:max-w-5xl   mt-6 px-2 md:px-4 py-4 bg-white shadow-md overflow-hidden  rounded-lg  ">
          <div
              class="lg:flex-col  flex flex-wrap   self-center  md:m-2  lg:mx-2 md:items-center lg:items-stretch rounded-lg    ">
            <!--            <InputLabel class="m-2 w-full md:text-start lg:text-center"-->
            <!--                        :value="__('video_images_max_%s_item').replace('%s',$page.props.max_images_limit)"/>-->

            <div class="flex-col   m-2 items-center rounded-lg max-w-xs  w-full mx-auto lg:mx-2   ">
              <div class="my-2">
                <ImageUploader ref="imageCropper" :label="__('image_cover_jpg')" cropRatio="1.25" id="img"
                               height="10" class="grow "/>
                <InputError class="mt-1 " :message="form.errors.img"/>
              </div>
              <div class="my-2">
                <Video :lang="$page.props.locale" for-id="video" view="linear" mode="create" ref="video"
                       :label="__('video_file_mp4')" classes="w-full h-64"/>
                <InputError class="mt-1 " :message="form.errors.video"/>
              </div>
            </div>

          </div>
          <div
              class="flex flex-col mx-2   col-span-2 w-full   lg:border-s px-2"
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
                <Selector ref="categorySelector" :data="$page.props.categories" :label="__('category')"
                          id="category_id" v-model="form.category_id">
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
                      <ChatBubbleBottomCenterTextIcon class="h-5 w-5"/>
                    </div>
                  </template>

                </TextInput>

              </div>
              <div v-if="form.progress" class="shadow w-full bg-grey-light m-2   bg-gray-200 rounded-full">
                <div
                    class=" bg-primary rounded  text-xs leading-none py-[.1rem] text-center text-white duration-300 "
                    :class="{' animate-pulse': form.progress.percentage <100}"
                    :style="`width: ${form.progress.percentage }%`">
                  <span class="animate-bounce">{{ form.progress.percentage }}</span>
                </div>
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
    </template>


  </Panel>
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
  ChatBubbleBottomCenterTextIcon,
  Squares2X2Icon,
  SignalIcon,

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
import ProvinceCounty from "@/Components/ProvinceCounty.vue";
import PhoneFields from "@/Components/PhoneFields.vue";
import SocialFields from "@/Components/SocialFields.vue";
import Video from "@/Components/Video.vue";


export default {

  data() {
    return {

      form: useForm({
        lang: null,
        name: null,
        socials: null,
        category_id: null,
        tags: '',
        description: '',

      }),
      img: null,
      video: null,
      duration: null,

    }
  },
  components: {
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
    ChatBubbleBottomCenterTextIcon,
    TagInput,
    QuestionMarkCircleIcon,
    Selector,
    Squares2X2Icon,
    ProvinceCounty,
    PhoneFields,
    SocialFields,
    SignalIcon,
    Video,

  },
  mounted() {
    // this.log(this.$page.props)
  },
  methods: {
    submit() {
      this.img = this.$refs.imageCropper.getCroppedData();
      this.video = this.$refs.video.file;
      this.duration = parseInt(this.$refs.video.getDuration());
      this.form.uploading = false;
      this.form.lang = this.$refs.langSelector.selected;
      // this.form.category_id = this.$refs.categorySelector.selected;
      this.form.clearErrors();

      // this.isLoading(true, this.form.progress ? this.form.progress.percentage : null);

      this.form.post(route('video.create'), {
        preserveScroll: false,

        onSuccess: (data) => {

          if (!this.form.uploading) {
            this.form.uploading = true;

            this.form.transform((data) => ({
              ...data,
              uploading: true,
              img: this.img,
              video: this.video,
              duration: this.duration,
            }))
                .post(route('video.create'), {
                  preserveScroll: false,
                  onSuccess: (data) => {

                    // else {
                    //   this.showAlert(this.$page.props.flash.status, this.$page.props.flash.message);
                    //   this.form.reset();
                    // }
                  },
                  onError: () => {

                    this.showToast('danger', Object.values(this.form.errors).join("<br/>"));
                  },
                  onFinish: (data) => {
                    // this.isLoading(false,);
                  },
                });
          }
          // else {
          //   this.showAlert(this.$page.props.flash.status, this.$page.props.flash.message);
          //   this.form.reset();
          // }
        },
        onError: () => {
          this.showToast('danger', Object.values(this.form.errors).join("<br/>"));
        },
        onFinish: (data) => {
          // this.isLoading(false,);
        },
      });
    }
  },
  watch: {},
}
</script>
