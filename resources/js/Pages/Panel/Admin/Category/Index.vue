<template>

  <Panel>
    <template v-slot:header>
      <title>{{__('panel')}}</title>
    </template>


    <template v-slot:content>
      <!-- Content header -->
      <div
          class="flex items-center justify-between px-4 py-2 text-primary-500 border-b md:py-4">
        <div class="flex">
          <Bars2Icon class="h-7 w-7 mx-3"/>
          <h5 class="  font-semibold">{{ __('categories') }}</h5>
        </div>
        <div>
          <button @click="params.cmnd='add'; params.parent_id=null;params.name=null;modal.show()"
                  data-te-toggle="modal"
                  data-te-target="#createModal"
                  data-te-ripple-init
                  class="inline-flex items-center  justify-center px-4 py-2 bg-green-500 border border-transparent rounded-md font-semibold  transition-all duration-500 text-white     hover:bg-green-600 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
          >
            {{ __('new_category') }}
          </button>
        </div>
      </div>
      <!-- Content -->
      <div class="px-2 flex flex-col   md:px-4">

        <div class="flex-col p-2 md:p-4  bg-white  overflow-x-auto shadow-lg  rounded-lg">
          <Draggable class="mtl-tree" v-model="treeData" treeLine dir="ltr">
            <template #default="{ node, stat }">

              <OpenIcon
                  v-if="stat.children.length"
                  :open="stat.open"
                  class="mtl-mr"
                  @click.native="stat.open = !stat.open"
              />
              <input
                  class="mtl-checkbox mtl-mr"
                  type="checkbox" @change="node.status=stat.checked?'active':'inactive'"
                  v-model="node.checked"
              />
              <span
                  @click="params.cmnd='edit'; params.checked=node.checked;params.id=node.id;params.parent_id=node.parent_id;params.name=node.name; modal.show()"
                  class="mtl-ml p-1 select-none cursor-pointer">{{ node.name }}</span>
            </template>
          </Draggable>
        </div>

        <div class="    mt-4">

          <PrimaryButton @click="edit({tree_data:  treeData})" type="button"
                         class="w-full flex items-center justify-center"
                         :class="{ 'opacity-25': loading }"
                         :disabled="loading ">
            <LoadingIcon class="w-4 h-4 mx-3 " v-if="  loading"/>
            <span class=" text-lg  ">  {{ __('register_info') }} </span>
          </PrimaryButton>

        </div>
      </div>
    </template>


  </Panel>
  <!-- Modal -->
  <div
      data-te-modal-init
      class="fixed left-0 top-0 backdrop-blur z-[1055] hidden h-full w-full overflow-y-auto overflow-x-hidden outline-none"
      id="createModal"
      tabindex="-1"
      aria-labelledby="createModalLabel"
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
              id="createModalLabel">

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
        <div class="relative flex-auto p-4" data-te-modal-body-ref :dir="dir()">
          <div
              class="flex items-center justify-start px-4 py-2 text-primary-500 border-b md:py-4">
            <FolderPlusIcon class="h-7 w-7 mx-3"/>

            <h1 class="text-2xl font-semibold">{{
                params.cmnd == 'edit' ? __('edit_category') : __('new_category')
              }}</h1>

          </div>


          <div class="px-2  md:px-4">

            <div
                class="    mx-auto md:max-w-3xl   mt-6 px-2 md:px-4 py-4   overflow-hidden  rounded-lg  ">


              <div
                  class="flex flex-col mx-2   col-span-2 w-full     px-2"
              >

                <form @submit.prevent="">

                  <TextInput
                      id="checked"
                      type="checkbox"
                      :placeholder="__('active')"
                      classes=" px-0 mx-0 "
                      v-model="params.checked"
                      :autocomplete="checked"

                  >
                  </TextInput>
                  <div v-if="false" class="my-4">

                    <InputLabel for="selectParentId" :value="__('parent_category')"/>
                    <div class="flex items-stretch">
                      <div @click.stop="params.parent_id=null "
                           class="bg-red-500 cursor-pointer text-white align-middle rounded-s hover:bg-red-400">
                        <XMarkIcon class="w-8 h-6 m-2 "/>
                      </div>
                      <select class="grow rounded-e border-gray-300 cursor-pointer" name=""
                              :id=" `selectParentId` " v-model="params.parent_id">
                        <option class="text-start rounded p-2 m-2"
                                v-for="d in $page.props.categories "
                                :value="d.id">
                          <div class="p-2"> {{ __(d.name) }}</div>
                        </option>
                      </select>
                    </div>

                  </div>
                  <div class="my-2">
                    <TextInput
                        id="name"
                        type="text"
                        :placeholder="__('name')"
                        classes="  "
                        v-model="params.name"
                        autocomplete="name"
                        :error="params.errors.name"
                    >
                      <template v-slot:prepend>
                        <div class="p-3">
                          <Bars2Icon class="h-5 w-5"/>
                        </div>
                      </template>

                    </TextInput>
                  </div>

                  <div v-if="loading" class="shadow w-full bg-grey-light m-2   bg-gray-200 rounded-full">
                    <div
                        class=" bg-primary rounded  text-xs leading-none py-[.1rem] text-center text-white duration-300 "
                        :class="{' animate-pulse': loading}"
                        :style="`width: 100%`">
                    </div>
                  </div>

                  <div class="    mt-4">

                    <PrimaryButton @click="edit(params)" type="button" class="w-full  "
                                   :class="{ 'opacity-25': loading}"
                                   :disabled="loading">
                      <LoadingIcon class="w-4 h-4 mx-3 " v-if="  loading"/>
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

