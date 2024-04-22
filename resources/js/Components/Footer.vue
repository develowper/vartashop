<template>
  <!-- Footer container -->
  <footer :dir="dir()"
          class="text-primary-700 bg-gradient-to-b from-primary-100 via-primary-200 to-primary-300 text-center text-neutral-600 dark:bg-neutral-600 dark:text-neutral-200 lg:text-left">
    <div
        class="mx-4 flex items-center justify-center border-b-2 border-primary-200 p-4 dark:border-neutral-500 lg:justify-between">


    </div>

    <div class="mx-6 py-10 text-start text-primary-700">
      <div class="grid-cols-1 grid gap-2 md:grid-cols-2 lg:grid-cols-4 items-center  ">

        <!-- About Us section -->
        <div class="  col-span-3 lg:col-span-1 text-start">
          <!-- Social network icons container -->
          <div class="flex justify-center items-center   w-full p-4">

            <a v-if="$page.props.socials.whatsapp" target="_blank" :href="$page.props.socials.whatsapp"
               class="mx-2 text-primary-700 dark:text-neutral-200">
              <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5  "
                  fill="currentColor"
                  viewBox="0 0 24 24">
                <path
                    d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
              </svg>
            </a>

            <a v-if="$page.props.socials.telegram" target="_blank" :href="$page.props.socials.telegram"
               class="mx-2 text-primary-700 dark:text-neutral-200">
              <svg
                  class="h-5 w-5"
                  fill="currentColor"
                  viewbox="0 0 24 24"
                  version="1.1"
                  xmlns="http://www.w3.org/2000/svg"
                  xmlns:xlink="http://www.w3.org/1999/xlink"
                  xml:space="preserve"
                  xmlns:serif="http://www.serif.com/"
                  style="fill-rule:evenodd;clip-rule:evenodd;stroke-linejoin:round;stroke-miterlimit:1.41421;">
          <path
              id="telegram-1"
              d="M18.384,22.779c0.322,0.228 0.737,0.285 1.107,0.145c0.37,-0.141 0.642,-0.457 0.724,-0.84c0.869,-4.084 2.977,-14.421 3.768,-18.136c0.06,-0.28 -0.04,-0.571 -0.26,-0.758c-0.22,-0.187 -0.525,-0.241 -0.797,-0.14c-4.193,1.552 -17.106,6.397 -22.384,8.35c-0.335,0.124 -0.553,0.446 -0.542,0.799c0.012,0.354 0.25,0.661 0.593,0.764c2.367,0.708 5.474,1.693 5.474,1.693c0,0 1.452,4.385 2.209,6.615c0.095,0.28 0.314,0.5 0.603,0.576c0.288,0.075 0.596,-0.004 0.811,-0.207c1.216,-1.148 3.096,-2.923 3.096,-2.923c0,0 3.572,2.619 5.598,4.062Zm-11.01,-8.677l1.679,5.538l0.373,-3.507c0,0 6.487,-5.851 10.185,-9.186c0.108,-0.098 0.123,-0.262 0.033,-0.377c-0.089,-0.115 -0.253,-0.142 -0.376,-0.064c-4.286,2.737 -11.894,7.596 -11.894,7.596Z">

          </path>
        </svg>
            </a>

          </div>
          <p>
            {{ __('footer_what_is') }}
          </p>
        </div>

        <!-- Useful links section -->
        <div class="hidden  md:flex   flex-col items-start text-start font-light">
          <h6
              class="mb-4 flex justify-center font-semibold uppercase md:justify-start">
            {{ __('fast_access') }}
          </h6>


          <p class="mb-4 hover:text-primary-900">
            <a :href="route('article.index')" class=""> {{ __('articles') }}</a>

          </p>
          <p class="hover:text-primary-900">
            <a :href="route('user.panel.index')" class=""> {{ __('panel') }}</a>

          </p>
        </div>

        <!-- Contact section -->
        <div>
          <h6
              class="mb-4 flex justify-center font-semibold uppercase md:justify-start">
            {{ __('contact') }}
          </h6>
          <p class="mb-4 flex items-center justify-center md:justify-start">
            <svg
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 24 24"
                fill="currentColor"
                class="mr-3 h-5 w-5">
              <path
                  d="M11.47 3.84a.75.75 0 011.06 0l8.69 8.69a.75.75 0 101.06-1.06l-8.689-8.69a2.25 2.25 0 00-3.182 0l-8.69 8.69a.75.75 0 001.061 1.06l8.69-8.69z"/>
              <path
                  d="M12 5.432l8.159 8.159c.03.03.06.058.091.086v6.198c0 1.035-.84 1.875-1.875 1.875H15a.75.75 0 01-.75-.75v-4.5a.75.75 0 00-.75-.75h-3a.75.75 0 00-.75.75V21a.75.75 0 01-.75.75H5.625a1.875 1.875 0 01-1.875-1.875v-6.198a2.29 2.29 0 00.091-.086L12 5.43z"/>
            </svg>
            <span class="mx-2">  {{ $page.props.socials.address }}</span>
          </p>
          <p class="mb-4 flex items-center justify-center md:justify-start">
            <svg
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 24 24"
                fill="currentColor"
                class="mr-3 h-5 w-5">
              <path
                  d="M1.5 8.67v8.58a3 3 0 003 3h15a3 3 0 003-3V8.67l-8.928 5.493a3 3 0 01-3.144 0L1.5 8.67z"/>
              <path
                  d="M22.5 6.908V6.75a3 3 0 00-3-3h-15a3 3 0 00-3 3v.158l9.714 5.978a1.5 1.5 0 001.572 0L22.5 6.908z"/>
            </svg>
            <span class="mx-2">  {{ $page.props.socials.email }}</span>
          </p>
          <p class="mb-4 flex items-center justify-center md:justify-start">
            <svg
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 24 24"
                fill="currentColor"
                class="mr-3 h-5 w-5">
              <path
                  fill-rule="evenodd"
                  d="M1.5 4.5a3 3 0 013-3h1.372c.86 0 1.61.586 1.819 1.42l1.105 4.423a1.875 1.875 0 01-.694 1.955l-1.293.97c-.135.101-.164.249-.126.352a11.285 11.285 0 006.697 6.697c.103.038.25.009.352-.126l.97-1.293a1.875 1.875 0 011.955-.694l4.423 1.105c.834.209 1.42.959 1.42 1.82V19.5a3 3 0 01-3 3h-2.25C8.552 22.5 1.5 15.448 1.5 6.75V4.5z"
                  clip-rule="evenodd"/>
            </svg>
            <span class="mx-2">  {{ $page.props.socials.phone }}</span>
          </p>
          <p v-if="false" class="flex items-center justify-center md:justify-start">
            <svg
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 24 24"
                fill="currentColor"
                class="mr-3 h-5 w-5">
              <path
                  fill-rule="evenodd"
                  d="M7.875 1.5C6.839 1.5 6 2.34 6 3.375v2.99c-.426.053-.851.11-1.274.174-1.454.218-2.476 1.483-2.476 2.917v6.294a3 3 0 003 3h.27l-.155 1.705A1.875 1.875 0 007.232 22.5h9.536a1.875 1.875 0 001.867-2.045l-.155-1.705h.27a3 3 0 003-3V9.456c0-1.434-1.022-2.7-2.476-2.917A48.716 48.716 0 0018 6.366V3.375c0-1.036-.84-1.875-1.875-1.875h-8.25zM16.5 6.205v-2.83A.375.375 0 0016.125 3h-8.25a.375.375 0 00-.375.375v2.83a49.353 49.353 0 019 0zm-.217 8.265c.178.018.317.16.333.337l.526 5.784a.375.375 0 01-.374.409H7.232a.375.375 0 01-.374-.409l.526-5.784a.373.373 0 01.333-.337 41.741 41.741 0 018.566 0zm.967-3.97a.75.75 0 01.75-.75h.008a.75.75 0 01.75.75v.008a.75.75 0 01-.75.75H18a.75.75 0 01-.75-.75V10.5zM15 9.75a.75.75 0 00-.75.75v.008c0 .414.336.75.75.75h.008a.75.75 0 00.75-.75V10.5a.75.75 0 00-.75-.75H15z"
                  clip-rule="evenodd"/>
            </svg>
            + 01 234 567 89
          </p>
        </div>
        <div class="h-full ">
          <a referrerpolicy='origin' target='_blank' class="block"
             href='https://trustseal.enamad.ir/?id=476882&Code=nUwdZBbfdvIy7sEf5ss1RusrrQT32c8H'>
            <img
                referrerpolicy='origin'
                src='https://trustseal.enamad.ir/logo.aspx?id=476882&Code=nUwdZBbfdvIy7sEf5ss1RusrrQT32c8H' alt=''
                style='cursor:pointer' Code='nUwdZBbfdvIy7sEf5ss1RusrrQT32c8H'></a>
        </div>

      </div>
    </div>

    <!--Copyright section-->
    <div class="bg-primary-300 p-4 text-center dark:bg-primary-300 text-primary-700">
      <span>© {{ new Date().getFullYear() }}</span>
      <a
          class="font-semibold   dark:text-neutral-400"
          :href="route('/')"
      >{{ __('copyright') }}</a
      >
    </div>
  </footer>
</template>

<script setup>

import {defineComponent, ref, onMounted} from "vue";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import {Link} from "@inertiajs/vue3"
import telegram from "@/../images/social/telegram.png";
import whatsapp from "@/../images/social/whatsapp.png";


// const telegram = ref(null);
onMounted(() => {

});
defineComponent({
  components: {
    ApplicationLogo, Link,
    telegram,
    whatsapp,
  }
})
</script>
