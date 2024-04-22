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
          <h1 class="text-2xl font-semibold">{{ __('notifications_list') }}</h1>
        </div>
        <div>
          <Link :href="route('panel.admin.notification.create')"
                class="inline-flex items-center  justify-center px-4 py-2 bg-green-500 border border-transparent rounded-md font-semibold  transition-all duration-500 text-white     hover:bg-green-600 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
          >
            {{ __('new_notification') }}
          </Link>
        </div>
      </div>
      <!-- Content -->
      <div class="px-2 flex flex-col   md:px-4">

        <div class="flex-col   bg-white  overflow-x-auto shadow-lg  rounded-lg">
          <div class="flex   items-center justify-between py-4 p-4">
            <!--              Dropdown Actions-->
            <div>
              <div class="relative mx-1  " data-te-dropdown-ref>
                <button
                    id="dropdownActionsSetting"
                    data-te-dropdown-toggle-ref
                    aria-expanded="false"
                    data-te-ripple-init
                    data-te-ripple-color="light"
                    class=" inline-flex items-center text-gray-500 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 font-medium rounded-lg text-sm px-3 py-1.5">

                  <span class="sr-only">table actions</span>
                  <span>{{ __('bulk_actions') }}</span>
                  <ChevronDownIcon class="h-4 w-4 mx-1"/>
                </button>

                <!--     menu -->
                <div ref="actionsMenu" data-te-dropdown-menu-ref
                     class="min-w-[12rem] absolute z-[1000] float-start m-0 hidden min-w-max list-none overflow-hidden rounded-lg border-none bg-white bg-clip-padding text-start text-base shadow-lg [&[data-te-dropdown-show]]:block"
                     tabindex="-1" role="menu" aria-orientation="vertical" aria-label="Actions menu"

                     aria-labelledby="dropdownActionsSetting">

                </div>
              </div>
            </div>
            <!--              Dropdown Paginate-->
            <div class="flex items-center">
              <div class="relative mx-1  " data-te-dropdown-ref>
                <button
                    id="dropdownPaginate"
                    data-te-dropdown-toggle-ref
                    aria-expanded="false"
                    data-te-ripple-init
                    data-te-ripple-color="light"
                    class=" inline-flex items-center text-gray-500 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 font-medium rounded-lg text-sm px-3 py-1.5">

                  <span class="sr-only">table actions</span>
                  <span>{{ params.paginate }}</span>
                  <ChevronDownIcon class="h-4 w-4 mx-1"/>
                </button>

                <!--     menu -->
                <div ref="userMenu" data-te-dropdown-menu-ref
                     class="min-w-[12rem] absolute z-[1000] start-0 text-gray-500  m-0 hidden min-w-max list-none overflow-hidden rounded-lg border-none bg-white bg-clip-padding text-start text-base shadow-lg [&[data-te-dropdown-show]]:block"
                     tabindex="-1" role="menu" aria-orientation="vertical" aria-label="User menu"

                     aria-labelledby="dropdownPaginate">
                  <div v-for=" num in $page.props.pageItems " class="">
                    <div @click="params.paginate=num;getData()" role="menuitem"
                         class=" cursor-pointer  select-none block  p-2 px-6 text-sm   transition-colors hover:bg-gray-100">
                      {{ num }}
                    </div>
                    <hr class="border-gray-200 ">
                  </div>
                </div>
              </div>

              <!--                Paginate-->
              <Pagination @paginationChanged="paginationChanged" :pagination="pagination"/>
            </div>

            <div class="relative ">
              <label for="table-search" class="sr-only">Search</label>
              <div
                  class="absolute inset-y-0 cursor-pointer text-gray-500 hover:text-gray-700  start-0 flex items-center px-3  ">
                <MagnifyingGlassIcon @click=" getData() " class="w-4 h-4 "/>
              </div>
              <div
                  class="absolute inset-y-0 end-0 text-gray-500 flex items-center px-3 cursor-pointer hover:text-gray-700  "
                  @click="params.search=null; getData() ">
                <XMarkIcon class="w-4 h-4 "/>
              </div>
              <input type="text" id="table-search-users" v-model="params.search" @keydown.enter="getData()"
                     class="block p-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg w-80 bg-gray-50 focus:ring-blue-500 focus:border-blue-500"
                     :placeholder="__('search')">
            </div>
          </div>
          <!--           table-->
          <table class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
            <!--         table header-->
            <tr class="text-sm text-center">
              <th scope="col" class="p-4" @click="toggleAll">
                <div class="flex items-center">
                  <input id="checkbox-all-search" type="checkbox" v-model="toggleSelect"
                         class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
                  <label for="checkbox-all-search" class="sr-only">checkbox</label>
                </div>
              </th>
              <th scope="col"
                  class="px-2 py-3   cursor-pointer duration-300 hover:text-gray-500 hover:scale-[105%]"
                  @click="params.order_by='subject';params.dir=params.dir=='ASC'? 'DESC':'ASC'; params.page=1;getData()">
                <div class="flex items-center justify-center">
                  <span class="px-2">  {{ __('subject') }}</span>
                  <ArrowsUpDownIcon class="w-4 h-4 "/>
                </div>
              </th>

              <th scope="col"
                  class="px-2 py-3   cursor-pointer duration-300 hover:text-gray-500 hover:scale-[105%]"
                  @click="params.order_by='owner_id';params.dir=params.dir=='ASC'? 'DESC':'ASC'; params.page=1;getData()">
                <div class="flex items-center justify-center">
                  <span class="px-2">    {{ __('user') }} </span>
                  <ArrowsUpDownIcon class="w-4 h-4 "/>
                </div>
              </th>

              <th scope="col"
                  class="px-2 py-3   cursor-pointer duration-300 hover:text-gray-500 hover:scale-[105%]"
                  @click="params.order_by='data_id';params.dir=params.dir=='ASC'? 'DESC':'ASC'; params.page=1;getData()">
                <div class="flex items-center justify-center">
                  <span class="px-2">    {{ __('item') }} </span>
                  <ArrowsUpDownIcon class="w-4 h-4 "/>
                </div>
              </th>


              <th scope="col"
                  class="px-2 py-3   cursor-pointer duration-300 hover:text-gray-500 hover:scale-[105%]"
                  @click="params.order_by='type';params.dir=params.dir=='ASC'? 'DESC':'ASC'; params.page=1;getData()">
                <div class="flex items-center justify-center">
                  <span class="px-2">   {{ __('type') }}</span>
                  <ArrowsUpDownIcon class="w-4 h-4 "/>
                </div>
              </th>

              <th scope="col" class="px-2 py-3">
                {{ __('actions') }}
              </th>
            </tr>
            </thead>
            <tbody class=" ">
            <tr v-if="loading" v-for="i in 3"
                class="animate-pulse bg-white text-center border-b hover:bg-gray-50">
              <td class="w-4 p-4">
                <div class="flex items-center">
                  <input id="checkbox-table-search-1" type="checkbox"
                         class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2">

                </div>
              </td>
              <td
                  class="flex  items-center px-6 py-4 text-gray-900 whitespace-nowrap">
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
              <td class="px-2 py-4 ">
                <div class="bg-gray-200 px-5 py-2 rounded-lg">

                </div>
              </td>
              <td class="px-2 py-4 ">
                <div class="bg-gray-200 px-5 py-2 rounded-lg"></div>
              </td>
              <td class="px-2 py-4">
                <div
                    class="  justify-center bg-gray-200 px-5 py-3 rounded-lg  items-center text-center rounded-md "
                >

                </div>
              </td>
              <td class="px-2 py-4">
                <div class="bg-gray-200 px-5 py-2 rounded-lg"></div>
              </td>
              <td class="px-2 py-4">
                <!-- Actions Group -->
                <div
                    class="  bg-gray-200 px-5 py-4 rounded-lg rounded-md   "
                    role="group">

                </div>
              </td>
            </tr>
            <tr v-for="(d,idx) in data"
                class="bg-white text-center border-b hover:bg-gray-50">
              <td class="w-4 p-4" @click="d.selected=!d.selected">
                <div class="flex items-center">
                  <input id="checkbox-table-search-1" type="checkbox" v-model="d.selected"
                         class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2">

                </div>
              </td>
              <td
                  class="flex  items-center px-6 py-4 text-gray-900 whitespace-nowrap">

                <Link class="px-3 hover:text-gray-500" :href="route('panel.admin.notification.edit',d.id)">
                  <div class="text-base font-semibold">{{ cropText(d.subject, 30) }}</div>
                  <div class="font-normal text-gray-500">{{ }}</div>
                </Link>
              </td>

              <td class="px-2 py-4 ">
                <div v-if="d.owner_id">
                  <Link class="hover:bg-primary-100 p-4 rounded" :href="route('panel.admin.user.edit',d.owner_id)">{{
                      d.owner_id
                    }}
                  </Link>
                </div>
                <div v-else>_</div>
              </td>

              <td class="px-2 py-4 ">
                <div v-if="d.data_id">
                  <Link class="hover:bg-secondary-100 p-4 rounded" :href="createLink(d)">{{
                      d.data_id
                    }}
                  </Link>
                </div>
                <div v-else>_</div>
              </td>


              <td class="px-2 py-4 text-primary ">
                <div>
                  {{ __(d.type) }}
                </div>
              </td>

              <td class="px-2 py-4">
                <!-- Actions Group -->
                <div
                    class=" inline-flex rounded-md shadow-[0_4px_9px_-4px_#3b71ca] transition duration-150 ease-in-out hover:bg-primary-600 hover:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.3),0_4px_18px_0_rgba(59,113,202,0.2)] focus:bg-primary-600 focus:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.3),0_4px_18px_0_rgba(59,113,202,0.2)] focus:outline-none focus:ring-0 active:bg-primary-700 active:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.3),0_4px_18px_0_rgba(59,113,202,0.2)]"
                    role="group">
                  <Link
                      type="button" :href="route('panel.admin.notification.edit',d.id)"
                      class="inline-block rounded-s  bg-orange-500 text-white px-6  py-2 text-xs font-medium uppercase leading-normal text-white transition duration-150 ease-in-out hover:bg-orange-400   focus:outline-none focus:ring-0  "
                      data-te-ripple-init
                      data-te-ripple-color="light">
                    {{ __('edit') }}
                  </Link>
                  <button
                      type="button"
                      @click.prevent="showDialog('danger',__('remove_item?'),__('remove'),remove,{id:d.id,idx:idx})"
                      class="inline-block rounded-e  bg-danger-500 text-white px-6  py-2 text-xs font-medium uppercase leading-normal text-white transition duration-150 ease-in-out hover:bg-danger-400   focus:outline-none focus:ring-0  "
                      data-te-ripple-init
                      data-te-ripple-color="light">
                    {{ __('remove') }}
                  </button>

                  <!--                  <button -->
                  <!--                      type="button"-->
                  <!--                      class="inline-block rounded-e bg-teal-500 px-6 py-2 text-xs font-medium uppercase leading-normal text-white transition duration-150 ease-in-out hover:bg-teal-400   focus:outline-none focus:ring-0  "-->
                  <!--                      data-te-ripple-init-->
                  <!--                      data-te-ripple-color="light">-->
                  <!--                    {{ __('charge') }}-->
                  <!--                  </button>-->
                </div>
              </td>
            </tr>

            </tbody>
          </table>

        </div>

      </div>
    </template>


  </Panel>
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

} from "@heroicons/vue/24/outline";
import Image from "@/Components/Image.vue"
import Tooltip from "@/Components/Tooltip.vue"

