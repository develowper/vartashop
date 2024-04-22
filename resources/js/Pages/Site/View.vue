<template>
  <Scaffold navbar-theme="light">
    <template v-slot:header>
      <title v-if="data">{{ data.name }}</title>
      <meta v-if="data" name="description" :content=" data.description ">

    </template>
    <div v-if="false" class="relative   bg-gradient-to-t from-pink-300 via-purple-300 to-indigo-400">

      <div class="h-32      "></div>

      <div class="absolute  bottom-0  start-0 end-0  ">

        <!--        hero and buttons-->

        <div class="  md:px-3  sm:px-1  flex   flex-col md:flex-row items-center ">
          <!--                    Right Col-->
          <!--          <div class="  md:w-2/5 py-6 text-center">-->
          <!--            <img class="w-full xs:w-3/4 sm:w-3/4   mx-auto  z-50   " :src="heroImage"/>-->
          <!--          </div>-->
          <!--Left Col-->
          <div
              class="flex flex-col max-w-3xl text-white w-full   justify-center  mx-auto  text-center ">
            <h1 class="my-4 text-5xl font-bold leading-tight">

            </h1>
            <p class="leading-normal text-2xl mb-8">

            </p>
            <!--                        search-->
            <div v-if="false" class="w-full mx-auto mt-2 my-10">
              <div class="relative  px-6 mx-auto  ">
                <div
                    class="absolute top-0 bottom-0 start-0 flex items-center opacity-60  ps-10  ">
                  <svg
                      class="w-4 h-4 text-gray-600 fill-current   "
                      xmlns="http://www.w3.org/2000/svg"
                      viewBox="0 0 20 20">
                    <path
                        d="M12.9 14.32a8 8 0 1 1 1.41-1.41l5.35 5.33-1.42 1.42-5.33-5.34zM8 14A6 6 0 1 0 8 2a6 6 0 0 0 0 12z"></path>
                  </svg>
                  <span class="absolute border-gray-300  border-s top-0 bottom-0 my-2 ms-6"></span>
                </div>
                <input id="search-toggle" type="search" :placeholder="__('hero_search_placeholder')"
                       class="placeholder-gray-400 border-transparent block w-full py-3 ps-12 pe-4 font-bold text-gray-700 bg-gray-100 rounded-lg shadow-lg focus:outline-none focus:bg-white"
                       onkeyup="updateSearchResults(this.value);">

              </div>


            </div>

          </div>

        </div>

        <!--wave-->
        <svg viewBox="0 0 1428 174" xmlns="http://www.w3.org/2000/svg"
             xmlns:xlink="http://www.w3.org/1999/xlink">
          <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
            <g transform="translate(-2.000000, 44.000000)" fill="#FFFFFF" fill-rule="nonzero">
              <path
                  d="M0,0 C90.7283404,0.927527913 147.912752,27.187927 291.910178,59.9119003 C387.908462,81.7278826 543.605069,89.334785 759,82.7326078 C469.336065,156.254352 216.336065,153.6679 0,74.9732496"
                  opacity="0.100000001"></path>
              <path
                  d="M100,104.708498 C277.413333,72.2345949 426.147877,52.5246657 546.203633,45.5787101 C666.259389,38.6327546 810.524845,41.7979068 979,55.0741668 C931.069965,56.122511 810.303266,74.8455141 616.699903,111.243176 C423.096539,147.640838 250.863238,145.462612 100,104.708498 Z"
                  opacity="0.100000001"
              ></path>
              <path
                  d="M1046,51.6521276 C1130.83045,29.328812 1279.08318,17.607883 1439,40.1656806 L1439,120 C1271.17211,77.9435312 1140.17211,55.1609071 1046,51.6521276 Z"
                  id="Path-4" opacity="0.200000003"></path>
            </g>
            <g transform="translate(-4.000000, 76.000000)" fill="#FFFFFF" fill-rule="nonzero">
              <path
                  d="M0.457,34.035 C57.086,53.198 98.208,65.809 123.822,71.865 C181.454,85.495 234.295,90.29 272.033,93.459 C311.355,96.759 396.635,95.801 461.025,91.663 C486.76,90.01 518.727,86.372 556.926,80.752 C595.747,74.596 622.372,70.008 636.799,66.991 C663.913,61.324 712.501,49.503 727.605,46.128 C780.47,34.317 818.839,22.532 856.324,15.904 C922.689,4.169 955.676,2.522 1011.185,0.432 C1060.705,1.477 1097.39,3.129 1121.236,5.387 C1161.703,9.219 1208.621,17.821 1235.4,22.304 C1285.855,30.748 1354.351,47.432 1440.886,72.354 L1441.191,104.352 L1.121,104.031 L0.457,34.035 Z"
              ></path>
            </g>
          </g>
        </svg>
      </div>

    </div>
    <section class="flex justify-center pt-24">
      <div
          class="w-full   rounded-lg overflow-x-hidden shadow-lg max-w-6xl  xs:mx-2 md:mx-4    blur-xs opacity-75 bg-white  backdrop-filter">
        <div v-if="$page.props.error_message" class="text-center flex flex-col font-bold p-4 text-danger  text-lg">
          <div class="text-gray-900">{{ $page.props.error_message }}</div>
          <Link :href="$page.props.error_link" class="my-4">{{ __('return') }}</Link>
        </div>
        <!--      main section-->

        <div v-else-if="data" class=" flex flex-col ">
          <div class="px-4 py-2 text-white bg-primary">{{ data.name }}</div>
          <div v-if="data" class="border-t   p-4 flex flex-col space-y-4 m-4">

            <p v-if="data.owner" class="text-sm flex flex-wrap">
              <span class="text-gray-500 ">{{ __('owner') }}: </span>
              <span>{{ data.owner.fullname }}</span>
              <span class="border-s mx-2">  </span>
              <span class="text-gray-500  ">{{ __('phone') }}: </span>
              <span>{{ data.owner.phone }}</span>
            </p>
            <p class="text-sm">
              <span class="text-gray-500 ">{{ __('tags') }}: </span>
              <span>{{ data.tags }}</span>
            </p>
            <p class="text-sm">
              <span class="text-gray-500 ">{{ __('description') }}: </span>
              <span>{{ data.description }}</span>
            </p>

          </div>
          <div
              class=" flex  flex-wrap sm:flex-nowrap items-center justify-between     text-white bg-primary px-4 py-2 ">

            <div
                class=" flex flex-wrap w-full justify-start  items-center  text-sm  ">
              <div class="flex items-center">
                <EyeIcon class="w-4 h-4"/>
                <span class="px-1">{{ __('view') }}:</span>
                <span class="px-1">{{ data.view }}</span>
              </div>
              <div class=" border-s   py-4 md:mx-6  mx-1"></div>
              <div class="flex items-center">
                <UserIcon class="w-4 h-4"/>
                <span class="px-1">{{ __('viewer') }}:</span>
                <span class="px-1">{{ data.viewer }}</span>
              </div>
              <div class=" border-s   py-4 md:mx-6  mx-1"></div>
              <div v-if="!hasWallet()" class="flex items-center">
                <CurrencyDollarIcon class="w-4 h-4"/>

                <span class="px-1">{{ __('reward') }}:</span>
                <span class="px-1">{{ $page.props.site_view_meta_reward }} {{ __('meta') }}</span>
              </div>
              <div v-else class="flex items-center">
                <CurrencyDollarIcon class="w-4 h-4"/>
                <span class="px-1">{{ __('reward') }}:</span>
                <span class="px-1">{{ asPrice(data.view_reward) }} {{ __('currency_symbol') }}</span>
              </div>
              <div class=" border-s   py-4 md:mx-6  mx-1"></div>
              <div class="flex items-center" dir="ltr">
                <input v-model="auto_view"
                       @change="auto_view && timer>=$page.props.reward_second  ?startTimer($refs.iframe  ):null"
                       class="mx-2   h-3.5 w-8 appearance-none rounded-[0.4375rem]  bg-neutral-300 before:pointer-events-none before:absolute before:h-3.5 before:w-3.5 before:rounded-full before:bg-transparent before:content-[''] after:absolute after:z-[2] after:-mt-[0.1975rem] after:h-5 after:w-5 after:rounded-full after:border-none after:bg-neutral-100 after:shadow-[0_0px_3px_0_rgb(0_0_0_/_7%),_0_2px_2px_0_rgb(0_0_0_/_4%)] after:transition-[background-color_0.2s,transform_0.2s] after:content-[''] checked:bg-success checked:after:absolute checked:after:z-[2] checked:after:-mt-[3px] checked:after:ml-[1.0625rem] checked:after:h-5 checked:after:w-5 checked:after:rounded-full checked:after:border-none checked:after:bg-success checked:after:shadow-[0_3px_1px_-2px_rgba(0,0,0,0.2),_0_2px_2px_0_rgba(0,0,0,0.14),_0_1px_5px_0_rgba(0,0,0,0.12)] checked:after:transition-[background-color_0.2s,transform_0.2s] checked:after:content-[''] hover:cursor-pointer focus:outline-none focus:ring-0 focus:before:scale-100 focus:before:opacity-[0.12] focus:before:shadow-[3px_-1px_0px_13px_rgba(0,0,0,0.6)] focus:before:transition-[box-shadow_0.2s,transform_0.2s] focus:after:absolute focus:after:z-[1] focus:after:block focus:after:h-5 focus:after:w-5 focus:after:rounded-full focus:after:content-[''] checked:focus:border-success checked:focus:bg-success checked:focus:before:ml-[1.0625rem] checked:focus:before:scale-100 checked:focus:before:shadow-[3px_-1px_0px_13px_#3b71ca] checked:focus:before:transition-[box-shadow_0.2s,transform_0.2s]"
                       type="checkbox"
                       role="switch"
                       id="auto_view"
                />
                <label
                    class="inline-block px-2 hover:cursor-pointer"
                    for="auto_view"
                > {{ __('auto_view') }}</label
                >
              </div>
              <div>
                <div @click="stop=!stop" class="rounded-full p-1 mx-1 border cursor-pointer hover:bg-gray-400">
                  <PlayIcon v-if="stop" class="w-5 h-5"/>
                  <PauseIcon v-else class="w-5 h-5"/>
                </div>
              </div>
            </div>
            <div class="text-primary-500 bg-primary-100 rounded-lg p-2 justify-center flex text-xs">
              <span class="mx-1">  {{ __('viewable') }}:</span>
              <span class="mx-1"> {{ available_sites > 0 ? available_sites - 1 : 0 }}</span>

            </div>
          </div>
          <div class="flex">
            <div class="shadow w-full bg-grey-light m-2   bg-gray-200 rounded-full">
              <div
                  class=" bg-primary rounded  text-xs leading-none py-[.1rem] text-center text-white duration-300 "
                  :class="{' animate-pulse': timerPercent<100}"
                  :style="`width: ${timerPercent}%`">
                <span class="animate-bounce">{{ timer }}</span>
              </div>
            </div>
          </div>

          <iframe ref="iframe" class="w-full  min-h-[24rem]" :src="$page.props.data.link"
                  @load.once="startTimer">

          </iframe>

        </div>
      </div>
    </section>
    <section>
      <div class=" w-full px-3 my-8   flex  items-center justify-center">
        <PrimaryButton @click="$inertia.visit(route($page.props.auth.user?'panel.site.create':'site.new' ))"
                       class="mx-2 py-2  px-6  ">
          {{ __('register_site') }}
        </PrimaryButton>
      </div>
    </section>
    <section class="flex justify-center  p-5 max-w-7xl  mx-auto border-t mt-5">
      <!--      <div class=" w-80 p-3   mx-2  bg-white rounded-lg      lg:flex md:hidden sm:hidden xs:hidden"></div>-->

      <div
          class="   grid sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-4   gap-2     max-w-6xl">
        <Link v-for="(d,idx) in sites" :href="route('site',d.id)"
              class="flex-col items-stretch cursor-pointer hover:scale-[101%] duration-300 rounded-lg overflow-hidden shadow-lg">
          <Image :src="route('storage.sites')+`/${d.id}.jpg`" classes="object-cover h-48 rounded-lg  w-full"/>
          <div class="p-2  text-gray-700">{{ cropText(d.name, 30) }}</div>
          <div class="px-4 py-2 text-sm   text-gray-400">{{ getCategory(d.category_id) }}</div>
          <hr class="border-gray-200  ">
          <div class="flex justify-around  items-center p-4 text-sm text-gray-500">
            <div class="flex items-center">
              <!--              <EyeIcon class="w-4 h-4"/>-->
              <span class="px-1">{{ __('view') }}:</span>
              <span class="px-1">{{ d.view }}</span>
            </div>
            <div class=" border-s   py-2"></div>
            <div v-if="!hasWallet()" class="flex items-center">
              <!--              <EyeIcon class="w-4 h-4"/>-->
              <span class="px-1">{{ __('reward') }}:</span>
              <span class="px-1">{{ $page.props.site_view_meta_reward }} {{ __('meta') }}</span>
            </div>
            <div v-else class="flex items-center">
              <!--              <EyeIcon class="w-4 h-4"/>-->
              <span class="px-1">{{ __('reward') }}:</span>
              <span class="px-1">{{ asPrice(d.view_fee) }} {{ __('currency_symbol') }}</span>
            </div>

          </div>
        </Link>
      </div>
    </section>
    <LoadingIcon v-show="loading" ref="loader" type="linear"/>
  </Scaffold>
