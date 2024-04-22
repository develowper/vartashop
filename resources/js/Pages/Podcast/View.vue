<template>
  <Scaffold navbar-theme="light">
    <template v-slot:header>
      <title v-if="data">{{ data.name }}</title>
      <meta v-if="data" name="description" :content=" data.description ">

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
          <div class="px-4 py-2 text-white bg-primary">{{ data.name }}</div>
          <div v-if="data" class="    p-4 flex flex-col  m-4">

            <div class="grid grid-cols-1 sm:grid-cols-2 items-center my-2 ">
              <Image :src="route('storage.podcasts')+`/${data.id}.jpg`"
                     classes="object-cover rounded-none rounded-t sm:rounded-none sm:rounded-s   h-full   w-full"/>

              <Podcast classes="rounded-none rounded-b sm:rounded-none sm:rounded-e"
                       :preload="{name: data.name,artist: data.narrator,url:route('storage.podcasts')+`/${data.id}.mp3`}"
                       view="linear" mode="view"
                       ref="podcast" :label="__('podcast_file_mp3')"/>
            </div>

            <p v-if="data.owner" class="text-sm bg-gray-200 p-4 rounded flex flex-wrap">
              <span class="text-gray-500 ">{{ __('owner') }}: </span>
              <span>{{ data.owner.fullname }}</span>
              <span class="border-s mx-2">  </span>
              <span class="text-gray-500  ">{{ __('phone') }}: </span>
              <span>{{ data.owner.phone }}</span>
            </p>

            <div class="space-y-2 flex flex-col my-8   p-4">
              <p class="text-sm my-2">
                <span class="text-gray-500 ">{{ __('tags') }}: </span>
                <span>{{ data.tags }}</span>
              </p>
              <p class="text-sm  my-2">
                <span class="text-gray-500 ">{{ __('description') }}: </span>
                <span>{{ data.description }}</span>
              </p>

            </div>
          </div>


        </div>
      </div>
    </section>
    <section>
      <div class=" w-full px-3 my-8   flex  items-center justify-center">
        <PrimaryButton @click="$inertia.visit(route(($page.props.auth.user?'panel.podcast.create':'login')  ))"
                       class="mx-2 py-2  px-6  ">
          {{ __('register_podcast') }}
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
import Podcast from "@/Components/Podcast.vue";
import Image from "@/Components/Image.vue";
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
    Podcast,
  },
  created() {
    // this.isLoading(true);

  },
  mounted() {

    this.data = this.$page.props.data;


  },
  methods: {},

}

</script>