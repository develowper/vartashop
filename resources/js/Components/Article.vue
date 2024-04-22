<template>
  <div v-if="mode!='view'" class="rounded border border-dashed p-2">
    <div class="flex items-center">
      <Tooltip class="p-2 " :content="__('help_article_content')">
        <QuestionMarkCircleIcon class="text-gray-500 hover:bg-gray-50 w-4 h-4"/>
      </Tooltip>
      <InputLabel class="my-2" :value="placeholder"/>
    </div>
    <div v-for="(d,idx) in cells" class="my-2 " :key="`${d.key}` ">

      <div class="flex">
        <div class="grow">
          <div v-if="  d.type==null"
               class="h-24 border border-dashed border-gray-400 border-2 rounded flex flex-wrap">
            <div @click="d.type='content';d.id= Date.now()"
                 class="grow hover:bg-gray-200 cursor-pointer flex flex-col items-center justify-center">
              <div>{{ __('text') }}</div>
            </div>
            <div class="grow hover:bg-gray-200 cursor-pointer flex flex-col items-center justify-center"
                 @click="showFilesModal(idx)">
              <div>{{ __('file') }}</div>
            </div>
            <div class="  hover:bg-danger-200 cursor-pointer flex flex-col items-center justify-center"
                 @click="clear(idx)">
              <XMarkIcon class="w-6 h-6 m-2  text-gray text-danger"/>

            </div>
          </div>
          <div v-if="  d.type=='content'">
            <TextEditor mode="create" :preload="d.value" :lang="$page.props.locale" :id="`editor-${d.id}`"
                        :ref="`text-${d.id}`"/>
          </div>
          <div v-if="d.type=='text'   " class="flex   h-full  ">

            <div class="border border-gray-400 rounded p-2 text-gray-500  w-full  " v-html="d.value"></div>

          </div>
          <div v-if="d.type=='podcast'" class="flex items-center justify-center h-full">
            <Podcast classes="  rounded  " :for-id="idx"
                     :preload="{name: d.value,
                 url:route(`storage.${d.type}s`)+`/${d.id}.mp3`,
                 cover:route(`storage.${d.type}s`)+`/${d.id}.jpg`}"
                     view="linear" mode="multi"
                     :ref="`podcast-${d.id}`"
            />
          </div>
          <div v-if="d.type=='video'" class="  ">
            <Video classes="  rounded w-full h-64 " :for-id="idx"
                   :preload="{name: d.value, url:route(`storage.${d.type}s`)+`/${d.id}.mp4`}"
                   view="linear" mode="view"
                   :ref="`video-${d.id}`"/>
          </div>
          <div v-if="d.type=='banner'" class="flex justify-center max-w-full">
            <Banner classes="rounded  object-contain     " :for-id="idx"
                    :preload="{name: d.value, url:route(`storage.${d.type}s`)+`/${d.id}.jpg`}"
                    view="linear" mode="view"
                    :ref="`banner-${d.id}`"/>
          </div>

          <InputError class="mt-1" :message="error"/>
        </div>
        <div v-if=" (mode=='edit' || mode=='create') &&  d.type" class="flex flex-col ms-1  w-100   " role="group">
          <div
              :title="__('move_up')"
              class="text-center flex rounded-t  grow  cursor-pointer bg-primary-500 hover:bg-primary-600  p-2 bg-danger text-white   "
              @click="move('up',idx)">
            <ChevronUpIcon class="w-4 h-4  mx-auto text-white  "/>
          </div>
          <div
              class="text-center flex   grow  cursor-pointer hover:bg-danger-600  p-2 bg-danger text-white   "
              :title="__('remove')"
              @click="clear(idx) ">
            <XMarkIcon class="w-4 h-4  mx-auto text-white  " v-if="!removing"/>
          </div>
          <div
              :title="__('move_down')"
              class="text-center flex rounded-b  grow  cursor-pointer bg-primary-500 hover:bg-primary-600  p-2 bg-danger text-white   "
              @click="move('down',idx)">
            <ChevronDownIcon class="w-4 h-4  mx-auto text-white  "/>
          </div>
        </div>
      </div>
      <div v-if="idx==cells.length-1  " @click="cells.push({key: Date.now(),type:null,id:null,value:null}); "
           class="mt-2 border border-dashed border-gray-400 border-2 rounded flex flex-wrap text-center cursor-pointer hover:bg-gray-200">
        <PlusIcon class="w-6 h-6  mx-auto  m-2 text-gray-500"/>
      </div>

    </div>
    <!--Modal  -->
    <div
        data-te-modal-init
        class="fixed left-0 top-0 backdrop-blur z-[1055] hidden h-full w-full overflow-y-auto overflow-x-hidden outline-none"
        id="modalFiles"
        tabindex="-1"
        aria-labelledby="exampleModalLabel"
        aria-hidden="true">
      <div
          data-te-modal-dialog-ref
          class="pointer-events-none relative w-auto translate-y-[-50px] opacity-0 transition-all duration-300 ease-in-out p-5 md:p-10 ">
        <div
            class="min-[576px]:shadow-[0_0.5rem_1rem_rgba(#000, 0.15)] pointer-events-auto relative flex w-full flex-col rounded-md border-none bg-white bg-clip-padding text-current shadow-lg outline-none dark:bg-neutral-600">
          <div
              class="flex flex-shrink-0 items-center justify-between rounded-t-md border-b-2 border-neutral-100 border-opacity-100 p-4 dark:border-opacity-50">
            <!--Modal title-->
            <h5
                class="text-xl font-medium leading-normal text-neutral-800 dark:text-neutral-200"
                id="exampleModalLabel">

            </h5>
            <!--Close button-->
            <button

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
            <div class="px-2 flex flex-col   md:px-4">

              <div class="flex-col   bg-white  overflow-x-auto shadow-lg  rounded-lg">
                <div class="flex flex-wrap  items-center justify-between py-4 dark:bg-gray-800 p-4">

                  <!--              Dropdown Paginate-->
                  <div class="flex items-center p-1">
                    <div class="relative mx-1  " data-te-dropdown-ref>
                      <button
                          id="dropdownPaginate"
                          data-te-dropdown-toggle-ref
                          aria-expanded="false"
                          data-te-ripple-init
                          data-te-ripple-color="light"
                          class=" inline-flex items-center text-gray-500 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 font-medium rounded-lg text-sm px-3 py-1.5 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700">

                        <span class="sr-only">table actions</span>
                        <span>{{ params.paginate }}</span>
                        <ChevronDownIcon class="h-4 w-4 mx-1"/>
                      </button>

                      <!--     menu -->
                      <div ref="userMenu" data-te-dropdown-menu-ref
                           class="min-w-[12rem] absolute z-[1000] start-0 text-gray-500  m-0 hidden min-w-max list-none overflow-hidden rounded-lg border-none bg-white bg-clip-padding text-start text-base shadow-lg dark:bg-neutral-700 [&[data-te-dropdown-show]]:block"
                           tabindex="-1" role="menu" aria-orientation="vertical" aria-label="User menu"
                           aria-labelledby="dropdownPaginate">
                        <div v-for=" num in $page.props.pageItems " class="">
                          <div @click="params.paginate=num;getData()" role="menuitem"
                               class=" cursor-pointer  select-none block  p-2 px-6 text-sm   transition-colors hover:bg-gray-100 dark:text-light dark:hover:bg-primary">
                            {{ num }}
                          </div>
                          <hr class="border-gray-200 dark:border-gray-700 ">
                        </div>
                      </div>
                    </div>

                    <!--                Paginate-->
                    <Pagination @paginationChanged="paginationChanged" :pagination="pagination"/>
                  </div>

                  <div class="relative p-1">
                    <label for="table-search" class="sr-only">Search</label>
                    <div
                        class="absolute inset-y-0 cursor-pointer text-gray-500 hover:text-gray-700  start-0 flex items-center px-3  ">
                      <MagnifyingGlassIcon @click=" getData() " class="w-4 h-4  dark:text-gray-400"/>
                    </div>
                    <div
                        class="absolute inset-y-0 end-0 text-gray-500 flex items-center px-3 cursor-pointer hover:text-gray-700  "
                        @click="params.search=null; getData() ">
                      <XMarkIcon class="w-4 h-4  dark:text-gray-400"/>
                    </div>
                    <input type="text" id="table-search-users" v-model="params.search"
                           @keydown.enter.prevent="getData()"
                           class="block p-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg w-80 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                           :placeholder="__('search')">
                  </div>
                </div>
                <!--           table-->
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                  <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                  <!--         table header-->
                  <tr class="text-sm text-center">

                    <th scope="col"
                        class="px-2 py-3   cursor-pointer duration-300 hover:text-gray-500 hover:scale-[105%]"
                        @click="params.order_by='name';params.dir=params.dir=='ASC'? 'DESC':'ASC'; params.page=1;getData()">
                      <div class="flex items-center justify-center">
                        <span class="px-2">  {{ __('name') }}</span>
                        <ArrowsUpDownIcon class="w-4 h-4 "/>
                      </div>
                    </th>

                    <th scope="col"
                        class="px-2 py-3   cursor-pointer duration-300 hover:text-gray-500 hover:scale-[105%]"
                        @click="params.order_by='view';params.dir=params.dir=='ASC'? 'DESC':'ASC'; params.page=1;getData()">
                      <div class="flex items-center justify-center">
                        <span class="px-2">    {{ __('type') }} </span>
                        <ArrowsUpDownIcon class="w-4 h-4 "/>
                      </div>
                    </th>


                  </tr>
                  </thead>
                  <tbody class=" ">
                  <tr v-if="loading" v-for="i in 3"
                      class="animate-pulse bg-white text-center border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                    <td class="w-4 p-4">
                      <div class="flex items-center">
                        <input id="checkbox-table-search-1" type="checkbox"
                               class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 dark:focus:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">

                      </div>
                    </td>
                    <td
                        class="flex  items-center px-6 py-4 text-gray-900 whitespace-nowrap dark:text-white">
                      <div class="w-10 h-10 rounded-full"
                      />
                      <div class="px-3">
                        <div class="text-base bg-gray-200 px-5 py-2 rounded-lg  "></div>
                        <div class="font-normal text-gray-500"></div>
                      </div>
                    </td>
                    <td class="px-2 py-4 ">
                      <div class="bg-gray-200 px-5 py-2 rounded-lg">

                      </div>
                    </td>

                  </tr>
                  <tr v-for="(d,idx) in data"
                      class="cursor-pointer hover:bg-gray-400 bg-white text-center border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600"
                      @click="selectItem(d)"
                  >

                    <td
                        class="flex  items-center px-6 py-4 text-gray-900 whitespace-nowrap dark:text-white">
                      <Image class="w-10 h-10 rounded-full"
                             :src="d.type !='text'?`${ route(`storage.${d.type}s`)}/${d.id}.jpg`:``"
                             :alt="cropText(d.name,5)"/>
                      <div class="text-base font-semibold">{{ cropText(d.name, 40) }}</div>
                      <div class="font-normal text-gray-500">{{ }}</div>
                    </td>

                    <td class="px-2 py-4">
                      {{ __(d.type) }}
                    </td>

                  </tr>

                  </tbody>
                </table>

              </div>

            </div>
          </div>

          <!--Modal footer-->
          <div
              class="flex flex-shrink-0 flex-wrap items-center justify-end rounded-b-md border-t-2 border-neutral-100 border-opacity-100 p-4 dark:border-opacity-50">

          </div>
        </div>
      </div>
    </div>
  </div>

  <div v-else>
    <div v-for="(d,idx) in cells" class="my-2 " :key="`${d.key}` ">

      <div class="flex my-4">
        <div class="grow">
          <div v-if="mode=='create' && d.type==null"
               class="h-24 border border-dashed border-gray-400 border-2 rounded flex flex-wrap">
            <div @click="d.type='content';d.id= Date.now()"
                 class="grow hover:bg-gray-200 cursor-pointer flex flex-col items-center justify-center">
              <div>{{ __('text') }}</div>
            </div>
            <div class="grow hover:bg-gray-200 cursor-pointer flex flex-col items-center justify-center"
                 @click="showFilesModal(idx)">
              <div>{{ __('file') }}</div>
            </div>
            <div class="  hover:bg-danger-200 cursor-pointer flex flex-col items-center justify-center"
                 @click="clear(idx)">
              <XMarkIcon class="w-6 h-6 m-2  text-gray text-danger"/>

            </div>
          </div>
          <div v-if="d.type=='text' || d.type=='content'">
            <div v-html="d.value"></div>
          </div>
          <div v-if="d.type=='podcast'" class="flex items-center justify-center h-full">
            <Podcast classes="  rounded  " :for-id="idx"
                     :preload="{name: d.value,
                 url:route(`storage.${d.type}s`)+`/${d.id}.mp3`,
                 cover:route(`storage.${d.type}s`)+`/${d.id}.jpg`}"
                     view="linear" mode="multi"
                     :ref="`podcast-${d.id}`"
            />
          </div>
          <div v-if="d.type=='video'" class="  ">
            <Video classes="  rounded w-full h-64 " :for-id="idx"
                   :preload="{name: d.value, url:route(`storage.${d.type}s`)+`/${d.id}.mp4`}"
                   view="linear" mode="view"
                   :ref="`video-${d.id}`"/>
          </div>
          <div v-if="d.type=='banner'" class="flex justify-center max-w-full">
            <Banner classes="rounded  object-contain     " :for-id="idx"
                    :preload="{name: d.value, url:route(`storage.${d.type}s`)+`/${d.id}.jpg`}"
                    view="linear" mode="view"
                    :ref="`banner-${d.id}`"/>
          </div>

        </div>
      </div>


    </div>
  </div>