<script>
import Scaffold from "@/Layouts/Scaffold.vue";
import Panel from "@/Layouts/Panel.vue";
import {Head, Link, useForm} from "@inertiajs/vue3";
import Pagination from "@/Components/Pagination.vue";
import {
  Bars2Icon,
  MagnifyingGlassIcon,
  ChevronDownIcon,
  HomeIcon,
  XMarkIcon,
  ArrowsUpDownIcon,
  FolderPlusIcon,
  Squares2X2Icon,

} from "@heroicons/vue/24/outline";
import Image from "@/Components/Image.vue"
import Tooltip from "@/Components/Tooltip.vue"
import {Dropdown, Modal} from "tw-elements";
import {BaseTree, Draggable, pro, OpenIcon, walkTreeData} from '@he-tree/vue'
import '@he-tree/vue/style/default.css'
import '@he-tree/vue/style/material-design.css'
import PrimaryButton from "@/Components/PrimaryButton.vue";
import LoadingIcon from "@/Components/LoadingIcon.vue";
import TextInput from "@/Components/TextInput.vue";
import Selector from "@/Components/Selector.vue";
import InputLabel from "@/Components/InputLabel.vue";

export default {
  data() {
    return {
      params: {
        errors: {},
      },
      data: [],
      treeData: [],
      pagination: {},
      toggleSelect: false,
      loading: false,
      error: null,
      checked: [],
      textKey: 'name',
      defaultOpen: true,
      modal: null,
    }
  },
  components: {
    InputLabel,
    TextInput,
    Head,
    Link,
    HomeIcon,
    ChevronDownIcon,
    Panel,
    Bars2Icon,
    Image,
    MagnifyingGlassIcon,
    XMarkIcon,
    Pagination,
    ArrowsUpDownIcon,
    Tooltip,
    BaseTree,
    Draggable,
    OpenIcon,
    PrimaryButton,
    LoadingIcon,
    FolderPlusIcon,
    Selector,
    Squares2X2Icon,
  },
  watch: {
    treeData() {
      // this.log(this.treeData)
    }
  },
  mounted() {


    this.getData();
    const modalEl = document.getElementById('createModal');
    this.modal = new Modal(modalEl);
    // this.showDialog('danger', 'message',()=>{});
    // this.isLoading(false);
  },
  methods: {
    onCheckNode() {
      this.checked = this.$refs.tree.getChecked().map((v) => v.data.text)
    },
    getData() {
      this.isLoading(true);
      this.loading = true;
      this.data = [];
      window.axios.get(route('admin.panel.category.tree'), {
        params: this.params
      }, {})
          .then((response) => {
            this.data = response.data.data;
            this.treeData = response.data.data;

          })

          .catch((error) => {
            if (error.response) {
              // The request was made and the server responded with a status code
              // that falls out of the range of 2xx
              console.log(error.response.data);
              console.log(error.response.status);
              console.log(error.response.headers);
              this.error = error.response.data ? error.response.data.message ? error.response.data.message : error.response.data : this.__('response_error');

            } else if (error.request) {
              // The request was made but no response was received
              // `error.request` is an instance of XMLHttpRequest in the browser and an instance of
              // http.ClientRequest in node.js
              console.log(error.request);
              this.error = error.request;
            } else {
              // Something happened in setting up the request that triggered an Error
              console.log('Error', error.message);
              this.error = error.message;
            }
            console.log(error.config);
            this.showToast('danger', this.error)
          })
          .finally(() => {
            // always executed
            this.loading = false;
            this.isLoading(false);

          });
    },
    initTableDropdowns() {
      const dropdownElementList = [].slice.call(document.querySelectorAll('td [data-te-dropdown-toggle-ref]'));
      window.dropdownList = dropdownElementList.map((dropdownToggleEl) => {
        let d = new Dropdown(dropdownToggleEl);
        dropdownToggleEl.addEventListener('click', function (event) {
          d.toggle();
        })
        return d;
      });
    },
    toggleAll() {

      this.toggleSelect = !this.toggleSelect;
      this.data.forEach(e => {
        e.selected = this.toggleSelect;
      });
    },
    edit(params) {


      this.isLoading(true);
      window.axios.patch(route('admin.panel.category.update'), params,
          {})
          .then((response) => {
            if (response.data && response.data.message) {
              this.showToast('success', response.data.message);

            }
            if (response.data.tree_data) {
              this.treeData = response.data.tree_data;
              this.modal.hide();
            }


          })

          .catch((error) => {
            this.error = this.getErrors(error);
            if (error.response && error.response.data) {
              if (error.response.data.charge) {
                this.data[params.idx].charge = error.response.data.charge;
              }
              if (error.response.data.view_fee) {
                this.data[params.idx].view_fee = error.response.data.view_fee;
              }
              if (error.response.data.meta) {
                this.data[params.idx].meta = error.response.data.meta;
              }
            }
            this.showToast('danger', this.error);
          })
          .finally(() => {
            // always executed
            this.isLoading(false);
          });
    },
    paginationChanged(data) {

      this.params.page = data.page;
      this.getData();
    },
    bulkAction(cmnd) {
      if (this.data.filter(e => e.selected).length == 0) {
        this.showToast('danger', this.__('nothing_selected'));
        return;
      }
      this.isLoading(true);
      const params = {
        cmnd: cmnd, data: this.data.reduce((result, el) => {
          if (el.selected) result.push(el.id);
          return result;
        }, [])
      };

      window.axios.patch(route('admin.panel.pack.update'), params,
          {})
          .then((response) => {
            if (response.data && response.data.message) {
              this.showToast('success', response.data.message);

            }
            if (response.data && response.data.results) {
              const res = response.data.results;
              for (let i in this.data)
                for (let j in res)
                  if (res[j].id == this.data[i].id) {
                    this.data[i].status = res[j].status;
                    break;
                  }
            }

          })

          .catch((error) => {
            this.error = this.getErrors(error);

            this.showToast('danger', this.error);
          })
          .finally(() => {
            this.isLoading(false);
          });
    }
  },

}
</script>
