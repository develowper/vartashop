<template>

  <Panel>
    <template v-slot:header>
      <title>{{__('edit_article')}}</title>
    </template>


    <template v-slot:content>
      <!-- Content header -->
      <div
          class="flex items-center justify-start px-4 py-2 text-primary-500 border-b md:py-4">
        <PencilSquareIcon class="h-7 w-7 mx-3"/>

        <h1 class="text-2xl font-semibold">{{ __('edit_article') }}</h1>

      </div>


      <div class="px-2  md:px-4">

        <div
            class="    mx-auto md:max-w-5xl   mt-6 px-2 md:px-4 py-4 bg-white shadow-md overflow-hidden  rounded-lg  ">


          <div
              class="flex flex-col mx-2   col-span-2 w-full     px-2"
          >
            <div class="flex-col   m-2 items-center rounded-lg max-w-xs  w-full mx-auto    ">
              <div class="my-2">
                <ImageUploader :replace="$page.props.max_images_limit==1"
                               :preload="route('storage.articles')+`/${$page.props.data.id}.jpg`"
                               mode="edit" :for-id="$page.props.data.id"
                               :link="route('panel.admin.article.update')"
                               ref="imageCropper" :label="__('image_cover_jpg')" cropRatio="1.25" id="img"
                               height="10" class="grow "/>
                <InputError class="mt-1 " :message="form.errors.img"/>
              </div>

            </div>
            <form @submit.prevent="submit">

              <div class="my-2">
                <RadioGroup :beforeSelected="form.status" ref="statusSelector" class="grow" name="status"
                            v-model="form.status"
                            :items="myMap($page.props.statuses,(e)=>e.name)"/>
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
                    id="author"
                    type="text"
                    :placeholder="__('author')"
                    classes="  "
                    v-model="form.author"
                    autocomplete="author"
                    :error="form.errors.author"
                >
                  <template v-slot:prepend>
                    <div class="p-3">
                      <PencilIcon class="h-5 w-5"/>
                    </div>
                  </template>

                </TextInput>

              </div>


              <div class="my-2">
                <TagInput
                    id="tags"
                    ref="tags"
                    :placeholder="__('tags')"
                    classes="  "
                    v-model="form.tags"
                    autocomplete="tags"
                    :error="form.errors.tags"
                >
                </TagInput>
              </div>


              <!--                article content-->
              <div class="my-2">
                <TextEditor mode="edit" lang="fa" :id="`editor`" :preload="data.content"
                            :ref="`text`" :label="__('article_content')"
                            :error="form.errors.content"/>

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
  LinkIcon,
  Squares2X2Icon,
  PencilSquareIcon,
  SignalIcon,
  ChatBubbleBottomCenterTextIcon,
  PencilIcon,
  XMarkIcon,
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
import Article from "@/Components/Article.vue";
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
        lang: null,
        title: null,
        author: null,
        link: null,
        category_id: null,
        socials: null,
        tags: null,
        content: null,
        summary: '',
        message: '',
        status: this.$page.props.data.status,

      }),
      img: null,
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
    LinkIcon,
    TagInput,
    QuestionMarkCircleIcon,
    Selector,
    Squares2X2Icon,
    ProvinceCounty,
    PhoneFields,
    SocialFields,
    PencilSquareIcon,
    Article,
    SignalIcon,
    ChatBubbleBottomCenterTextIcon,
    PencilIcon,
    UserSelector,
    XMarkIcon,
  },
  created() {

  },
  mounted() {

    // console.log(this.data.content);


    this.form.title = this.data.title;
    this.form.author = this.data.author;
    this.form.phone = this.data.phone;
    this.form.link = this.data.link;
    this.$refs.tags.set(this.data.tags);
    this.form.status = this.data.status == 'active' ? 'active' : 'inactive';
    this.$refs.statusSelector.selected = this.form.status;
    this.form.message = this.data.message;
    this.form.owner = this.data.owner;
    this.form.owner_id = this.data.owner_id;


  },
  methods: {
    submit() {


      this.form.content = this.$refs.text.getData();


      // this.form.category_id = this.$refs.categorySelector.selected;
      this.form.clearErrors();

      // this.isLoading(true, this.form.progress ? this.form.progress.percentage : null);
      // this.images = [];
      // for (let i = 0; i < this.$page.props.max_images_limit; i++) {
      //   let tmp = this.$refs.imageCropper[i].getCroppedData();
      //   if (tmp) this.images.push(tmp);
      // }
      this.form.patch(route('panel.admin.article.update'), {
        preserveScroll: false,

        onSuccess: (data) => {
          if (this.$page.props.flash.status)
            this.showAlert(this.$page.props.flash.status, this.$page.props.flash.message);

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
    },

  },

}
</script>
