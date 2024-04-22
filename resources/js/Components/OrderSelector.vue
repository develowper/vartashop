<template>

  <div class="">
    <div v-if="label" class="text-sm text-gray-700">{{ label }}</div>


    <div class="border rounded p-2">

      <PrimaryButton type="button" @click="getData();  Modal.show();">
        {{ __('ready_orders') }}
      </PrimaryButton>

      <div class="     w-full overflow-x-auto   md:rounded-lg">
        <table ref="tableRef "
               class=" table-auto   text-sm   text-gray-500  ">
          <thead
              class="   sticky top-0 shadow-md   text-xs text-gray-700   bg-gray-50 ">
          <!--         table header-->
          <tr class="text-sm text-center ">

            <th scope="col"
                class="px-2 py-3   duration-300 hover:text-gray-500 hover:scale-[99%]">
              <div class="flex items-center justify-center">
                <span class="px-0">    {{ __('id') }} </span>
              </div>
            </th>

            <th scope="col"
                class="px-2 py-3   duration-300 hover:text-gray-500 hover:scale-[99%]"
            >
              <div class="flex items-center justify-center">
                <span class="px-2">    {{ __('repository_id') }} </span>
              </div>
            </th>
            <th scope="col"
                class="px-8 py-3   duration-300 hover:text-gray-500 hover:scale-[99%]"
            >
              <div class="flex items-center justify-center">
                <span class="px-2">    {{ __('items') }} </span>
              </div>
            </th>

            <th scope="col"
                class="px-2 py-3   duration-300 hover:text-gray-500 hover:scale-[99%]"
            >
              <div class="flex items-center justify-center">
                <span class="px-2">    {{ __('receiver') }} </span>
              </div>
            </th>

            <th scope="col"
                class="px-2 py-3   duration-300 hover:text-gray-500 hover:scale-[99%]"
            >
              <div class="flex items-center justify-center">
                <span class="px-2">    {{ __('county') }} </span>
              </div>
            </th>

            <th scope="col"
                class="px-2 py-3   cursor-pointer duration-300 hover:text-gray-500 hover:scale-[99%]"
            >
              <div class="flex items-center justify-center">
                <span class="px-2">    {{ `${__('city')}/${__('district')}` }} </span>
              </div>
            </th>

            <th scope="col"
                class="px-2 py-3   cursor-pointer duration-300 hover:text-gray-500 hover:scale-[99%]"
            >
              <div class="flex items-center justify-center">
                <span class="px-2">    {{ __('shipping_price') }} </span>
              </div>
            </th>

            <th scope="col"
                class="px-2 py-3   cursor-pointer duration-300 hover:text-gray-500 hover:scale-[99%]"
            >
              <div class="flex items-center justify-center">
                <span class="px-2">    {{ __('total_price') }} </span>
              </div>
            </th>

            <th scope="col"
                class="px-2 py-3   cursor-pointer duration-300 hover:text-gray-500 hover:scale-[99%]"
            >
              <div class="flex items-center justify-center">
                <span class="px-2">    {{ __('delivery_time') }} </span>
              </div>
            </th>
            <th scope="col"
                class="px-2 py-3   cursor-pointer duration-300 hover:text-gray-500 hover:scale-[99%]"
            >
              <div class="flex items-center justify-center">
                <span class="px-2">    {{ __('status') }} </span>
              </div>
            </th>

            <th scope="col" class="px-2 py-3">
              {{ __('actions') }}
            </th>
          </tr>
          </thead>
          <tbody
              class="    overflow-y-scroll   text-xs   ">
          <tr v-for="(d,idx) in selecteds"
              class="text-center border-b hover:bg-gray-50 " :class="idx%2==1?'bg-gray-50':'bg-white'">

            <td class="px-2 py-4    " style="font-family: Serif!important;">
              {{ `${d.type == 'agency' ? 'A' : ''}${f2e(d.id)}` }}
            </td>
            <td class="px-2 py-4    ">
              {{ d.from_repo_id }}
              <InputError class="mt-1" :message="error.orders"/>
            </td>
            <td class="px-2 py-4    ">
              <div v-for="(item ,ix) in d.items" class="text-xs " :class="{'border-b':ix+1<d.items.length}">
                {{
                  `${item.name} ( ${parseFloat(item.qty)} ${getPack(item.variation.pack_id)}  ${parseFloat(item.variation.weight)} ${__('kg')})`
                }}
              </div>
            </td>
            <td class="px-2 py-4   text-xs ">
              {{ `${d.to_fullname || ''}\n${d.to_phone || ''}` }}
            </td>

            <td>
              {{ getCityName(d.to_county_id) }}
            </td>

            <td>
              {{ getCityName(d.to_district_id) }}
            </td>
            <td>
              {{ d.total_shipping_price }}
            </td>

            <td>
              {{ asPrice(d.total_price) }}
            </td>
            <td>
              {{ `${toShamsi(d.delivery_date)}\n${d.delivery_timestamp}` }}
            </td>
            <td class="px-2 py-4    " data-te-dropdown-ref>
              <button type="button"
                      :id="`dropdownStatusSetting${d.id}`"
                      data-te-dropdown-toggle-ref
                      aria-expanded="false"
                      data-te-ripple-init
                      data-te-ripple-color="light"
                      class="  min-w-[5rem]  px-1 cursor-pointer items-center text-center rounded-md py-[.2rem]"
                      :class="`bg-${getStatus('order_statuses', d.status).color}-100 hover:bg-${getStatus('order_statuses', d.status).color}-200 text-${getStatus('order_statuses', d.status).color}-500`">
                {{ getStatus('order_statuses', d.status).name }}
              </button>
              <ul :ref="`statusMenu${d.id}`" data-te-dropdown-menu-ref
                  class="  absolute z-[1000]   m-0 hidden   list-none overflow-hidden rounded-lg border-none bg-white bg-clip-padding text-center text-base shadow-lg [&[data-te-dropdown-show]]:block"
                  tabindex="-1" role="menu" aria-orientation="vertical" aria-label="User menu"
                  :aria-labelledby="`dropdownStatusSetting${d.id}`">

                <li v-for="(s,ix) in d.statuses" role="menuitem"
                    @click="showDialog('danger',s.message,__('accept'),editOrder,{'idx':idx,'id':d.id,'type':d.type,'cmnd':'status','status':s.name}) "
                    class="   cursor-pointer   text-sm   transition-colors hover:bg-gray-100">
                  <div class="flex items-center justify-center    px-6 py-2   "
                       :class="` hover:bg-gray-200 text-${s.color}-500`">
                    {{ __(s.name) }}
                  </div>
                  <hr class="border-gray-200 ">
                </li>

              </ul>
            </td>


            <td v-if="false" class="px-2 py-4    ">
              {{ d.is_private ? __('internal') : __('public') }}
            </td>


            <td class="px-2 py-4">
              <!-- Actions Group -->
              <div
                  class=" inline-flex rounded-md  transition duration-150 ease-in-out    focus:outline-none focus:ring-0 active:bg-primary-700 active:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.3),0_4px_18px_0_rgba(59,113,202,0.2)]"
                  role="group">
                <a
                    target="_blank" :href="route(`admin.panel.order.factor`,d.id)"
                    class="inline-block shadow-sm rounded  bg-blue-500 text-white px-6  py-2 text-xs font-medium uppercase leading-normal text-white transition duration-150 ease-in-out hover:bg-blue-400   focus:outline-none focus:ring-0  "
                    data-te-ripple-init
                    data-te-ripple-color="light">
                  {{ __('details') }}
                </a>
                <div type="button"
                     @click="removeItem(d,idx)  "
                     class="rounded shadow-sm cursor-pointer text-white bg-red-500 hover:bg-red-400 text-sm px-6  py-2 mx-1">
                  <TrashIcon class="w-4 h-4 "/>
                </div>
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

    <InputError class="mt-1" :message="error.orders"/>
  </div>
  <!--   Modal  -->
  <div
      data-te-modal-init
      class="fixed left-0 top-0 backdrop-blur z-[1055] hidden h-full w-full overflow-y-auto overflow-x-hidden outline-none"
      :id="`modalOrders-${id}`"
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
              class="text-xl text-primary-500 font-medium leading-normal text-neutral-800 dark:text-neutral-200"
              id="exampleModalLabel">
            {{ __('ready_orders') }}
          </h5>
          <!--Close button-->
          <button

              type="button"
              class="box-content text-danger rounded-none border-none hover:no-underline hover:opacity-75 focus:opacity-100 focus:shadow-none focus:outline-none"
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

            <div class="flex-col   bg-white  overflow-x-auto    rounded-lg">
              <div class="flex flex-wrap  items-center justify-between py-1 dark:bg-gray-800 p-4">

                <!--              Dropdown Paginate-->
                <div class="flex items-center p-1">
                  <div class="relative mx-1  " data-te-dropdown-ref>
                    <button
                        type="button"
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
                    <div ref="paginateMenu" data-te-dropdown-menu-ref
                         class="min-w-[12rem] absolute z-[1000] start-0 text-gray-500  m-0 hidden min-w-max list-none overflow-hidden rounded-lg border-none bg-white bg-clip-padding text-start text-base shadow-lg dark:bg-neutral-700 [&[data-te-dropdown-show]]:block"
                         tabindex="-1" role="menu" aria-orientation="vertical" aria-label="User menu"
                         aria-labelledby="dropdownPaginate">
                      <div v-for=" num in $page.props.pageItems " class="">
                        <div @click.stop="params.paginate=num;getData()" role="menuitem"
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
              <div class="mb-2">
                <UserSelector :colsData="['id','name','phone','agency']" :labelsData="['id','name','phone','agency']"
                              :callback="{'agency':(e)=>`${e.name||''} (${e.id||''})`}"
                              :link="route('admin.panel.repository.search')+(`?status=active` )"
                              :label="__('repository')"
                              @change="getData"
                              :id="'repository'" v-model:selected="params.repo_id" :preload="null">
                  <template v-slot:selector="props">
                    <div :class="props.selectedText?'py-2':'py-2'"
                         class=" px-4 border border-gray-300 rounded hover:bg-gray-100 cursor-pointer flex items-center ">
                      <div class="grow">
                        {{ props.selectedText ?? __('select') }}
                      </div>
                      <div v-if="props.selectedText"
                           class="bg-danger rounded p-2   cursor-pointer text-white hover:bg-danger-400"
                           @click.stop="props.clear();getData()">
                        <XMarkIcon class="w-5 h-5"/>

                      </div>
                    </div>
                  </template>
                </UserSelector>

              </div>
              <!--           table-->
              <table class="w-full  text-sm text-start text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700   bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <!--         table header-->
                <tr class="text-sm text-center">

                  <th v-for="(col,idx) in cols" scope="col"
                      class="px-2 py-3   cursor-pointer   hover:text-gray-500  "
                      @click="params.order_by=col;params.dir=params.dir=='ASC'? 'DESC':'ASC'; params.page=1;getData()">
                    <div class="flex items-center justify-center">
                      <span class="px-2">  {{ __(labels[idx]) }}</span>
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
                <tr v-for="(d,idx) in data" :class="{'border-b':idx!=data.length-1}"
                    class="cursor-pointer hover:bg-gray-400 bg-white text-center  dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600"
                    @click="selectItem(d,idx)"
                >
                  <template v-for="(col,idx) in cols">

                    <td v-if="idx==1">

                      <div v-for="(item ,ix) in d[col] " class="text-xs">
                        {{
                          `${item.name} ( ${parseFloat(item.qty)} ${getPack(item.variation.pack_id)}  ${parseFloat(item.variation.weight)} ${__('kg')})`
                        }}
                      </div>
                    </td>
                    <td v-else class="px-2 py-4">
                      {{ callback && callback[col] ? callback[col](d[col]) : __(d[col]) }}
                    </td>
                  </template>
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
</template>

