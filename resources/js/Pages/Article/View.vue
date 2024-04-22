<template>
  <Scaffold navbar-theme="light">
    <template v-slot:header>
      <title v-if="data">{{ data.title }}</title>
      <meta v-if="data" name="description" :content=" data.summary ">

    </template>
    <section class="flex justify-center pt-24">
      <div
          class="w-full   rounded-lg overflow-x-hidden  max-w-4xl  xs:mx-2 md:mx-4    blur-xs opacity-75 bg-white  backdrop-filter">
        <div v-if="$page.props.error_message" class="text-center flex flex-col font-bold p-4 text-danger  text-lg">
          <div class="text-gray-900">{{ $page.props.error_message }}</div>
          <Link :href="$page.props.error_link" class="my-4">{{ __('return') }}</Link>
        </div>
        <!--      main section-->

        <div v-else-if="data" class=" flex flex-col ">
          <div
              class="px-4 py-4 text-white   bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-emerald-500 to-lime-600">
            {{ data.title }}
          </div>
          <div v-if="data" class="      flex flex-col   ">

            <div class="grid grid-cols-1   items-center    h-64 w-full   relative">
              <div class="h-[inherit]">
                <Image :src="route('storage.articles')+`/${data.id}.jpg`"
                       classes="object-cover    h-[inherit]     w-full"/>
                <div
                    class="text-sm    absolute  top-0 start-0 w-full h-full  bg-primary-500 opacity-10  backdrop-blur">
                </div>
              </div>

            </div>

            <p v-if="data.owner" class="text-sm bg-gray-200 p-4 rounded flex flex-wrap">
              <span class="text-gray-500 ">{{ __('author') }}: </span>
              <span>{{ data.author }}</span>
              <span class="border-s mx-2">  </span>
              <span class="text-gray-500  ">{{ __('phone') }}: </span>
              <span>{{ data.owner.phone }}</span>
            </p>

            <div class="space-y-2 flex flex-col my-2   px-4">
              <p class="text-sm my-2">
                <span class="text-gray-500 ">{{ __('tags') }}: </span>
                <span>{{ data.tags }}</span>
              </p>
            </div>
            <div class="border-b my-2 border-primary-200"></div>
            <div v-if="data.content" class="container self-center space-y-2 flex flex-col my-2   px-4 ">
              <div v-html="data.content"></div>
            </div>

          </div>


        </div>
      </div>
    </section>
    <section v-if="false">
      <div class=" w-full px-3 my-8   flex  items-center justify-center">
        <PrimaryButton @click="$inertia.visit(route(($page.props.auth.user?'panel.article.create':'login')  ))"
                       class="mx-2 py-2  px-6  ">
          {{ __('register_article') }}
        </PrimaryButton>
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
import {} from "@heroicons/vue/24/outline";
import {EyeIcon, CurrencyDollarIcon, UserIcon} from "@heroicons/vue/24/solid";

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