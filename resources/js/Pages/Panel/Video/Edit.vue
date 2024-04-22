<template>

  <Panel>
    <template v-slot:header>
      <title>{{__('edit_video')}}</title>
    </template>


    <template v-slot:content>
      <!-- Content header -->
      <div
          class="flex items-center justify-start px-4 py-2 text-primary-500 border-b md:py-4">
        <PencilSquareIcon class="h-7 w-7 mx-3"/>

        <h1 class="text-2xl font-semibold">{{ __('edit_video') }}</h1>

      </div>

      <!-- Content -->
      <div class="px-2  md:px-4">

        <div
            class="lg:grid      lg:grid-cols-3  mx-auto md:max-w-5xl   mt-6 px-2 md:px-4 py-4 bg-white shadow-md overflow-hidden  rounded-lg  ">

          <div v-if="isAdmin()" class="border bg-sky-50 border-dashed rounded p-4 col-span-3">
            <p class="border-b text-md w-fit flex mx-auto mb-2 text-primary font-bold ">{{ __('admin_section') }}</p>
            <div class="flex items-center">

              <RadioGroup :beforeSelected="form.status" ref="statusSelector" class="grow" name="status"
                          v-model="form.status"
                          :items="myMap($page.props.statuses,(e)=>e.name)"/>
            </div>
            <div v-show="form.status=='reject'">

              <InputLabel class="text-red-500" for="editor" :value="__('reject_message')"/>
              <TextEditor mode="create"
                          :preload="form.message"
                          :lang="$page.props.locale"
                          :id="`editor`"
                          :ref="`editor`"/>
            </div>
          </div>
          <div v-else-if="!isAdmin() && form.message" v-html="form.message"
               class="border text-sm text-rose-400 bg-rose-50 border-dashed rounded p-4 ">
          </div>

          <!--          project section-->
          <div v-if="form.project_item" class="my-2 border bg-indigo-50 border-dashed rounded p-4 col-span-3">
            <p class="border-b text-md w-fit flex mx-auto mb-2 text-primary font-bold ">{{ __('project_info') }}</p>

            <div class="flex flex-col text-primary text-sm space-y-1">
              <div class="flex items-center">
                <a v-if="isAdmin() " class="hover:text-gray-700"
                   :href="route('panel.project.edit',form.project_item.project_id)">{{ __('project') }}
                  {{ form.project_item.id }}</a>
                <div v-else>{{ __('project') }} {{ form.project_item.id }}</div>
              </div>
              <div class="flex items-center border-b py-1">
                <div class="text-gray-500">{{ __('operator') }}:</div>
                <div v-if="form.project_item.operator" class="mx-1">{{
                    form.project_item.operator.fullname
                  }} _{{ form.project_item.operator.phone }}
                </div>
              </div>
              <div class="flex items-center  border-b py-1">
                <div class="text-gray-500">{{ __('status') }}:</div>
                <div class="  min-w-[5rem]  px-1   items-center text-center rounded-md py-[.2rem]"
                     :class="`bg-${getStatus('project_statuses', form.project_item.status).color}-100   text-${getStatus('project_statuses', form.project_item.status).color}-500`">
                  {{ getStatus('project_statuses', form.project_item.status).name }}
                </div>
              </div>
              <div class="flex items-center  border-b py-1">
                <div class="text-gray-500">{{ __('commission') }}:</div>
                <div v-if="form.project_item.price" class="mx-1">
                  {{
                    asPrice(form.project_item.price)
                  }} {{ __('currency') }}
                </div>
              </div>
              <div class="flex items-center  border-b py-1">
                <div class="text-gray-500">{{ __('expire') }}:</div>
                <div v-if="form.project_item.expires_at" class="mx-1 text-red-600">
                  {{
                    toShamsi(form.project_item.expires_at, true)
                  }}
                </div>
              </div>
              <div class="flex items-center  border-b py-1">
                <div class="text-gray-500">{{ __('payed_at') }}:</div>
                <div v-if="form.project_item.payed_at" class="mx-1 text-green-600">
                  {{
                    toShamsi(form.project_item.payed_at, true)
                  }}
                </div>
              </div>
              <div class="flex items-center    py-1">
                <div v-if="form.project_item.chats"
                     class="mx-1 w-full text-gray-500 whitespace-pre-line   px-2 rounded">
                  <div v-for="(chat,idx) in JSON.parse(form.project_item.chats)">
                    <div v-if="idx==0">{{ chat.text }}</div>
                  </div>
                </div>
              </div>
              <button
                  @click="showDialog('danger',__('complete_project_and_pay_after_admin_review?') + `<br>${asPrice(form.project_item.price)} ${__('currency')}<br>${__('user')} ${ form.project_item.operator? `${form.project_item.operator.fullname} | ${form.project_item.operator.phone} ` :'?' } `,__('final_accept')  , submit,{cmnd:'operator-finish'   })"
                  :disabled="!isAdmin() &&  form.project_item.status!='progress'  "
                  :class="{'opacity-50':form.project_item.payed_at || form.project_item.status!='progress'}"
                  class="rounded py-2 text-white cursor-pointer bg-success hover:bg-success-400">
                {{
                  form.project_item.payed_at ? __('payed_at') + ` ${toShamsi(form.project_item.payed_at)} ` : __('finish_and_request_pay')
                }}
              </button>
            </div>
          </div>


          <div
              class="lg:flex-col  flex flex-wrap   self-center  md:m-2  lg:mx-2 md:items-center lg:items-stretch rounded-lg    ">
            <!--            <InputLabel class="m-2 w-full md:text-start lg:text-center"-->
            <!--                        :value="__('video_images_max_%s_item').replace('%s',$page.props.max_images_limit)"/>-->

            <div class="flex-col   m-2 items-center rounded-lg max-w-xs  w-full mx-auto lg:mx-2   ">
              <div class="my-2">
                <ImageUploader :replace="$page.props.max_images_limit==1"
                               :preload="route('storage.videos')+`/${$page.props.data.id}.jpg`"
                               mode="edit" :for-id="$page.props.data.id"
                               :link="route('video.update')"
                               ref="imageCropper" :label="__('image_cover_jpg')" cropRatio="1.25" id="img"
                               height="10" class="grow "/>
                <InputError class="mt-1 " :message="form.errors.img"/>
              </div>
              <div class="my-2">
                <Video
                    :lang="$page.props.locale" classes="w-full h-64"
                    :preload="{name:$page.props.data.name, url:route('storage.videos')+`/${$page.props.data.id}.mp4`}"
                    view="linear" mode="edit" :link="route('video.update')" :for-id="$page.props.data.id"
                    ref="video" :label="__('video_file_mp4')"/>
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
              <div class="my-2" v-if="isAdmin() && data">

                <UserSelector :id="'user'" v-model:selected="form.owner_id" :preload="data.owner"
                >
                  <template v-slot:selector="props">
                    <div :class="props.selectedText?'py-2':'py-2'"
                         class=" px-4 border rounded hover:bg-gray-100 cursor-pointer flex items-center ">
                      <div class="grow">
                        {{ props.selectedText ?? __('select_owner') }}
                      </div>
                      <div v-if="props.selectedText"
                           class="bg-danger rounded p-2   cursor-pointer text-white hover:bg-danger-400"
                           @click.stop="props.clear()">
                        <XMarkIcon class="w-5 h-5"/>
                      </div>
                    </div>
                  </template>
                </UserSelector>
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
                    ref="tags"
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
                               @click.prevent="isAdmin()? submit():  showDialog('primary',__('will_active_after_review'), __('ok') , submit )"
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
  SignalIcon, XMarkIcon,

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
import TextEditor from "@/Components/TextEditor.vue";
import UserSelector from "@/Components/UserSelector.vue";