<script>
import {Link} from "@inertiajs/vue3";
import InputError from "@/Components/InputError.vue";
import Pagination from "@/Components/Pagination.vue";
import Image from "@/Components/Image.vue";
import {
  ArrowsUpDownIcon,
  XMarkIcon,
  MagnifyingGlassIcon,
  ChevronDownIcon,
  ChevronUpIcon,
  PlusIcon,
  TrashIcon,


} from "@heroicons/vue/24/outline";
import {Modal} from "tw-elements";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import UserSelector from "@/Components/UserSelector.vue";

let call;
export default {
  name: "OrderSelector",
  props: ['id', 'mode', 'updateLink', 'preload', 'paginate', 'selecteds', 'placeholder', 'error', 'link', 'label', 'colsData', 'labelsData', 'callback'],
  components: {
    UserSelector,
    PrimaryButton,
    ChevronDownIcon,
    MagnifyingGlassIcon,
    Pagination,
    XMarkIcon,
    ArrowsUpDownIcon,
    Image,
    InputError,
    Link,
    TrashIcon,
  },
  data() {
    return {
      params: {
        page: 1,
        search: null,
        paginate: this.paginate || 24,
        order_by: null,
        repo_id: null,
        status: 'ready',
        dir: 'DESC',
        before_selected_user_orders: [],
        before_selected_agency_orders: [],

      },
      Modal: null,
      data: [],
      pagination: {},
      loading: false,
      removing: false,
      uploading: false,
      selectingForIndex: null,
      selectedText: null,
      selectedItem: null,
      errors: null,
    }
  },
  emits: ['update:selecteds',],
  created() {
    this.cols = this.colsData || ['id', 'items', 'from_agency_id', 'from_repo_id'];
    this.labels = this.labelsData || ['id', 'items', 'agency_id', 'repository'];
  },
  mounted() {
    call = this.callback;

    const modalEl = document.getElementById(`modalOrders-${this.id}`);
    this.Modal = new Modal(modalEl);

    if (this.preload && this.preload.length > 0) {
      this.$emit('update:selecteds', this.preload);
    }

    this.$nextTick(() => {
      this.initTableDropdowns();
    });
  },
  methods: {
    clear() {
      this.selectedItem = null;
      this.selectedText = null;
      this.$emit('update:selecteds', []);

    },
    removeItem(item, idx) {
      if (item.shipping_id) {
        this.edit({cmnd: 'remove-order', idx: idx, id: item.shipping_id, order_id: item.id, order_type: item.type});
      } else {
        if (item.type == 'user')
          this.params.before_selected_user_orders.filter(e => e != item.id);
        if (item.type == 'agency')
          this.params.before_selected_agency_orders.filter(e => e != item.id);

        this.selecteds.splice(idx, 1);
        this.$emit('update:selecteds', this.selecteds);
      }
      // this.log(this.selecteds);
      // this.Modal.hide();
    },
    selectItem(item, idx) {

      this.selecteds.push(item);
      if (item.type == 'user')
        this.params.before_selected_user_orders.push(item.id);
      if (item.type == 'agency')
        this.params.before_selected_agency_orders.push(item.id);

      this.data.splice(idx, 1);

      this.$emit('update:selecteds', this.selecteds);

      // this.Modal.hide();
    },
    getData() {

      this.loading = true;
      this.data = [];
      this.params.before_selected_user_orders = this.selecteds.filter(e => e.type == 'user').map(e => e.id);
      this.params.before_selected_agency_orders = this.selecteds.filter(e => e.type == 'agency').map(e => e.id);
      window.axios.get(this.link, {params: this.params}, {})

          .then((response) => {

            this.data = response.data.data;
            delete response.data.data;
            this.pagination = response.data;
          })

          .catch((error) => {
            if (error.response) {
              // console.log(error.response.data);
              // console.log(error.response.status);
              // console.log(error.response.headers);
              this.errors = error.response.data;

            } else if (error.request) {
              // console.log(error.request);
              this.errors = error.request;
            } else {
              // console.log('Error', error.message);
              this.errors = error.message;
            }
            // console.log(error.config);
            // this.showToast('danger', error)
          })
          .finally(() => {
            this.loading = false;
          });
    },
    edit(params) {
      this.isLoading(true);
      this.errors = {};

      window.axios.patch(this.updateLink, params,
          {})
          .then((response) => {
            if (response.data) {
              if (response.data.message)
                this.showToast('success', response.data.message);
              else
                this.showToast('success', this.__('updated_successfully'));

              if (params.cmnd == 'remove-order') {
                for (const idx in this.preload) {
                  if (this.preload[idx].id == params.order_id && this.preload[idx].type == params.order_type) {
                    this.preload.splice(idx, 1);
                    this.removeItem(params, params.idx);
                  }
                }
              }
            }


            this.selected = null;


          })

          .catch((error) => {
            this.errorMessage = this.getErrors(error);
            if (error.response && error.response.data) {
              this.errors = error.response.data.errors || {};

            }
            this.showToast('danger', this.errorMessage);
          })
          .finally(() => {
            // always executed
            this.isLoading(false);
          });
    },
    editOrder(params) {
      this.isLoading(true);
      this.errors = {};
      window.axios.patch(route(`admin.panel.order.${params.type}.update`), params,
          {})
          .then((response) => {
            if (response.data && response.data.message) {
              this.showToast('success', response.data.message);

            }

            if (response.data.status) {
              this.selecteds[params.idx].status = response.data.status;
              if (response.data.statuses)
                this.selecteds[params.idx].statuses = response.data.statuses;
            } else {
              this.getData();
            }
            this.selected = null;


          })

          .catch((error) => {
            this.errorMessage = this.getErrors(error);
            if (error.response && error.response.data) {
              this.errors = error.response.data.errors || {};

            }
            this.showToast('danger', this.errorMessage);
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
  },
}
</script>