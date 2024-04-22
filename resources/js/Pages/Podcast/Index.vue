<template>
  <Scaffold navbar-theme="dark">
    <template v-slot:header>
      <title>{{__('podcasts')}}</title>

    </template>

    <div class="relative  bg-gradient-to-t from-pink-300 via-purple-300 to-indigo-400">
      <!--Hero-->
      <div class="py-4         mx-auto    ">
        <div class="  px-3  sm:px-1  flex   flex-col md:flex-row items-center ">
          <!--                    Right Col-->
          <!--          <div class="  md:w-2/5 py-6 text-center">-->
          <!--            <img class="w-full xs:w-3/4 sm:w-3/4   mx-auto  z-50   " :src="heroImage"/>-->
          <!--          </div>-->
          <!--Left Col-->
          <div
              class="flex flex-col max-w-3xl text-white w-full   justify-center  mx-auto  text-center ">
            <h1 class="my-4 text-5xl font-bold leading-tight">

            </h1>
            <p class="leading-normal text-2xl mb-8" v-html="heroText">

            </p>
            <!--                        search-->

            <div class="  px-3    flex  items-stretch justify-center">
              <!--              <PrimaryButton class="mx-2 p-2 grow  ">{{ __('register_podcast') }}</PrimaryButton>-->
              <SecondaryButton @click="$inertia.visit(route('panel.podcast.create'))"
                               class="md:mx-2 p-2  text-xs md:text-sm  ">
                {{ __('register_podcast') }}
              </SecondaryButton>
              <SearchInput v-model="params.search" @search="getData(0)"/>
            </div>
          </div>

        </div>
      </div>
      <!--wave-->
      <div v-if="false" class="absolute  bottom-0 start-0 end-0">
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

    <section v-if="false" class="flex justify-center  p-1 max-w-8xl  py-8  ">
      <!--      <div class=" w-80 p-3   mx-2  bg-white rounded-lg     lg:flex md:hidden sm:hidden xs:hidden"></div>-->


      <div
          class="   grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-3 xl:grid-cols-4  gap-4     max-w-6xl">
        <Link v-for="(d,idx) in data" :href="route('article',d.article_id)"
              class="max-w-xs flex-col relative items-stretch cursor-pointer hover:scale-[101%] duration-300 rounded-lg overflow-hidden shadow-lg">
          <Image :src="route('storage.podcasts')+`/${d.id}.jpg`" classes="object-cover rounded-lg h-48   w-full"/>
          <div class="absolute text-gray-500 rounded-full p-4 mx-4 top-[10rem] bg-white   shadow-lg">
            <MicrophoneIcon class="w-5 h-5 "/>
          </div>
          <div v-if="d.status=='active'"
               class="absolute text-gray-500 rounded-lg text-white bg-rose-500 p-1 px-2 m-2  end-0 top-0    shadow-lg">
            {{ `${d.view_fee} ⭐️` }}
          </div>
          <div class="p-2 mt-4  text-gray-700">{{ cropText(d.name, 30) }}</div>
          <div class="px-2 py-2 text-sm   text-gray-400">{{ cropText(d.narrator, 30) }}</div>
          <div class="px-4 py-2 text-xs   text-gray-400">{{ getCategory(d.category_id) }}</div>
          <hr class="border-gray-200  ">
          <div class="flex justify-around  items-center p-4 text-sm text-gray-500">
            <div class="flex items-center">
              <!--              <EyeIcon class="w-4 h-4"/>-->
              <span class="px-1">{{ __('view') }}:</span>
              <span class="px-1">{{ d.view }}</span>
            </div>
            <div class=" border-s   py-4"></div>
            <!--            <div v-if="!hasWallet()" class="flex items-center">-->
            <!--              &lt;!&ndash;              <EyeIcon class="w-4 h-4"/>&ndash;&gt;-->
            <!--              <span class="px-1">{{ __('reward') }}:</span>-->
            <!--              <span class="px-1">{{ $page.props.site_view_meta_reward }} {{ __('meta') }}</span>-->
            <!--            </div>-->
            <div class="flex items-center">
              <!--              <EyeIcon class="w-4 h-4"/>-->
              <!--              <span class="px-1">{{ __('location') }}:</span>-->
              <span class="px-1">{{ getDuration(d.duration) }} </span>
            </div>

          </div>
        </Link>
      </div>
    </section>
    <section class="    ">
      <div v-for="(cat,idx) in categories" class="  ps-8">

        <div v-if="cat && cat.length>0" class="my-8">
          <div class="p-2 my-2  font-semibold w-fit  text-primary-500   border-b-2 border-primary-500">{{
              getCategory(idx)
            }}
          </div>
          <swiper class="w-full   "
                  :modules="modules"

                  :slides-per-view="'auto'"
                  :space-between="16"
                  :pagination="{ clickable: true }"
                  :scrollbar="{ draggable: true }"
                  @swiper=""
                  @slideChange=""
          >
            <swiper-slide v-for="(d,idx) in cat" class="max-w-[16rem]    ">
              <Link :href="route('article',d.article_id)"
                    class="flex my-2 max-w-xs flex-col relative items-stretch cursor-pointer hover:scale-[101%] duration-300 rounded-lg overflow-hidden shadow-lg">
                <Image :src="route('storage.podcasts')+`/${d.id}.jpg`" classes="object-cover rounded-lg h-48   w-full"/>
                <div class="absolute text-gray-500 rounded-full p-4 mx-4 top-[10rem] bg-white   shadow-lg">
                  <MicrophoneIcon class="w-5 h-5 "/>
                </div>
                <div v-if="d.status=='active'"
                     class="absolute text-gray-500 rounded-lg text-white bg-rose-500 p-1 px-2 m-2  end-0 top-0    shadow-lg">
                  {{ `${d.view_fee} ⭐️` }}
                </div>
                <div class="p-2 mt-4  text-gray-700">{{ cropText(d.name, 30) }}</div>
                <div class="px-2 py-2 text-sm   text-gray-400">{{ cropText(d.narrator, 30) }}</div>
                <div class="px-4 py-2 text-xs   text-gray-400">{{ getCategory(d.category_id) }}</div>
                <hr class="border-gray-200  ">
                <div class="flex justify-around  items-center p-4 text-sm text-gray-500">
                  <div class="flex items-center">
                    <!--              <EyeIcon class="w-4 h-4"/>-->
                    <span class="px-1">{{ __('view') }}:</span>
                    <span class="px-1">{{ d.view }}</span>
                  </div>
                  <div class=" border-s   py-4"></div>
                  <!--            <div v-if="!hasWallet()" class="flex items-center">-->
                  <!--              &lt;!&ndash;              <EyeIcon class="w-4 h-4"/>&ndash;&gt;-->
                  <!--              <span class="px-1">{{ __('reward') }}:</span>-->
                  <!--              <span class="px-1">{{ $page.props.site_view_meta_reward }} {{ __('meta') }}</span>-->
                  <!--            </div>-->
                  <div class="flex items-center">
                    <!--              <EyeIcon class="w-4 h-4"/>-->
                    <!--              <span class="px-1">{{ __('location') }}:</span>-->
                    <span class="px-1">{{ getDuration(d.duration) }} </span>
                  </div>

                </div>
              </Link>
            </swiper-slide>


          </swiper>
        </div>
      </div>
    </section>
    <LoadingIcon v-show="loading" ref="loader" type="linear"/>
  </Scaffold>

