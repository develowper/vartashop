<template>

  <Panel>
    <template v-slot:header>
      <title>{{__('orders_list')}}</title>
    </template>


    <template v-slot:content>
      <div class="   ">
        <!-- Content header -->
        <div
            class="flex  items-center justify-between px-4 py-2 text-primary-500 border-b md:py-4">
          <div class="flex">
            <Bars2Icon class="h-7 w-7 mx-3"/>
            <h5 class="  font-semibold">{{ `${__('orders_list')} ( ${__(status) || ''} )` }}</h5>
          </div>
          <div>
            <Link v-if="hasAccess('create_order')" :href="route('admin.panel.repository.order.create')"
                  class="inline-flex items-center  justify-center px-4 py-2 bg-green-500 border border-transparent rounded-md font-semibold  transition-all duration-500 text-white     hover:bg-green-600 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
            >
              {{ __('new_order') }}
            </Link>
          </div>
        </div>
        <!-- Content -->
        <div class="px-2     md:px-4">


          <div class="flex flex-col    overflow-x-aut bg-white   shadow-lg  rounded-lg">

            <div class="flex  gap-1 flex-wrap items-center justify-start py-4 p-4">
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

              <div class="relative  ">
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
                       class="  w-fit   p-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg w-80 bg-gray-50 focus:ring-blue-500 focus:border-blue-500"
                       :placeholder="__('search')">
              </div>
            </div>
            <div class="flex-grow   w-full">

              <!--           table-->
              <div class="     w-full overflow-x-auto   md:rounded-lg">
                <table ref="tableRef "
                       class=" table-auto   text-sm   text-gray-500  ">
                  <thead
                      class="   sticky top-0 shadow-md   text-xs text-gray-700   bg-gray-50 ">
                  <!--         table header-->
                  <tr class="text-sm text-center ">
                    <th scope="col" class="p-4" @click="toggleAll">
                      <div class="flex items-center">
                        <input id="checkbox-all-search" type="checkbox" v-model="toggleSelect"
                               class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
                        <label for="checkbox-all-search" class="sr-only">checkbox</label>
                      </div>
                    </th>
                    <th scope="col"
                        class="px-2 py-3   cursor-pointer duration-300 hover:text-gray-500 hover:scale-[99%]"
                        @click="params.order_by='id';params.dir=params.dir=='ASC'? 'DESC':'ASC'; params.page=1;getData()">
                      <div class="flex items-center justify-center">
                        <span class="px-0">    {{ __('id') }} </span>
                        <ArrowsUpDownIcon class="w-4 h-4 "/>
                      </div>
                    </th>


                    <th scope="col"
                        class="px-2 py-3   cursor-pointer duration-300 hover:text-gray-500 hover:scale-[99%]"
                        @click="params.order_by='from_repo_id';params.dir=params.dir=='ASC'? 'DESC':'ASC'; params.page=1;getData()">
                      <div class="flex items-center justify-center">
                        <span class="px-2">    {{ __('origin_repository') }} </span>
                        <ArrowsUpDownIcon class="w-4 h-4 "/>
                      </div>
                    </th>
                    <th scope="col"
                        class="px-2 py-3   cursor-pointer duration-300 hover:text-gray-500 hover:scale-[99%]"
                        @click="params.order_by='id';params.dir=params.dir=='ASC'? 'DESC':'ASC'; params.page=1;getData()">
                      <div class="flex items-center justify-center">
                        <span class="px-2">    {{ __('items') }} </span>
                        <ArrowsUpDownIcon class="w-4 h-4 "/>
                      </div>
                    </th>
                    <th scope="col"
                        class="px-2 py-3   cursor-pointer duration-300 hover:text-gray-500 hover:scale-[99%]"
                        @click="params.order_by='to_repo_id';params.dir=params.dir=='ASC'? 'DESC':'ASC'; params.page=1;getData()">
                      <div class="flex items-center justify-center">
                        <span class="px-2">    {{ __('destination_repository') }} </span>
                        <ArrowsUpDownIcon class="w-4 h-4 "/>
                      </div>
                    </th>

                    <th scope="col"
                        class="px-2 py-3   cursor-pointer duration-300 hover:text-gray-500 hover:scale-[99%]"
                        @click="params.order_by='from_fullname';params.dir=params.dir=='ASC'? 'DESC':'ASC'; params.page=1;getData()">
                      <div class="flex items-center justify-center">
                        <span class="px-2">    {{ __('sender') }} </span>
                        <ArrowsUpDownIcon class="w-4 h-4 "/>
                      </div>
                    </th>

                    <th scope="col"
                        class="px-2 py-3   cursor-pointer duration-300 hover:text-gray-500 hover:scale-[99%]"
                        @click="params.order_by='from_county_id';params.dir=params.dir=='ASC'? 'DESC':'ASC'; params.page=1;getData()">
                      <div class="flex items-center justify-center">
                        <span class="px-2">    {{ __('county') }} </span>
                        <ArrowsUpDownIcon class="w-4 h-4 "/>
                      </div>
                    </th>

                    <th scope="col"
                        class="px-2 py-3   cursor-pointer duration-300 hover:text-gray-500 hover:scale-[99%]"
                        @click="params.order_by='district_id';params.dir=params.dir=='ASC'? 'DESC':'ASC'; params.page=1;getData()">
                      <div class="flex items-center justify-center">
                        <span class="px-2">    {{ __('city') }} </span>
                        <ArrowsUpDownIcon class="w-4 h-4 "/>
                      </div>
                    </th>
                    <th scope="col"
                        class="px-2 py-3   cursor-pointer duration-300 hover:text-gray-500 hover:scale-[99%]"
                        @click="params.order_by='total_discount';params.dir=params.dir=='ASC'? 'DESC':'ASC'; params.page=1;getData()">
                      <div class="flex items-center justify-center">
                        <span class="px-2">    {{ __('discount') }} </span>
                        <ArrowsUpDownIcon class="w-4 h-4 "/>
                      </div>
                    </th>
                    <th scope="col"
                        class="px-2 py-3   cursor-pointer duration-300 hover:text-gray-500 hover:scale-[99%]"
                        @click="params.order_by='total_price';params.dir=params.dir=='ASC'? 'DESC':'ASC'; params.page=1;getData()">
                      <div class="flex items-center justify-center">
                        <span class="px-2">    {{ __('total_price') }} </span>
                        <ArrowsUpDownIcon class="w-4 h-4 "/>
                      </div>
                    </th>
                    <th scope="col"
                        class="px-2 py-3   cursor-pointer duration-300 hover:text-gray-500 hover:scale-[99%]"
                        @click="params.order_by='created_at';params.dir=params.dir=='ASC'? 'DESC':'ASC'; params.page=1;getData()">
                      <div class="flex items-center justify-center">
                        <span class="px-2">    {{ __('created_at') }} </span>
                        <ArrowsUpDownIcon class="w-4 h-4 "/>
                      </div>
                    </th>
                    <th scope="col"
                        class="px-2 py-3   cursor-pointer duration-300 hover:text-gray-500 hover:scale-[99%]"
                        @click="params.order_by='status';params.dir=params.dir=='ASC'? 'DESC':'ASC'; params.page=1;getData()">
                      <div class="flex items-center justify-center">
                        <span class="px-2">    {{ __('status') }} </span>
                        <ArrowsUpDownIcon class="w-4 h-4 "/>
                      </div>
                    </th>


                    <th scope="col" class="px-2 py-3">
                      {{ __('actions') }}
                    </th>
                  </tr>
                  </thead>
                  <tbody
                      class="    overflow-y-scroll   text-xs   ">
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
                      class="text-center border-b hover:bg-gray-50 " :class="idx%2==1?'bg-gray-50':'bg-white'">
                    <td class="w-4 p-4" @click="d.selected=!d.selected">
                      <div class="flex items-center">
                        <input id="checkbox-table-search-1" type="checkbox" v-model="d.selected"
                               class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2">

                      </div>
                    </td>
                    <td class="px-2 py-4    ">
                      {{ d.id }}
                    </td>
                    <td class="px-2 py-4    ">
                      {{ d.from_repo_id }}
                    </td>
                    <td class="px-2 py-4    ">
                      <div v-for="(item ,ix) in d.items" class="text-xs " :class="{'border-b':ix+1<d.items.length}">
                        {{
                          `${item.name} ( ${parseFloat(item.qty)} ${getPack(item.variation.pack_id)}  ${parseFloat(item.variation.weight)} ${__('kg')})`
                        }}
                      </div>
                    </td>
                    <td class="px-2 py-4    ">
                      {{ d.to_repo_id }}
                    </td>
                    <td class="px-2 py-4   text-xs ">
                      {{ `${d.from_fullname || ''}\n${d.from_phone || ''}` }}
                    </td>

                    <td>
                      {{ getCityName(d.from_county_id) }}
                    </td>

                    <td>
                      {{ getCityName(d.from_district_id) }}
                    </td>
                    <td>
                      {{ asPrice(d.total_discount) }}
                    </td>
                    <td>
                      {{ asPrice(d.total_price) }}
                    </td>
                    <td>
                      {{ toShamsi(d.created_at, true) }}
                    </td>
                    <td>
                      <button
                          id="dropdownStatusSetting"
                          data-te-dropdown-toggle-ref
                          aria-expanded="false"
                          data-te-ripple-init
                          data-te-ripple-color="light"
                          class="  min-w-[5rem]   px-1 cursor-pointer items-center text-center rounded-md py-[.2rem]"
                          :class="`bg-${getStatus('order_statuses', d.status).color}-100 hover:bg-${getStatus('order_statuses', d.status).color}-200 text-${getStatus('order_statuses', d.status).color}-500`">
                        {{ getStatus('order_statuses', d.status).name }}
                      </button>
                    </td>

                    <td v-if="false"
                        class="px-2     " data-te-dropdown-ref>
                      <button @click="selected=d"
                              id="dropdownRepoId"
                              data-te-dropdown-toggle-ref
                              aria-expanded="false"
                              data-te-ripple-init
                              data-te-ripple-color="light"
                              class="  min-w-[5rem]    px-1 cursor-pointer items-center text-center rounded-md py-[.2rem]"
                              :class="`bg-primary-50 border border-primary-100 hover:bg-primary-200 text-primary-500`"
                      >
                        {{ d.repo_id }}
                      </button>
                      <ul @click.stop ref="dropdownRepoIdMenu" data-te-dropdown-menu-ref
                          class="p-4  absolute z-[1050]    hidden   list-none overflow-hidden rounded-lg border-none bg-white bg-clip-padding text-center text-base shadow-lg [&[data-te-dropdown-show]]:block"
                          tabindex="-1" role="menu" aria-orientation="vertical" aria-label="RepoId menu"
                          aria-labelledby="dropdownRepoId">
                        <li
                            class="   text-sm  ">
                          <span class="text-xs py-2 text-danger-500">{{ __('help_change_repo') }}</span>
                          <div class="flex flex-col  space-y-2 text-start ">
                            <div class="flex items-stretch">
                              <div @click.stop="d.new_repo_id=null "
                                   class="bg-red-500 cursor-pointer text-white align-middle rounded-s hover:bg-red-400">
                                <XMarkIcon class="w-8 h-6 my-2 "/>
                              </div>
                              <select class="grow rounded-e border-400 cursor-pointer" name=""
                                      @change="($e)=>{ d.new_repo_id=$e.target.value;}"
                                      :id=" `selectRepo${d.id}` " v-model="d.new_repo_id">
                                <option class="text-start rounded p-2 m-2"
                                        v-for="d in filteredRepositories[d.agency_id] "
                                        :value="d.id">
                                  <div class="p-2"> {{ __(d.name) }}</div>
                                </option>
                              </select>
                            </div>

                            <span class="text-xs   pt-2  font-light text-gray-400">
                               {{ `${__('get_from_repo')} (${__('max')}: ${d.in_repo} ${__('unit')})` }}
                            </span>
                            <input
                                @keydown.enter="edit({'idx':idx,'id':d.id,'cmnd':'change-repo','repo_id':d.new_repo_id,'in_repo':d.new_in_repo})"
                                type="number" min="0" class="grow mb-2  p-1 rounded  border-gray-400"
                                v-model="d.new_in_repo">

                            <button class="bg-success-100 text-success-700 p-2 rounded-lg  hover:bg-success-50 w-full"
                                    @click="edit({'idx':idx,'id':d.id,'cmnd':'change-repo','repo_id':d.new_repo_id,'in_repo':d.new_in_repo})">
                              {{ __('edit') }}
                            </button>
                          </div>
                        </li>


                      </ul>
                    </td>

                    <td v-if="false" class="px-2 py-4    ">
                      {{ d.is_private ? __('internal') : __('public') }}
                    </td>


                    <td class="px-2 py-4">
                      <!-- Actions Group -->
                      <div
                          class=" inline-flex rounded-md shadow-sm transition duration-150 ease-in-out hover:bg-primary-600 hover:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.3),0_4px_18px_0_rgba(59,113,202,0.2)] focus:bg-primary-600 focus:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.3),0_4px_18px_0_rgba(59,113,202,0.2)] focus:outline-none focus:ring-0 active:bg-primary-700 active:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.3),0_4px_18px_0_rgba(59,113,202,0.2)]"
                          role="group">
                        <Link
                            type="button" :href="route('admin.panel.repository.order.edit',d.id)"
                            class="inline-block rounded  bg-blue-500 text-white px-6  py-2 text-xs font-medium uppercase leading-normal text-white transition duration-150 ease-in-out hover:bg-blue-400   focus:outline-none focus:ring-0  "
                            data-te-ripple-init
                            data-te-ripple-color="light">
                          {{ __('details') }}
                        </Link>
                        <Link
                            type="button" :href="route('admin.panel.order.agency.factor',d.id)"
                            class="flex mx-1 rounded  bg-indigo-500 text-white px-6  py-2 text-xs font-medium   leading-normal text-white transition duration-150 ease-in-out hover:bg-indigo-400   focus:outline-none focus:ring-0  "
                            data-te-ripple-init
                            data-te-ripple-color="light">
                          <PrinterIcon class="h-4 w-4"/>
                          <span class="mx-1">{{ __('factor') }}</span>
                        </Link>
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
  Squares2X2Icon,
  CurrencyDollarIcon,
  PrinterIcon,

} from "@heroicons/vue/24/outline";
import Image from "@/Components/Image.vue"
import Tooltip from "@/Components/Tooltip.vue"
import Selector from "@/Components/Selector.vue"
import {Dropdown, Modal, initTE} from "tw-elements";
import TextInput from "@/Components/TextInput.vue";


