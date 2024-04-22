<template>

  <Panel>
    <template v-slot:header>
      <title>{{__('new_ticket')}}</title>
    </template>


    <template v-slot:content>
      <!-- Content header -->
      <div
          class="flex items-center justify-start px-4 py-2 text-primary-500 border-b md:py-4">
        <FolderPlusIcon class="h-7 w-7 mx-3"/>

        <h1 class="text-2xl font-semibold">{{ __('new_ticket') }}</h1>

      </div>


      <div class="px-2  md:px-4">

        <div
            class="    mx-auto md:max-w-3xl   mt-6 px-2 md:px-4 py-4 bg-white shadow-md overflow-hidden  rounded-lg  ">


          <div
              class="flex flex-col mx-2   col-span-2 w-full     px-2"
          >

            <form @submit.prevent="submit">


              <div class="my-2">
                <TextInput
                    id="subject"
                    type="text"
                    :placeholder="__('subject')"
                    classes="  "
                    v-model="form.subject"
                    autocomplete="subject"
                    :error="form.errors.subject"
                >
                  <template v-slot:prepend>
                    <div class="p-3">
                      <Bars2Icon class="h-5 w-5"/>
                    </div>
                  </template>

                </TextInput>

              </div>


              <!--              <div class="my-2">-->
              <!--                <Selector ref="categorySelector" :data="$page.props.categories" :label="__('category')"-->
              <!--                          id="category_id" v-model="form.category_id">-->
              <!--                  <template v-slot:append>-->
              <!--                    <div class="  p-3">-->
              <!--                      <Squares2X2Icon class="h-5 w-5"/>-->
              <!--                    </div>-->
              <!--                  </template>-->
              <!--                </Selector>-->
              <!--              </div>-->


              <div class="my-2">
                <TextEditor :label="__('message')" mode="simple" :lang="$page.props.locale" :id="`editor`"
                            :ref="`text`" :error="form.errors.message"/>
              </div>

              <div v-if="form.progress" class="shadow w-full bg-grey-light m-2   bg-gray-200 rounded-full">
                <div
                    class=" bg-primary rounded  text-xs leading-none py-[.1rem] text-center text-white duration-300 "
                    :class="{' animate-pulse': form.progress.percentage <100}"
                    :style="`width: ${form.progress.percentage }%`">
                  <span class="animate-bounce">{{ form.progress.percentage }}</span>
                </div>
              </div>
              <div class="my-4 flex flex-col">
                <div class="flex items-center   ">
                  <div class="text-gray-700 my-2 ">{{ __('attachments') }}</div>
                </div>
                <div class="border-b w-full mb-2"></div>
                <div v-for="(f,idx) in files">
                  <div class="flex justify-between items-center">
                    <input :id="`attachment-${idx}`" class="w-full my-1  "
                           :accept="$page.props.attachment_allowed_mimes"
                           type="file"
                           name="attachments"/>
                    <XMarkIcon @click="files.splice(idx,1);    "
                               class="w-6 h-6 text-danger cursor-pointer   hover:scale-[112%] hover:text-danger-500 "/>
                  </div>
                </div>
                <div
                    class="flex w-fit text-sm items-center text-white cursor-pointer bg-success hover:bg-success-500 rounded p-2 my-2"
                    @click="files.push(null)"
                >
                  <PlusIcon class="h-4 w-4 mx-1"/>
                  {{ __('add_more') }}
                </div>
              </div>
              <div class="    mt-4">

                <PrimaryButton class="w-full   flex justify-center items-center"
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
  PlusIcon,
  XMarkIcon,
  Bars2Icon,
  ChatBubbleBottomCenterTextIcon,
  Squares2X2Icon,
  SignalIcon,
  PencilIcon,


} from "@heroicons/vue/24/outline";
import {QuestionMarkCircleIcon,} from "@heroicons/vue/24/solid";
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import Tooltip from '@/Components/Tooltip.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import RadioGroup from '@/Components/RadioGroup.vue'
import LoadingIcon from "@/Components/LoadingIcon.vue";
import Popover from "@/Components/Popover.vue";
import Selector from "@/Components/Selector.vue";
import TextEditor from "@/Components/TextEditor.vue";


export default {

  data() {
    return {

      form: useForm({
        subject: null,
        message: null,

      }),
      files: [null],

    }
  },
  components: {
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
    QuestionMarkCircleIcon,
    Selector,
    Squares2X2Icon,
    SignalIcon,
    PencilIcon,
    TextEditor,
    PlusIcon,
    XMarkIcon,

  },
  mounted() {
    // this.log(this.$page.props)
  },
  methods: {
    submit() {
      let tmp = document.querySelectorAll("input[type=file]");
      let attachments = [];
      for (let idx in tmp)
        if (tmp[idx].files && tmp[idx].files.length > 0)
          attachments.push(tmp[idx].files[0]);

      this.form.message = this.$refs.text.getData();
      this.form.uploading = false;
      // this.form.category_id = this.$refs.categorySelector.selected;
      this.form.clearErrors();

      // this.isLoading(true, this.form.progress ? this.form.progress.percentage : null);

      this.form.post(route(`${this.isAdmin() ? 'admin' : 'user'}.panel.ticket.create`), {
        preserveScroll: false,

        onSuccess: (data) => {

          if (!this.form.uploading) {
            this.form.uploading = true;

            this.form.transform((data) => ({
              ...data,
              uploading: true,
              attachments: attachments,
            }))
                .post(route(`${this.isAdmin() ? 'admin' : 'user'}.panel.ticket.create`), {
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
