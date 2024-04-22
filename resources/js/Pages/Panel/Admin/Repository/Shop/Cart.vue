<template>
  <Panel>
    <template v-slot:header>
      <title>{{ __('cart')}}</title>

    </template>
    <template v-slot:content>


      <div v-if="cart" class="flex flex-col lg:flex-row p-2 lg:p-4 lg:max-w-5xl mx-auto">


        <section class=" grow  py-4 border  rounded-lg   md:rounded-e-none   bg-neutral-50">

          <!--        payment section-->
          <div v-if=" page=='payment'"
               class="border-b flex flex-col   rounded-lg mx-4 mt-4 p-2 lg:p-4">
            <div class="text-neutral-400 pb-2">{{ __('payment_method') }}</div>

            <div v-for="payment in cart.payment_methods"
                 class="border rounded-md p-4 cursor-pointer hover:bg-neutral-100"
                 :class="{'bg-neutral-100':payment.selected}">
              <div>{{ payment.name }}</div>
            </div>
          </div>
          <!--        address section-->
          <div
              class="border-b flex flex-col shadow  rounded-lg mx-1 mt-4 p-2 lg:p-4">
            <div class="text-neutral-400 pb-2">{{ __('delivery_repository') }}</div>

            <div class="my-2">
              <UserSelector @update:selected="($e)=>update({to_repo_id:$e})" :colsData="['name','phone' ]"
                            :labelsData="['name','phone' ]"
                            :callback="{ }"
                            :error="cart.errors &&   cart.errors.filter((e)=>e.type=='address').length>0?cart.errors.filter((e)=>e.type=='address')[0].message :null"
                            :link="route('admin.panel.repository.search')+(`?status=active&is_shop=1` )"
                            :label="__('repository')"
                            :id="'repository'" v-model:selected="cart.to_repo_id" :preload="cart.to_repo">
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

            </div>

            <div v-if="cart.to_address" class="bg-gray-50 p-2 border rounded  w-full">

              <div class="flex items-center py-3 text-sm">
                <MapIcon class="w-4 h-4  text-primary-600"/>
                <div class="mx-1 text-neutral-700"> {{ cart.to_address.address }}</div>
              </div>
              <div class="flex items-center py-1 ">
                <MapPinIcon class="w-4 h-4  text-primary-600"/>
                <div class="mx-1 text-neutral-700"> {{ getCityName(cart.to_address.province_id) }}</div>
                <div v-if="getCityName(cart.to_address.county_id)" class="mx-1 text-neutral-700"> -
                  {{ getCityName(cart.to_address.county_id) }}
                </div>
                <div v-if="getCityName(cart.to_address.district_id)" class="mx-1 text-neutral-700"> -
                  {{ getCityName(cart.to_address.district_id) }}
                </div>
              </div>

              <div class="flex items-center py-1">
                <PhoneIcon class="w-4 h-4 text-primary-600"/>
                <div class="mx-1 text-neutral-700"> {{ cart.to_address.to_phone }}</div>
              </div>
              <div class="flex items-center py-1">
                <HomeIcon class="w-4 h-4 text-primary-600"/>
                <div class="mx-1 text-neutral-700"> {{ cart.to_address.postal_code }}</div>
              </div>
            </div>
            <div v-show="  page=='payment' && cart.orders.length>0 && cart.need_self_receive"
                 class="text-primary-500 font-bold py-2">
              {{ __('you_selected_self_receive') }}
            </div>
          </div>
          <div v-if=" cart.orders.length==0" class="w-full p-4  items-center flex flex-col justify-center ">
            <div> {{ __('cart_is_empty') }}</div>
            <Link class="text-primary-500 hover:text-primary-400 cursor-pointer"
                  :href="route('admin.panel.repository.shop.index')"> {{
                __('shop')
              }}
            </Link>
          </div>
          <div v-for="(cart,idx) in cart.orders" class="shadow-md    rounded">
            <div class="p-2">{{ `${__('order')} ${idx + 1}` }}</div>
            <div v-for="( shipment ,id) in cart.shipments"
                 :class="{'bg-danger-100':shipment.method.error_message && page!='cart'  }"
                 class="     p-2 m-2    ">
              <div v-for="(item,idx) in shipment.items" :key="item.cart_item.variation_id"
                   class="flex p-2  flex-col my-2"
                   :class="{'bg-danger-100':item.cart_item.error_message}">
                <div class="flex items-start" v-if="item.cart_item "
                >
                  <div>
                    <Image :src="route('storage.variations')+`/${item.cart_item.variation_id}/thumb.jpg`"
                           classes="w-32 h-32 object-contain rounded  mx-1 "
                           :data-lity="route('storage.variations')+`/${item.cart_item.variation_id}/thumb.jpg`"/>
                  </div>
                  <div v-if=" item.cart_item.product "
                       class="   w-full flex-col p-2 space-y-2 items-start">
                    <div class="flex items-center justify-between">
                      <Link
                          :href=" route( 'variation.view',{id:item.cart_item.product.id,name:item.cart_item.product.name})"
                          class="cursor-pointer hover:text-primary-500">
                        {{ item.cart_item.product.name || '' }}
                      </Link>
                      <div v-if="item.cart_item.product.grade"
                           class="text-sm text-neutral-500 mx-2 ">{{
                          __('grade') + ' ' + item.cart_item.product.grade
                        }}
                      </div>
                    </div>
                    <div class="text-neutral-400 text-sm">{{ item.repo_name }}</div>
                    <div class="flex  items-center text-sm">
                      <!--                <ShoppingBagIcon class="w-5 h-5 text-neutral-500"/>-->
                      <div class="text-neutral-600 mx-1">{{ __('qty') }}:</div>
                      <div class="text-neutral-600 mx-1">{{
                          item.cart_item.qty ? parseFloat(item.cart_item.qty) : 0
                        }}
                      </div>
                      <div class="text-neutral-400"> {{ getPack(item.cart_item.product.pack_id) }}</div>
                    </div>

                    <div class="flex  items-center text-sm">
                      <!--                <ShoppingBagIcon class="w-5 h-5 text-neutral-500"/>-->
                      <div class="text-neutral-600 mx-1">{{ __('price_unit') }}:</div>
                      <div class="text-neutral-600 mx-1">{{ asPrice(item.cart_item.product.price) }}</div>
                      <TomanIcon class="w-5 h-5 text-neutral-400"/>

                    </div>
                    <div class="flex  items-center text-sm">
                      <!--                <ShoppingBagIcon class="w-5 h-5 text-neutral-500"/>-->
                      <div class="text-neutral-600 mx-1">{{ __('weight_unit') }}:</div>
                      <div class="text-neutral-600 mx-1">{{ parseFloat(item.cart_item.product.weight) }}</div>
                      <div class="text-neutral-400 mx-1">{{ __('kg') }}</div>
                      <!--                <TomanIcon class="w-5 h-5 text-neutral-500"/>-->

                    </div>
                  </div>

                </div>
                <div v-if="item.cart_item.error_message" class="text-danger-600 font-bold text-sm">
                  {{ item.cart_item.error_message }}
                </div>
                <div class="flex flex-wrap items-center justify-start my-2">
                  <CartItemButton :link="route('admin.panel.repository.cart.update')"
                                  :product-id="item.cart_item.variation_id"
                                  class="flex  min-w-[100%]   xs:min-w-[50%] sm:min-w-[36%] lg:min-w-[20%]  hover:cursor-pointer"/>
                  <div class="flex">
                    <div class="mx-2 ">{{ asPrice(item.cart_item.total_price) }}</div>
                    <div>
                      <TomanIcon class=""/>
                    </div>
                  </div>
                </div>
              </div>
              <div>
                <div class="flex  items-center text-sm  border-b p-2 py-2">
                  <div class="text-neutral-600 mx-1">{{ __('cart_total_qty') }}:</div>
                  <div class="text-neutral-800 mx-1">{{ shipment.total_items }}</div>
                </div>
                <div class="flex  items-center text-sm  p-2 py-2">
                  <div class="text-neutral-600 mx-1">{{ __('cart_total_price') }}:</div>
                  <div class="text-neutral-800 mx-1">{{ asPrice(shipment.total_items_price) }}</div>
                  <TomanIcon class="w-5 h-5 text-neutral-400"/>
                </div>

              </div>
              <!--           shipping_method-->
              <div class="border-t p-2">
                <div class="text-neutral-500">{{ __('shipping_method') }}</div>

                <div v-if="shipment.method.error_message" class="text-red-500 font-bold">
                  {{ shipment.method.error_message }}
                </div>
                <div v-else>
                  <div>{{ shipment.method.name }}
                    <span v-if="shipment.method.description" class="text-sm">
                      ({{ shipment.method.description }})</span>
                  </div>
                  <div v-if="shipment.method.address" class="text-sm my-2 text-primary-700">{{ __('address') }}: {{
                      shipment.method.address
                    }}
                  </div>
                  <div class="flex items-center text-sm">
                    <div class="text-neutral-500">{{ __('distance') }} :</div>
                    <div class="mx-2">{{ `${shipment.distance || '?'} ${__('km')}` }}</div>
                  </div>
                  <div class="flex items-center">
                    <div class="text-neutral-500">{{ __('shipping_price') }} :</div>
                    <div class="mx-2 text-neutral-800  ">{{ asPrice(shipment.total_shipping_price) }}</div>
                    <TomanIcon class=""/>
                  </div>
                </div>

                <div v-if=" shipment.has_available_shipping " class="my-4 ">
                  <TextInput
                      @change=" ($e)=>{let params={};params[`visit_repo_${shipment.repo_id}`]= shipment.visit_checked  ; update( params)}"
                      :id="`visit_repo_${shipment.repo_id}`"
                      type="checkbox"
                      :placeholder="__('visit_repo')"
                      classes=" px-0 mx-0 "
                      v-model="shipment.visit_checked"
                      :autocomplete="`visit_repo_${shipment.repo_id}`"
                  >
                  </TextInput>
                </div>
              </div>
            </div>
            <div class="flex border-t items-center justify-end font-bold text-sm  p-4 py-2">
              <div class="text-neutral-600 mx-1">{{ __('order_price') }}:</div>
              <div class="text-neutral-800 mx-1">{{ asPrice(cart.total_price) }}</div>
              <TomanIcon class="w-5 h-5 text-neutral-400"/>
            </div>
          </div>
        </section>

        <aside class="min-w-[15rem] sticky bg-neutral-100 my-2 md:my-0  p-2 rounded-lg   md:rounded-s-none ">
          <div class="flex flex-col md:my-4  ">
            <div class="flex  items-center text-sm  border-b p-4 py-2">
              <div class="text-neutral-600 mx-1">{{ __('orders_count') }}:</div>
              <div class="text-neutral-800 mx-1">{{ cart.orders.length }}</div>
            </div>
            <div class="flex  items-center text-sm  border-b p-4 py-2">
              <div class="text-neutral-600 mx-1">{{ __('cart_total_qty') }}:</div>
              <div class="text-neutral-800 mx-1">{{ cart.total_items }}</div>
            </div>
            <div class="flex  items-center text-sm  p-4 py-2">
              <div class="text-neutral-600 mx-1">{{ __('total_shipping_price') }}:</div>
              <div class="text-neutral-800 mx-1">{{ asPrice(cart.total_shipping_price) }}</div>
              <TomanIcon class="w-5 h-5 text-neutral-400"/>
            </div>
            <div class="flex  items-center text-sm  p-4 py-2">
              <div class="text-neutral-600 mx-1">{{ __('cart_total_price') }}:</div>
              <div class="text-neutral-800 mx-1">{{ asPrice(cart.total_items_price) }}</div>
              <TomanIcon class="w-5 h-5 text-neutral-400"/>
            </div>
            <div class="flex  items-center justify-start font-bold text-sm  p-4 py-2">
              <div class="text-neutral-600 mx-1">{{ __('total_price') }}:</div>
              <div class="text-neutral-800 mx-1">{{ asPrice(cart.total_price) }}</div>
              <TomanIcon class="w-5 h-5 text-neutral-400"/>
            </div>


            <PrimaryButton :class="{'opacity-50 disabled':cart.orders.length==0}"
                           @click="handleNextButtonClick"
                           classes="" class="my-2">
            <span v-if="!loading">   {{
                __('reg_order')
              }}</span>
              <LoadingIcon v-else class="fill-white w-8 mx-auto" ref="loader" type="line-dot"/>
            </PrimaryButton>
          </div>
        </aside>

      </div>

      <LoadingIcon v-show="loading" ref="loader" type="linear"/>
    </template>
  </Panel>

