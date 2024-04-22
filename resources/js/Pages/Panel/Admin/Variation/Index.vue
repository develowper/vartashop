<template>

  <Panel>
    <template v-slot:header>
      <title>{{__('panel')}}</title>
    </template>


    <template v-slot:content>
      <div class="   ">
        <!-- Content header -->
        <div
            class="flex  items-center justify-between px-4 py-2 text-primary-500 border-b md:py-4">
          <div class="flex">
            <Bars2Icon class="h-7 w-7 mx-3"/>
            <h5 class="  font-semibold">{{ __('variations_list') }}</h5>
          </div>
          <div>
            <Link :href="route('admin.panel.variation.create')"
                  class="inline-flex items-center  justify-center px-4 py-2 bg-green-500 border border-transparent rounded-md font-semibold  transition-all duration-500 text-white     hover:bg-green-600 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
            >
              {{ __('new_product') }}
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
                        class="px-4 py-3   cursor-pointer duration-300 hover:text-gray-500 hover:scale-[99%]"
                        @click="params.order_by='name';params.dir=params.dir=='ASC'? 'DESC':'ASC'; params.page=1;getData()">
                      <div class="flex items-center justify-center">
                        <span class="px-2">  {{ __('product_title') }}</span>
                        <ArrowsUpDownIcon class="w-4 h-4 "/>
                      </div>
                    </th>

                    <th scope="col"
                        class="px-2 py-3   cursor-pointer duration-300 hover:text-gray-500 hover:scale-[99%]"
                        @click="params.order_by='repo_id';params.dir=params.dir=='ASC'? 'DESC':'ASC'; params.page=1;getData()">
                      <div class="flex items-center justify-center">
                        <span class="px-2">    {{ __('repository_id') }} </span>
                        <ArrowsUpDownIcon class="w-4 h-4 "/>
                      </div>
                    </th>

                    <th scope="col"
                        class="px-2 py-3   cursor-pointer duration-300 hover:text-gray-500 hover:scale-[99%]"
                        @click="params.order_by='grade';params.dir=params.dir=='ASC'? 'DESC':'ASC'; params.page=1;getData()">
                      <div class="flex items-center justify-center">
                        <span class="px-2">    {{ __('grade') }} </span>
                        <ArrowsUpDownIcon class="w-4 h-4 "/>
                      </div>
                    </th>

                    <th scope="col"
                        class="px-2 py-3   cursor-pointer duration-300 hover:text-gray-500 hover:scale-[99%]"
                        @click="params.order_by='pack_id';params.dir=params.dir=='ASC'? 'DESC':'ASC'; params.page=1;getData()">
                      <div class="flex items-center justify-center">
                        <span class="px-2">    {{ __('pack') }} </span>
                        <ArrowsUpDownIcon class="w-4 h-4 "/>
                      </div>
                    </th>

                    <th scope="col"
                        class="px-2 py-3   cursor-pointer duration-300 hover:text-gray-500 hover:scale-[99%]"
                        @click="params.order_by='weight';params.dir=params.dir=='ASC'? 'DESC':'ASC'; params.page=1;getData()">
                      <div class="flex items-center justify-center">
                        <span class="px-2">    {{ __('weight') }} </span>
                        <ArrowsUpDownIcon class="w-4 h-4 "/>
                      </div>
                    </th>

                    <th scope="col"
                        class="px-2 py-3   cursor-pointer duration-300 hover:text-gray-500 hover:scale-[99%]"
                        @click="params.order_by='price';params.dir=params.dir=='ASC'? 'DESC':'ASC'; params.page=1;getData()">
                      <div class="flex items-center justify-center">
                        <span class="px-2">    {{ __('fee') }} </span>
                        <ArrowsUpDownIcon class="w-4 h-4 "/>
                      </div>
                    </th>
                    <th scope="col"
                        class="px-2 py-3   cursor-pointer duration-300 hover:text-gray-500 hover:scale-[99%]"
                        @click="params.order_by='auction_price';params.dir=params.dir=='ASC'? 'DESC':'ASC'; params.page=1;getData()">
                      <div class="flex items-center justify-center">
                        <span class="px-2">    {{ __('auction_fee') }} </span>
                        <ArrowsUpDownIcon class="w-4 h-4 "/>
                      </div>
                    </th>

                    <th scope="col"
                        class="px-2 py-3   cursor-pointer duration-300 hover:text-gray-500 hover:scale-[99%]"
                        @click="params.order_by='in_shop';params.dir=params.dir=='ASC'? 'DESC':'ASC'; params.page=1;getData()">
                      <div class="flex items-center justify-center">
                        <span class="px-2">    {{ __('shop_count') }} </span>
                        <ArrowsUpDownIcon class="w-4 h-4 "/>
                      </div>
                    </th>

                    <th scope="col"
                        class="px-2 py-3   cursor-pointer duration-300 hover:text-gray-500 hover:scale-[99%]"
                        @click="params.order_by='in_repo';params.dir=params.dir=='ASC'? 'DESC':'ASC'; params.page=1;getData()">
                      <div class="flex items-center justify-center">
                        <span class="px-2">    {{ __('repository_count') }} </span>
                        <ArrowsUpDownIcon class="w-4 h-4 "/>
                      </div>
                    </th>
                    <th v-if="false" scope="col"
                        class="px-2 py-3   cursor-pointer duration-300 hover:text-gray-500 hover:scale-[99%]"
                        @click="params.order_by='is_private';params.dir=params.dir=='ASC'? 'DESC':'ASC'; params.page=1;getData()">
                      <div class="flex items-center justify-center">
                        <span class="px-2">    {{ __('repository_special') }} </span>
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
                    <td
                        class="flex  text-xs items-center px-1 py-4 text-gray-900  ">
                      <Image class="w-10 h-10 cursor-pointer rounded-full"
                             :src="`${route('storage.variations')}/${d.id}/thumb.jpg`"
                             :data-lity="`${route('storage.variations')}/${d.id}/thumb.jpg`"
                             :alt="cropText(d.title,5)"/>
                      <Link class="px-1 whitespace-nowra hover:text-gray-500"
                            :href="route('admin.panel.variation.edit',d.id)">
                        <div class=" font-semibold ">{{ cropText(d.name, 30) }}</div>
                        <div class="font-normal text-gray-500">{{ }}</div>
                      </Link>
                    </td>
                    <td>

                      <button
                          @click="d.idx=idx;d.cmnd='change-repo';selected=d; "
                          id="RepoId"
                          aria-expanded="false"
                          data-te-ripple-init
                          data-te-ripple-color="light"
                          class="  min-w-[5rem]    p-2 cursor-pointer items-center text-center rounded-md  "
                          :class="`bg-primary-50 border border-primary-300 hover:bg-primary-200 text-primary-500`"
                      >
                        {{ d.repo_id }}
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
                                      @change="($e)=>{log(d.agency_id);d.new_repo_id=$e.target.value;}"
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


                    <td class="px-2 py-4    ">
                      <button
                          @click="d.idx=idx;d.cmnd='change-grade-pack-weight';d.new_grade=d.grade;d.new_pack_id=d.pack_id;d.new_in_repo=0;selected=d; "
                          id="GradeId"
                          aria-expanded="false"
                          data-te-ripple-init
                          data-te-ripple-color="light"
                          class="  min-w-[5rem]    p-2 cursor-pointer items-center text-center rounded-md  "
                          :class="`bg-blue-50 border border-blue-300 hover:bg-blue-200 text-primary-500`"
                      >
                        {{ d.grade }}
                      </button>
                    </td>

                    <td class="px-2 py-4    ">
                      <button
                          @click="d.idx=idx;d.cmnd='change-grade-pack-weight';d.new_grade=d.grade;d.new_pack_id=d.pack_id;d.new_in_repo=0;selected=d; "
                          id="PackId"
                          aria-expanded="false"
                          data-te-ripple-init
                          data-te-ripple-color="light"
                          class="  min-w-[5rem]    p-2 cursor-pointer items-center text-center rounded-md  "
                          :class="`bg-blue-50 border border-blue-300 hover:bg-blue-200 text-primary-500`"
                      >
                        {{ getPack(d.pack_id) }}
                      </button>

                    </td>
                    <td class="px-2 py-4    ">
                      <button
                          @click="d.idx=idx;d.cmnd='change-grade-pack-weight';d.new_grade=d.grade;d.new_pack_id=d.pack_id;d.new_in_repo=0;selected=d; "
                          id="WeightId"
                          aria-expanded="false"
                          data-te-ripple-init
                          data-te-ripple-color="light"
                          class="  min-w-[5rem]    p-2 cursor-pointer items-center text-center rounded-md  "
                          :class="`bg-blue-50 border border-blue-300 hover:bg-blue-200 text-primary-500`"
                      >
                        {{ parseFloat(d.weight) }}
                      </button>

                    </td>
                    <td class="px-2 py-4    ">
                      <button
                          @click="d.idx=idx;d.cmnd='change-price';d.new_price=d.price;d.new_auction_price=d.auction_price; selected=d; "
                          id="PriceId"
                          aria-expanded="false"
                          data-te-ripple-init
                          data-te-ripple-color="light"
                          class="  min-w-[5rem]    p-2 cursor-pointer items-center text-center rounded-md  "
                          :class="`bg-indigo-50 border border-indigo-300 hover:bg-indigo-200 text-indigo-500`"
                      >
                        {{ asPrice(d.price) }}
                      </button>

                    </td>
                    <td class="px-2 py-4    ">
                      <button
                          @click="d.idx=idx;d.cmnd='change-price';d.new_price=d.price;d.new_auction_price=d.auction_price; selected=d; "
                          id="PriceId"
                          aria-expanded="false"
                          data-te-ripple-init
                          data-te-ripple-color="light"
                          class="  min-w-[5rem]    p-2 cursor-pointer items-center text-center rounded-md  "
                          :class="`bg-indigo-50 border border-indigo-300 hover:bg-indigo-200 text-indigo-500`"
                      >
                        {{ asPrice(d.auction_price) }}
                      </button>

                    </td>
                    <td class="px-2 py-4    ">
                      <button
                          @click="d.idx=idx;d.cmnd='change-qty';d.new_in_shop=parseFloat(d.in_shop);d.new_in_repo=parseFloat(d.in_repo); selected=d; "
                          id="InShopId"
                          aria-expanded="false"
                          data-te-ripple-init
                          data-te-ripple-color="light"
                          class="  min-w-[5rem]    p-2 cursor-pointer items-center text-center rounded-md  "
                          :class="`bg-sky-50 border border-sky-300 hover:bg-sky-200 text-sky-500`"
                      >
                        {{ parseFloat(d.in_shop) }}
                      </button>

                    </td>
                    <td class="px-2 py-4    ">
                      <button
                          @click="d.idx=idx;d.cmnd='change-qty';d.new_in_shop=parseFloat(d.in_shop);d.new_in_repo=parseFloat(d.in_repo); selected=d; "
                          id="InShopId"
                          aria-expanded="false"
                          data-te-ripple-init
                          data-te-ripple-color="light"
                          class="  min-w-[5rem]    p-2 cursor-pointer items-center text-center rounded-md  "
                          :class="`bg-sky-50 border border-sky-300 hover:bg-sky-200 text-sky-500`"
                      >
                        {{ parseFloat(d.in_repo) }}
                      </button>
                    </td>
                    <td v-if="false" class="px-2 py-4    ">
                      {{ d.is_private ? __('internal') : __('public') }}
                    </td>


                    <td class="px-2 py-4">
                      <!-- Actions Group -->
                      <div
                          class=" inline-flex rounded-md shadow-sm transition duration-150 ease-in-out    focus:bg-primary-600 focus:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.3),0_4px_18px_0_rgba(59,113,202,0.2)] focus:outline-none focus:ring-0 active:bg-primary-700 active:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.3),0_4px_18px_0_rgba(59,113,202,0.2)]"
                          role="group">

                        <Link
                            type="button" :href="route('admin.panel.variation.edit',d.id)"
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
                          {{ `${selected.name} | ${getPack(selected.pack_id)} | ${__('grade')} ${selected.grade}` }}
                        </h3>
                      </div>
                      <div class="m-2  text-start">
                        <!--                         modal body-->
                        <div class="mt-2">

                          <div v-if="selected.cmnd=='change-repo'"
                               class="   text-sm text-gray-500 ">
                            <span class="text-xs py-2 text-danger-500">{{ __('help_change_repo') }}</span>
                            <div class="flex flex-col  space-y-2 text-start ">

                              <div class="flex  flex-col justify-start  ">
                                <UserSelector :colsData="['id','name','phone','agency_id']"
                                              :labelsData="['id','name','phone','agency_id']"
                                              :callback="{'level':getAgency}" :error="errors.new_repo_id"
                                              :link="route('admin.panel.repository.search')+(`?status=active` )"
                                              :label="__('repository')"
                                              :id="'repository'" v-model:selected="selected.new_repo_id"
                                              :preload="null">
                                  <template v-slot:selector="props">
                                    <div :class="props.selectedText?'py-2':'py-2'"
                                         class=" px-4 border border-gray-300 rounded hover:bg-gray-100 cursor-pointer flex items-center ">
                                      <div class="grow">
                                        {{ props.selectedText ?? __('select') }}
                                      </div>
                                      <div v-if="props.selectedText"
                                           class="bg-danger rounded p-2   cursor-pointer text-white hover:bg-danger-400"
                                           @click.stop="props.clear()">
                                        <XMarkIcon class="w-5 h-5"/>

                                      </div>
                                    </div>
                                  </template>
                                </UserSelector>


                                <div class="my-2">
                                  <TextInput
                                      id="new_in_repo"
                                      type="number"
                                      :placeholder="`${__('get_from_repo')} (${__('max')}: ${parseFloat(selected.in_repo)} ${__('unit')})`"
                                      classes="  "
                                      v-model="selected.new_in_repo"
                                      :autocomplete="selected.new_in_repo"
                                      :error="  errors.new_in_repo">

                                    <template v-slot:prepend>
                                      <div class="p-3">
                                        <Bars2Icon class="h-5 w-5"/>
                                      </div>
                                    </template>
                                  </TextInput>
                                </div>
                                <button
                                    class="bg-success-200 text-success-700 p-2 rounded-lg  hover:bg-success-300 w-full"
                                    @click="edit({'idx':selected.idx ,'id':selected.id,'cmnd':'change-repo','new_repo_id':selected.new_repo_id,'new_in_repo':selected.new_in_repo})">
                                  {{ __('accept') }}
                                </button>

                              </div>
                            </div>
                          </div>
                          <div v-if="selected.cmnd=='change-grade-pack-weight'"
                               class="   text-sm text-gray-500 ">
                            <span class="text-xs py-2 text-danger-500">{{ __('help_change_grade_pack') }}</span>
                            <div class="flex flex-col  space-y-2 text-start ">

                              <div class="flex flex-col  ">

                                <Selector ref="gradeSelector" v-model="selected.new_grade"
                                          @change="($e)=>{ selected.new_grade=$e.target.value;}"
                                          :data="$page.props.grades.map((e)=> {return {id:e,name:e}})"
                                          :error="errors.new_grade"
                                          :label="__('grade')" :id=" `selectGrade${selected.id}`">
                                  <template v-slot:append>
                                    <div class="  p-3">
                                      <Squares2X2Icon class="h-5 w-5"/>
                                    </div>
                                  </template>
                                </Selector>
                                <Selector ref="packSelector" v-model="selected.new_pack_id"
                                          @change="($e)=>{ selected.new_pack_id=$e.target.value;}"
                                          :data="$page.props.packs"
                                          :error="errors.new_pack_id"
                                          :label="__('pack')" :id=" `selectPack${selected.id}`">
                                  <template v-slot:append>
                                    <div class="  p-3">
                                      <Squares2X2Icon class="h-5 w-5"/>
                                    </div>
                                  </template>
                                </Selector>
                                <div class="my-2">
                                  <TextInput
                                      id="new_in_repo"
                                      type="number"
                                      :placeholder="`${__('get_from_repo')} (${__('max')}: ${parseFloat(selected.in_repo)} ${__('unit')})`"
                                      classes="  "
                                      v-model="selected.new_in_repo"
                                      :autocomplete="selected.new_in_repo"
                                      :error="  errors.new_in_repo">

                                    <template v-slot:prepend>
                                      <div class="p-3">
                                        <Bars2Icon class="h-5 w-5"/>
                                      </div>
                                    </template>
                                  </TextInput>
                                </div>
                                <div class="my-2">
                                  <TextInput v-if="selected.new_pack_id!=1"
                                             id="new_unit_weight"
                                             type="number"
                                             :placeholder="`${__('new_unit_weight')} (${__('max')}: ${parseFloat(selected.new_in_repo*selected.weight)} ${__('unit')})`"
                                             classes="  "
                                             v-model="selected.new_unit_weight"
                                             :autocomplete="selected.new_unit_weight"
                                             :error="  errors.new_unit_weight">

                                    <template v-slot:prepend>
                                      <div class="p-3">
                                        <Bars2Icon class="h-5 w-5"/>
                                      </div>
                                    </template>
                                  </TextInput>
                                </div>
                                <button
                                    class="bg-success-200 text-success-700 p-2 rounded-lg  hover:bg-success-300 w-full"
                                    @click="edit({'idx':selected.idx ,'id':selected.id,'cmnd':'change-grade-pack-weight','new_in_repo':selected.new_in_repo,'new_pack_id':selected.new_pack_id,'new_grade':selected.new_grade,'new_unit_weight':selected.new_unit_weight,})">
                                  {{ __('accept') }}
                                </button>

                              </div>
                            </div>
                          </div>
                          <div v-if="selected.cmnd=='change-price'"
                               class="   text-sm text-gray-500 ">
                            <span class="text-xs py-2 text-danger-500">{{ __('help_price') }}</span>
                            <div class="flex flex-col  space-y-2 text-start ">

                              <div class="flex flex-col  ">

                                <div class="my-2">
                                  <TextInput
                                      id="new_price"
                                      type="number"
                                      :placeholder="`${__('new_price')}`"
                                      classes="  "
                                      v-model="selected.new_price"
                                      :autocomplete="selected.new_price"
                                      :error="  errors.new_price">

                                    <template v-slot:prepend>
                                      <div class="p-3">
                                        <CurrencyDollarIcon class="h-5 w-5"/>
                                      </div>
                                    </template>
                                  </TextInput>
                                </div>
                                <div class="my-2">
                                  <TextInput
                                      id="new_auction_price"
                                      type="number"
                                      :placeholder="`${__('new_auction_price')}`"
                                      classes="  "
                                      v-model="selected.new_auction_price"
                                      :autocomplete="selected.new_auction_price"
                                      :error="  errors.new_auction_price">

                                    <template v-slot:prepend>
                                      <div class="p-3">
                                        <CurrencyDollarIcon class="h-5 w-5"/>
                                      </div>
                                    </template>
                                  </TextInput>
                                </div>
                                <button
                                    class="bg-success-200 text-success-700 p-2 rounded-lg  hover:bg-success-300 w-full"
                                    @click="edit({'idx':selected.idx ,'id':selected.id,'cmnd':'change-price','new_price':selected.new_price,'new_auction_price':selected.new_auction_price, })">
                                  {{ __('accept') }}
                                </button>

                              </div>
                            </div>
                          </div>
                          <div v-if="selected.cmnd=='change-qty'"
                               class="   text-sm text-gray-500 ">
                            <span class="text-xs py-2 text-danger-500">{{ __('help_repo_shop_qty') }}</span>
                            <div class="flex flex-col  space-y-2 text-start ">

                              <div class="flex flex-col  ">

                                <div class="my-2">
                                  <TextInput
                                      id="new_in_shop"
                                      type="number"
                                      :placeholder="`${__('new_in_shop')}`"
                                      classes="  "
                                      v-model="selected.new_in_shop"
                                      :autocomplete="selected.new_in_shop"
                                      :error="  errors.new_in_shop">

                                    <template v-slot:prepend>
                                      <div class="p-3">
                                        <CurrencyDollarIcon class="h-5 w-5"/>
                                      </div>
                                    </template>
                                  </TextInput>
                                </div>
                                <div class="my-2">
                                  <TextInput
                                      id="new_in_repo"
                                      type="number"
                                      :placeholder="`${__('new_in_repo')}`"
                                      classes="  "
                                      v-model="selected.new_in_repo"
                                      :autocomplete="selected.new_in_repo"
                                      :error="  errors.new_in_repo">

                                    <template v-slot:prepend>
                                      <div class="p-3">
                                        <CurrencyDollarIcon class="h-5 w-5"/>
                                      </div>
                                    </template>
                                  </TextInput>
                                </div>
                                <button
                                    class="bg-success-200 text-success-700 p-2 rounded-lg  hover:bg-success-300 w-full"
                                    @click="edit({'idx':selected.idx ,'id':selected.id,'cmnd':'change-qty','new_in_repo':selected.new_in_repo,'new_in_shop':selected.new_in_shop, })">
                                  {{ __('accept') }}
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
  DocumentDuplicateIcon,

} from "@heroicons/vue/24/outline";
import Image from "@/Components/Image.vue"
import Tooltip from "@/Components/Tooltip.vue"
import Selector from "@/Components/Selector.vue"
import {Dropdown, Modal, initTE} from "tw-elements";
import TextInput from "@/Components/TextInput.vue";
import UserSelector from "@/Components/UserSelector.vue";


export default {
  data() {
    return {
      errors: {},
      filteredRepositories: [],
      repoModal: null,
      selected: null,
      selectedParams: null,
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
  directives: {}
  ,
  components: {
    UserSelector,
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
    DocumentDuplicateIcon,
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
      window.axios.get(route('admin.panel.variation.search'), {
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