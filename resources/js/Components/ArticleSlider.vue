<template>

  <section class="   h-full ">


    <swiper class="w-full h-full "
            :modules="modules"
            :autoplay="{delay:delay}"

            :loop="true"
            :speed="1000"
            :slides-per-view="'auto'"
            :space-between="16"
            :pagination="{ clickable: true }"
            :scrollbar="{ draggable: true }"
            @swiper=""
            @slideChange=""
    >
      <swiper-slide v-for="(item,idx) in items" class="relative  max-w-[18rem]   ">
        <Link :href="route('article',item.id)" class="group  ">
          <Image classes="h-full w-full object-cover"
                 :src="route('storage.articles')+`/${item.id}.jpg`"
                 :data-lity="route('storage.slides')+`/${item.id}.jpg`"></Image>
          <div
              class="absolute bottom-0  text-center h-24 group-hover:h-full transition-height duration-300 w-full bg-opacity-40 bg-primary-700">
            <div v-if="item.title || item.description" class=" text-white my-4 p-2  ">
              <div v-if="item.title" class="">{{ item.title }}</div>
            </div>
          </div>
        </Link>
      </swiper-slide>

    </swiper>

  </section>
</template>
<script>
import Image from "@/Components/Image.vue";
import {Link} from '@inertiajs/vue3';
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
    Link,

  },
  mounted() {


  }
}
</script>
