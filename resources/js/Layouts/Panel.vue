<template>
  <PanelScaffold>
    <template #header>
      <slot name="header"></slot>
    </template>

    <!--         Sidenav -->
    <!--    data-te-sidenav-init-->
    <template #sidenav>
      <nav id="sidenav-1"
           class="fixed start-0 top-0 z-[1035] h-screen w-60 -translate-x-full overflow-hidden bg-white shadow-[0_4px_12px_0_rgba(0,0,0,0.07),_0_2px_4px_rgba(0,0,0,0.05)] md:data-[te-sidenav-hidden='false']:translate-x-0"

           data-te-sidenav-mode-breakpoint-over="0"
           data-te-sidenav-mode-breakpoint-side="md"
           data-te-sidenav-hidden="false"
           data-te-sidenav-color="dark"
           data-te-sidenav-mode="side"
           data-te-sidenav-right="true"
           data-te-sidenav-content="#toggler"
           data-te-sidenav-scroll-container="#scrollContainer">


        <ul v-if="isAdmin()" id="scrollContainer" class="relative m-0 list-none    text-primary-500"
            data-te-sidenav-menu-ref>

          <li class="relative">
            <Link :href="route('admin.panel.index')"
                  class="py-4  flex  px-3 outline-none transition duration-300 ease-linear hover:bg-primary-200 hover:text-inherit hover:outline-none focus:bg-slate-50 focus:text-inherit focus:outline-none active:bg-slate-50 active:text-inherit active:outline-none data-[te-sidenav-state-active]:text-inherit data-[te-sidenav-state-focus]:outline-none motion-reduce:transition-none"
                  :class="{' bg-primary-100 text-primary-500':menuIsActive ( 'admin.panel.index' )}"
            >
              <span class="w-full text-primary-600 text-center"> {{ __('admin_dashboard') }}</span>
            </Link>

            <!--            <hr class="border-gray-200 py-2 mx-4">-->

            <div v-if="false"
                 class="flex text-primary mx-2 justify-center items-center text-sm text-gray-500">

              <Tooltip v-if="!hasWallet()" class="p-2 " :content="__('help_activate_wallet')">
                <QuestionMarkCircleIcon class="text-gray-500 hover:bg-gray-50 w-4 h-4"/>
              </Tooltip>
              <span class="text-gray-700">{{ __('wallet') + ' :' }}</span>

              <div v-if="hasWallet()" class="flex items-center ">
                <strong class="mx-2 text-primary">{{ asPrice(user.wallet) }} </strong>
                <span class="text-xs text-gray-500"> {{ __('currency') }}</span>
                <span @click="showWalletChargeDialog"
                      class="mx-2   text-center  bg-success-200 text-success-700 hover:bg-success-100 cursor-pointer px-2 py-[.1rem] rounded-lg transition-all duration-300">
                   {{ __('charge') }}
                  </span>
              </div>
              <div v-else class="flex">

                <Link :href="route(`${isAdmin()?'admin':'user'}.panel.profile.edit`)"
                      class="text-danger-700 bg-danger-200 hover:bg-danger-100 rounded-lg px-2 py-1 cursor-pointer">
                  {{ __('inactive') }}
                </Link>
              </div>

            </div>
            <div class="bg-gray-100 rounded-lg   m-1 py-2">
              <div v-if="$page.props.auth.user"
                   class=" flex items-center justify-center text-center text-sm text-gray-500">
                <UserIcon class="w-4  "/>
                <span class="mx-2"> {{ $page.props.auth.user.fullname }}</span>
              </div>
              <div v-if="$page.props.agency" class="flex items-center justify-center text-center text-sm">
                <UGP class="w-4 "/>
                <span class="mx-2">{{ $page.props.agency.name }}</span>
              </div>
            </div>
            <hr class="border-gray-200 my-2 mx-4">


          </li>
          <!-- Products links -->
          <li v-if="  hasAccess('view_product') " class="relative ">
            <a :class="{'bg-primary-50 text-primary-500':menuIsActive ( 'admin.panel.product.*' )}"
               class="flex   cursor-pointer items-center truncate   px-3 py-4 text-[0.875rem] text-gray-600 outline-none transition duration-300 ease-linear hover:bg-primary-100 hover:text-inherit hover:outline-none focus:bg-slate-50 focus:text-inherit focus:outline-none active:bg-slate-50 active:text-inherit active:outline-none data-[te-sidenav-state-active]:text-inherit data-[te-sidenav-state-focus]:outline-none motion-reduce:transition-none"
               data-te-sidenav-link-ref>
              <CubeIcon class="w-5 h-5  "/>
              <span class="mx-2 text-sm "> {{ __('parent_products') }} </span>
              <span
                  class="  right-0 ml-auto mr-[0.8rem] transition-transform duration-300 ease-linear motion-reduce:transition-none [&>svg]:text-gray-600"
                  data-te-sidenav-rotate-icon-ref>
                <ChevronDownIcon class="h-5 w-5"/>
              </span>
            </a>
            <ul
                v-bind="{ 'data-te-collapse-show':menuIsActive ( 'admin.panel.product.*' )?true:null }"
                class="  !visible relative m-0 hidden list-none    data-[te-collapse-show]:block "
                data-te-collapse-item data-te-sidenav-collapse-ref>
              <li class="relative ps-7">

                <Link :href="route('admin.panel.product.index')" role="menuitem"
                      :class="subMenuIsActive( 'admin.panel.product.index' )"
                      class="flex   border-s-2 hover:border-primary-500  items-center p-2   text-sm  transition-all duration-200   hover:text-primary-700 hover:bg-primary-50">
                  <Bars2Icon class="w-5 h-5 mx-1"/>
                  {{ __('list') }}
                </Link>
                <Link :href="route('admin.panel.product.create')" role="menuitem"
                      :class="subMenuIsActive ( 'admin.panel.product.create' )"
                      class="flex   border-s-2 hover:border-primary-500  items-center p-2   text-sm  transition-all duration-200   hover:text-primary-700 hover:bg-primary-50">
                  <PlusSmallIcon class="w-5 h-5 mx-1"/>
                  {{ __('new') }}
                </Link>
              </li>

            </ul>
          </li>
          <!-- Variations links -->
          <li v-if="  hasAccess('view_variation') " class="relative ">
            <a :class="{'bg-primary-50 text-primary-500':menuIsActive ( 'admin.panel.variation.*' )}"
               class="flex   cursor-pointer items-center truncate   px-3 py-4 text-[0.875rem] text-gray-600 outline-none transition duration-300 ease-linear hover:bg-primary-100 hover:text-inherit hover:outline-none focus:bg-slate-50 focus:text-inherit focus:outline-none active:bg-slate-50 active:text-inherit active:outline-none data-[te-sidenav-state-active]:text-inherit data-[te-sidenav-state-focus]:outline-none motion-reduce:transition-none"
               data-te-sidenav-link-ref>
              <RectangleGroupIcon class="w-5 h-5  "/>
              <span class="mx-2 text-sm "> {{ __('products') }} </span>
              <span
                  class="  right-0 ml-auto mr-[0.8rem] transition-transform duration-300 ease-linear motion-reduce:transition-none [&>svg]:text-gray-600"
                  data-te-sidenav-rotate-icon-ref>
                <ChevronDownIcon class="h-5 w-5"/>
              </span>
            </a>
            <ul
                v-bind="{ 'data-te-collapse-show':menuIsActive ( 'admin.panel.variation.*' )?true:null }"
                class="  !visible relative m-0 hidden list-none    data-[te-collapse-show]:block "
                data-te-collapse-item data-te-sidenav-collapse-ref>
              <li class="relative ps-7">

                <Link :href="route('admin.panel.variation.index')" role="menuitem"
                      :class="subMenuIsActive( 'admin.panel.variation.index' )"
                      class="flex   border-s-2 hover:border-primary-500  items-center p-2   text-sm  transition-all duration-200   hover:text-primary-700 hover:bg-primary-50">
                  <Bars2Icon class="w-5 h-5 mx-1"/>
                  {{ __('list') }}
                </Link>
                <Link :href="route('admin.panel.variation.create')" role="menuitem"
                      :class="subMenuIsActive ( 'admin.panel.variation.create' )"
                      class="flex   border-s-2 hover:border-primary-500  items-center p-2   text-sm  transition-all duration-200   hover:text-primary-700 hover:bg-primary-50">
                  <PlusSmallIcon class="w-5 h-5 mx-1"/>
                  {{ __('new') }}
                </Link>
              </li>

            </ul>
          </li>
          <!-- orders links -->
          <li v-if="  hasAccess('view_user_order') || hasAccess('view_agency_order') " class="relative ">
            <a :class="{'bg-primary-50 text-primary-500':menuIsActive ( 'admin.panel.order.*' )}"
               class="flex   cursor-pointer items-center truncate   px-3 py-4 text-[0.875rem] text-gray-600 outline-none transition duration-300 ease-linear hover:bg-primary-100 hover:text-inherit hover:outline-none focus:bg-slate-50 focus:text-inherit focus:outline-none active:bg-slate-50 active:text-inherit active:outline-none data-[te-sidenav-state-active]:text-inherit data-[te-sidenav-state-focus]:outline-none motion-reduce:transition-none"
               data-te-sidenav-link-ref>
              <ShoppingBagIcon class="w-5 h-5  "/>
              <span class="mx-2 text-sm "> {{ __('orders') }} </span>
              <span
                  class="  right-0 ml-auto mr-[0.8rem] transition-transform duration-300 ease-linear motion-reduce:transition-none [&>svg]:text-gray-600"
                  data-te-sidenav-rotate-icon-ref>
                <ChevronDownIcon class="h-5 w-5"/>
              </span>
            </a>
            <ul
                v-bind="{ 'data-te-collapse-show':menuIsActive ( 'admin.panel.order.*' )?true:null }"
                class="  !visible relative m-0 hidden list-none    data-[te-collapse-show]:block "
                data-te-collapse-item data-te-sidenav-collapse-ref>
              <li class="relative ps-7">

                <Link v-if="  hasAccess('view_user_order') " :href="route('admin.panel.order.user.index')"
                      role="menuitem"
                      :class="subMenuIsActive( 'admin.panel.order.user.index' )"
                      class="flex   border-s-2 hover:border-primary-500  items-center p-2   text-sm  transition-all duration-200   hover:text-primary-700 hover:bg-primary-50">
                  <Bars2Icon class="w-5 h-5 mx-1"/>
                  {{ __('customers') }}
                </Link>
                <template v-if="  hasAccess('view_user_order') ">
                  <Link v-for="(s,idx) in $page.props.order_statuses"
                        :href="`${route('admin.panel.order.user.index')}?status=${s.name}`"
                        role="menuitem"
                        :class="subMenuIsActive( `admin.panel.order.user.index`, {status:s.name} )"
                        class="flex   border-s-2 hover:border-primary-500  items-center p-2 px-4   text-sm  transition-all duration-200   hover:text-primary-700 hover:bg-primary-50">
                    <TagIcon class="w-5 h-5 mx-1" :class="` text-${getStatus('order_statuses', s.name).color}-500 `"/>
                    <div :class="` text-${getStatus('order_statuses', s.name).color}-500 `">
                      {{ getStatus('order_statuses', s.name).name }}
                    </div>
                  </Link>
                </template>

                <Link v-if="  hasAccess('view_agency_order') " :href="route('admin.panel.order.agency.index')"
                      role="menuitem"
                      :class="subMenuIsActive( 'admin.panel.order.agency.index' )"
                      class="flex   border-s-2 hover:border-primary-500  items-center p-2   text-sm  transition-all duration-200   hover:text-primary-700 hover:bg-primary-50">
                  <Bars2Icon class="w-5 h-5 mx-1"/>
                  {{ __('agencies') }}
                </Link>
                <template v-if="  hasAccess('view_agency_order') ">
                  <Link v-for="(s,idx) in $page.props.order_statuses"
                        :href="`${route('admin.panel.order.agency.index')}?status=${s.name}`"
                        role="menuitem"
                        :class="subMenuIsActive( `admin.panel.order.agency.index`, {status:s.name} )"
                        class="flex   border-s-2 hover:border-primary-500  items-center p-2 px-4   text-sm  transition-all duration-200   hover:text-primary-700 hover:bg-primary-50">
                    <TagIcon class="w-5 h-5 mx-1" :class="` text-${getStatus('order_statuses', s.name).color}-500 `"/>
                    <div :class="` text-${getStatus('order_statuses', s.name).color}-500 `">
                      {{ getStatus('order_statuses', s.name).name }}
                    </div>
                  </Link>
                </template>
              </li>

            </ul>
          </li>


          <!-- Agencies links -->
          <li v-if="  hasAccess('view_agency')" class="relative ">
            <a :class="{'bg-primary-50 text-primary-500':menuIsActive ( 'admin.panel.agency.*' )}"
               class="flex   cursor-pointer items-center truncate   px-3 py-4 text-[0.875rem] text-gray-600 outline-none transition duration-300 ease-linear hover:bg-primary-100 hover:text-inherit hover:outline-none focus:bg-slate-50 focus:text-inherit focus:outline-none active:bg-slate-50 active:text-inherit active:outline-none data-[te-sidenav-state-active]:text-inherit data-[te-sidenav-state-focus]:outline-none motion-reduce:transition-none"
               data-te-sidenav-link-ref>
              <UserGroupIcon class="w-5 h-5  "/>
              <span class="mx-2 text-sm "> {{ __('agencies') }} </span>
              <span
                  class="  right-0 ml-auto mr-[0.8rem] transition-transform duration-300 ease-linear motion-reduce:transition-none [&>svg]:text-gray-600"
                  data-te-sidenav-rotate-icon-ref>
                                             <ChevronDownIcon class="h-5 w-5"/>
                                             </span>
            </a>
            <ul
                v-bind="{ 'data-te-collapse-show':menuIsActive ( 'admin.panel.agency.*' )?true:null }"
                class="  !visible relative m-0 hidden list-none    data-[te-collapse-show]:block "
                data-te-collapse-item data-te-sidenav-collapse-ref>
              <li class="relative ps-7">

                <Link :href="route('admin.panel.agency.index')" role="menuitem"
                      :class="subMenuIsActive( 'admin.panel.agency.index' )"
                      class="flex   border-s-2 hover:border-primary-500  items-center p-2   text-sm  transition-all duration-200   hover:text-primary-700 hover:bg-primary-50">
                  <Bars2Icon class="w-5 h-5 mx-1"/>
                  {{ __('list') }}
                </Link>
                <Link :href="route('admin.panel.agency.create')" role="menuitem"
                      :class="subMenuIsActive ( 'admin.panel.agency.create' )"
                      class="flex   border-s-2 hover:border-primary-500  items-center p-2   text-sm  transition-all duration-200   hover:text-primary-700 hover:bg-primary-50">
                  <PlusSmallIcon class="w-5 h-5 mx-1"/>
                  {{ __('new') }}
                </Link>
              </li>

            </ul>
          </li>

          <!-- Repositories links -->
          <li v-if="  hasAccess('view_repository')" class="relative ">
            <a :class="{'bg-primary-50 text-primary-500':menuIsActive ( 'admin.panel.repository.*' )}"
               class="flex   cursor-pointer items-center truncate   px-3 py-4 text-[0.875rem] text-gray-600 outline-none transition duration-300 ease-linear hover:bg-primary-100 hover:text-inherit hover:outline-none focus:bg-slate-50 focus:text-inherit focus:outline-none active:bg-slate-50 active:text-inherit active:outline-none data-[te-sidenav-state-active]:text-inherit data-[te-sidenav-state-focus]:outline-none motion-reduce:transition-none"
               data-te-sidenav-link-ref>
              <InboxStackIcon class="w-5 h-5  "/>
              <span class="mx-2 text-sm "> {{ __('repositories') }} </span>
              <span
                  class="  right-0 ml-auto mr-[0.8rem] transition-transform duration-300 ease-linear motion-reduce:transition-none [&>svg]:text-gray-600"
                  data-te-sidenav-rotate-icon-ref>
                <ChevronDownIcon class="h-5 w-5"/>
              </span>
            </a>
            <ul
                v-bind="{ 'data-te-collapse-show':menuIsActive ( 'admin.panel.repository.*' )?true:null }"
                class="  !visible relative m-0 hidden list-none    data-[te-collapse-show]:block "
                data-te-collapse-item data-te-sidenav-collapse-ref>
              <li class="relative ps-7">

                <Link :href="route('admin.panel.repository.index')" role="menuitem"
                      :class="subMenuIsActive( 'admin.panel.repository.index' )"
                      class="flex   border-s-2 hover:border-primary-500  items-center p-2   text-sm  transition-all duration-200   hover:text-primary-700 hover:bg-primary-50">
                  <Bars2Icon class="w-5 h-5 mx-1"/>
                  {{ __('list') }}
                </Link>
                <Link :href="route('admin.panel.repository.transport.index')" role="menuitem"
                      :class="subMenuIsActive ( 'admin.panel.repository.transport.index' )"
                      class="flex   border-s-2 hover:border-primary-500  items-center p-2   text-sm  transition-all duration-200   hover:text-primary-700 hover:bg-primary-50">
                  <ArrowsRightLeftIcon class="w-5 h-5 mx-1"/>
                  {{ __('send/receive_transport') }}
                </Link>
                <Link :href="route('admin.panel.repository.shop.index')" role="menuitem"
                      :class="subMenuIsActive ( 'admin.panel.repository.shop.index' )"
                      class="flex   border-s-2 hover:border-primary-500  items-center p-2   text-sm  transition-all duration-200   hover:text-primary-700 hover:bg-primary-50">
                  <ShoppingBagIcon class="w-5 h-5 mx-1"/>
                  {{ __('shop') }}
                </Link>
                <Link :href="route('admin.panel.repository.order.index')" role="menuitem"
                      :class="subMenuIsActive ( 'admin.panel.repository.order.index' )"
                      class="flex   border-s-2 hover:border-primary-500  items-center p-2   text-sm  transition-all duration-200   hover:text-primary-700 hover:bg-primary-50">
                  <ShoppingCartIcon class="w-5 h-5 mx-1"/>
                  {{ __('orders') }}
                </Link>
              </li>

            </ul>
          </li>

          <!-- ShippingMethods links -->
          <li v-if="  hasAccess('view_shipping-method')" class="relative ">
            <a :class="{'bg-primary-50 text-primary-500':menuIsActive ( 'admin.panel.shipping-method.*' )}"
               class="flex   cursor-pointer items-center truncate   px-3 py-4 text-[0.875rem] text-gray-600 outline-none transition duration-300 ease-linear hover:bg-primary-100 hover:text-inherit hover:outline-none focus:bg-slate-50 focus:text-inherit focus:outline-none active:bg-slate-50 active:text-inherit active:outline-none data-[te-sidenav-state-active]:text-inherit data-[te-sidenav-state-focus]:outline-none motion-reduce:transition-none"
               data-te-sidenav-link-ref>
              <CalculatorIcon class="w-5 h-5  "/>
              <span class="mx-2 text-sm "> {{ __('shipping_method') }} </span>
              <span
                  class="  right-0 ml-auto mr-[0.8rem] transition-transform duration-300 ease-linear motion-reduce:transition-none [&>svg]:text-gray-600"
                  data-te-sidenav-rotate-icon-ref>
                <ChevronDownIcon class="h-5 w-5"/>
              </span>
            </a>
            <ul
                v-bind="{ 'data-te-collapse-show':menuIsActive ( 'admin.panel.shipping-method.*' )?true:null }"
                class="  !visible relative m-0 hidden list-none    data-[te-collapse-show]:block "
                data-te-collapse-item data-te-sidenav-collapse-ref>
              <li class="relative ps-7">

                <Link :href="route('admin.panel.shipping-method.index')" role="menuitem"
                      :class="subMenuIsActive( 'admin.panel.shipping-method.index' )"
                      class="flex   border-s-2 hover:border-primary-500  items-center p-2   text-sm  transition-all duration-200   hover:text-primary-700 hover:bg-primary-50">
                  <Bars2Icon class="w-5 h-5 mx-1"/>
                  {{ __('list') }}
                </Link>
                <Link :href="route('admin.panel.shipping-method.create')" role="menuitem"
                      :class="subMenuIsActive ( 'admin.panel.shipping-method.create' )"
                      class="flex   border-s-2 hover:border-primary-500  items-center p-2   text-sm  transition-all duration-200   hover:text-primary-700 hover:bg-primary-50">
                  <PlusSmallIcon class="w-5 h-5 mx-1"/>
                  {{ __('new') }}
                </Link>
              </li>

            </ul>
          </li>

          <!-- Shipping links -->
          <li v-if="  hasAccess('view_shipping')" class="relative ">
            <a :class="{'bg-primary-50 text-primary-500':menuIsActive ( 'admin.panel.shipping.*' )}"
               class="flex   cursor-pointer items-center truncate   px-3 py-4 text-[0.875rem] text-gray-600 outline-none transition duration-300 ease-linear hover:bg-primary-100 hover:text-inherit hover:outline-none focus:bg-slate-50 focus:text-inherit focus:outline-none active:bg-slate-50 active:text-inherit active:outline-none data-[te-sidenav-state-active]:text-inherit data-[te-sidenav-state-focus]:outline-none motion-reduce:transition-none"
               data-te-sidenav-link-ref>
              <TruckIcon class="w-5 h-5  "/>
              <span class="mx-2 text-sm "> {{ __('shipping') }} </span>
              <span
                  class="  right-0 ml-auto mr-[0.8rem] transition-transform duration-300 ease-linear motion-reduce:transition-none [&>svg]:text-gray-600"
                  data-te-sidenav-rotate-icon-ref>
                <ChevronDownIcon class="h-5 w-5"/>
              </span>
            </a>
            <ul
                v-bind="{ 'data-te-collapse-show':menuIsActive ( 'admin.panel.shipping.*' )?true:null }"
                class="  !visible relative m-0 hidden list-none    data-[te-collapse-show]:block "
                data-te-collapse-item data-te-sidenav-collapse-ref>
              <li class="relative ps-7">

                <Link :href="route('admin.panel.shipping.index')" role="menuitem"
                      :class="subMenuIsActive( 'admin.panel.shipping.index' )"
                      class="flex   border-s-2 hover:border-primary-500  items-center p-2   text-sm  transition-all duration-200   hover:text-primary-700 hover:bg-primary-50">
                  <Bars2Icon class="w-5 h-5 mx-1"/>
                  {{ __('list') }}
                </Link>
                <Link :href="route('admin.panel.shipping.driver.index')" role="menuitem"
                      :class="subMenuIsActive( 'admin.panel.shipping.driver.index' )"
                      class="flex   border-s-2 hover:border-primary-500  items-center p-2   text-sm  transition-all duration-200   hover:text-primary-700 hover:bg-primary-50">
                  <UserCircleIcon class="w-5 h-5 mx-1"/>
                  {{ __('drivers') }}
                </Link>
                <Link :href="route('admin.panel.shipping.car.index')" role="menuitem"
                      :class="subMenuIsActive( 'admin.panel.shipping.car.index' )"
                      class="flex   border-s-2 hover:border-primary-500  items-center p-2   text-sm  transition-all duration-200   hover:text-primary-700 hover:bg-primary-50">
                  <CogIcon class="w-5 h-5 mx-1"/>
                  {{ __('cars') }}
                </Link>

              </li>

            </ul>
          </li>


          <!-- Admins links -->
          <li v-if="  hasAccess('view_admin')" class="relative ">
            <a :class="{'bg-primary-50 text-primary-500':menuIsActive ( 'admin.panel.admin.*' )}"
               class="flex   cursor-pointer items-center truncate   px-3 py-4 text-[0.875rem] text-gray-600 outline-none transition duration-300 ease-linear hover:bg-primary-100 hover:text-inherit hover:outline-none focus:bg-slate-50 focus:text-inherit focus:outline-none active:bg-slate-50 active:text-inherit active:outline-none data-[te-sidenav-state-active]:text-inherit data-[te-sidenav-state-focus]:outline-none motion-reduce:transition-none"
               data-te-sidenav-link-ref>
              <UserCircleIcon class="w-5 h-5  "/>
              <span class="mx-2 text-sm "> {{ __('admins') }} </span>
              <span
                  class="  right-0 ml-auto mr-[0.8rem] transition-transform duration-300 ease-linear motion-reduce:transition-none [&>svg]:text-gray-600"
                  data-te-sidenav-rotate-icon-ref>
                                             <ChevronDownIcon class="h-5 w-5"/>
                                             </span>
            </a>
            <ul
                v-bind="{ 'data-te-collapse-show':menuIsActive ( 'admin.panel.admin.*' )?true:null }"
                class="  !visible relative m-0 hidden list-none    data-[te-collapse-show]:block "
                data-te-collapse-item data-te-sidenav-collapse-ref>
              <li class="relative ps-7">

                <Link :href="route('admin.panel.admin.index')" role="menuitem"
                      :class="subMenuIsActive( 'admin.panel.admin.index' )"
                      class="flex   border-s-2 hover:border-primary-500  items-center p-2   text-sm  transition-all duration-200   hover:text-primary-700 hover:bg-primary-50">
                  <Bars2Icon class="w-5 h-5 mx-1"/>
                  {{ __('list') }}
                </Link>
                <Link :href="route('admin.panel.admin.create')" role="menuitem"
                      :class="subMenuIsActive ( 'admin.panel.admin.create' )"
                      class="flex   border-s-2 hover:border-primary-500  items-center p-2   text-sm  transition-all duration-200   hover:text-primary-700 hover:bg-primary-50">
                  <PlusSmallIcon class="w-5 h-5 mx-1"/>
                  {{ __('new') }}
                </Link>
              </li>

            </ul>
          </li>


          <!-- 2 level test links -->
          <li v-if="false &&  hasAccess('view_shipping')" class="relative ">
            <a :class="{'bg-primary-50 text-primary-500':menuIsActive ( 'admin.panel.shipping.*' )}"
               class="flex   cursor-pointer items-center truncate   px-3 py-4 text-[0.875rem] text-gray-600 outline-none transition duration-300 ease-linear hover:bg-primary-100 hover:text-inherit hover:outline-none focus:bg-slate-50 focus:text-inherit focus:outline-none active:bg-slate-50 active:text-inherit active:outline-none data-[te-sidenav-state-active]:text-inherit data-[te-sidenav-state-focus]:outline-none motion-reduce:transition-none"
               data-te-sidenav-link-ref>
              <TruckIcon class="w-5 h-5  "/>
              <span class="mx-2 text-sm "> {{ __('shipping') }} </span>
              <span
                  class="  right-0 ml-auto mr-[0.8rem] transition-transform duration-300 ease-linear motion-reduce:transition-none [&>svg]:text-gray-600"
                  data-te-sidenav-rotate-icon-ref>
                <ChevronDownIcon class="h-5 w-5"/>
              </span>
            </a>
            <!-- level 2 links -->
            <ul
                v-bind="{ 'data-te-collapse-show':menuIsActive ( 'admin.panel.shipping.*' )?true:null }"
                class="  !visible relative m-0 hidden list-none    data-[te-collapse-show]:block "
                data-te-collapse-item data-te-sidenav-collapse-ref>
              <li class="relative ps-7">

                <Link :href="route('admin.panel.shipping.method.index')" role="menuitem"
                      :class="subMenuIsActive( 'admin.panel.shipping.method.index' )"
                      class="flex   border-s-2 hover:border-primary-500  items-center    text-sm  transition-all duration-200   hover:text-primary-700 hover:bg-primary-50">
                  <div v-if="  hasAccess('view_shipping_method')" class="relative ">
                    <a :class="{'bg-primary-50 text-primary-500':menuIsActive ( 'admin.panel.shipping.method.*' )}"
                       class="flex   cursor-pointer items-center truncate   px-1 py-4 text-[0.875rem] text-gray-600 outline-none transition duration-300 ease-linear hover:bg-primary-100 hover:text-inherit hover:outline-none focus:bg-slate-50 focus:text-inherit focus:outline-none active:bg-slate-50 active:text-inherit active:outline-none data-[te-sidenav-state-active]:text-inherit data-[te-sidenav-state-focus]:outline-none motion-reduce:transition-none"
                       data-te-sidenav-link-ref>
                      <CalculatorIcon class="w-5 h-5  "/>
                      <span class="mx-2 text-sm "> {{ __('shipping_method') }} </span>
                      <span
                          class="  right-0 ml-auto mr-[0.8rem] transition-transform duration-300 ease-linear motion-reduce:transition-none [&>svg]:text-gray-600"
                          data-te-sidenav-rotate-icon-ref>
                <ChevronDownIcon class="h-5 w-5"/>
              </span>
                    </a>
                    <ul
                        v-bind="{ 'data-te-collapse-show':menuIsActive ( 'admin.panel.shipping.method.*' )?true:null }"
                        class="  !visible relative m-0 hidden list-none    data-[te-collapse-show]:block "
                        data-te-collapse-item data-te-sidenav-collapse-ref>
                      <li class="relative ps-7">

                        <Link :href="route('admin.panel.shipping.method.index')" role="menuitem"
                              :class="subMenuIsActive( 'admin.panel.shipping.method.index' )"
                              class="flex   border-s-2 hover:border-primary-500  items-center p-2   text-sm  transition-all duration-200   hover:text-primary-700 hover:bg-primary-50">
                          <Bars2Icon class="w-5 h-5 mx-1"/>
                          {{ __('list') }}
                        </Link>
                        <Link :href="route('admin.panel.shipping.method.create')" role="menuitem"
                              :class="subMenuIsActive( 'admin.panel.shipping.method.create' )"
                              class="flex   border-s-2 hover:border-primary-500  items-center p-2   text-sm  transition-all duration-200   hover:text-primary-700 hover:bg-primary-50">
                          <PlusSmallIcon class="w-5 h-5 mx-1"/>
                          {{ __('new') }}
                        </Link>
                      </li>

                    </ul>
                  </div>
                </Link>

              </li>

            </ul>
            <!-- level 2 links 2-->

          </li>

          <!-- Packs links -->
          <li v-if="  hasAccess('view_pack')" class="relative ">
            <a :class="{'bg-primary-50 text-primary-500':menuIsActive ( 'admin.panel.pack.*' )}"
               class="flex   cursor-pointer items-center truncate   px-3 py-4 text-[0.875rem] text-gray-600 outline-none transition duration-300 ease-linear hover:bg-primary-100 hover:text-inherit hover:outline-none focus:bg-slate-50 focus:text-inherit focus:outline-none active:bg-slate-50 active:text-inherit active:outline-none data-[te-sidenav-state-active]:text-inherit data-[te-sidenav-state-focus]:outline-none motion-reduce:transition-none"
               data-te-sidenav-link-ref>
              <GiftTopIcon class="w-5 h-5  "/>
              <span class="mx-2 text-sm "> {{ __('packs') }} </span>
              <span
                  class="  right-0 ml-auto mr-[0.8rem] transition-transform duration-300 ease-linear motion-reduce:transition-none [&>svg]:text-gray-600"
                  data-te-sidenav-rotate-icon-ref>
                <ChevronDownIcon class="h-5 w-5"/>
              </span>
            </a>
            <ul
                v-bind="{ 'data-te-collapse-show':menuIsActive ( 'admin.panel.pack.*' )?true:null }"
                class="  !visible relative m-0 hidden list-none    data-[te-collapse-show]:block "
                data-te-collapse-item data-te-sidenav-collapse-ref>
              <li class="relative ps-7">

                <Link :href="route('admin.panel.pack.index')" role="menuitem"
                      :class="subMenuIsActive( 'admin.panel.pack.index' )"
                      class="flex   border-s-2 hover:border-primary-500  items-center p-2   text-sm  transition-all duration-200   hover:text-primary-700 hover:bg-primary-50">
                  <Bars2Icon class="w-5 h-5 mx-1"/>
                  {{ __('list') }}
                </Link>
                <Link :href="route('admin.panel.pack.create')" role="menuitem"
                      :class="subMenuIsActive ( 'admin.panel.pack.create' )"
                      class="flex   border-s-2 hover:border-primary-500  items-center p-2   text-sm  transition-all duration-200   hover:text-primary-700 hover:bg-primary-50">
                  <PlusSmallIcon class="w-5 h-5 mx-1"/>
                  {{ __('new') }}
                </Link>
              </li>

            </ul>
          </li>


          <!-- Article links -->
          <li v-if="  hasAccess('view_article')" class="relative ">
            <a :class="{'bg-primary-50 text-primary-500':menuIsActive ( 'admin.panel.article.*' )}"
               class="flex   cursor-pointer items-center truncate   px-3 py-4 text-[0.875rem] text-gray-600 outline-none transition duration-300 ease-linear hover:bg-primary-100 hover:text-inherit hover:outline-none focus:bg-slate-50 focus:text-inherit focus:outline-none active:bg-slate-50 active:text-inherit active:outline-none data-[te-sidenav-state-active]:text-inherit data-[te-sidenav-state-focus]:outline-none motion-reduce:transition-none"
               data-te-sidenav-link-ref>
              <NewspaperIcon class="w-5 h-5  "/>
              <span class="mx-2 text-sm "> {{ __('articles') }} </span>
              <span
                  class="  right-0 ml-auto mr-[0.8rem] transition-transform duration-300 ease-linear motion-reduce:transition-none [&>svg]:text-gray-600"
                  data-te-sidenav-rotate-icon-ref>
                                             <ChevronDownIcon class="h-5 w-5"/>
                                             </span>
            </a>
            <ul
                v-bind="{ 'data-te-collapse-show':menuIsActive ( 'admin.panel.article.*' )?true:null }"
                class="  !visible relative m-0 hidden list-none    data-[te-collapse-show]:block "
                data-te-collapse-item data-te-sidenav-collapse-ref>
              <li class="relative ps-7">

                <Link :href="route('admin.panel.article.index')" role="menuitem"
                      :class="subMenuIsActive( 'admin.panel.article.index' )"
                      class="flex   border-s-2 hover:border-primary-500  items-center p-2   text-sm  transition-all duration-200   hover:text-primary-700 hover:bg-primary-50">
                  <Bars2Icon class="w-5 h-5 mx-1"/>
                  {{ __('list') }}
                </Link>
                <Link :href="route('admin.panel.article.create')" role="menuitem"
                      :class="subMenuIsActive ( 'admin.panel.article.create' )"
                      class="flex   border-s-2 hover:border-primary-500  items-center p-2   text-sm  transition-all duration-200   hover:text-primary-700 hover:bg-primary-50">
                  <PlusSmallIcon class="w-5 h-5 mx-1"/>
                  {{ __('new') }}
                </Link>
              </li>

            </ul>
          </li>

          <!-- Support links -->
          <li class="relative  ">
            <a :class="{'bg-primary-50 text-primary-500':menuIsActive ( 'admin.panel.ticket.*' )}"
               class="flex   cursor-pointer items-center truncate   px-3 py-4 text-[0.875rem] text-gray-600 outline-none transition duration-300 ease-linear hover:bg-primary-100 hover:text-inherit hover:outline-none focus:bg-slate-50 focus:text-inherit focus:outline-none active:bg-slate-50 active:text-inherit active:outline-none data-[te-sidenav-state-active]:text-inherit data-[te-sidenav-state-focus]:outline-none motion-reduce:transition-none"
               data-te-sidenav-link-ref>
              <LightBulbIcon class="w-5 h-5  "/>
              <span class="mx-2 text-sm "> {{ __('support') }} </span>
              <span
                  class="  right-0 ml-auto mr-[0.8rem] transition-transform duration-300 ease-linear motion-reduce:transition-none [&>svg]:text-gray-600"
                  data-te-sidenav-rotate-icon-ref>
                                             <ChevronDownIcon class="h-5 w-5"/>
                                             </span>
            </a>
            <ul
                v-bind="{ 'data-te-collapse-show':menuIsActive ( 'admin.panel.ticket.*' )?true:null }"
                class="  !visible relative m-0 hidden list-none    data-[te-collapse-show]:block "
                data-te-collapse-item data-te-sidenav-collapse-ref>
              <li class="relative ps-7">

                <Link :href="route('admin.panel.notification.index')" role="menuitem"
                      :class="subMenuIsActive( 'admin.panel.notification.index' )"
                      class="flex   border-s-2 hover:border-primary-500  items-center p-2   text-sm  transition-all duration-200   hover:text-primary-700 hover:bg-primary-50">
                  <Bars2Icon class="w-5 h-5 mx-1"/>
                  {{ __('notifications') }}
                </Link>
                <Link :href="route('admin.panel.ticket.index')" role="menuitem"
                      :class="subMenuIsActive( 'admin.panel.ticket.index' )"
                      class="flex   border-s-2 hover:border-primary-500  items-center p-2   text-sm  transition-all duration-200   hover:text-primary-700 hover:bg-primary-50">
                  <Bars2Icon class="w-5 h-5 mx-1"/>
                  {{ __('tickets') }}
                </Link>
                <Link :href="route('admin.panel.ticket.create')" role="menuitem"
                      :class="subMenuIsActive ( 'admin.panel.ticket.create' )"
                      class="flex   border-s-2 hover:border-primary-500  items-center p-2   text-sm  transition-all duration-200   hover:text-primary-700 hover:bg-primary-50">
                  <PlusSmallIcon class="w-5 h-5 mx-1"/>
                  {{ __('new_ticket') }}
                </Link>
              </li>

            </ul>
          </li>

          <!-- Financial links -->
          <li v-if="hasAccess('view_financial') || hasAccess('view_transaction')" class="relative  ">
            <a :class="{'bg-primary-50 text-primary-500':menuIsActive ( 'admin.panel.financial.*' )}"
               class="flex   cursor-pointer items-center truncate rounded-[5px] px-3 py-4 text-[0.875rem] text-gray-600 outline-none transition duration-300 ease-linear hover:bg-primary-100 hover:text-inherit hover:outline-none focus:bg-slate-50 focus:text-inherit focus:outline-none active:bg-slate-50 active:text-inherit active:outline-none data-[te-sidenav-state-active]:text-inherit data-[te-sidenav-state-focus]:outline-none motion-reduce:transition-none"
               data-te-sidenav-link-ref>
              <CurrencyDollarIcon class="w-5 h-5  "/>
              <span class="mx-2 text-sm "> {{ __('financial') }} </span>
              <span
                  class="  right-0 ml-auto mr-[0.8rem] transition-transform duration-300 ease-linear motion-reduce:transition-none [&>svg]:text-gray-600"
                  data-te-sidenav-rotate-icon-ref>
                                             <ChevronDownIcon class="h-5 w-5"/>
                                             </span>
            </a>
            <ul
                v-bind="{ 'data-te-collapse-show':menuIsActive ( 'admin.panel.financial.*' )?true:null }"
                class="  !visible relative m-0 hidden list-none    data-[te-collapse-show]:block "
                data-te-collapse-item data-te-sidenav-collapse-ref>
              <li class="relative ps-7">

                <Link v-if="hasAccess('view_financial')" :href="route('admin.panel.financial.index')" role="menuitem"
                      :class="subMenuIsActive( 'admin.panel.financial.index' )"
                      class="flex   border-s-2 hover:border-primary-500  items-center p-2   text-sm  transition-all duration-200   hover:text-primary-700 hover:bg-primary-50">
                  <Bars2Icon class="w-5 h-5 mx-1"/>
                  {{ __('list') }}
                </Link>
                <Link v-if="hasAccess('view_transaction')" :href="route('admin.panel.financial.transaction.index')"
                      role="menuitem"
                      :class="subMenuIsActive( 'admin.panel.financial.transaction.index' )"
                      class="flex   border-s-2 hover:border-primary-500  items-center p-2   text-sm  transition-all duration-200   hover:text-primary-700 hover:bg-primary-50">
                  <Bars2Icon class="w-5 h-5 mx-1"/>
                  {{ __('transactions') }}
                </Link>

              </li>

            </ul>
          </li>

          <li>
            <div class="py-4">

            </div>
          </li>
        </ul>
        <!--         Users Menu-->
        <ul v-else id="scrollContainer" class="relative m-0 list-none    text-primary-500"
            data-te-sidenav-menu-ref>

          <li class="relative">

            <Link :href="route('user.panel.index')"
                  class="py-4  flex  px-3 outline-none transition duration-300 ease-linear hover:bg-primary-200 hover:text-inherit hover:outline-none focus:bg-slate-50 focus:text-inherit focus:outline-none active:bg-slate-50 active:text-inherit active:outline-none data-[te-sidenav-state-active]:text-inherit data-[te-sidenav-state-focus]:outline-none motion-reduce:transition-none"
                  :class="{' bg-primary-100 text-primary-500':menuIsActive ( 'user.panel.index' )}"
            >
              <span class="w-full text-primary-600 text-center"> {{ __('dashboard') }}</span>
            </Link>

            <hr class="border-gray-200 py-2 mx-4">


          </li>
          <!-- orders links -->
          <li class="relative ">
            <a :class="{'bg-primary-50 text-primary-500':menuIsActive ( 'user.panel.order.*' )}"
               class="flex   cursor-pointer items-center truncate   px-3 py-4 text-[0.875rem] text-gray-600 outline-none transition duration-300 ease-linear hover:bg-primary-100 hover:text-inherit hover:outline-none focus:bg-slate-50 focus:text-inherit focus:outline-none active:bg-slate-50 active:text-inherit active:outline-none data-[te-sidenav-state-active]:text-inherit data-[te-sidenav-state-focus]:outline-none motion-reduce:transition-none"
               data-te-sidenav-link-ref>
              <ShoppingBagIcon class="w-5 h-5  "/>
              <span class="mx-2 text-sm "> {{ __('orders') }} </span>
              <span
                  class="  right-0 ml-auto mr-[0.8rem] transition-transform duration-300 ease-linear motion-reduce:transition-none [&>svg]:text-gray-600"
                  data-te-sidenav-rotate-icon-ref>
                <ChevronDownIcon class="h-5 w-5"/>
              </span>
            </a>
            <ul
                v-bind="{ 'data-te-collapse-show':menuIsActive ( 'user.panel.order.*' )?true:null }"
                class="  !visible relative m-0 hidden list-none    data-[te-collapse-show]:block "
                data-te-collapse-item data-te-sidenav-collapse-ref>
              <li class="relative ps-7">

                <Link :href="route('user.panel.order.index')"
                      role="menuitem"
                      :class="subMenuIsActive( 'user.panel.order.index' )"
                      class="flex   border-s-2 hover:border-primary-500  items-center p-2   text-sm  transition-all duration-200   hover:text-primary-700 hover:bg-primary-50">
                  <Bars2Icon class="w-5 h-5 mx-1"/>
                  {{ __('list') }}
                </Link>


              </li>

            </ul>

            <!-- Financial links -->
          <li class="relative  ">
            <a :class="{'bg-primary-50 text-primary-500':menuIsActive ( 'user.panel.financial.*' )}"
               class="flex   cursor-pointer items-center truncate rounded-[5px] px-3 py-4 text-[0.875rem] text-gray-600 outline-none transition duration-300 ease-linear hover:bg-primary-100 hover:text-inherit hover:outline-none focus:bg-slate-50 focus:text-inherit focus:outline-none active:bg-slate-50 active:text-inherit active:outline-none data-[te-sidenav-state-active]:text-inherit data-[te-sidenav-state-focus]:outline-none motion-reduce:transition-none"
               data-te-sidenav-link-ref>
              <CurrencyDollarIcon class="w-5 h-5  "/>
              <span class="mx-2 text-sm "> {{ __('financial') }} </span>
              <span
                  class="  right-0 ml-auto mr-[0.8rem] transition-transform duration-300 ease-linear motion-reduce:transition-none [&>svg]:text-gray-600"
                  data-te-sidenav-rotate-icon-ref>
                                             <ChevronDownIcon class="h-5 w-5"/>
                                             </span>
            </a>
            <ul
                v-bind="{ 'data-te-collapse-show':menuIsActive ( 'user.panel.financial.*' )?true:null }"
                class="  !visible relative m-0 hidden list-none    data-[te-collapse-show]:block "
                data-te-collapse-item data-te-sidenav-collapse-ref>
              <li class="relative ps-7">

                <Link :href="route('user.panel.financial.transaction.index')" role="menuitem"
                      :class="subMenuIsActive( 'user.panel.financial.transaction.index' )"
                      class="flex   border-s-2 hover:border-primary-500  items-center p-2   text-sm  transition-all duration-200   hover:text-primary-700 hover:bg-primary-50">
                  <Bars2Icon class="w-5 h-5 mx-1"/>
                  {{ __('transactions') }}
                </Link>

              </li>

            </ul>
          </li>

          <!-- Support links -->
          <div class="relative  ">
            <a :class="{'bg-primary-50 text-primary-500':menuIsActive ( 'user.panel.ticket.*' )}"
               class="flex   cursor-pointer items-center truncate   px-3 py-4 text-[0.875rem] text-gray-600 outline-none transition duration-300 ease-linear hover:bg-primary-100 hover:text-inherit hover:outline-none focus:bg-slate-50 focus:text-inherit focus:outline-none active:bg-slate-50 active:text-inherit active:outline-none data-[te-sidenav-state-active]:text-inherit data-[te-sidenav-state-focus]:outline-none motion-reduce:transition-none"
               data-te-sidenav-link-ref>
              <LightBulbIcon class="w-5 h-5  "/>
              <span class="mx-2 text-sm "> {{ __('support') }} </span>
              <span
                  class="  right-0 ml-auto mr-[0.8rem] transition-transform duration-300 ease-linear motion-reduce:transition-none [&>svg]:text-gray-600"
                  data-te-sidenav-rotate-icon-ref>
                                             <ChevronDownIcon class="h-5 w-5"/>
                                             </span>
            </a>
            <ul
                v-bind="{ 'data-te-collapse-show':menuIsActive ( 'user.panel.ticket.*' ) || menuIsActive ( 'user.panel.notification.*' ) ?true:null }"
                class="  !visible relative m-0 hidden list-none    data-[te-collapse-show]:block "
                data-te-collapse-item data-te-sidenav-collapse-ref>
              <li class="relative ps-7">

                <Link :href="route('user.panel.notification.index')" role="menuitem"
                      :class="subMenuIsActive( 'user.panel.notification.index' )"
                      class="flex   border-s-2 hover:border-primary-500  items-center p-2   text-sm  transition-all duration-200   hover:text-primary-700 hover:bg-primary-50">
                  <Bars2Icon class="w-5 h-5 mx-1"/>
                  {{ __('notifications') }}
                </Link>
                <Link :href="route('user.panel.ticket.index')" role="menuitem"
                      :class="subMenuIsActive( 'user.panel.ticket.index' )"
                      class="flex   border-s-2 hover:border-primary-500  items-center p-2   text-sm  transition-all duration-200   hover:text-primary-700 hover:bg-primary-50">
                  <Bars2Icon class="w-5 h-5 mx-1"/>
                  {{ __('tickets') }}
                </Link>
                <Link :href="route('user.panel.ticket.create')" role="menuitem"
                      :class="subMenuIsActive ( 'user.panel.ticket.create' )"
                      class="flex   border-s-2 hover:border-primary-500  items-center p-2   text-sm  transition-all duration-200   hover:text-primary-700 hover:bg-primary-50">
                  <PlusSmallIcon class="w-5 h-5 mx-1"/>
                  {{ __('new_ticket') }}
                </Link>
              </li>

            </ul>
          </div>
          </li>


        </ul>

      </nav>
    </template>

    <template #content>
      <slot name="content" class=" "></slot>
    </template>
  </PanelScaffold>
