<template>

  <Panel>
    <template v-slot:header>
      <title>{{__('ticket')}}</title>
    </template>


    <template v-slot:content>
      <!-- Content header -->
      <div
          class="flex items-center justify-start px-4 py-2 text-primary-500 border-b md:py-4">
        <PencilSquareIcon class="h-7 w-7 mx-3"/>

        <h1 class="text-2xl font-semibold">{{ __('ticket') + " " + (data ? data.id : '') }}</h1>

      </div>


      <div class="px-2  md:px-4">

        <div
            class="     mx-auto md:max-w-3xl   mt-6  py-4  overflow-hidden   ">


          <div v-if="data"
               class="flex flex-col mx-2  space-y-2  col-span-2 w-full     px-2"
          >

            <div class="flex flex-col space-y-2    p-4 rounded-lg   bg-white shadow-md">
              <div class="">
                <span class="text-primary-700 text-lg font-bold ">{{ data.subject }}</span>
              </div>
              <div class="">
                <span class="text-gray-500">{{ __('status') }}:</span>
                <span class=" font-bold mx-2"
                      :class="`text-${getStatus('ticket', data.status).color}-700`">
                {{ __(data.status) }}
                </span>
              </div>
              <div class="">
                <span class="text-gray-500">{{ __('id') }}:</span>
                <span class="text-primary-700 font-bold mx-2">#{{ data.id }}</span>
              </div>
              <div class="">
                <span class="text-gray-500">{{ __('created_at') }}:</span>
                <span class="text-primary-700 font-bold mx-2">{{ toShamsi(data.created_at, true) }}</span>
              </div>
              <div class=" grid items-center grid-cols-1 sm:grid-cols-2 gap-2">
                <button type="button" @click="modal.show()"
                        data-te-toggle="modal"
                        data-te-target="#newTicketModal"
                        data-te-ripple-init
                        class="grow flex items-center justify-center   py-1 cursor-pointer px-2 bg-success hover:bg-success-400 text-white rounded">
                  {{ __('response') }}
                  <ChatBubbleBottomCenterTextIcon class="h-4 w-4 mx-2 text-white"/>
                </button>

                <button type="button" v-if="data.status!='closed'"
                        @click="showDialog('danger',__('close_ticket?'), __('close_ticket') , closeTicket ) "
                        class="grow flex items-center justify-center py-1 cursor-pointer px-2 bg-danger hover:bg-danger-400 text-white rounded">
                  {{ __('close') }}
                  <XMarkIcon class="h-4 w-4 mx-2 text-white"/>
                </button>
                <div v-else class="text-sm text-gray-400">{{ __('send_response_for_open_ticket') }}</div>
              </div>
            </div>

            <div class="relative">
              <div v-for="(ch,idx) in data.chats" :key="ch.id"
                   class="flex flex-col   my-2   p-4 rounded-lg   bg-white shadow-md">
                <div v-if="isAdmin()"
                     @click="showDialog('danger',__('remove_item?'),__('remove'),removeMessage,{cmnd:'del-chat',ticket_id:data.id,chat_id:ch.id,idx:idx})"
                     class="absolute p-2 bg-danger text-white rounded cursor-pointer hover:bg-danger-400 end-1">
                  <XMarkIcon class="w-5 h-5"/>
                </div>
                <div class="flex  items-start "
                     :class="ch.owner && $page.props.auth.user.id==ch.owner.id?'':'flex-row-reverse'">
                  <div v-if="ch.owner"
                       class="flex space-y-2    min-h-[10rem] flex-col justify-between items-center mx-2 border rounded-lg p-2">
                    <Image :src="route('storage.users')+`/${ch.owner.id}.jpg`"
                           classes="object-cover rounded-full h-20   "/>
                    <div class="text-sm text-center text-white rounded px-2 py-1 w-full"
                         :class="`bg-${$page.props.auth.user.id==ch.owner.id?'danger':'primary'}`">{{
                        ch.owner.fullname
                      }}
                    </div>
                    <div class="text-xs text-gray-400">{{ ch.created_at }}</div>
                  </div>
                  <div class="grow m-2 text-gray-700" v-html="ch.message"></div>

                </div>

                <div v-for="(name,idx) in  $page.props.attachments.filter(e=>e.includes(`${ch.id}-`))"
                     class="mt-1">
                  <div v-if="idx==0" class="border-b my-4"></div>
                  <div v-if="idx==0" class="mt-2 text-gray-700">{{ __('attachments') }}</div>
                  <a target="_blank" class=" flex items-center hover:text-primary-400 "
                     :href="`${route('storage.tickets')}/${data.id}/${name}`">
                    <LinkIcon class="w-4 h-4 mx-2 font-bold"/>
                    <span>{{ name }}</span>
                  </a>
                </div>

              </div>
            </div>

          </div>


        </div>
      </div>
      <!-- Modal -->
      <div
          data-te-modal-init
          class="fixed left-0 top-0 backdrop-blur z-[1055] hidden h-full w-full overflow-y-auto overflow-x-hidden outline-none"
          id="newTicketModal"
          tabindex="-1"
          aria-labelledby="newTicketModalLabel"
          aria-hidden="true">
        <div
            data-te-modal-dialog-ref
            class="pointer-events-none relative w-auto translate-y-[-50px] opacity-0 transition-all duration-300 ease-in-out min-[576px]:mx-auto min-[576px]:mt-7 px-2 sm:px-4 md:px8 min-[576px]:max-w-5xl">
          <div
              class="min-[576px]:shadow-[0_0.5rem_1rem_rgba(#000, 0.15)] pointer-events-auto relative flex w-full flex-col rounded-md border-none bg-white bg-clip-padding text-current shadow-lg outline-none">
            <div
                class="flex flex-shrink-0 items-center justify-between rounded-t-md border-b-2 border-neutral-100 border-opacity-100 p-4">
              <!--Modal title-->
              <h5
                  class="text-xl font-medium leading-normal text-neutral-800"
                  id="newTicketModalLabel">

              </h5>
              <!--Close button-->
              <button
                  :class="`text-danger`"
                  type="button"
                  class="box-content rounded-none border-none hover:no-underline hover:opacity-75 focus:opacity-100 focus:shadow-none focus:outline-none"
                  data-te-modal-dismiss
                  aria-label="Close">
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="1.5"
                    stroke="currentColor"
                    class="h-6 w-6">
                  <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      d="M6 18L18 6M6 6l12 12"/>
                </svg>
              </button>
            </div>

            <!--Modal body-->
            <div class="relative flex-auto p-4" data-te-modal-body-ref>
              <div
                  class="flex items-center justify-start px-4 py-2 text-primary-500 border-b md:py-4">
                <FolderPlusIcon class="h-7 w-7 mx-3"/>

                <h1 class="text-2xl font-semibold">{{ __('new_message') }}</h1>

              </div>


              <div class="px-2  md:px-4">

                <div
                    class="    mx-auto md:max-w-3xl   mt-6 px-2 md:px-4 py-4 bg-white shadow-md overflow-hidden  rounded-lg  ">


                  <div
                      class="flex flex-col mx-2   col-span-2 w-full     px-2"
                  >

                    <form @submit.prevent="addMessage">


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

                        <PrimaryButton class="w-full justify-center items-center flex "
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
            </div>


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
  PlusIcon,
} from "@heroicons/vue/24/outline";
import {QuestionMarkCircleIcon,} from "@heroicons/vue/24/solid";
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import LoadingIcon from "@/Components/LoadingIcon.vue";
import Popover from "@/Components/Popover.vue";
import Tooltip from "@/Components/Tooltip.vue";
import Selector from "@/Components/Selector.vue";
import TextEditor from "@/Components/TextEditor.vue";
import Image from "@/Components/Image.vue";

