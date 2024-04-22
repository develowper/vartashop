<template>
  <Scaffold>
    <template v-slot:header>
      <title>{{__('home')}}</title>

    </template>

    <div
        class="relative    bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-emerald-500 to-emerald-800">
      <!--Hero-->
      <div
          class="opacity-60   bg-no-repeat  bg-cover  blur-sm   z-0 absolute  bottom-2     w-full   h-full      "
          :style="`background-image: url(${heroImage})`"></div>


      <div class="py-24 md:py-32 xl:py-32   z-10 z-[1044]   mx-auto    ">
        <div class="justify-center  px-3  sm:px-1  flex   flex-col md:flex-row items-center">
          <!--                    Right Col-->
          <div v-if="false" class=" z-10 md:w-2/5 py-6 text-center">

            <!--            <h1 class="my-4 text-white text-5xl font-bold    ">-->
            <!--              {{ __('app_name') }}-->
            <!--            </h1>-->
          </div>
          <!--Left Col-->
          <div
              class="flex flex-col max-w-lg text-white w-full    justify-center   text-center  ">

            <p class="   font-bold  text-xl py-4 shadow-[rgba(0,0,15,0.1)_0px_5px_20px_0px] shadow-primary-400 lg:text-2xl  mb-8 z-10   delay-300">
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
                <input id="search-toggle" type="search" v-model=" search"
                       :placeholder="__('hero_search_placeholder')"
                       class="placeholder-gray-400 border-transparent block w-full py-3 ps-12 pe-4 font-bold text-gray-700 bg-gray-100 rounded-lg shadow-lg focus:outline-none focus:bg-white"
                       onkeyup="updateSearchResults(  );">

              </div>


            </div>
            <div class=" p-3    gap-2 grid  grid-cols-2 md:grid-cols-4 items-center">

              <SecondaryButton @click="$inertia.visit(route('shop.index'))"
                               class="   grow    "
                               classes="     py-8  ">
                {{ __('shop') }}


              </SecondaryButton>
              <PartnershipForm :type="'agency'">
                <template v-slot:partnershipForm="props">
                  <SecondaryButton
                      classes="  py-8  "
                      data-te-ripple-init class="      grow    ">{{
                      __('agency')
                    }}
                  </SecondaryButton>
                </template>
              </PartnershipForm>


              <PartnershipForm :type="'gardener'">
                <template v-slot:partnershipForm="props">
                  <SecondaryButton classes="  py-8  "
                                   data-te-ripple-init class="  grow    ">{{
                      __('gardeners')
                    }}
                  </SecondaryButton>
                </template>
              </PartnershipForm>
              <PartnershipForm :type="'farmer'">
                <template v-slot:partnershipForm="props">
                  <SecondaryButton
                      classes=" py-8 "
                      data-te-ripple-init class=" grow ">{{
                      __('farmers')
                    }}
                  </SecondaryButton>
                </template>
              </PartnershipForm>
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
                  d="M 5 -51 C 118 -34 116 -30 424 13 C 643 10 681 36 1263 -11 C 1300 5 1402 29 1444 34 L 1441.191 104.352 L 1.121 104.031 L 1 -54 Z"
                  opacity="0.100000001"></path>
              <path
                  d="M 106 -20 C 393 16 493 33 584 39 C 1306 33 1402 29 1444 34 L 1441.191 104.352 L 1.121 104.031 Z"
                  opacity="0.100000001"
              ></path>
              <path
                  d="M 0.457 34.035 C 72 39 99 36 217 45 C 342 31 681 36 1216 12 C 1291 20 1402 29 1444 34 L 1441.191 104.352 L 1.121 104.031 L 0.457 34.035 Z"
                  id="Path-4" opacity="0.200000003"></path>
            </g>
            <g transform="translate(-4.000000, 76.000000)" fill="#FFFFFF" fill-rule="nonzero">
              <path
                  d="M 0.457 34.035 C 71 22 156 3 208 -2 C 248 5 1039 48 1124 55 C 1237 46 1355 27 1442 12 L 1441.191 104.352 L 1.121 104.031 L 0.457 34.035 Z"
              ></path>
            </g>
          </g>
        </svg>
      </div>


    </div>
    <div class="container  mx-auto space-y-4 relative">
      <div v-if="$page.props.slides.length>0"
           class="   shadow-md bg-white rounded-lg  overflow-hidden h-72 lg:h-72 xl:h-[24rem] w-full md:w-[90%] lg:w-[80%] md:mx-auto  start-0 end-0 md:-mt-32 -mt-16 ">
        <Slider :items="$page.props.slides" :delay="8000"></Slider>
      </div>

      <Transition name="fade" mode="out-in">
        <section class="flex flex-col   justify-center   text-center ">
          <h1 class="slide-in-bottom-h1 my-4 text-primary-500 mx-auto w-fit text-3xl font-bold border-primary       ">
            {{ $page.props.section1Header }}
            <div
                class="h-1 mx-auto bg-gradient-to-l from-primary-100 via-primary-500 to-primary-600 w-64 opacity-25 my-0 py-0 rounded-t"></div>

          </h1>
          <div v-if="false" class="  text-primary-500  slide-in-bottom-subtitle ">{{ __('how_help_you_revenue') }}</div>

          <div class="m-4 mt-8 gap-4 grid md:grid-cols-2 lg:grid-cols-4 bounce-top-icons">

            <div v-for="(item,idx) in section1"
                 class="relative flex gap-4 bg-gradient-to-l   shadow-md rounded-lg p-4 py-8 items-stretch">
              <component v-bind:is="item.icon" class="w-12 h-12 lg:w-24 lg:h-24 text-primary-500"></component>
              <div class="flex flex-col justify-start py-4 space-y-2 items-start  ">
                <div class="text-sm  text-primary-900 font-bold">{{ __(item.header) }}</div>
                <p class="text-sm text-start text-primary-900">{{ __(item.sub) }}</p>
              </div>
              <!--wave-->
              <svg class="absolute z-[-1] opacity-70 bottom-0 w-full start-0 wave-top" viewBox="0 0 1439 147"
                   version="1.1" xmlns="http://www.w3.org/2000/svg"
                   xmlns:xlink="http://www.w3.org/1999/xlink">
                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                  <g transform="translate(-1.000000, -14.000000)" fill-rule="nonzero">
                    <g class="wave " fill="#fff">
                      <path
                          d="M 106 -20 C 393 16 493 33 584 39 C 1306 33 1402 29 1444 34 L 1441.191 104.352 L 1.121 104.031 Z"></path>
                    </g>
                    <g transform="translate(0.000000, 50.000000)" class="fill-primary-500" fill="" fill-rule="nonzero">
                      <path
                          d="M 0.457 34.035 C 72 39 99 36 217 45 C 342 31 681 36 1216 12 C 1291 20 1402 29 1444 34 L 1441.191 104.352 L 1.121 104.031 L 0.457 34.035 Z"></path>
                    </g>
                  </g>
                  <g transform="translate(0.000000, 50.000000)" class="fill-primary-200" fill="" fill-rule="nonzero">
                    <path
                        d="M 0.457 34.035 C 71 22 156 3 208 -2 C 248 5 1039 48 1124 55 C 1237 46 1355 27 1442 12 L 1441.191 104.352 L 1.121 104.031 L 0.457 34.035 Z"></path>
                  </g>
                </g>
              </svg>


            </div>


          </div>
        </section>
      </Transition>


      <div v-if="$page.props.articles.length>0"
           class=" flex flex-col justify-center   rounded-lg  overflow-hidden h-72 lg:h-72    w-fit mx-auto     ">
        <Link :href="route('article.index')" class="text-lg text-primary-500 font-bold p-2 hover:text-primary-400">
          {{ __('articles') }}
        </Link>
        <ArticleSlider :items="$page.props.articles" :delay="8000"></ArticleSlider>
      </div>
    </div>
    <SupportChat id="support-chat" :ip="$page.props.ip" :broadcast-link="route('chat.broadcast')"
                 :support-history-link="route('chat.support.history')"/>
  </Scaffold>

