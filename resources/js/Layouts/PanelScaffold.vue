<template>
  <App>
    <main :dir="dir()" class="h-full  panel   " :class="{'dark':isDark}">
      <Head>
        <slot name="header"/>
        <link rel="shortcut icon" type="image/x-icon" :href="favicon"/>
      </Head>
      <Alert ref="alert"/>
      <Dialog ref="modal"/>
      <Toast ref="toast"/>
      <WalletCharge ref="walletChargeModal"/>
      <div class="  flex  antialiased text-gray-900 bg-gray-100">
        <!-- Loading screen -->
        <div v-if="loading" class="  fixed w-screen h-screen backdrop-blur-sm     z-[999999]">

          <div class="flex items-center justify-center  w-full h-full  ">
            <div class="flex justify-center items-center space-x-1 text-sm text-gray-700">
              <LoadingIcon :percentage="percentage" type="line-dot" class=" fill-primary"/>

            </div>
          </div>
        </div>
        <!--      Dynamic Colors need to be added statically-->
        <div
            class="hidden border-success-200 border-success-300 border-danger-200 border-danger-300 border-warning-200 border-warning-300 border-orange-200 border-orange-300 border-primary-200 text-danger-500 text-success-500 text-danger-700 text-success-700 text-danger bg-success-100 bg-orange-100 bg-danger-200 bg-danger-100  hover:bg-orange-200 hover:bg-danger hover:bg-danger-200 hover:bg-success-200    text-orange-500 text-purple-500 bg-purple-100 text-sky-500 bg-sky-100 text-rose-500 bg-rose-100  bg-orange-50 bg-purple-50 bg-gray-50 bg-gray-100 bg-gray-200 bg-gray-500 text-gray-500 bg-violet-50 bg-violet-100 bg-violet-200 bg-violet-500 text-violet-500 "></div>

        <!-- Sidebar -->
        <aside class="  overflow-x-hidden  ">
          <slot name="sidenav"></slot>
        </aside>
        <!--Panel Main Side-->
        <div id="toggler" class="flex-1 h-screen overflow-x-hidden overflow-y-auto">
          <!-- Navbar -->
          <header class="relative bg-white">
            <div class="flex items-center justify-between p-2 border-b">
              <!--   Sidenav toggle button -->
              <!--                    @click="showSidenav=!showSidenav"-->
              <button
                  data-te-sidenav-toggle-ref
                  data-te-target="#sidenav-1"
                  aria-controls="#sidenav-1"
                  aria-haspopup="true"
                  aria-expanded="false"
                  class="block border-0 bg-transparent px-2.5 text-gray-500 hover:no-underline hover:shadow-none focus:no-underline focus:shadow-none focus:outline-none focus:ring-0 "

              >

                <span>
            <Bars3Icon class=" w-7 text-primary-500"/>

                </span>
              </button>

              <!-- Brand -->
              <Link :href="route('/')"
                    class="inline-block rounded bg-primary-100 p-1 md:p-2 hover:bg-primary-50 text-xs md:text-sm font-bold tracking-wider   text-primary-500">
                {{ __('site_view') }}
              </Link>


              <!-- Left   buttons -->
              <nav aria-label="Secondary" class="p-1    flex  items-center">
                <!-- Toggle dark theme button -->
                <button v-if="false" aria-hidden="true" class="relative focus:outline-none"
                        @click="isDark=!isDark">
                  <div
                      class="w-12 h-6 transition rounded-full outline-none bg-primary-100"></div>
                  <div
                      class="absolute top-0 left-0 inline-flex items-center justify-center w-6 h-6 transition-all duration-150 transform scale-110 rounded-full shadow-sm translate-x-0 -translate-y-px bg-white text-primary-dark"
                      :class="{ 'translate-x-0 -translate-y-px  bg-white text-primary-dark': !isDark, 'translate-x-6 text-primary-100 bg-primary-darker': isDark }">
                    <svg v-show="!isDark" class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                         viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                    </svg>
                    <svg v-show="isDark" class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                         viewBox="0 0 24 24" stroke="currentColor" style="display: none;">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                    </svg>
                  </div>
                </button>

                <!--cart button-->
                <CartButton v-if="isAdmin() && hasAccess('create_order')"
                            :link="route('admin.panel.repository.cart.update')"
                            :cart-link="route('admin.panel.repository.shop.cart')"
                />
                <!-- Notification button -->
                <NotificaionButton/>


                <!-- Settings button -->
                <div class="relative mx-1" data-te-dropdown-ref>
                  <button
                      class="flex p-2 items-center whitespace-nowrap rounded bg-primary  rounded-full text-primary-500 bg-primary-50 hover:text-primary hover:bg-primary-100    text-xs font-medium   leading-normal     transition duration-150 ease-in-out   hover:shadow-lg focus:bg-primary-200 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-primary-200 active:shadow-lg motion-reduce:transition-none"
                      type="button"
                      id="dropdownMenuSetting"
                      data-te-dropdown-toggle-ref
                      aria-expanded="false"
                      data-te-ripple-init
                      data-te-ripple-color="light">
                    <span class="sr-only">Open settings panel</span>
                    <Cog6ToothIcon class="w-7 h-7"/>
                  </button>
                  <ul
                      class="absolute z-[1000] float-start m-0 hidden min-w-max list-none overflow-hidden rounded-lg border-none bg-white bg-clip-padding text-start text-base shadow-lg [&[data-te-dropdown-show]]:block"
                      aria-labelledby="dropdownMenuSetting"
                      data-te-dropdown-menu-ref>
                    <li>
                      <Link
                          class="block w-full whitespace-nowrap bg-transparent px-4 py-2 text-sm font-normal text-neutral-700 hover:bg-neutral-100 active:text-neutral-800 active:no-underline disabled:pointer-events-none disabled:bg-transparent disabled:text-neutral-400"
                          :href="route(`${isAdmin()?'admin.':'user.'}panel.index`)"
                          data-te-dropdown-item-ref
                      >{{ __('dashboard') }}
                      </Link>

                    </li>


                  </ul>
                </div>


                <!-- User avatar button -->
                <div class="relative mx-1  " data-te-dropdown-ref>
                  <button
                      id="dropdownUserSetting"
                      data-te-dropdown-toggle-ref
                      aria-expanded="false"
                      data-te-ripple-init
                      data-te-ripple-color="light"
                      class=" flex   transition-colors duration-200 rounded-full text-primary-500 bg-primary-50 hover:text-primary hover:bg-primary-100 focus:outline-none focus:bg-primary-100 focus:ring-primary-darker">

                    <span class="sr-only">User menu</span>
                    <Image
                        classes="   hover:shadow-lg  object-cover   rounded-full w-12 h-12  "
                        :src="route(`storage.${isAdmin()?'admins':'users'}`)+`/${$page.props.auth.user.id}.jpg`"
                        alt="jane avatar"
                        type="user"/>
                  </button>

                  <!-- User dropdown menu -->
                  <div ref="userMenu" data-te-dropdown-menu-ref
                       class="min-w-[12rem] absolute z-[1000] float-start m-0 hidden min-w-max list-none overflow-hidden rounded-lg border-none bg-white bg-clip-padding text-start text-base shadow-lg [&[data-te-dropdown-show]]:block"
                       tabindex="-1" role="menu" aria-orientation="vertical" aria-label="User menu"

                       aria-labelledby="dropdownUserSetting">
                    <Link :href="route(`${isAdmin()?'admin':'user'}.panel.profile.edit`)" role="menuitem"
                          class="  block  p-4 text-sm text-gray-700 transition-colors hover:bg-gray-100">
                      {{ __('profile_setting') }}
                    </Link>
                    <hr class="border-gray-200 ">
                    <Link :href="route(`${isAdmin()?'admin':'user'}.panel.profile.password.edit`)" role="menuitem"
                          class="block p-4 text-sm text-gray-700 transition-colors hover:bg-gray-100">
                      {{ __('change_password') }}
                    </Link>
                    <hr class="border-gray-200 ">
                    <Link method="post" :href="route('logout')" class="flex w-full" as="button">
                      <button class="flex items-center justify-center p-4 m-3  w-full  hover:scale-110 focus:outline-none     px-4 py-2 rounded font-bold cursor-pointer
        hover:bg-red-700 hover:text-red-100  bg-red-100 text-red-500  border duration-200 ease-in-out border-red-600  ">
                        {{ __('signout') }}
                        <ArrowRightOnRectangleIcon class="h-5 w-5 text-red-500  "/>
                      </button>
                    </Link>
                  </div>
                </div>
              </nav>

            </div>

          </header>

          <!-- Main content -->
          <main class="min-h-[calc(100%-50rem)] text-primary-500">
            <!--   content-->
            <slot name="content" :showToast="showToast"/>
          </main>

          <!-- Main footer -->
          <footer v-if="false"
                  class="flex fixed bottom-0 w-fill end-0 shadow-lg drop-shadow items-center justify-between p-4 bg-white  ">
            <div>
              <a href="https://zil.ink/varta" target="_blank" class="text-blue-500 hover:underline">
              </a>
            </div>
            <div> {{ __('app_name') }} {{ (new Date()).toLocaleDateString('GMT', {year: 'numeric',}) }}</div>
          </footer>
        </div>


      </div>
    </main>
  </App>