</template>

<script>
import {Head, Link} from "@inertiajs/vue3";
import {loadScript} from "vue-plugin-load-script";
import {
  HomeIcon,
  ChevronDownIcon,
  Bars3Icon,
  PlusSmallIcon,
  Bars2Icon,
  NewspaperIcon,
  WindowIcon,
  GlobeAltIcon,
  PencilSquareIcon,
  PhotoIcon,
  FilmIcon,
  MicrophoneIcon,
  MegaphoneIcon,
  LightBulbIcon,
  CurrencyDollarIcon,
  BellAlertIcon,
  Cog6ToothIcon,
  ArrowRightOnRectangleIcon,
  WrenchScrewdriverIcon,
  ArrowsRightLeftIcon,
  BriefcaseIcon,
  RectangleStackIcon,
  UserGroupIcon,
  InboxStackIcon,
  TruckIcon,
  CalculatorIcon,
  GiftTopIcon,
  CubeIcon,
  ShoppingCartIcon,
  RectangleGroupIcon,
  ShoppingBagIcon,
  CogIcon,
  UserCircleIcon,
  TagIcon,
} from "@heroicons/vue/24/outline";
import {
  QuestionMarkCircleIcon,
  UserIcon,
  UserGroupIcon as UGP,
} from "@heroicons/vue/24/solid";
import Image from '@/Components/Image.vue';
import Toast from '@/Components/Toast.vue';
import Tooltip from '@/Components/Tooltip.vue';
import {useRemember} from '@inertiajs/vue3';
import {initTE, Dropdown, Sidenav} from "tw-elements";
import PanelScaffold from "@/Layouts/PanelScaffold.vue";
import {provide, ref} from 'vue';