</template>

<script>
import LoadingIcon from "@/Components/LoadingIcon.vue";
import TextInput from "@/Components/TextInput.vue";
import Scaffold from "@/Layouts/Scaffold.vue";
import {Head, Link} from '@inertiajs/vue3';
import heroImage from '@/../images/hero.jpg';
import {loadScript} from "vue-plugin-load-script";
import ButtonSkew from "@/Components/ButtonSkew.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import InputError from "@/Components/InputError.vue";
import Slider from "@/Components/Slider.vue";
import ArticleSlider from "@/Components/ArticleSlider.vue";
import PartnershipForm from "@/Components/PartnershipForm.vue";
import SupportChat from "@/Components/SupportChat.vue";
import vueRecaptcha from 'vue3-recaptcha2';
import {
  HomeModernIcon,
  RocketLaunchIcon,
  UsersIcon,
  WrenchScrewdriverIcon,
  PencilIcon,
  GlobeAltIcon,
  MicrophoneIcon,
  PlayIcon,
  PhotoIcon,
  FolderPlusIcon,
  Bars2Icon,
  StarIcon,
  TruckIcon,
  MapPinIcon,


} from "@heroicons/vue/24/solid";
import {

  PhoneIcon,
  ChatBubbleBottomCenterTextIcon,

} from "@heroicons/vue/24/outline";
import {Dropdown, initTE, Modal} from "tw-elements";

