<template>
  <Scaffold navbar-theme="dark">
    <template v-slot:header>
      <title>{{__('make_money')}}</title>

    </template>

    <div class="relative    bg-gradient-to-t from-pink-300 via-purple-300 to-indigo-400">
      <!--Hero-->
      <img class=" opacity-50  blur-sm h-full z-0 absolute  bottom-2    start-0 w-full   h-full "
           :src="heroImage"/>
      <div class="py-24 md:py-32 xl:py-48   z-10    mx-auto    ">
        <div class="  px-3  sm:px-1  flex   flex-col md:flex-row items-center">
          <!--                    Right Col-->
          <div class=" z-10 md:w-2/5 py-6 text-center">

            <!--            <h1 class="my-4 text-white text-5xl font-bold    ">-->
            <!--              {{ __('app_name') }}-->
            <!--            </h1>-->
          </div>
          <!--Left Col-->
          <div
              class="flex flex-col max-w-lg text-white w-full md:w-3/5 justify-center   text-center  ">
            <p class="leading-normal text-2xl mb-8 z-10">
              {{ heroText }}
            </p>
            <!--                        search-->
            <div v-if="false" class="w-full mx-auto mt-2  ">
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
            <div class=" z-10 p-3    flex  items-center">
              <PrimaryButton v-if="false" @click="$inertia.visit(route('user.panel.index'))" class="mx-2 p-2 grow">{{
                  __('adv_create')
                }}
              </PrimaryButton>
              <!--              <SecondaryButton @click="$inertia.visit(route('page.make_money'))" class="mx-2 p-2 grow">{{-->
              <!--                  __('make_money')-->
              <!--                }}-->
              <!--              </SecondaryButton>-->
            </div>
          </div>

        </div>
      </div>
      <!--wave-->
      <div class="absolute  bottom-0 start-0 end-0">
        <svg viewBox="0 0 1428 174" version="1.1" xmlns="http://www.w3.org/2000/svg"
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
    <div class="container mx-auto space-y-4">
      <Transition name="fade" mode="out-in">
        <section class="flex flex-col   justify-center   text-center ">
          <h1 class="slide-in-bottom-h1 my-4 text-primary-500 mx-auto w-fit text-3xl font-bold border-primary       ">
            {{ __('make_money_methods') }}
            <div
                class="h-1 mx-auto bg-gradient-to-l from-primary-100 via-primary-500 to-primary-600 w-64 opacity-25 my-0 py-0 rounded-t"></div>
          </h1>
          <div class="text-primary-500"> {{ __('revenue_from_below_items') }}</div>

          <div class="m-4 mt-8 mx-auto gap-4 grid grid-cols-1 md:grid-cols-2 max-w-4xl bounce-top-icons">

            <Link :href="route('site.index')"
                  class="flex cursor-pointer hover:scale-[101%] duration-300 gap-4 bg-gradient-to-l from-primary-50 via-gray-50 to-primary-100 shadow-md rounded-lg px-4 py-8 items-center">
              <CursorArrowRippleIcon class="w-12 h-12   text-primary-500"/>
              <div class="flex flex-col   space-y-2 items-center  ">
                <div class="text-sm font-bold text-primary">{{ __('revenue_from_view_sites') }}</div>
              </div>
            </Link>
            <Link :href="route('item.index')"
                  class="flex cursor-pointer hover:scale-[101%] duration-300 gap-4 bg-gradient-to-l from-primary-50 via-gray-50 to-primary-100 shadow-md rounded-lg px-4 py-8 items-center">
              <CursorArrowRippleIcon class="w-12 h-12   text-primary-500"/>
              <div class="flex flex-col   space-y-2 items-center  ">
                <div class="text-sm font-bold text-primary">{{
                    __('revenue_from_view_article_banner_podcast_video')
                  }}
                </div>
              </div>
            </Link>
            <Link :href="route('user.panel.index')"
                  class="flex cursor-pointer hover:scale-[101%] duration-300 gap-4 bg-gradient-to-l from-primary-50 via-gray-50 to-primary-100 shadow-md rounded-lg px-4 py-8 items-center">
              <WrenchScrewdriverIcon class="w-12 h-12   text-primary-500"/>
              <div class="flex flex-col   space-y-2 items-center  ">
                <div class="text-sm font-bold text-primary">{{
                    __('revenue_from_projects')
                  }}
                </div>
              </div>
            </Link>
            <Link :href="route('panel.profile.edit')"
                  class="flex cursor-pointer hover:scale-[101%] duration-300 gap-4 bg-gradient-to-l from-primary-50 via-gray-50 to-primary-100 shadow-md rounded-lg px-4 py-8 items-center">
              <UserGroupIcon class="w-12 h-12   text-primary-500"/>
              <div class="flex flex-col   space-y-2 items-center  ">
                <div class="text-sm font-bold text-primary">{{
                    __('revenue_from_referral')
                  }}
                </div>
              </div>
            </Link>


          </div>
        </section>
      </Transition>


    </div>

  </Scaffold>
</template>

<script>
import Scaffold from "@/Layouts/Scaffold.vue";
import {Head, Link} from '@inertiajs/vue3';
import heroImage from '@/../images/hero.jpg';
import {loadScript} from "vue-plugin-load-script";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import {
  GlobeAltIcon,
  CursorArrowRippleIcon,
  WrenchScrewdriverIcon,
  UserGroupIcon,
} from "@heroicons/vue/24/outline";

export default {
  data() {
    return {
      heroImage,

    }
  },
  props: ['heroText'],
  components: {
    Link,
    SecondaryButton,
    PrimaryButton,
    Scaffold,
    Head,
    GlobeAltIcon,
    CursorArrowRippleIcon,
    WrenchScrewdriverIcon,
    UserGroupIcon,

  },
  // mixins: [Mixin],
  setup(props) {

  }, mounted() {
    // loadScript("https://d1w019qw3bn26k.cloudfront.net/assets/application-b7a4037d87fe8e15e2a75db9f136291a1fd5cea2fa810311c7c1bb44ecb6c069.js");
    // loadScript("https://www.googletagmanager.com/gtag/js?id=G-P9M5S0890F");
    // loadScript("https://unpkg.com/@alpinejs/ui@3.10.5-beta.7/dist/cdn.min.js");
    // loadScript("https://unpkg.com/@alpinejs/focus@3.10.5/dist/cdn.min.js");
    // loadScript("https://unpkg.com/@alpinejs/collapse@3.x.x/dist/cdn.min.js");
    // loadScript("https://unpkg.com/alpinejs@3.10.5/dist/cdn.min.js");
    // loadScript("https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js");


  },

}
</script>
<style type="text/css">.turbo-progress-bar {
  position: fixed;
  display: block;
  top: 0;
  left: 0;
  height: 3px;
  background: #32CD32;
  z-index: 9999;
  transition: width 300ms ease-out,
  opacity 150ms 150ms ease-in;
  transform: translate3d(0, 0, 0);
}
</style>

