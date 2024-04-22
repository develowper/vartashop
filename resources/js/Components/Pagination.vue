<template>

  <ul class="flex items-center text-gray-500   border  p-0 rounded-lg">

    <!--disabled first/previous -->

    <template v-if=" pagination.current_page===1">
      <li class="   border-gray-300 p-2  rounded-r bg-gray-100   cursor-pointer">

        <ChevronDoubleRightIcon class="w-4 h-4"/>

      </li>
      <li class="   border-gray-300 p-2      bg-gray-100    cursor-pointer bg-gray-100 ">
        <ChevronRightIcon class="w-4 h-4"/>
      </li>
    </template>
    <!--first/previous -->
    <template v-else-if=" pagination.current_page > 1">
      <li @click=" $emit('paginationChanged',{'page': 1})"
          class="   border-gray-300 p-2  rounded-r bg-gray-100  hover:bg-gray-200 cursor-pointer">
        <ChevronDoubleRightIcon class="w-4 h-4"/>
      </li>

      <li class="  border-gray-300 p-2  rounded bg-gray-100  hover:bg-gray-200 cursor-pointer"
          @click=" $emit('paginationChanged',{'page':pagination.current_page-1})">
        <ChevronRightIcon class="w-4 h-4"/>
      </li>
    </template>

    <!--links-->

    <li v-for="i in this.range(this.from,this.to)"
        class="  border-gray-300   p-1 px-2    hover:bg-gray-200 cursor-pointer "
        :class="( pagination.current_page==i ?'bg-white':'bg-gray-100')+(i==from?' border-r border-l':' border-l ') "
        @click=" $emit('paginationChanged',{'page':i})">
      {{ i }}
    </li>

    <!-- next/last -->

    <template v-if="pagination.current_page < pagination.last_page">
      <li class="border-l border-gray-300 p-2    bg-gray-100  hover:bg-gray-300 cursor-pointer"
          @click=" $emit('paginationChanged',{'page':pagination.current_page+1})">
        <ChevronLeftIcon class="w-4 h-4"/>
      </li>

      <li class="  border-gray-300 p-1 px-2 rounded-l   bg-gray-100  hover:bg-gray-300 cursor-pointer"
          @click=" $emit('paginationChanged',{'page':pagination.last_page})">
        {{ pagination.last_page }}
      </li>
    </template>
    <!--disable next/last-->
    <template v-else-if="pagination.current_page >= pagination.last_page">
      <li class="  border-gray-300 p-2    bg-gray-100  hover:bg-gray-100 cursor-pointer bg-gray-100 ">
        <ChevronLeftIcon class="w-4 h-4"/>

      </li>

      <li class="  border-gray-300 p-1 px-2  rounded-l bg-gray-100  hover:bg-gray-100 cursor-pointer bg-gray-100 ">
        {{ pagination.last_page }}

      </li>
    </template>

  </ul>

</template>

<script>
import {Link} from '@inertiajs/vue3';
import {
  ChevronDoubleLeftIcon,
  ChevronDoubleRightIcon,
  ChevronLeftIcon,
  ChevronRightIcon,

} from "@heroicons/vue/24/outline";

export default {
  components: {
    Link,
    ChevronDoubleLeftIcon,
    ChevronDoubleRightIcon,
    ChevronLeftIcon,
    ChevronRightIcon,
  },
  data() {
    return {

      interval: 3,
      from: 0,
      to: -1,
    }
  },
  props: ['pagination'],
  watch: {
    pagination(data) {
      this.setEvents();
    }
  },
  mounted() {


  },
  emits: ['paginationChanged'],
  created() {
    this.setEvents();
  },

  methods: {
    setEvents() {
//       this.$parent.$on('paginationChangedChange', (pagination) => {
//         this.pagination = pagination;
//         this.from = this.pagination.current_page - this.interval;
//         if (this.from < 1)
//           this.from = 1;
//         this.to = this.pagination.current_page + this.interval;
//         if (this.to > this.pagination.last_page)
//           this.to = this.pagination.last_page;
//
// //                    console.log(this.from, this.to);
//
//       });
      this.from = this.pagination.current_page - this.interval;
      if (this.from < 1)
        this.from = 1;
      this.to = this.pagination.current_page + this.interval;
      if (this.to > this.pagination.last_page)
        this.to = this.pagination.last_page;

      // console.log(this.pagination.current_page);
    },
    range(from, to) {
      let array = [],
          j = 0;
      for (let i = from; i <= to; i++) {
        array[j] = i;
        j++;
      }
      return array;
    }
  }
}

</script>
