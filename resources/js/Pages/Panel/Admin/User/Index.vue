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
          <h1 class="text-2xl font-semibold">{{ __('users_list') }}</h1>
        </div>
        <div>
          <Link :href="route('admin.panel.user.create')"
                class="inline-flex items-center  justify-center px-4 py-2 bg-green-500 border border-transparent rounded-md font-semibold  transition-all duration-500 text-white     hover:bg-green-600 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
          >
            {{ __('new_user') }}
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
                  @click="params.order_by='fullname';params.dir=params.dir=='ASC'? 'DESC':'ASC'; params.page=1;getData()">
                <div class="flex items-center justify-center">
                  <span class="px-2">  {{ __('fullname') }}</span>
                  <ArrowsUpDownIcon class="w-4 h-4 "/>
                </div>
              </th>

              <th scope="col"
                  class="px-2 py-3   cursor-pointer duration-300 hover:text-gray-500 hover:scale-[105%]"
                  @click="params.order_by='phone';params.dir=params.dir=='ASC'? 'DESC':'ASC'; params.page=1;getData()">
                <div class="flex items-center justify-center">
                  <span class="px-2">    {{ __('phone') }} </span>
                  <ArrowsUpDownIcon class="w-4 h-4 "/>
                </div>
              </th>

              <th scope="col"
                  class="px-2 py-3   cursor-pointer duration-300 hover:text-gray-500 hover:scale-[105%]"
                  @click="params.order_by='wallet';params.dir=params.dir=='ASC'? 'DESC':'ASC'; params.page=1;getData()">
                <div class="flex items-center justify-center">
                  {{ __('wallet') }}
                </div>
              </th>
              <th scope="col"
                  class="px-2 py-3   cursor-pointer duration-300 hover:text-gray-500 hover:scale-[105%]"
                  @click="params.order_by='status';params.dir=params.dir=='ASC'? 'DESC':'ASC'; params.page=1;getData()">
                <div class="flex items-center justify-center">
                  <span class="px-2">    {{ __('status') }} </span>
                  <ArrowsUpDownIcon class="w-4 h-4 "/>
                </div>
              </th>
              <th scope="col"
                  class="px-2 py-3   cursor-pointer duration-300 hover:text-gray-500 hover:scale-[105%]"
                  @click="params.order_by='role';params.dir=params.dir=='ASC'? 'DESC':'ASC'; params.page=1;getData()">
                <div class="flex items-center justify-center">
                  <span class="px-2">    {{ __('role') }}  </span>
                  <ArrowsUpDownIcon class="w-4 h-4 "/>
                </div>
              </th>
              <th scope="col"
                  class="px-2 py-3   cursor-pointer duration-300 hover:text-gray-500 hover:scale-[105%]"
                  @click="params.order_by='access';params.dir=params.dir=='ASC'? 'DESC':'ASC'; params.page=1;getData()">
                <div class="flex items-center justify-center">
                  <span class="px-2">    {{ __('access') }}  </span>
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
                <Image class="w-10 h-10 rounded-full" :src="`${route('storage.users')}/${d.id}.jpg`"
                       :alt="cropText(d.fullname,5)"/>
                <Link class="px-3 hover:text-gray-500" :href="route('admin.panel.user.edit',d.id)">
                  <div class="text-base font-semibold">{{ cropText(d.fullname, 30) }}</div>
                  <div class="font-normal text-gray-500">{{ }}</div>
                </Link>
              </td>

              <td class="px-8 py-4">
                {{ d.phone }}
              </td>

              <td
                  class="px-2 py-4    " data-te-dropdown-ref>
                <button
                    id="dropdownViewFee"
                    data-te-dropdown-toggle-ref
                    aria-expanded="false"
                    data-te-ripple-init
                    data-te-ripple-color="light"
                    class="  min-w-[5rem] bg-gray-100 hover:bg-gray-200 px-1 cursor-pointer items-center text-center rounded-md py-[.2rem]"
                    :class="`bg-primary-100 hover:bg-primary-200 text-primary-500`"
                >
                  {{ asPrice(d.wallet) }}
                </button>
                <ul ref="dropdownViewFeeMenu" data-te-dropdown-menu-ref
                    class="p-4  absolute z-[1000]    hidden   list-none overflow-hidden rounded-lg border-none bg-white bg-clip-padding text-center text-base shadow-lg [&[data-te-dropdown-show]]:block"
                    tabindex="-1" role="menu" aria-orientation="vertical" aria-label="User menu"
                    aria-labelledby="dropdownViewFee">
                  <li
                      class="   text-sm  ">
                    <span class="text-xs py-2 text-primary-500">{{ __('insert_new_value') }}</span>
                    <div class="flex items-center ">
                      <input @keydown.enter="edit({'idx':idx,'id':d.id,'cmnd':'wallet','wallet':d.wallet})"
                             type="number" min="0" class="grow my-2  p-1 rounded-lg border-gray-400"
                             v-model="d.wallet">
                      <span class="text-xs ms-1 font-light text-gray-400">{{ __('currency') }}</span>
                    </div>
                  </li>


                </ul>
              </td>
              <td class="px-2 py-4    " data-te-dropdown-ref>
                <button
                    id="dropdownStatusSetting"
                    data-te-dropdown-toggle-ref
                    aria-expanded="false"
                    data-te-ripple-init
                    data-te-ripple-color="light"
                    class="  min-w-[5rem]  px-1 cursor-pointer items-center text-center rounded-md py-[.2rem]"
                    :class="`bg-${d.is_block || !d.is_active?'danger':'success'}-100 hover:bg-${d.is_block || !d.is_active?'danger':'success'}-200 text-${d.is_block || !d.is_active?'danger':'success'}-500`">
                  {{ d.is_block ? __('blocked') : !d.is_active ? __('inactive') : __('active') }}
                </button>
                <ul ref="statusMenu" data-te-dropdown-menu-ref
                    class="  absolute z-[1000]   m-0 hidden   list-none overflow-hidden rounded-lg border-none bg-white bg-clip-padding text-center text-base shadow-lg [&[data-te-dropdown-show]]:block"
                    tabindex="-1" role="menu" aria-orientation="vertical" aria-label="User menu"
                    aria-labelledby="dropdownStatusSetting">

                  <li v-if="d.is_active || d.is_block" role="menuitem"
                      @click="edit({'idx':idx,'id':d.id,'cmnd':'inactive'})"
                      class="   cursor-pointer   text-sm   transition-colors hover:bg-danger-100">
                    <div class="flex items-center text-danger  px-6 py-2 justify-between ">
                      {{ __('inactive') }}
                    </div>
                    <hr class="border-gray-200 ">
                  </li>

                  <li v-if=" !d.is_block" role="menuitem"
                      @click="edit({'idx':idx,'id':d.id,'cmnd':'block'})"
                      class="   cursor-pointer   text-sm text-orange-700 transition-colors hover:bg-orange-100">
                    <div class="flex items-center  px-6 py-2 justify-between ">
                      {{ __('blocked') }}
                    </div>
                    <hr class="border-gray-200 ">
                  </li>
                  <li v-if="!d.is_active || d.is_block" role="menuitem"
                      @click="edit({'idx':idx,'id':d.id,'cmnd':'active'})"
                      class="   cursor-pointer   text-sm text-success-700 transition-colors hover:bg-success-100">
                    <div class="flex items-center  px-6 py-2 justify-between ">
                      {{ __('activate') }}
                    </div>
                    <hr class="border-gray-200 ">
                  </li>
                </ul>
              </td>
              <td
                  class="px-2 py-4    " data-te-dropdown-ref>
                <button
                    id="dropdownRole"
                    data-te-dropdown-toggle-ref
                    aria-expanded="false"
                    data-te-ripple-init
                    data-te-ripple-color="light"
                    class="  min-w-[5rem]  bg-gray-100 hover:bg-gray-200  px-1 cursor-pointer items-center text-center rounded-md py-[.2rem]"
                    :class="`bg-${d.role=='ad' || d.role=='go'?'primary':'gray'}-100 hover:bg-${d.role=='ad' || d.role=='go'?'primary':'gray'}-200 text-${d.role=='ad' || d.role=='go'?'primary':'gray'}-500`"
                >
                  {{ __(d.role) }}
                </button>
                <ul ref="dropdownRole" data-te-dropdown-menu-ref
                    class="  absolute z-[1000]     hidden   list-none overflow-hidden rounded-lg border-none bg-white bg-clip-padding text-center text-base shadow-lg [&[data-te-dropdown-show]]:block"
                    tabindex="-1" role="menu" aria-orientation="vertical" aria-label="User menu"
                    aria-labelledby="dropdownRole">


                  <li v-if="d.role!='ad'" role="menuitem"
                      @click="edit({'idx':idx,'id':d.id,'cmnd':'role-ad'})"
                      class="   cursor-pointer   text-sm   transition-colors hover:bg-gray-100">
                    <div class="flex items-center text-gray  px-6 py-2 justify-between ">
                      {{ __('ad') }}
                    </div>
                    <hr class="border-gray-200 ">
                  </li>
                  <li v-if="d.role!='us'" role="menuitem"
                      @click="edit({'idx':idx,'id':d.id,'cmnd':'role-us'})"
                      class="   cursor-pointer   text-sm   transition-colors hover:bg-gray-100">
                    <div class="flex items-center text-gray  px-6 py-2 justify-between ">
                      {{ __('us') }}
                    </div>
                    <hr class="border-gray-200 ">
                  </li>
                  <li v-if="d.role!='go'" role="menuitem"
                      @click="edit({'idx':idx,'id':d.id,'cmnd':'role-go'})"
                      class="   cursor-pointer   text-sm   transition-colors hover:bg-gray-100">
                    <div class="flex items-center text-gray  px-6 py-2 justify-between ">
                      {{ __('go') }}
                    </div>
                    <hr class="border-gray-200 ">
                  </li>

                </ul>


              </td>

              <td
                  class="px-2 py-4    " data-te-dropdown-ref>
                <button
                    id="dropdownViewFee"
                    data-te-dropdown-toggle-ref
                    aria-expanded="false"
                    data-te-ripple-init
                    data-te-ripple-color="light"
                    class="  min-w-[5rem] bg-gray-100 hover:bg-gray-200 px-1 cursor-pointer items-center text-center rounded-md py-[.2rem]"
                    :class="`bg-primary-100 hover:bg-primary-200 text-primary-500`"
                >
                  {{ d.access || '-' }}
                </button>
                <ul ref="dropdownViewFeeMenu" data-te-dropdown-menu-ref
                    class="p-4  absolute z-[1000]    hidden   list-none overflow-hidden rounded-lg border-none bg-white bg-clip-padding text-center text-base shadow-lg [&[data-te-dropdown-show]]:block"
                    tabindex="-1" role="menu" aria-orientation="vertical" aria-label="User menu"
                    aria-labelledby="dropdownViewFee">
                  <li
                      class="   text-sm  ">
                    <span class="text-xs py-2 text-primary-500">{{ __('accesses') }}</span>
                    <div class="my-2">
                      <div v-for="(access,ix) in $page.props.accesses" class=" flex items-center">
                        <label
                            class="relative flex items-center    p-3 rounded-full cursor-pointer "
                            :for="`access${ix}`"
                            data-ripple-dark="true"
                        >
                          <input :value="access.role" v-model="d.accesses"
                                 :checked="  (d.accesses).indexOf(access.role)>-1"
                                 :id="`access${ix}`"
                                 type="checkbox"
                                 class="before:content[''] peer relative h-5 w-5 cursor-pointer appearance-none rounded-md border border-blue-gray-200 transition-all before:absolute before:top-2/4 before:left-2/4 before:block before:h-12 before:w-12 before:-translate-y-2/4 before:-translate-x-2/4 before:rounded-full before:bg-blue-gray-500 before:opacity-0 before:transition-opacity checked:border-primary-500 checked:bg-primary-500 checked:before:bg-primary-500 hover:before:opacity-10"
                          />
                          <div
                              class="absolute text-white transition-opacity opacity-0 pointer-events-none top-2/4 left-2/4 -translate-y-2/4 -translate-x-2/4 peer-checked:opacity-100">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-3.5 w-3.5"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                                stroke="currentColor"
                                stroke-width="1"
                            >
                              <path
                                  fill-rule="evenodd"
                                  d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                  clip-rule="evenodd"
                              ></path>
                            </svg>
                          </div>
                        </label>
                        <label @click.stop
                               class="mt-px font-light text-sm text-gray-500 cursor-pointer select-none"
                               :for="`access${ix}`"
                        >
                          {{ __(access.name) }}
                        </label>
                      </div>
                    </div>

                  </li>
                  <li class="border-t py-1 ">
                    <button @click="edit({cmnd:'access',idx:idx,id:d.id,accesses:d.accesses})"
                            class="bg-primary-500 rounded w-full text-white p-2 hover:bg-primary-400">
                      {{ __('reg') }}
                    </button>
                  </li>

                </ul>
              </td>

              <td class="px-2 py-4">
                <!-- Actions Group -->
                <div
                    class=" inline-flex rounded-md shadow-[0_4px_9px_-4px_#3b71ca] transition duration-150 ease-in-out hover:bg-primary-600 hover:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.3),0_4px_18px_0_rgba(59,113,202,0.2)] focus:bg-primary-600 focus:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.3),0_4px_18px_0_rgba(59,113,202,0.2)] focus:outline-none focus:ring-0 active:bg-primary-700 active:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.3),0_4px_18px_0_rgba(59,113,202,0.2)]"
                    role="group">
                  <Link
                      type="button" :href="route('admin.panel.user.edit',d.id)"
                      class="inline-block rounded  bg-orange-500 text-white px-6  py-2 text-xs font-medium uppercase leading-normal text-white transition duration-150 ease-in-out hover:bg-orange-400   focus:outline-none focus:ring-0  "
                      data-te-ripple-init
                      data-te-ripple-color="light">
                    {{ __('edit') }}
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

    // this.showDialog('danger', 'message',()=>{});
    // this.isLoading(false);
  },
  methods: {
    getData() {

      this.loading = true;
      this.data = [];
      window.axios.get(route('admin.panel.user.search'), {
        params: this.params
      }, {})
          .then((response) => {
            this.data = response.data.data;
            this.data.forEach(el => {
              el.selected = false;
              el.accesses = el.access ? el.access.split(',') : [];
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
    edit(params) {
      this.isLoading(true);
      window.axios.patch(route('admin.panel.user.update'), params,
          {})
          .then((response) => {
            if (response.data && response.data.message) {
              this.showToast('success', response.data.message);

            }
            if (response.data.wallet) {
              this.data[params.idx].wallet = response.data.wallet;
              this.user.wallet = response.data.wallet;
            }
            if (response.data.is_active != null) {
              this.data[params.idx].is_active = response.data.is_active;
            }
            if (response.data.is_block != null) {
              this.data[params.idx].is_block = response.data.is_block;
            }
            if (response.data.role) {
              this.data[params.idx].role = response.data.role;
            }
            if (response.data.access) {
              this.data[params.idx].access = response.data.access;
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

    }
  },

}
</script>
