<template>
  <div class="relative mx-1" data-te-dropdown-ref @click="resetNotifications">
    <button
        class="flex p-2 items-center whitespace-nowrap rounded bg-primary  rounded-full text-primary-500 bg-primary-50 hover:text-primary hover:bg-primary-100    text-xs font-medium   leading-normal     transition duration-150 ease-in-out   hover:shadow-lg focus:bg-primary-200 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-primary-200 active:shadow-lg motion-reduce:transition-none dark:shadow-lg dark:hover:shadow-lg dark:focus:shadow-lg"
        type="button"
        id="dropdownNotidicationSetting"
        data-te-dropdown-toggle-ref
        aria-expanded="false"
        data-te-ripple-init
        data-te-ripple-color="light">
      <span class="sr-only">Open notifications panel</span>
      <span v-if=" badge>0"
            class="bg-red-500 rounded-full text-white px-[.3rem] absolute top-0 start-0  ">
                                    {{ badge }}
                                </span>
      <BellAlertIcon class="w-7 h-7"/>
    </button>
    <ul
        class="absolute z-[99000]   p-1 start-[1rem] end-[1rem]  hidden max-h-[80vh] overflow-x-hidden overflow-y-scroll  w-[90vw]    sm:w-[20rem] list-none   rounded-lg border-none bg-white bg-clip-padding text-start text-base shadow-lg dark:bg-neutral-700 [&[data-te-dropdown-show]]:block"
        aria-labelledby="dropdownNotidicationSetting"
        data-te-dropdown-menu-ref>
      <li class="  p-2 text-primary text-center bg-primary-100 rounded hover:bg-primary hover:text-white cursor-pointer  ">

        <Link :href="route(`panel.notification.index`)">{{ __('see_all') }}</Link>

      </li>
      <template v-if="!error">
        <li v-for="(d,idx) in data">
          <Link :class="idx>0? 'border-t':''"
                class="block w-full  bg-transparent px-4 py-2 text-sm font-normal text-neutral-700 hover:bg-neutral-100 active:text-neutral-800 active:no-underline disabled:pointer-events-none disabled:bg-transparent disabled:text-neutral-400 dark:text-neutral-200 dark:hover:bg-neutral-600"
                :href="createLink(d)"
                data-te-dropdown-item-ref
          >
            <div class="flex flex-col">
              <div class="flex justify-between">
                <div class="text-gray-500 my-1">{{ d.subject }}</div>
                <div class="text-gray-300 my-1 text-xs">{{ toShamsi(d.created_at) }}</div>
              </div>
              <div class="text-gray-300 text-sm m-1" v-html="getHTML(d.description)"></div>
            </div>
          </Link
          >
        </li>
      </template>
      <li v-else class="flex justify-center p-4 text-primary-500 hover:text-primary-400 text-center w-full"
      >
        <ArrowPathIcon @click.prevent="getData" class="w-10   h-10 cursor-pointer hover:scale-[110%]"/>
      </li>
    </ul>

  </div>
</template>

<script>
import {Link} from '@inertiajs/vue3';
import {
  BellAlertIcon,
  ArrowPathIcon,
} from "@heroicons/vue/24/outline";

export default {

  data() {
    return {
      error: false,
      badge: this.$page.props.auth.user.notifications,
      data: [],
    }
  },
  components: {
    Link,
    BellAlertIcon,
    ArrowPathIcon,
  },
  props: [],
  setup(props) {

  },
  mounted() {
    // this.log(this.$page.props);
    // this.getData();
  },
  computed: {},
  methods: {
    getHTML(desc) {
      try {
        desc = JSON.parse(desc);
      } catch (e) {

      }
      return this.cropText(desc, 1024)
    },
    createLink(data) {
      if (!data) return '';
      if (data.link) return data.link;
      if (data.type == "access_change")
        return route('panel.profile.edit');

      if (data.data_id && data.type)
        return route('/') + "/" + data.type.split("_")[0] + `/${data.type === 'ticket_answer' ? '' : 'edit/'}` + data.data_id;
      return route(`panel.notification.index`);
    },
    resetNotifications() {
      if (this.error)
        this.getData();
      if (this.badge && this.badge > 0)
        window.axios.patch(route('panel.notification.update'), {cmnd: 'reset'},
        )
            .then((response) => {
              if (response.data && response.data.message) {
                this.badge = 0;
              }
            })
            .catch((error) => {
            })
            .finally(() => {

            });
    },
    getData() {

      this.error = false;
      this.loading = true;
      this.data = [];
      window.axios.get(route('panel.notification.search'), {
        params: this.params
      }, {})
          .then((response) => {
            this.data = response.data.data;
            // this.log(this.data)

          })

          .catch((error) => {

            this.error = true

          })
          .finally(() => {
            // always executed
            this.loading = false;
          });
    },
  },
}

</script>