</template>

<script>
import {Modal} from "tw-elements";
import Pagination from "@/Components/Pagination.vue";
import {
  ArrowsUpDownIcon,
  MagnifyingGlassIcon,
  ChevronDownIcon,
  ChevronUpIcon,
  PlusIcon,

} from "@heroicons/vue/24/outline";
import {
  QuestionMarkCircleIcon
} from "@heroicons/vue/24/solid";
import Image from "@/Components/Image.vue"
import TextEditor from "@/Components/TextEditor.vue";
import Tooltip from "@/Components/Tooltip.vue";
import {XMarkIcon, CheckIcon,} from "@heroicons/vue/24/solid";
import LoadingIcon from "@/Components/LoadingIcon.vue";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import Podcast from "@/Components/Podcast.vue";
import Video from "@/Components/Video.vue";
import Banner from "@/Components/Banner.vue";

export default {
  name: "ArticleCell",
  props: ['mode', 'ownerId', 'placeholder', 'error'],
  components: {
    Banner,
    Video,
    Podcast,
    TextEditor,
    ArrowsUpDownIcon,
    XMarkIcon,
    MagnifyingGlassIcon,
    ChevronDownIcon,
    ChevronUpIcon,
    Pagination,
    Image,
    CheckIcon,
    LoadingIcon,
    Tooltip,
    PlusIcon,
    InputLabel,
    InputError,
    QuestionMarkCircleIcon,

  },
  data() {
    return {
      cells: [{key: Date.now(), type: null, id: null, value: null}],
      params: {
        page: 1,
        search: null,
        paginate: this.$page.props.pageItems[0],
        order_by: null,
        dir: 'DESC',
        owner_id: this.ownerId || this.$page.props.auth.user ? this.$page.props.auth.user.id : null
      },
      Modal: null,
      data: [],
      pagination: {},
      loading: false,
      removing: false,
      uploading: false,
      selectingForIndex: null,
    }
  },
  mounted() {
    if (this.mode == 'view') return;
    const modalEl = document.getElementById('modalFiles');
    this.Modal = new Modal(modalEl);
    this.getData();
  },
  methods: {
    move(type, idx) {
      let tmp;
      if (type == 'up' && idx > 0) {
        tmp = this.cells[idx - 1];

        this.cells[idx - 1] = this.cells[idx];
        this.cells[idx] = tmp;


      } else if (type == 'down' && idx < this.cells.length - 1) {
        tmp = this.cells[idx + 1];

        this.cells[idx + 1] = this.cells[idx];
        this.cells[idx] = tmp;

      }

      // this.log(this.cells);
    },
    clear(idx) {
      this.cells.splice(idx, 1);
      if (this.cells.length == 0)
        this.cells.push({key: Date.now(), type: null, id: null, value: null,});
    },
    selectItem(item) {
      this.cells[this.selectingForIndex].type = item.type;
      this.cells[this.selectingForIndex].value = item.name;
      this.cells[this.selectingForIndex].id = item.id;

      this.selectingForIndex = null;
      this.Modal.hide();
    },
    showFilesModal(selectingForIndex) {
      this.selectingForIndex = selectingForIndex;
      this.Modal.show();
    },
    getData() {

      this.loading = true;
      this.data = [];
      window.axios.get(route('panel.merged.search'), {
        params: this.params
      }, {})
          .then((response) => {
            // console.log(response.data.data)
            this.data = response.data.data;

            delete response.data.data;
            this.pagination = response.data;

          })

          .catch((error) => {
            if (error.response) {
              console.log(error.response.data);
              console.log(error.response.status);
              console.log(error.response.headers);
              this.error = error.response.data;

            } else if (error.request) {
              console.log(error.request);
              this.error = error.request;
            } else {
              console.log('Error', error.message);
              this.error = error.message;
            }
            console.log(error.config);
            this.showToast('danger', error)
          })
          .finally(() => {
            this.loading = false;
          });
    },
    paginationChanged(data) {

      this.params.page = data.page;
      this.getData();
    },
    getContent() {
      let res = [];
      for (let i in this.cells) {
        res.push({id: this.cells[i].id, type: this.cells[i].type, value: this.cells[i].value,});
        if (res[i].type == 'content') {
          res[i].value = this.$refs[`text-${res[i].id}`][0].getData();
        }
      }
      return res;
    },
    setContent(data) {
      // console.log(data)
      if (!data || data == undefined) return null;
      data = JSON.parse(data);
      this.cells = [];
      for (let i in data) {
        this.cells.push({key: Date.now() + i, id: data[i].id, type: data[i].type, value: data[i].value,});
        this.$nextTick(() => {

          if (data[i].type == 'content') {
          }
        });
      }

    }
  }
}
</script>

<style scoped>

</style>