export default {
  data() {
    return {
      search: null,

      loading: false,
      heroImage,
      section1: this.$page.props.section1,
      section2: this.$page.props.section2,

      counts: this.$page.props.counts,
    }
  },
  props: ['heroText'],
  components: {
    SecondaryButton, PrimaryButton, Scaffold, Head,
    HomeModernIcon,
    RocketLaunchIcon,
    UsersIcon,
    WrenchScrewdriverIcon,
    PencilIcon,
    GlobeAltIcon,
    MicrophoneIcon,
    PlayIcon,
    PhotoIcon,
    Slider,
    FolderPlusIcon,
    Bars2Icon,
    TextInput,
    LoadingIcon,
    vueRecaptcha,
    PhoneIcon,
    ChatBubbleBottomCenterTextIcon,
    InputError,
    StarIcon,
    ArticleSlider,
    Link,
    TruckIcon,
    MapPinIcon,
    ButtonSkew,
    PartnershipForm,
    SupportChat,
  },
  // mixins: [Mixin],
  setup(props) {

  },
  mounted() {
    // loadScript("https://d1w019qw3bn26k.cloudfront.net/assets/application-b7a4037d87fe8e15e2a75db9f136291a1fd5cea2fa810311c7c1bb44ecb6c069.js");
    // loadScript("https://www.googletagmanager.com/gtag/js?id=G-P9M5S0890F");
    // loadScript("https://unpkg.com/@alpinejs/ui@3.10.5-beta.7/dist/cdn.min.js");
    // loadScript("https://unpkg.com/@alpinejs/focus@3.10.5/dist/cdn.min.js");
    // loadScript("https://unpkg.com/@alpinejs/collapse@3.x.x/dist/cdn.min.js");
    // loadScript("https://unpkg.com/alpinejs@3.10.5/dist/cdn.min.js");
    // loadScript("https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js");

    const countupEls = document.querySelectorAll('.countup');
    countupEls.forEach(this.animateCountUp);


    this.updateCart();
  },
  methods: {

    updateSearchResults() {
      this.log('search')
      if (!this.search) return;
      this.loading = true;
      this.params.phone = f2e(this.params.phone);
      window.axios.get(route('main.search'),
          {params: {search: this.search}})
          .then((response) => {
            if (response.data && response.data.message) {
              // this.modal.hide();
              this.showToast('success', response.data.message);
              this.params = {errors: {}, fullname: null, phone: null, description: null, recaptcha: null};
              this.recaptchaExpired();

            }


          })

          .catch((error) => {
            this.error = this.getErrors(error);
            if (error.response && error.response.data) {
              // this.log(error.response)
              this.params.errors = error.response.data.errors;

            }
            this.showToast('danger', this.error);
          })
          .finally(() => {
            // always executed
            this.loading = false;
          });
    },

    animateCountUp(el) {
      // How long you want the animation to take, in ms
      const animationDuration = 1000 + Math.random(3000);
// Calculate how long each ‘frame’ should last if we want to update the animation 60 times per second
      const frameDuration = 1000 / 60;
// Use that to calculate how many frames we need to complete the animation
      const totalFrames = Math.round(animationDuration / frameDuration);
// An ease-out function that slows the count as it progresses
      const easeOutQuad = t => t * (2 - t);


      let frame = 0;
      const countTo = parseInt(el.innerHTML, 10);
      // Start the animation running 60 times per second
      const counter = setInterval(() => {
        frame++;
        // Calculate our progress as a value between 0 and 1
        // Pass that value to our easing function to get our
        // progress on a curve
        const progress = easeOutQuad(frame / totalFrames);
        // Use the progress value to calculate the current count
        const currentCount = Math.round(countTo * progress);

        // If the current count has changed, update the element
        if (parseInt(el.innerHTML, 10) !== currentCount) {
          el.innerHTML = currentCount;
        }

        // If we’ve reached our last frame, stop the animation
        if (frame === totalFrames) {
          clearInterval(counter);
        }
      }, frameDuration);
    },

  },

}
</script>
<style>
/* ----------------------------------------------
* Generated by Animista
* w: http://animista.net, t: @cssanimista
* ---------------------------------------------- */

.slide-in-bottom {
  -webkit-animation: slide-in-bottom .5s cubic-bezier(.25, .46, .45, .94) both;
  animation: slide-in-bottom .5s cubic-bezier(.25, .46, .45, .94) both
}