</template>

<script>
import LoadingIcon from "@/Components/LoadingIcon.vue";
import Image from "@/Components/Image.vue";
import Scaffold from "@/Layouts/Scaffold.vue";
import {Head, Link} from '@inertiajs/vue3';
import heroImage from '@/../images/hero.jpg';
import {loadScript} from "vue-plugin-load-script";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import {EyeIcon} from "@heroicons/vue/24/outline";
import {MicrophoneIcon} from "@heroicons/vue/24/solid";
import SearchInput from "@/Components/SearchInput.vue";
import {Swiper, SwiperSlide} from 'swiper/vue';
import {Navigation, Pagination, Scrollbar, A11y} from 'swiper/modules';
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';
import 'swiper/css/scrollbar';

export default {
  data() {
    return {
      modules: [Navigation, Pagination, Scrollbar, A11y],
      categories: [],
      heroImage,
      loading: false,
      total: 0,
      data: [],
      params: {
        page: 0,
        search: null,
        order_by: null,
        dir: null,
      }
    }
  },
  props: ['heroText'],
  components: {
    SearchInput,
    SecondaryButton,
    PrimaryButton,
    Scaffold,
    Head,
    LoadingIcon,
    Image,
    EyeIcon,
    Link,
    MicrophoneIcon,
    Swiper,
    SwiperSlide,
  },
  // mixins: [Mixin],
  setup(props) {

  }, mounted() {
    // this.setScroll(this.$refs.loader.$el);
    this.getData();
  },
  methods: {
    getData(page) {
      if (page == 0) {
        this.params.page = 1;
        this.data = [];
      }

      if (this.total > 0 && this.total <= this.data.length) return;
      this.loading = true;

      window.axios.get(route('podcast.search'), {
        params: this.params
      })
          .then((response) => {
            // this.data = this.data.concat(response.data.data);
            this.total = response.data.total;
            this.params.page = response.data.current_page + 1;
            this.categories = response.data;

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
  }

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