import {
  Modal,

} from "tw-elements";


export default {

  data() {
    return {
      modal: null,
      data: null,
      params: {ticket_id: this.$page.props.data.id,},
      form: useForm({}),
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
    UserIcon,
    EyeIcon,
    Popover,
    Tooltip,
    FolderPlusIcon,
    Bars2Icon,
    QuestionMarkCircleIcon,
    Selector,
    Squares2X2Icon,
    PencilSquareIcon,
    SignalIcon,
    ChatBubbleBottomCenterTextIcon,
    PencilIcon,
    TextEditor,
    XMarkIcon,
    PlusIcon,
    LinkIcon,
    Image,
  },
  created() {

  },
  mounted() {

    this.data = this.$page.props.data;
    // console.log(this.data.chats);

    const modalEl = document.getElementById('newTicketModal');
    this.modal = new Modal(modalEl);

  },
  methods: {
    addMessage() {
      let tmp = document.querySelectorAll("input[type=file]");
      let attachments = [];
      for (let idx in tmp)
        if (tmp[idx].files && tmp[idx].files.length > 0)
          attachments.push(tmp[idx].files[0]);

      this.params = {
        cmnd: 'add-chat',
        id: this.data.id,
        message: this.$refs.text.getData(),
        attachments: attachments,
        _method: 'patch',
      };
      this.submit();
    },
    removeMessage(params) {
      params._method = 'patch';
      this.submit(params)
    },
    closeTicket() {
      this.params = {
        cmnd: 'close',
        id: this.data.id,
        _method: 'patch',
      };
      this.submit();
    },

    submit(params) {
      // this.form.category_id = this.$refs.categorySelector.selected;
      this.form.clearErrors();
      this.form = useForm(params || this.params);
      // this.isLoading(true, this.form.progress ? this.form.progress.percentage : null);
      // this.images = [];
      // for (let i = 0; i < this.$page.props.max_images_limit; i++) {
      //   let tmp = this.$refs.imageCropper[i].getCroppedData();
      //   if (tmp) this.images.push(tmp);
      // }
      this.form.post(route(`panel.ticket.update`), {
        preserveScroll: false,

        onSuccess: (data) => {
          if (this.$page.props.flash.status)
            this.showAlert(this.$page.props.flash.status, this.$page.props.flash.message);
          if (this.$page.props.data.status)
            this.data.status = this.$page.props.data.status;
          if (this.$page.props.data.chats)
            this.data.chats = this.$page.props.data.chats;

          this.$nextTick(e => {
            this.modal.hide();
            this.files = [];
            this.$refs.text.setData('');

          });

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