.slide-in-bottom-h1 {
  -webkit-animation: slide-in-bottom .5s cubic-bezier(.25, .46, .45, .94) .5s both;
  animation: slide-in-bottom .5s cubic-bezier(.25, .46, .45, .94) .5s both
}

.slide-in-bottom-subtitle {
  -webkit-animation: slide-in-bottom .5s cubic-bezier(.25, .46, .45, .94) .75s both;
  animation: slide-in-bottom .5s cubic-bezier(.25, .46, .45, .94) .75s both
}

.fade-in {
  -webkit-animation: fade-in 1.2s cubic-bezier(.39, .575, .565, 1.000) 1s both;
  animation: fade-in 1.2s cubic-bezier(.39, .575, .565, 1.000) 1s both
}

.bounce-top-icons {
  -webkit-animation: bounce-top .9s 1s both;
  animation: bounce-top .9s 1s both
}

@-webkit-keyframes slide-in-bottom {
  0% {
    -webkit-transform: translateY(1000px);
    transform: translateY(1000px);
    opacity: 0
  }
  100% {
    -webkit-transform: translateY(0);
    transform: translateY(0);
    opacity: 1
  }
}

@keyframes slide-in-bottom {
  0% {
    -webkit-transform: translateY(1000px);
    transform: translateY(1000px);
    opacity: 0
  }
  100% {
    -webkit-transform: translateY(0);
    transform: translateY(0);
    opacity: 1
  }
}

@-webkit-keyframes bounce-top {
  0% {
    -webkit-transform: translateY(-45px);
    transform: translateY(-45px);
    -webkit-animation-timing-function: ease-in;
    animation-timing-function: ease-in;
    opacity: 1
  }
  24% {
    opacity: 1
  }
  40% {
    -webkit-transform: translateY(-24px);
    transform: translateY(-24px);
    -webkit-animation-timing-function: ease-in;
    animation-timing-function: ease-in
  }
  65% {
    -webkit-transform: translateY(-12px);
    transform: translateY(-12px);
    -webkit-animation-timing-function: ease-in;
    animation-timing-function: ease-in
  }
  82% {
    -webkit-transform: translateY(-6px);
    transform: translateY(-6px);
    -webkit-animation-timing-function: ease-in;
    animation-timing-function: ease-in
  }
  93% {
    -webkit-transform: translateY(-4px);
    transform: translateY(-4px);
    -webkit-animation-timing-function: ease-in;
    animation-timing-function: ease-in
  }
  25%, 55%, 75%, 87% {
    -webkit-transform: translateY(0);
    transform: translateY(0);
    -webkit-animation-timing-function: ease-out;
    animation-timing-function: ease-out
  }
  100% {
    -webkit-transform: translateY(0);
    transform: translateY(0);
    -webkit-animation-timing-function: ease-out;
    animation-timing-function: ease-out;
    opacity: 1
  }
}

@keyframes bounce-top {
  0% {
    -webkit-transform: translateY(-45px);
    transform: translateY(-45px);
    -webkit-animation-timing-function: ease-in;
    animation-timing-function: ease-in;
    opacity: 1
  }
  24% {
    opacity: 1
  }
  40% {
    -webkit-transform: translateY(-24px);
    transform: translateY(-24px);
    -webkit-animation-timing-function: ease-in;
    animation-timing-function: ease-in
  }
  65% {
    -webkit-transform: translateY(-12px);
    transform: translateY(-12px);
    -webkit-animation-timing-function: ease-in;
    animation-timing-function: ease-in
  }
  82% {
    -webkit-transform: translateY(-6px);
    transform: translateY(-6px);
    -webkit-animation-timing-function: ease-in;
    animation-timing-function: ease-in
  }
  93% {
    -webkit-transform: translateY(-4px);
    transform: translateY(-4px);
    -webkit-animation-timing-function: ease-in;
    animation-timing-function: ease-in
  }
  25%, 55%, 75%, 87% {
    -webkit-transform: translateY(0);
    transform: translateY(0);
    -webkit-animation-timing-function: ease-out;
    animation-timing-function: ease-out
  }
  100% {
    -webkit-transform: translateY(0);
    transform: translateY(0);
    -webkit-animation-timing-function: ease-out;
    animation-timing-function: ease-out;
    opacity: 1
  }
}

@-webkit-keyframes fade-in {
  0% {
    opacity: 0
  }
  100% {
    opacity: 1
  }
}

@keyframes fade-in {
  0% {
    opacity: 0
  }
  100% {
    opacity: 1
  }
}

</style>