export default {
  data() {
    return {
      status: this.route().params.status,
      errors: {},
      filteredRepositories: [],
      repoModal: null,
      selected: null,
      selectedParams: null,
      params: {

        page: 1,
        search: null,
        is_from_agency: true,
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
  directives: {}
  ,
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
    Squares2X2Icon,
    Selector,
    CurrencyDollarIcon,
    PrinterIcon,
  },
  mounted() {

    this.getData();
    this.tableWrapper = document.querySelector('table  ').parentElement;
    // var element_position = tableWrapper.getBoundingClientRect().top;
    for (const idx in this.$page.props.agencyRepositories) {
      let agencyId = this.$page.props.agencyRepositories[idx].id;
      this.filteredRepositories[agencyId] =
          this.$page.props.agencyRepositories.filter(e => {
            return e.id == this.$page.props.agencyRepositories[idx].id
          })[0].repositories.map((e) => {
            return {
              id: e.id, name:
                  `(${e.id}) ${e.name}`
            }
          });

    }
    // this.log(document.body.clientHeight)
    // this.log(tableWrapper.offsetTop)
    // this.log(tableWrapper.offsetHeight)

    // tableWrapper.classList.add(`h-[300px]`);
    // this.log(tableWrapper.classList)
    // this.showDialog('danger', 'message',()=>{});
    // this.isLoading(false);


  },
  methods: {
    setTableHeight() {
      let a = window.innerHeight - this.tableWrapper.offsetTop;
      // this.tableWrapper.classList.add(`h-[60vh]`);
      this.tableWrapper.style.height = `${a}px`;
      // this.tableWrapper.firstChild.classList.add(`overflow-y-scroll`);
    },

    getData() {

      this.loading = true;
      this.data = [];
      if (this.status)
        this.params.status = this.status;

      window.axios.get(route('admin.panel.repository.order.search'), {
        params: this.params
      }, {})
          .then((response) => {
            this.data = response.data.data;
            this.data.forEach(el => {
              el.selected = false;
            });
            delete response.data.data;
            this.pagination = response.data;
            this.setTableHeight();
            this.$nextTick(() => {

              // this.initTableDropdowns();
              // this.initTableModals();

            });

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
      this.errors = {};
      window.axios.patch(route('admin.panel.variation.update'), params,
          {})
          .then((response) => {
            if (response.data && response.data.message) {
              this.showToast('success', response.data.message);

            }

            if (response.data.status) {
              this.data[params.idx].status = response.data.status;
            } else {
              this.getData();
            }
            this.selected = null;


          })

          .catch((error) => {
            this.error = this.getErrors(error);
            if (error.response && error.response.data) {
              this.errors = error.response.data.errors || {};


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

      window.axios.patch(route('admin.panel.variation.update'), params,
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
<style lang="scss">
.Flipped, .Flipped .Content {
  transform: rotateX(180deg);
  -ms-transform: rotateX(180deg); /* IE 9 */
  -webkit-transform: rotateX(180deg); /* Safari and Chrome */
}
</style>