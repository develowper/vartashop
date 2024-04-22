<template>
  <Scaffold navbar-theme="dark">
    <template v-slot:header>
      <title v-if="data">{{ data.title }}</title>
      <meta v-if="data" name="description" :content=" data.seo ">

    </template>
    <div
        class="  py-8  shadow-md bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-teal-500 to-teal-500">

    </div>
    <section class=" container  max-w-2xl  mx-auto justify-center pt-24  ">
      <div
          class="w-full   rounded-lg overflow-x-hidden     xs:mx-2 md:mx-4    blur-xs   bg-white   ">
        <div v-if="!data" class="text-center flex flex-col font-bold p-4 text-danger  text-lg">
          <div class="text-gray-900">{{ __('no_result') }}</div>
          <Link :href="$page.props.back_link" class="my-4">{{ __('return') }}</Link>
        </div>
        <!--      main section-->

        <div v-else class="flex flex-col     ">

          <div class="flex-col md:flex-row md:flex  ">

            <div class=" max-w-sm     mx-auto   ">
              <Image :src="route('storage.variations')+`/${data.id}/thumb.jpg`"
                     :data-lity="route('storage.variations')+`/${data.id}/1.jpg`"
                     classes="object-contain rounded-lg cursor-pointer   "/>

            </div>
            <div class="grow max-w-sm mx-auto">
              <div class="     bg-neutral-50 rounded-lg p-2 text-neutral-700">
                <div class="w-full   my-1   ">
                  <CartItemButton :key="data.id" class="w-full " :product-id="data.id"/>
                </div>
                <div class="flex items-center justify-between">
                  <div class="text-primary-600 ms-1 font-bold ">{{ data.name }}</div>
                  <div class="text-sm text-neutral-500 mx-2 ">{{ __('grade') + ' ' + data.grade }}</div>

                </div>
                <div v-if="data.repository" class="bg-neutral-100 flex justify-end items-center p-2 text-sm">
                  <div>{{ data.repository.name }}</div>
                </div>
                <div class="p-4   w-full flex flex-col items-stretch justify-start items-start items-between">

                  <hr class="border-gray-200  m-2">
                  <div class="flex items-center text-sm">

                    <div class="flex items-center text-sm ">
                      <div class="">{{ __('in_stock') + ' : ' }}</div>
                      <div class="  text-neutral-700 font-bold mx-1 ">{{ parseFloat(data.in_shop || 0) }}</div>
                      <div class="text-sm text-neutral-500   "> {{ ` ${getPack(data.pack_id)} ` }}</div>

                    </div>
                  </div>
                  <div class="flex items-center text-sm ">
                    <div class="">{{ __('weight') + ' : ' }}</div>
                    <div class="  text-neutral-700 font-bold mx-1 ">{{ parseFloat(data.weight) }}</div>
                    <div class="text-sm text-neutral-500   ">{{ __('kg') }}</div>

                  </div>
                  <div class="flex items-center justify-end text-neutral-900 font-bold">
                    <div class="flex items-center "
                         :class="{'line-through  ':$page.props.is_auction && data.in_auction}">
                      {{ asPrice(data.price) }}

                      <svg v-if="$page.props.is_auction && data.in_auction" xmlns="http://www.w3.org/2000/svg"
                           viewBox="0 0 14 14"
                           class="fill-gray-500 h-5 w-5">
                        <path fill-rule="evenodd"
                              d="M3.057 1.742L3.821 1l.78.75-.776.741-.768-.749zm3.23 2.48c0 .622-.16 1.111-.478 1.467-.201.221-.462.39-.783.505a3.251 3.251 0 01-1.083.163h-.555c-.421 0-.801-.074-1.139-.223a2.045 2.045 0 01-.9-.738A2.238 2.238 0 011 4.148c0-.059.001-.117.004-.176.03-.55.204-1.158.525-1.827l1.095.484c-.257.532-.397 1-.419 1.403-.002.04-.004.08-.004.12 0 .252.055.458.166.618a.887.887 0 00.5.354c.085.028.178.048.278.06.079.01.16.014.243.014h.555c.458 0 .769-.081.933-.244.14-.139.21-.383.21-.731V2.02h1.2v2.202zm5.433 3.184l-.72-.7.709-.706.735.707-.724.7zm-2.856.308c.542 0 .973.19 1.293.569.297.346.445.777.445 1.293v.364h.18v-.004h.41c.221 0 .377-.028.467-.084.093-.055.14-.14.14-.258v-.069c.004-.243.017-1.044 0-1.115L13 8.05v1.574a1.4 1.4 0 01-.287.863c-.306.405-.804.607-1.495.607h-.627c-.061.733-.434 1.257-1.117 1.573-.267.122-.58.21-.937.265a5.845 5.845 0 01-.914.067v-1.159c.612 0 1.072-.082 1.38-.247.25-.132.376-.298.376-.499h-.515c-.436 0-.807-.113-1.113-.339-.367-.273-.55-.667-.55-1.18 0-.488.122-.901.367-1.24.296-.415.728-.622 1.296-.622zm.533 2.226v-.364c0-.217-.048-.389-.143-.516a.464.464 0 00-.39-.187.478.478 0 00-.396.187.705.705 0 00-.136.449.65.65 0 00.003.067c.008.125.066.22.177.283.093.054.21.08.352.08h.533zM9.5 6.707l.72.7.724-.7L10.209 6l-.709.707zm-6.694 4.888h.03c.433-.01.745-.106.937-.29.024.012.065.035.12.068l.074.039.081.042c.135.073.261.133.379.18.345.146.67.22.977.22a1.216 1.216 0 00.87-.34c.3-.285.449-.714.449-1.286a2.19 2.19 0 00-.335-1.145c-.299-.457-.732-.685-1.3-.685-.502 0-.916.192-1.242.575-.113.132-.21.284-.294.456-.032.062-.06.125-.084.191a.504.504 0 00-.03.078 1.67 1.67 0 00-.022.06c-.103.309-.171.485-.205.53-.072.09-.214.14-.427.147-.123-.005-.209-.03-.256-.076-.057-.054-.085-.153-.085-.297V7l-1.201-.5v3.562c0 .261.048.496.143.703.071.158.168.296.29.413.123.118.266.211.43.28.198.084.42.13.665.136v.001h.036zm2.752-1.014a.778.778 0 00.044-.353.868.868 0 00-.165-.47c-.1-.134-.217-.201-.35-.201-.18 0-.33.103-.447.31-.042.071-.08.158-.114.262a2.434 2.434 0 00-.04.12l-.015.053-.015.046c.142.118.323.216.544.293.18.062.325.092.433.092.044 0 .086-.05.125-.152z"
                              clip-rule="evenodd"></path>
                      </svg>


                    </div>
                    <div v-if=" data.in_auction==true" class="flex items-center ">
                      <ArrowTrendingUpIcon class="  rotate-180 text-neutral-500 mx-2"/>
                      <span> {{ asPrice(data.auction_price) }}</span>

                    </div>
                    <TomanIcon class="w-4 h-4 mx-2"/>
                  </div>
                </div>
              </div>
            </div>

          </div>
          <div v-html="data.description"
               class="   p-2 md:p-4  bg-gray-50 rounded-lg  ">

          </div>


        </div>
      </div>
    </section>


  </Scaffold>
