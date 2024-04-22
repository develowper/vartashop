<template>

  <Panel>
    <template v-slot:header>
      <title>{{__('transactions')}}</title>
    </template>


    <template v-slot:content>
      <!-- Content header -->
      <div
          class="flex items-center justify-between px-4 py-2 text-primary-500 border-b md:py-4">
        <div class="flex">
          <Bars2Icon class="h-7 w-7 mx-3"/>
          <h1 class="text-2xl font-semibold">{{ __('financial') }}</h1>
        </div>
        <div v-if="false">
          <Link :href="route('admin.panel.admin.create')"
                class="inline-flex items-center  justify-center px-4 py-2 bg-green-500 border border-transparent rounded-md font-semibold  transition-all duration-500 text-white     hover:bg-green-600 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
          >
            {{ __('new_transaction') }}
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
                <div ref="adminMenu" data-te-dropdown-menu-ref
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
              <input type="text" id="table-search-admins" v-model="params.search" @keydown.enter="getData()"
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
                  @click="params.order_by='id';params.dir=params.dir=='ASC'? 'DESC':'ASC'; params.page=1;getData()">
                <div class="flex items-center justify-center">
                  <span class="px-2">  {{ __('id') }}</span>
                  <ArrowsUpDownIcon class="w-4 h-4 "/>
                </div>
              </th>
              <th scope="col"
                  class="px-2 py-3   cursor-pointer duration-300 hover:text-gray-500 hover:scale-[105%]"
                  @click="params.order_by='type';params.dir=params.dir=='ASC'? 'DESC':'ASC'; params.page=1;getData()">
                <div class="flex items-center justify-center">
                  <span class="px-2">  {{ __('type') }}</span>
                  <ArrowsUpDownIcon class="w-4 h-4 "/>
                </div>
              </th>
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
                  @click="params.order_by='card';params.dir=params.dir=='ASC'? 'DESC':'ASC'; params.page=1;getData()">
                <div class="flex items-center justify-center">
                  <span class="px-2">    {{ __('card') }} </span>
                  <ArrowsUpDownIcon class="w-4 h-4 "/>
                </div>
              </th>
              <th scope="col"
                  class="px-2 py-3   cursor-pointer duration-300 hover:text-gray-500 hover:scale-[105%]"
                  @click="params.order_by='sheba';params.dir=params.dir=='ASC'? 'DESC':'ASC'; params.page=1;getData()">
                <div class="flex items-center justify-center">
                  <span class="px-2">    {{ __('sheba') }} </span>
                  <ArrowsUpDownIcon class="w-4 h-4 "/>
                </div>
              </th>
              <th scope="col"
                  class="px-2 py-3   cursor-pointer duration-300 hover:text-gray-500 hover:scale-[105%]"
                  @click="params.order_by='wallet';params.dir=params.dir=='ASC'? 'DESC':'ASC'; params.page=1;getData()">
                <div class="flex items-center justify-center">
                  <span class="px-2">    {{ __('wallet') }} </span>
                  <ArrowsUpDownIcon class="w-4 h-4 "/>
                </div>
              </th>
              <th>
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
                  class="   items-center    text-gray-900  ">
                <div>{{ d.id }}</div>
              </td>
              <td
                  class="   items-center    text-gray-900  ">
                {{ __(d.type) }}
              </td>
              <td
                  class="   items-center    text-gray-900  ">
                <div class="px-3 text-xs hover:text-gray-500" :title="d.name">
                  <div class="  font-semibold">{{ cropText(d.name, 50) }}</div>
                </div>
              </td>


              <td class="px-2 py-4    " :title="d.card">
                <div> {{ d.card }}</div>
              </td>
              <td class="px-2 py-4    " :title="d.sheba">
                <div> {{ d.sheba }}</div>
              </td>

              <td class="px-2 py-4    ">
                <div> {{ asPrice(d.wallet) }}</div>
              </td>
              <td class="px-2 py-4 flex  ">
                <!-- Actions Group -->
                <button @click=" d.idx=idx;d.amount=d.wallet; d.cmnd='settlement';  selected=d; "
                        :disabled="d.wallet && d.wallet>0 ?null:true"
                        type="button"
                        :class="`${d.wallet && d.wallet>0?'bg-green-500 hover:bg-green-400 cursor-pointer':'bg-gray-400'}` "
                        class="inline-block rounded  mx-1   text-white px-6  py-2 text-xs font-medium uppercase leading-normal text-white transition duration-150 ease-in-out    focus:outline-none focus:ring-0  "
                        data-te-ripple-init
                        data-te-ripple-color="light">
                  {{ __('settlement') }}
                </button>
                <button
                    @click=" d.idx=idx;d.amount=0; d.cmnd='charge';  selected=d; "
                    type="button"
                    class="inline-block rounded cursor-pointer bg-sky-500 text-white px-6  py-2 text-xs font-medium uppercase leading-normal text-white transition duration-150 ease-in-out hover:bg-sky-400   focus:outline-none focus:ring-0  "
                    data-te-ripple-init
                    data-te-ripple-color="light">
                  {{ __('charge') }}
                </button>
              </td>

            </tr>

            </tbody>
          </table>

        </div>

      </div>
      <!--Modals-->

      <div v-if="selected" class="relative z-[1050]" aria-labelledby="modal-title" role="dialog" aria-modal="true">

        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

        <div class="fixed inset-0 z-10  w-screen overflow-y-auto">
          <div @click.self="selected=null;errors={}"
               class="flex min-h-full   justify-center p-4 text-center sm:items-center sm:p-0">
            <div
                class="relative transform overflow-auto rounded-lg bg-white   shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
              <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                <div class=" flex flex-col items-stretch">
                  <div class="flex items-center  gap-2">
                    <div
                        class="  flex text-warning  h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-warning-100 sm:mx-0 sm:h-10 sm:w-10">
                      <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                           fill="currentColor" viewBox="0 0 16 16">
                        <path
                            d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"></path>
                      </svg>
                    </div>
                    <h3 class="text-base     text-gray-900" id="modal-title">
                      {{ `${__(selected.cmnd)} ${selected.name}` }}
                    </h3>
                  </div>
                  <div class="m-2  text-start">
                    <!--                         modal body-->
                    <div class="mt-2">

                      <div
                          class="   text-sm text-gray-500 ">
                        <span class="text-sm py-2 text-danger-500">{{
                            `${selected.cmnd == 'settlement' ? __('max') : __('current_balance')}: ${asPrice(selected.wallet)} ${__('currency')}`
                          }}</span>
                        <div class="flex flex-col  space-y-2 text-start ">

                          <div class="flex flex-col  ">

                            <div class="my-2">
                              <TextInput
                                  id="amount"
                                  type="number"
                                  :placeholder="`${__('amount')}`"
                                  classes="  "
                                  v-model="selected.amount"
                                  :autocomplete="selected.amount"
                                  :error="  errors.amount">

                                <template v-slot:prepend>
                                  <div class="p-3">
                                    <CurrencyDollarIcon class="h-5 w-5"/>
                                  </div>
                                </template>
                              </TextInput>
                            </div>
                            <button
                                class="bg-success-200 text-success-700 p-2 rounded-lg  hover:bg-success-300 w-full"
                                @click="edit({'idx':selected.idx ,'id':selected.id,'cmnd':selected.cmnd,'amount':selected.amount, 'type':selected.type,  })">
                              {{ __(selected.cmnd) }}
                            </button>

                          </div>
                        </div>
                      </div>
                      <button class="bg-gray-200 my-2 text-gray-700 p-2 rounded-lg  hover:bg-gray-300 w-full"
                              @click="selected=null;errors={}">
                        {{ __('cancel') }}
                      </button>

                    </div>
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
import Pagination from "@/Components/Pagination.vue";
import {
  Bars2Icon,
  MagnifyingGlassIcon,
  ChevronDownIcon,
  HomeIcon,
  XMarkIcon,
  ArrowsUpDownIcon,
  CurrencyDollarIcon,

} from "@heroicons/vue/24/outline";
import Image from "@/Components/Image.vue"
import Tooltip from "@/Components/Tooltip.vue"
import {Dropdown} from "tw-elements";
import TextInput from "@/Components/TextInput.vue";