export default {
  setup() {

    // const weatherData = ref('hi');
    // provide('showToast', weatherData);


  },

  data() {
    return {
      open: false,

      isMobileMainMenuOpen: false,
      isMobileSubMenuOpen: false,
      isOn: false,
      activeTabe: false,
      isNotificationsPanelOpen: false,
      isOpen: {'business': false, 'article': false,},
      user: this.$page.props.auth.user,

    }
  },
  props: [],
  created() {
  },
  mounted() {
    // initSidenav();
    // this.$nextTick(function () {
    //     console.log(this.$parent.toast);
    // });
    // console.log(this.$emit('showToast'))
    // this.$refs.toast.success('hi');
    // loadScript("https://cdn.jsdelivr.net/gh/alpine-collective/alpine-magic-helpers@0.5.x/dist/component.min.js")
    // loadScript("https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.7.3/dist/alpine.min.js")
  },
  watch: {
    // isOpen: {
    //     handler(val) {
    //         localStorage.setItem("menuStatus", JSON.stringify(val));
    //     },
    //     deep: true,
    // }
  },
  components: {
    PanelScaffold,
    Toast,
    Head,
    Link,
    HomeIcon,
    ChevronDownIcon,
    Bars3Icon,
    Image,
    PlusSmallIcon,
    Bars2Icon,
    NewspaperIcon,
    WindowIcon,
    GlobeAltIcon,
    PencilSquareIcon,
    PhotoIcon,
    FilmIcon,
    MicrophoneIcon,
    MegaphoneIcon,
    LightBulbIcon,
    CurrencyDollarIcon,
    BellAlertIcon,
    Cog6ToothIcon,
    ArrowRightOnRectangleIcon,
    Tooltip,
    QuestionMarkCircleIcon,
    WrenchScrewdriverIcon,
    ArrowsRightLeftIcon,
    BriefcaseIcon,
    RectangleStackIcon,
    UserGroupIcon,
    InboxStackIcon,
    TruckIcon,
    CalculatorIcon,
    GiftTopIcon,
    CubeIcon,
    ShoppingCartIcon,
    RectangleGroupIcon,
    ShoppingBagIcon,
    CogIcon,
    UserCircleIcon,
    UserIcon,
    UGP,
    TagIcon,
  },
  methods: {
    delay(time) {
      return new Promise(resolve => setTimeout(resolve, time));
    },

    menuIsActive(link) {
      return this.route().current(`${link}`);
    },
    subMenuIsActive(link, params = null) {
      return this.route().current(`${link}`, params) ? "text-primary-500 bg-primary-50 border-s border-primary-500  " : "text-gray-500   ";
    },

  },
}
</script>