</template>

<script>

import Scaffold from "@/Layouts/Scaffold.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import {Head, Link} from "@inertiajs/vue3";
import LoadingIcon from "@/Components/LoadingIcon.vue";
import Image from "@/Components/Image.vue";
import Article from "@/Components/Article.vue";
import TomanIcon from "@/Components/TomanIcon.vue";
import {ChatBubbleLeftEllipsisIcon} from "@heroicons/vue/24/outline";
import {
  EyeIcon,
  CurrencyDollarIcon,
  UserIcon,
  ArrowTrendingUpIcon,

} from "@heroicons/vue/24/solid";
import CartItemButton from "@/Components/CartItemButton.vue";

export default {
  data() {
    return {
      html: null,
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
    CartItemButton,
    Scaffold,
    Image,
    Link,
    EyeIcon,
    CurrencyDollarIcon,
    LoadingIcon,
    PrimaryButton,
    SecondaryButton,
    UserIcon,
    Article,
    ArrowTrendingUpIcon,
    TomanIcon,
    ChatBubbleLeftEllipsisIcon,
  },
  created() {
    // this.isLoading(true);

  },
  mounted() {

    this.data = this.$page.props.data;

    this.increaseView(this.data.id);
  },
  methods: {
    increaseView(id) {
      window.axios.post(route('article.view'), {id: id},);


    },
  },

}

</script>