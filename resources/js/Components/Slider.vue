<template>

  <section class="   h-full ">


    <swiper class="w-full h-full "
            :modules="modules"
            :autoplay="{delay:delay}"
            :loop="true"
            :speed="1000"
            :slides-per-view="1"
            :space-between="16"
            :pagination="{ clickable: true }"
            :scrollbar="{ draggable: true }"
            @swiper=""
            @slideChange=""
    >
      <swiper-slide v-for="(item,idx) in items" class="relative     ">

        <Image classes="h-full w-full" :src="route('storage.slides')+`/${item.id}.jpg`"
               :data-lity="route('storage.slides')+`/${item.id}.jpg`"></Image>
        <div class="absolute bottom-0 text-center w-full">
          <div v-if="item.title || item.description" class=" text-white my-4 p-2 bg-primary-700 bg-opacity-40">
            <div v-if="item.title" class="">{{ item.title }}</div>
            <div v-if="item.description" class="text-xs text-primary-100">{{ item.description }}</div>
          </div>
        </div>
      </swiper-slide>


    </swiper>

  </section>
</template>
<script>
import Image from "@/Components/Image.vue";

import {Alert, initTE,} from "tw-elements";
import {Swiper, SwiperSlide} from 'swiper/vue';
import {Navigation, Pagination, Scrollbar, A11y, Autoplay} from 'swiper/modules';
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';
import 'swiper/css/scrollbar';
import {
  PlayIcon,

} from "@heroicons/vue/24/solid";

export default {
  data() {
    return {
      modules: [Navigation, Pagination, /*Scrollbar,*/ A11y, Autoplay],
    }
  },
  methods: {},
  props: ['delay', 'items'],
  components: {
    Image,
    Swiper,
    SwiperSlide,

  },
  mounted() {


  }
}
</script>