export default {

  data() {
    return {
      data: this.$page.props.data || {},
      form: useForm({
        id: this.$page.props.data.id,
        owner_id: null,
        owner: null,
        phone: null,
        phone_verify: null,
        lang: null,
        name: null,
        link: null,
        category_id: null,
        socials: null,
        tags: null,
        description: '',
        message: '',
        status: this.$page.props.data.status,
      }),
      img: null,
      video: null,
      cmnd: null,

    }
  },
  components: {
    TextEditor,
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
    Video,
    SignalIcon,
    UserSelector,
    XMarkIcon,
  },
  created() {

  },
  mounted() {


    this.form.name = this.data.name;
    this.form.phone = this.data.phone;
    this.form.link = this.data.link;
    this.form.narrator = this.data.narrator;
    this.form.category_id = this.data.category_id;
    this.$refs.tags.set(this.data.tags);
    this.form.description = this.data.description;
    this.form.message = this.data.message;

    this.$refs.langSelector.selected = this.data.lang;
    this.form.project_item = this.data.project_item;
    this.form.owner = this.data.owner;
    this.form.owner_id = this.data.owner_id;
  },
  methods: {
    submit(params) {

      if (this.$refs.editor)
        this.form.message = this.$refs.editor.getData();

      this.form.lang = this.$refs.langSelector.selected;
      // this.form.category_id = this.$refs.categorySelector.selected;
      this.form.clearErrors();

      // this.isLoading(true, this.form.progress ? this.form.progress.percentage : null);
      // this.images = [];
      // for (let i = 0; i < this.$page.props.max_images_limit; i++) {
      //   let tmp = this.$refs.imageCropper[i].getCroppedData();
      //   if (tmp) this.images.push(tmp);
      // }
      this.form.transform((data) => ({
        ...data,
        ...params || {},
      })).patch(route('video.update'), {
        preserveScroll: false,

        onSuccess: (data) => {
          // if (this.$page.props.flash.status)
          //   this.showAlert(this.$page.props.flash.status, this.$page.props.flash.message);

        },
        onError: () => {
          this.showToast('danger', Object.values(this.form.errors).join("<br/>"));
        },
        onFinish: (data) => {
          // this.isLoading(false,);
          if (this.$page.props.extra) {
            if (this.$page.props.extra.project_status)
              this.form.project_item.status = this.$page.props.extra.project_status;
          }
          if (this.$page.props.flash.status)
            this.showAlert(this.$page.props.flash.status, this.$page.props.flash.message);
        },
      });
    }
  },

}
</script>
