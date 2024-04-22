<template>

  <img :src="url" :alt="alt" :class="classes+(loading?'   bg-gray-300 ':' ')"
       @loadstart="loading=true" @loadeddata="loading=false" @load="loading=false;"
       @error="imageError  ">
</template>

<script>
import {UserIcon} from "@heroicons/vue/24/outline";

let self;

export default {
  data() {
    return {
      loading: true,
      retry: 1,
      url: this.src,
    }
  },
  components: {UserIcon},
  created() {

  }, mounted() {
  },
  watch: {
    loading() {

    }
  },
  props: ['type', 'src', 'alt', 'classes','failUrl'],
  methods: {
    imageError() {
      this.loading = false;
      if (this.retry < 0) return;
      this.loading = true;
      if (this.type == 'user')
        this.url = this.failUrl || "/assets/images/def-user.png";
      else
        this.url = this.failUrl || "/assets/images/noimage.png";
      this.retry--;
    }
  }
}
</script>
