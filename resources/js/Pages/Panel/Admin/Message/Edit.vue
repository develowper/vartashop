<template>

  <Panel>
    <template v-slot:header>
      <title>{{__('create_slider_item')}}</title>
    </template>


    <template v-slot:content>
      <!-- Content header -->
      <div
          class="flex items-center justify-start px-4 py-2 text-primary-500 border-b md:py-4">
        <PencilSquareIcon class="h-7 w-7 mx-3"/>

        <h1 class="text-2xl font-semibold">{{ __('edit_slider_item') }}</h1>

      </div>

      <!-- Content -->
      <div v-if="data" class="px-2  md:px-4">


        <div
            class="lg:grid      lg:grid-cols-1  mx-auto md:max-w-5xl   my-6 px-2 md:px-4 py-4 bg-white shadow-md overflow-hidden  rounded-lg  ">

          <div
              class="flex flex-col mx-2   col-span-2 w-full    px-2"
          >
            <form @submit.prevent="submit">

              <div class="my-2  ">
                <ImageUploader :replace="true"
                               :preload="route('storage.slides')+`/${$page.props.data.id}.jpg`"
                               mode="edit" :for-id="data.id" :cropRatio="$page.props.sliderRatio"
                               :link="route('panel.admin.slider.update')"
                               ref="imageCropper" :label="__('image_cover_jpg')" cropRatio="1" id="img"
                               height="10" class="grow "/>
                <InputError class="mt-1 " :message="form.errors.img"/>
              </div>
              <div class="flex items-center">

                <RadioGroup v-model="form.status" ref="statusSelector" class="grow"
                            name="role"
                            :items="['active','inactive' ]"/>
              </div>
              <div class="my-4">
                <TextInput
                    id="title"
                    type="text"
                    :placeholder="__('title')"
                    classes="  "
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
  PencilSquareIcon,
  PaintBrushIcon,
  CreditCardIcon,
  AtSymbolIcon,
  PhoneIcon,
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
import ProvinceCounty from "@/Components/ProvinceCounty.vue";
import PhoneFields from "@/Components/PhoneFields.vue";
import SocialFields from "@/Components/SocialFields.vue";
import EmailFields from "@/Components/EmailFields.vue";

export default {

  data() {
    return {
      data: this.data || {},
      form: useForm({
        id: null,
        title: null,
        description: null,
        status: null,
        img: null,
        _method: 'PATCH',

      }),
    }
  },
  components: {
    EmailFields,
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
    PencilSquareIcon,
    PaintBrushIcon,
    CreditCardIcon,
    AtSymbolIcon,
    PhoneIcon,
    KeyIcon,

  },
  created() {
  },
  mounted() {
    this.data = this.$page.props.data;
    // console.log(this.data);

    this.form.id = this.data.id;
    this.form.title = this.data.title;
    this.form.description = this.data.description;
    this.form.status = this.data.is_active ? 'active' : 'inactive';
    this.$refs.statusSelector.selected = this.form.status;

    // console.log(this.form.status);


  },
  methods: {
    submit() {

      // this.form.category_id = this.$refs.categorySelector.selected;
      this.form.clearErrors();

      // this.isLoading(true, this.form.progress ? this.form.progress.percentage : null);

      this.form.post(route('panel.admin.slider.update'), {
        preserveScroll: false,

        onSuccess: (data) => {

          this.showAlert(this.$page.props.flash.status, this.$page.props.flash.message);
          // this.form.reset();

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

}
</script>
