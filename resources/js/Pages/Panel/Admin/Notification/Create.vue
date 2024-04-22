<template>

  <Panel>
    <template v-slot:header>
      <title>{{__('new_notification')}}</title>
    </template>


    <template v-slot:content>
      <!-- Content header -->
      <div
          class="flex items-center justify-start px-4 py-2 text-primary-500 border-b md:py-4">
        <FolderPlusIcon class="h-7 w-7 mx-3"/>

        <h1 class="text-2xl font-semibold">{{ __('new_notification') }}</h1>

      </div>


      <div class="px-2  md:px-4">

        <div
            class="    mx-auto md:max-w-3xl   mt-6 px-2 md:px-4 py-4 bg-white shadow-md overflow-hidden  rounded-lg  ">


          <div
              class="flex flex-col mx-2   col-span-2 w-full     px-2"
          >

            <form @submit.prevent="submit">


              <div class="my-2">
                <p class="text-sm text-gray-500 flex items-center">
                  {{ __('user') }}
                  <Tooltip class="p-2 " :content="__('empty_for_send_to_all')">
                    <QuestionMarkCircleIcon class="text-gray-500 hover:bg-gray-50 w-4 h-4"/>
                  </Tooltip>
                </p>
                <UserSelector v-model:selected="form.owner_id">
                  <template v-slot:selector="props">
                    <div :class="props.selectedText?'py-2':'py-4'"
                         class=" px-4 border rounded hover:bg-gray-100 cursor-pointer flex items-center ">
                      <div class="grow">
                        {{ props.selectedText }}
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
                            :ref="`text`"/>
                <p class="text-danger" v-if="form.errors.description"> {{ form.errors.description }} </p>
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
  PlusIcon,
  XMarkIcon,
  Bars2Icon,
  ChatBubbleBottomCenterTextIcon,
  Squares2X2Icon,
  SignalIcon,
  PencilIcon,
  LinkIcon,


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
import UserSelector from "@/Components/UserSelector.vue";


export default {

  data() {
    return {

      form: useForm({
        link: null,
        subject: null,
        description: null,
        owner_id: null,
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
    LinkIcon,
    UserSelector,

  },
  mounted() {
    // this.log(this.$page.props)
  },
  methods: {
    submit() {


      this.form.description = this.$refs.text.getData();
      // this.form.category_id = this.$refs.categorySelector.selected;
      this.form.clearErrors();

      // this.isLoading(true, this.form.progress ? this.form.progress.percentage : null);

      this.form.post(route('panel.admin.notification.create'), {
        preserveScroll: false,

        onSuccess: (data) => {

          // this.showAlert(this.$page.props.flash.status, this.$page.props.flash.description);
          // else {
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