export default {
  data() {
    return {
      params: {
        page: 1,
        search: null,
        paginate: this.$page.props.pageItems[0],
        order_by: null,
        dir: 'DESC',
      },
      data: [],
      pagination: {},
      toggleSelect: false,
      loading: false,
      error: null,
    }
  },
  components: {
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
  },
  mounted() {

    this.getData();
    // if (this.$page.props.flash)
    //   this.showAlert(this.$page.props.flash.status, this.$page.props.flash.description);
    // this.showDialog('danger', 'message',()=>{});
    // this.isLoading(false);
  },
  methods: {
    getData() {

      this.loading = true;
      this.data = [];
      window.axios.get(route('panel.notification.search'), {
        params: this.params
      }, {
        onUploadProgress: function (axiosProgressEvent) {
          console.log(axiosProgressEvent);
          /*{
            loaded: number;
            total?: number;
            progress?: number; // in range [0..1]
            bytes: number; // how many bytes have been transferred since the last trigger (delta)
            estimated?: number; // estimated time in seconds
            rate?: number; // upload speed in bytes
            upload: true; // upload sign
          }*/
        },

        onDownloadProgress: function (axiosProgressEvent) {
          console.log(axiosProgressEvent);

          /*{
            loaded: number;
            total?: number;
            progress?: number;
            bytes: number;
            estimated?: number;
            rate?: number; // download speed in bytes
            download: true; // download sign
          }*/
        }
      })
          .then((response) => {
            this.data = response.data.data;
            this.data.forEach(el => {
              el.selected = false;
            });
            delete response.data.data;
            this.pagination = response.data;

          })

          .catch((error) => {
            if (error.response) {
              // The request was made and the server responded with a status code
              // that falls out of the range of 2xx
              console.log(error.response.data);
              console.log(error.response.status);
              console.log(error.response.headers);
              this.error = error.response.data;

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
            this.showToast('danger', error)
          })
          .finally(() => {
            // always executed
            this.loading = false;
          });
    },
    toggleAll() {

      this.toggleSelect = !this.toggleSelect;
      this.data.forEach(e => {
        e.selected = this.toggleSelect;
      });
    },
    remove(params) {
      this.isLoading(true);
      window.axios.delete(route('panel.admin.notification.delete', params.id), {},
          {
            onUploadProgress: function (axiosProgressEvent) {
              // console.log(axiosProgressEvent);
              /*{
                loaded: number;
                total?: number;
                progress?: number; // in range [0..1]
                bytes: number; // how many bytes have been transferred since the last trigger (delta)
                estimated?: number; // estimated time in seconds
                rate?: number; // upload speed in bytes
                upload: true; // upload sign
              }*/
            },

            onDownloadProgress: function (axiosProgressEvent) {
              // console.log(axiosProgressEvent);

              /*{
                loaded: number;
                total?: number;
                progress?: number;
                bytes: number;
                estimated?: number;
                rate?: number; // download speed in bytes
                download: true; // download sign
              }*/
            }
          })
          .then((response) => {
            if (response.data && response.data.message) {
              this.showToast('success', response.data.message);
              this.data.splice(params.idx, 1);
            }


          })

          .catch((error) => {
            this.error = this.getErrors(error);
            if (error.response && error.response.data) {

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

      window.axios.patch(route('notification.update'), params,
          {
            onUploadProgress: function (axiosProgressEvent) {
            },

            onDownloadProgress: function (axiosProgressEvent) {
            }
          })
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
    },
    createLink(data) {
      if (!data) return '';
      if (data.link) return data.link;
      if (data.data_id && data.type)
        return route('/') + "/" + data.type.split("_")[0] + "/edit/" + data.data_id;
      return route('panel.notification.index');
    },
  },

}
</script>