</template>

<script>
import UserSelector from "@/Components/UserSelector.vue";
import AddressSelector from "@/Components/AddressSelector.vue";
import LoadingIcon from "@/Components/LoadingIcon.vue";
import TomanIcon from "@/Components/TomanIcon.vue";
import Image from "@/Components/Image.vue";
import Scaffold from "@/Layouts/Scaffold.vue";
import Panel from "@/Layouts/Panel.vue";
import {Head, Link} from '@inertiajs/vue3';
import heroImage from '@/../images/hero.jpg';
import {loadScript} from "vue-plugin-load-script";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import {
  EyeIcon,
  MapPinIcon,
  ShoppingBagIcon,
  XMarkIcon,
  MapIcon,
  PhoneIcon,
  HomeIcon,
} from "@heroicons/vue/24/outline";
import {
  PencilIcon,
  ArrowTrendingUpIcon,
} from "@heroicons/vue/24/solid";
import SearchInput from "@/Components/SearchInput.vue";
import LocationSelector from "@/Components/LocationSelector.vue";
import {Swiper, SwiperSlide} from 'swiper/vue';
import {Navigation, Pagination, Scrollbar, A11y} from 'swiper/modules';
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';
import 'swiper/css/scrollbar';
import CartItemButton from "@/Components/CartItemButton.vue";
import TextInput from "@/Components/TextInput.vue";
import {Dropdown, initTE, Modal} from "tw-elements";
import Timestamp from "@/Components/Timestamp.vue";

