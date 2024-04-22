<template>
  <Panel>
    <template v-slot:header>
      <title>{{__('repo_order')}}</title>

    </template>
    <template v-slot:content>

      <div class="mx-2">
        <section
            class="flex    flex-wrap gap-2 w-full bg-gray-100 rounded-b-2xl shadow-md p-2  px-2 lg:px-4 items-center z-[-10]">

          <SearchInput v-model="params.search" @search="getData(0)"/>
          <swiper
              :modules="[modules[0], modules[2],modules[3],]"
              slides-per-view="auto"
              :space-between="16"
              :pagination="{ clickable: true }"
              :scrollbar="{ draggable: true }"
              @swiper=""
              @slideChange=""
              class="w-full p-3 ">
            <swiper-slide @click=" toggleArray(p.id ,params.parent_ids);getData(0)"
                          :class="{'bg-primary-500 hover:bg-primary-400':params.parent_ids.filter((e)=>e==p.id).length>0}"
                          class="flex max-w-[3.5rem] flex-col rounded p-1 mb-4  items-center hover:cursor-pointer hover:bg-gray-50 hover:scale-[105%]"
                          v-for="p in $page.props.products">
              <div>
                <Image :id="p.id" classes="rounded-full h-12 w-12 border-primary-500 border"
                       :src="route('storage.products')+`/${p.id}.jpg`"></Image>
              </div>
              <div :class="{'text-white':params.parent_ids.filter((e)=>e==p.id).length>0}"
                   class="text-xs text-center text-neutral-500">{{ replaceAll(p.name, ' ', "‌") }}
              </div>
            </swiper-slide>
          </swiper>
        </section>


        <section v-if="products.length>0" class="container-lg  mx-auto  ">

          <div
              class="  mt-6   gap-y-3 gap-x-2 grid   sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 ">
            <div class="bg-white  shadow-md rounded-lg  "
                 v-for="(p,idx) in products">
              <article :id="p.id" @click.self="$inertia.visit(  route( 'variation.view',{id:p.id,name:p.name}) )"
                       class=" overflow-hidden flex flex-row sm:flex-col hover:bg-gray-100 hover:cursor-pointer hover:scale-[101%] duration-300">
                <Image
                    classes="   object-contain md:mx-auto sm:h-64      w-32 sm:w-full  h-32  rounded-b-lg mx-2  "
                    :src="route('storage.variations')+`/${p.id}/thumb.jpg`"></Image>

                <div class="hidden sm:flex min-w-[36%]   mx-auto">
                  <CartItemButton :link="route('admin.panel.repository.cart.update')" :key=" p.id
                  " class="w-full " :product-id="p.id"/>
                </div>
                <div class="p-4   w-full flex flex-col items-stretch justify-start items-start items-between">
                  <div class="flex items-center justify-between">
                    <div class="text-primary-600 ms-1  ">{{ p.name }}</div>
                    <div class="text-sm text-neutral-500 mx-2 ">{{
                        __('grade') + ' ' + (p.grade || __('unknown'))
                      }}
                    </div>

                  </div>
                  <hr class="border-gray-200  m-2">
                  <div class="text-neutral-500 text-sm">{{ p.repo_name }}</div>
                  <div class="flex items-center text-sm">
                    <div>{{ __('in_stock') + ` : ${p.in_shop ? parseFloat(p.in_shop) : 0}` }}</div>
                    <div class="text-sm text-neutral-500 mx-2" v-if="getPack(p.pack_id)">{{
                        ` ${getPack(p.pack_id)} `
                      }}
                    </div>
                  </div>
                  <div class="flex items-center text-sm">
                    <div>{{ __('weight') + ` : ${parseFloat(p.weight)}` }}</div>
                    <div class="text-sm text-neutral-500 mx-2">{{ __('kg') }}</div>

                  </div>
                  <div class="flex items-center justify-end">
                    <div class="flex items-center "
                         :class="{'line-through text-neutral-500':$page.props.is_auction && p.in_auction}">
                      {{ asPrice(p.price) }}

                      <svg v-if="$page.props.is_auction && p.in_auction" xmlns="http://www.w3.org/2000/svg"
                           viewBox="0 0 14 14"
                           class="fill-gray-500 h-5 w-5">
                        <path fill-rule="evenodd"
                              d="M3.057 1.742L3.821 1l.78.75-.776.741-.768-.749zm3.23 2.48c0 .622-.16 1.111-.478 1.467-.201.221-.462.39-.783.505a3.251 3.251 0 01-1.083.163h-.555c-.421 0-.801-.074-1.139-.223a2.045 2.045 0 01-.9-.738A2.238 2.238 0 011 4.148c0-.059.001-.117.004-.176.03-.55.204-1.158.525-1.827l1.095.484c-.257.532-.397 1-.419 1.403-.002.04-.004.08-.004.12 0 .252.055.458.166.618a.887.887 0 00.5.354c.085.028.178.048.278.06.079.01.16.014.243.014h.555c.458 0 .769-.081.933-.244.14-.139.21-.383.21-.731V2.02h1.2v2.202zm5.433 3.184l-.72-.7.709-.706.735.707-.724.7zm-2.856.308c.542 0 .973.19 1.293.569.297.346.445.777.445 1.293v.364h.18v-.004h.41c.221 0 .377-.028.467-.084.093-.055.14-.14.14-.258v-.069c.004-.243.017-1.044 0-1.115L13 8.05v1.574a1.4 1.4 0 01-.287.863c-.306.405-.804.607-1.495.607h-.627c-.061.733-.434 1.257-1.117 1.573-.267.122-.58.21-.937.265a5.845 5.845 0 01-.914.067v-1.159c.612 0 1.072-.082 1.38-.247.25-.132.376-.298.376-.499h-.515c-.436 0-.807-.113-1.113-.339-.367-.273-.55-.667-.55-1.18 0-.488.122-.901.367-1.24.296-.415.728-.622 1.296-.622zm.533 2.226v-.364c0-.217-.048-.389-.143-.516a.464.464 0 00-.39-.187.478.478 0 00-.396.187.705.705 0 00-.136.449.65.65 0 00.003.067c.008.125.066.22.177.283.093.054.21.08.352.08h.533zM9.5 6.707l.72.7.724-.7L10.209 6l-.709.707zm-6.694 4.888h.03c.433-.01.745-.106.937-.29.024.012.065.035.12.068l.074.039.081.042c.135.073.261.133.379.18.345.146.67.22.977.22a1.216 1.216 0 00.87-.34c.3-.285.449-.714.449-1.286a2.19 2.19 0 00-.335-1.145c-.299-.457-.732-.685-1.3-.685-.502 0-.916.192-1.242.575-.113.132-.21.284-.294.456-.032.062-.06.125-.084.191a.504.504 0 00-.03.078 1.67 1.67 0 00-.022.06c-.103.309-.171.485-.205.53-.072.09-.214.14-.427.147-.123-.005-.209-.03-.256-.076-.057-.054-.085-.153-.085-.297V7l-1.201-.5v3.562c0 .261.048.496.143.703.071.158.168.296.29.413.123.118.266.211.43.28.198.084.42.13.665.136v.001h.036zm2.752-1.014a.778.778 0 00.044-.353.868.868 0 00-.165-.47c-.1-.134-.217-.201-.35-.201-.18 0-.33.103-.447.31-.042.071-.08.158-.114.262a2.434 2.434 0 00-.04.12l-.015.053-.015.046c.142.118.323.216.544.293.18.062.325.092.433.092.044 0 .086-.05.125-.152z"
                              clip-rule="evenodd"></path>
                      </svg>


                    </div>
                    <div v-if=" p.in_auction==true" class="flex items-center ">
                      <ArrowTrendingUpIcon class="  rotate-180 text-neutral-500 mx-2"/>
                      <span> {{ asPrice(p.auction_price) }}</span>

                    </div>
                    <TomanIcon class="w-4 h-4 mx-2"/>
                  </div>
                  <div class="flex sm:hidden  min-w-[100%] xs:min-w-[70%]   me-auto">
                    <CartItemButton :link="route('admin.panel.repository.cart.update')" :key="p.id" class="w-full "
                                    :product-id="p.id"/>
                  </div>
                </div>
              </article>

            </div>
          </div>
        </section>
        <section v-else-if="!loading   "
                 class="font-bold text-rose-500  mt-8 justify-center  flex flex-col items-center   ">
          <div>
            {{ __('no_product_in_selected_city') }}
          </div>
        </section>
        <LoadingIcon v-show="loading" ref="loader" type="linear"/>
      </div>
    </template>
  </Panel>

