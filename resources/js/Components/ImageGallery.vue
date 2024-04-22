<template>
  <div class=" flex flex-col    " style="z-index: 2; ">
    <div
        style="--swiper-navigation-color: #ccc; --swiper-pagination-color: #ccc"
        class="swiper mySwiper2 bg-gray-200  rounded"
    >
      <div class="swiper-wrapper  ">

        <a v-for="(img,idx) in images" :href="img" class="swiper-slide " data-lity>
          <Image :src="img" class="  " classes=" "/>
        </a>

      </div>
      <div class="swiper-button-next"></div>
      <div class="swiper-button-prev"></div>
    </div>
    <div thumbsSlider="" class="swiper mySwiper py-2 my-1 bg-gray-200  rounded  ">
      <div class="swiper-wrapper  px-2 ">

        <div v-for="(img,idx) in images " class="swiper-slide bg-gray-200   ">
          <Image :src="img" class="  cursor-pointer  hover:scale-[101%] duration-300"
                 classes="overflow-hidden rounded"/>
        </div>

      </div>
    </div>
  </div>
</template>

<script>
import Image from "@/Components/Image.vue";
import Swiper from 'swiper';
import {Navigation, Pagination, A11y, Thumbs,} from 'swiper/modules';
// Swiper.use([Navigation, /*SwiperSlide, Scrollbar, A11y, Pagination,*/ Thumbs,]);
// Import Swiper styles
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';
import 'swiper/css/scrollbar';

export default {
  name: "ImageGallery",
  props: ['images'],
  components: {
    Image,
  },
  mounted() {
    this.initGallery();
  },
  methods: {
    initGallery() {
      let swiper = new Swiper('.mySwiper', {
        modules: [Navigation, Pagination, A11y, Thumbs,],
        spaceBetween: 10,
        slidesPerView: 4,
        freeMode: true,
        watchSlidesProgress: false,
      });

      let swiper2 = new Swiper('.mySwiper2', {
        modules: [Navigation, Pagination, A11y, Thumbs,],
        spaceBetween: 10,
        watchSlidesProgress: true,
        navigation: {
          nextEl: ".swiper-button-next",
          prevEl: ".swiper-button-prev",
        },
        thumbs: {
          swiper: swiper,
        },
      });
    }
  },
}
</script>

<style scoped>

.accordion-button::after {
  background-color: white !important;
  border-radius: 100%;
  font-weight: bold;
  margin-right: auto;
}

.swiper {
  width: 100%;
  height: 100%;
  z-index: 2;
}

.swiper-slide {
  text-align: center;
  font-size: 18px;
  background: #fff;

  /* Center slide text vertically */
  display: -webkit-box;
  display: -ms-flexbox;
  display: -webkit-flex;
  display: flex;
  -webkit-box-pack: center;
  -ms-flex-pack: center;
  -webkit-justify-content: center;
  justify-content: center;
  -webkit-box-align: center;
  -ms-flex-align: center;
  -webkit-align-items: center;
  align-items: center;
}

.swiper-slide img {
  display: block;
  width: 100%;
  height: 100% !important;
  object-fit: fill;
}

.swiper {
  width: 100%;

  margin-left: auto;
  margin-right: auto;
}

.swiper-slide {
  background-size: cover;
  background-position: center;
  height: 100% !important;
}

.mySwiper2 {

  height: 18rem;
  width: 100%;
  z-index: 2;

}

.mySwiper {
  height: 20%;
  box-sizing: border-box;
  padding: 10px 0;
}

.mySwiper .swiper-slide {
  width: 25%;
  height: 100%;
  opacity: 0.9;

}

.mySwiper .swiper-slide-thumb-active {
  opacity: 1;
}

.swiper-slide img {
  display: block;
  width: 100%;
  height: 100%;
  object-fit: contain;

}

</style>