export default {
  data() {
    return {
      page: 'cart',
      cart: null,
      loading: false,
      modules: [Navigation, Pagination, Scrollbar, A11y],
    }
  },
  props: ['heroText'],
  components: {
    Timestamp,
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
    ShoppingBagIcon,
    AddressSelector,
    Panel,
    XMarkIcon,
    UserSelector,
    MapIcon,
    PhoneIcon,
    HomeIcon,
    TextInput,
  },
  // mixins: [Mixin],
  setup(props) {

  }, mounted() {
    // this.setScroll(this.$refs.loader.$el);
    if (route().current('checkout.shipping'))
      this.page = 'shipping';
    else if (route().current('checkout.payment'))
      this.page = 'payment';

    // this.getCart();
    this.update();
    this.emitter.on('updateCart', (cart) => {
      this.cart = cart;
    });

  },
  methods: {
    handleNextButtonClick() {
      if (this.loading) return;
      this.showDialog('success', this.__('order_will_process_and_send_pay_link'), this.__('accept'), this.update, {
        cmnd: 'create_order_and_process'
      })
      // this.update();


    },
    update(params = {}) {
      this.isLoading(true);
      params.current = `checkout.${this.page}`;
      this.loading = true;
      window.axios.patch(route('admin.panel.repository.cart.update'), params,
          {})
          .then((response) => {
            if (response.data && response.data.message && params.length > 0) {
              this.showToast('success', response.data.message);
            }

            if (response.data.cart) {
              this.updateCart(response.data.cart);
              this.cart = response.data.cart;
            }
            if (this.cart.errors && this.cart.errors.length > 0)
              this.showToast('danger', this.__('please_correct_errors'));
            else if (response.data.status && response.data.status == 'success') {

              window.location = response.data.url;
              this.showToast('success', response.data.message);

            }
          })

          .catch((error) => {
            // this.log(error)
            this.error = this.getErrors(error);
            if (error.response && error.response.data) {
              if (error.response.data.cart) {
                this.updateCart(error.response.data.cart);
                this.cart = error.response.data.cart;
              }
            }
            this.showToast('danger', this.error);

          })
          .finally(() => {
            // always executed
            this.loading = false;
            this.isLoading(false);
          });
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