</template>

<script>
import LoadingIcon from "@/Components/LoadingIcon.vue";
import TomanIcon from "@/Components/TomanIcon.vue";
import Image from "@/Components/Image.vue";
import Scaffold from "@/Layouts/Scaffold.vue";
import {Head, Link} from '@inertiajs/vue3';
import heroImage from '@/../images/hero.jpg';
import {loadScript} from "vue-plugin-load-script";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import {EyeIcon, MapPinIcon} from "@heroicons/vue/24/outline";
import {PencilIcon, ArrowTrendingUpIcon} from "@heroicons/vue/24/solid";
import SearchInput from "@/Components/SearchInput.vue";
import LocationSelector from "@/Components/LocationSelector.vue";
import {Swiper, SwiperSlide} from 'swiper/vue';
import {Navigation, Pagination, Scrollbar, A11y} from 'swiper/modules';
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';
import 'swiper/css/scrollbar';
import CartItemButton from "@/Components/CartItemButton.vue";
import {Dropdown, initTE, Modal} from "tw-elements";
import Panel from "@/Layouts/Panel.vue";

export default {
  data() {
    return {
      products: [],
      categories: [],
      heroImage,
      loading: false,
      total: 0,
      params: {
        page: 0,
        search: null,
        products: [],
        order_by: null,
        dir: null,
        parent_ids: [],
        province_id: this.getUserProvinceId(),
        city_id: this.getUserCityId(),
      },
      modules: [Navigation, Pagination, Scrollbar, A11y],
    }
  },
  props: ['heroText'],
  components: {
    CartItemButton,
    SearchInput,
    SecondaryButton,
    PrimaryButton,
    Scaffold,
    Head,
    LoadingIcon,
    Image,
    EyeIcon,
    Link,
    PencilIcon,
    Swiper,
    SwiperSlide,
    LocationSelector,
    MapPinIcon,
    ArrowTrendingUpIcon,
    TomanIcon,
    Panel,
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
        this.products = [];
      }

      if (this.total > 0 && this.total <= this.products.length) return;
      this.loading = true;

      window.axios.get(route('admin.panel.repository.shop.search'), {
        params: this.params
      })
          .then((response) => {
            // this.data = this.data.concat(response.data.data);
            this.total = response.data.total;
            this.params.page = response.data.current_page + 1;
            this.products = response.data.data;
            // console.log(response.data);
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
<!--swiper settings in swiper tag-->
<!--:auto-height="true"-->
<!--:slides-per-view="'auto'"-->
<!--:breakpoints="{-->
<!--0: {-->
<!--slidesPerView: 1,-->
<!--},-->
<!--350: {-->
<!--slidesPerView: 1,-->

<!--},-->
<!--540: {-->
<!--slidesPerView: 2,-->

<!--},-->
<!--768: {-->
<!--slidesPerView: 3,-->

<!--},-->
<!--1100: {-->
<!--slidesPerView: 4,-->

<!--},-->
<!--1200: {-->
<!--slidesPerView: 5,-->

<!--},-->
<!--}"-->