export default {
  data() {
    return {
      selected: null,
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
      errors: {},
    }
  },
  components: {
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
    CurrencyDollarIcon,
  },
  mounted() {
    this.tableWrapper = document.querySelector('table').parentElement;

    this.getData();

    // this.showDialog('danger', 'message',()=>{});
    // this.isLoading(false);
  },
  methods: {
    getData() {

      this.loading = true;
      this.data = [];
      window.axios.get(route(`admin.panel.financial.search`), {
        params: this.params
      }, {})
          .then((response) => {
            this.data = response.data.data;
            this.data.forEach(el => {
              el.selected = false;
              el.accesses = el.accesses ? el.accesses.split(',') : [];
            });
            delete response.data.data;
            this.pagination = response.data;

            this.$nextTick(() => {
              this.initTableDropdowns();
              this.setTableHeight();
            });
          })

          .catch((error) => {
            if (error.response) {
              // The request was made and the server responded with a status code
              // that falls out of the range of 2xx
              console.log(error);
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
    setTableHeight() {
      let a = window.innerHeight - this.tableWrapper.offsetTop;
      // this.tableWrapper.classList.add(`h-[60vh]`);
      this.tableWrapper.style.height = `${a}px`;
      // this.tableWrapper.firstChild.classList.add(`overflow-y-scroll`);
    },
    toggleAll() {

      this.toggleSelect = !this.toggleSelect;
      this.data.forEach(e => {
        e.selected = this.toggleSelect;
      });
    },
    edit(params) {
      this.isLoading(true);
      window.axios.patch(route('admin.panel.financial.update'), params,
          {})
          .then((response) => {
            if (response.data && response.data.message) {
              this.showToast('success', response.data.message);

            }
            if (response.data.wallet) {
              this.data[params.idx].wallet = response.data.wallet;
            }
            this.selected = null;

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

    }
  },

}
</script>