</template>

<script>
import {Head, Link} from '@inertiajs/vue3';
import Navbar from "@/Components/Navbar.vue";
import Footer from "@/Components/Footer.vue";
import Toast from "@/Components/Toast.vue";
import Image from '@/Components/Image.vue';
import Dialog from '@/Components/Dialog.vue';
import CartButton from '@/Components/CartButton.vue';
import App from '@/Layouts/App.vue';
import {
  Bars3Icon,
  BellAlertIcon,
  Cog6ToothIcon,
  ArrowRightOnRectangleIcon,
} from "@heroicons/vue/24/outline";
import {Dropdown, initTE, Input, Popover, Ripple, Select, Sidenav, Tooltip} from "tw-elements";
import {provide, onMounted, getCurrentInstance} from "vue";
import {router} from '@inertiajs/vue3'
import mitt from 'mitt'
import Alert from "@/Components/Alert.vue";
import LoadingIcon from "@/Components/LoadingIcon.vue";
import NotificaionButton from "@/Components/NotificaionButton.vue";
import WalletCharge from "@/Components/WalletCharge.vue";
import favicon from "@/../images/logo.png";
import CartItemButton from "@/Components/CartItemButton.vue";

export const emitter = mitt()

export default {
  components: {
    CartButton,
    CartItemButton,
    WalletCharge,
    NotificaionButton,
    LoadingIcon,
    Alert,
    Dialog,
    Head, Link, Navbar, Footer, Toast, Bars3Icon, Cog6ToothIcon, Image, ArrowRightOnRectangleIcon, App
  },

  created() {
  },
  data() {
    return {
      isDark: false,
      loading: false,
      percentage: null,
      toast: null,
      showSidenav: false,
      favicon: favicon,
    }
  },
  // watch: {
  //   showSidenav() {
  //     this.toggleSideNav();
  //   }
  // },
  mounted() {
    // initTE({Sidenav});
    // window.initSidenav();


    // this.$nextTick(() => {
    //   window.tailwindElements();
    //   // if (!window.Dropdown) {
    //   window.Dropdown = Dropdown;
    //   initTE({Dropdown}, {allowReinits: true});
    //   // }
    //
    // });


    this.emitter.on('showToast', (e) => {
      if (this.$refs.toast)
        this.$refs.toast.show(e.type, e.message);
    });

    this.emitter.on('showAlert', (e) => {
      if (this.$refs.alert)
        this.$refs.alert.show(e.type, e.message);
    });

    this.emitter.on('showDialog', (e) => {
      if (this.$refs.modal)
        this.$refs.modal.show(e.type, e.message, e.button, e.action, e.items);
    });

    this.emitter.on('showWalletChargeDialog', (e) => {
      if (this.$refs.walletChargeModal)
        this.$refs.walletChargeModal.show();
    });

    this.emitter.on('loading', (e, percentage) => {
      this.loading = e;
      this.percentage = percentage;
    });
    if (this.$page.props.flash.message && this.$page.props.flash.message != undefined) {
      this.$nextTick(() => {

        this.showAlert(this.$page.props.flash.status, this.$page.props.flash.message);
      });
    }

    // document.addEventListener('hidden.te.sidenav', function () {
    //   console.log('hi');
    // });
  },

  methods: {
    toggleSideNav() {
      return;
      console.log((window.Sidenav.options));

      if (window.sidenavHidden)
        window.Sidenav.show();
      else
        window.Sidenav.hide();
      window.sidenavHidden = !window.sidenavHidden;
      // window.Sidenav.update();

      return;
      if (this.showSidenav) {
        window.Sidenav.changeMode("over");
        window.Sidenav.hide();
      } else {

        window.Sidenav.changeMode("side");
        window.Sidenav.show();
      }
    }
  },

}
</script>