</template>

<script>

import Scaffold from "@/Layouts/Scaffold.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import {Head, Link} from "@inertiajs/vue3";
import LoadingIcon from "@/Components/LoadingIcon.vue";
import Image from "@/Components/Image.vue";
import {} from "@heroicons/vue/24/outline";
import {
  EyeIcon,
  CurrencyDollarIcon,
  UserIcon,
  PlayIcon,
  PauseIcon,
} from "@heroicons/vue/24/solid";

export default {
  data() {
    return {
      html: null,
      stop: false,
      timer: 0,
      timerPercent: 0,
      auto_view: this.$page.props.auto_view,
      error: null,
      data: null,
      available_sites: 0,
      hiddenProp: null,
      intervalId: null,
      sites: [],
      loading: false,
      params: {
        page: 0,
        search: null,
        order_by: null,
        dir: null,
      }
    }
  },
  components: {
    Scaffold,
    Image,
    Link,
    EyeIcon,
    CurrencyDollarIcon,
    LoadingIcon,
    PrimaryButton,
    SecondaryButton,
    UserIcon,
    PlayIcon,
    PauseIcon,
  },
  beforeUnmount() {
    clearInterval(this.intervalId);
  },
  created() {
    // this.isLoading(true);
    window.addEventListener("beforeunload", () => {
      clearInterval(this.intervalId);
    });
  },
  mounted() {
    //if support hidden method with perfixes?
    this.hiddenProp = this.getHiddenProp();
    if (!this.$page.props.error_message)
      this.isLoading(true);
    this.data = this.$page.props.data;
    this.available_sites = this.$page.props.available_sites;
    this.setScroll(this.$refs.loader.$el);
    this.getData();
  },
  methods: {
    startTimer(el) {
      this.isLoading(false);
      this.timer = 0;
      const iframe = el.target;
      let seconds = this.$page.props.reward_second;
      // seconds = 5;
      this.intervalId = setInterval(function () {
        if (!this.tabIsVisible() && !this.stop) {
          this.timer++;
          this.timerPercent = Math.floor((this.timer / seconds) * 100)
        }
        if (this.timer >= seconds) {
          clearInterval(this.intervalId);
          this.addTransaction();
        }
      }.bind(this), 1000);
    },
    addTransaction() {
      if (!this.$page.props.data) return; //user changed page
      this.isLoading(true);
      window.axios.post(route('transaction.site.view'), {
            id: this.$page.props.data.id,
            auto_view: this.auto_view,
          },
      )
          .then((response) => {
            this.isLoading(false);
            if (response.data && response.data.message) {
              this.showToast('success', response.data.message);
              this.sleep(3000).then(() => {
                if (response.data.next && this.auto_view)
                  this.$inertia.visit(route('site', response.data.next), {method: 'get'});

              });
            }

          })

          .catch((error) => {
            this.isLoading(false);
            this.error = this.getErrors(error);
            // console.log(this.error);
            this.showToast('danger', this.error);

            if (error.response && error.response.data) {
              this.sleep(3000).then(() => {
                if (error.response.data.next && this.auto_view)
                  this.$inertia.visit(route('site', error.response.data.next), {method: 'get'});

              });
            }
          })
          .finally(() => {
            // always executed
            this.isLoading(false);
          });
    },
    tabIsVisible() {


      const prop = this.hiddenProp;
      if (!prop) return false;

      return document[prop];
    },
    getHiddenProp() {
      var prefixes = ['webkit', 'moz', 'ms', 'o'];

      // if 'hidden' is natively supported just return it
      if ('hidden' in document) return 'hidden';

      // otherwise loop over all the known prefixes until we find one
      for (var i = 0; i < prefixes.length; i++) {
        if ((prefixes[i] + 'Hidden') in document)
          return prefixes[i] + 'Hidden';
      }

      // otherwise it's not supported
      return null;
    },
    getData() {

      if (this.total > 0 && this.total <= this.sites.length) return;
      this.loading = true;

      window.axios.get(route('site.search'), {
        params: this.params
      })
          .then((response) => {
            this.sites = this.sites.concat(response.data.data);
            this.total = response.data.total;
            this.params.page = response.data.current_page + 1;

          })
          .catch((error) => {
            this.error = this.getErrors(error);

            this.showToast('danger', this.error)
          })
          .finally(() => {
            // always executed
            this.loading = false;
          });
    },
    setScroll(el) {
      window.onscroll = () => {
//                    const {top, bottom, height} = this.loader.getBoundingClientRect();

        let top_of_element = el.offsetTop;
        let bottom_of_element = el.offsetTop + el.offsetHeight;
        let bottom_of_screen = window.pageYOffset + window.innerHeight;
        let top_of_screen = window.pageYOffset;

        if ((bottom_of_screen + 300 > top_of_element) && (top_of_screen < bottom_of_element + 200) && !this.loading) {

          this.getData();
          // scrolled = true;
//                        console.log('visible')
          // the element is visible, do something
        } else {
//                        console.log('invisible')
          // the element is not visible, do something else
        }
      };
    },
  },

}

</script>