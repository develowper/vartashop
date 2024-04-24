<template>
  <div class="flex items-center ">
    <div v-if=" user">

      <div class="group flex relative dropdown text-start">
        <!-- Dropdown toggle button -->
        <button
            type="button"
            id="dropdownUser"

            @click="chevronShow=!chevronShow" @mouseover="chevronRotate=true;chevronShow=true"
            @mouseleave="chevronRotate=false"
            class="relative btn z-10 flex items-center p-2 text-sm text-gray-600 bg-white border border-primary-500 rounded-md focus:border-blue-500 focus:ring-opacity-40 dark:focus:ring-opacity-40 focus:ring-blue-300 dark:focus:ring-blue-400 focus:ring dark:text-white dark:bg-gray-800 focus:outline-none">
                    <span :class=" chevronRotate?'rotate-90':'' " class="transition duration-500"><ChevronDownIcon
                        class="h-5 w-5"/> </span>
          <span class="mx-1"> {{ user.phone || user.email }}</span>
        </button>

        <!-- Dropdown menu -->
        <ul v-if="  chevronShow" @mouseover="chevronRotate=true" @mouseleave="chevronRotate=false"
            class="flex-col    bg-white  border shadow-xl rounded-lg transform scale-0 group-hover:scale-100  absolute end-0 top-10
                    transition duration-200 ease-in-out origin-top overflow-hidden   ">
          <li>
            <Link href="#"
                  class="flex px-6   py-4  justify-around      text-sm text-gray-600 transition-colors duration-200 transform dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 dark:hover:text-white">
              <Image classes=" flex-shrink-0  object-cover mx-1 rounded-full w-9 h-9"
                     :src="route(`storage.${isAdmin()?'admins':'users'}`)+`/${user.id}.jpg`"
                     alt="jane avatar"
                     type="user"/>

              <div class="flex-col  mx-1  ">
                <h1 class="    text-sm font-semibold text-gray-700 dark:text-gray-200">
                  {{ user.fullname }}</h1>
                <div class="   text-sm text-gray-500 dark:text-gray-400 ">{{ user.phone || user.email }}
                </div>
              </div>
            </Link>
          </li>
          <li>
            <hr class="border-gray-200 dark:border-gray-700  ">
          </li>
          <li>
            <Link :href="isAdmin()?route('admin.panel.index'):route('user.panel.index')"
                  class="flex px-4 py-4 text-sm text-gray-600 capitalize transition-colors duration-200 transform dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 dark:hover:text-white">
              {{ __('dashboard') }}
            </Link>
          </li>
          <li>
            <hr class="border-gray-200 dark:border-gray-700 ">
          </li>
          <li>
            <button @click="logout" class=" w-full flex ">
              <div class="flex items-center justify-center  m-3 px-4 py-2  w-full  hover:scale-110 focus:outline-none      rounded font-bold cursor-pointer
        hover:bg-red-700 hover:text-red-100  bg-red-100 text-red-500  border duration-200 ease-in-out border-red-600 transition">
                {{ __('signout') }}
                <ArrowRightOnRectangleIcon class="h-5 w-5 text-red-500  "/>
              </div>
            </button>
          </li>
        </ul>
      </div>

    </div>
    <Link v-else :href="profileLink( )"
          class="flex mx-1 btn  border    font-medium
            focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-300 ease-in-out     p-2 rounded-lg  rounded-lg hover:bg-primary-400 hover:text-white">
      <UserIcon class=" h-5 w-5"/>

    </Link>


  </div>
</template>

<script>
import {Link} from '@inertiajs/vue3';
import {UserIcon, ChevronDownIcon, ArrowRightOnRectangleIcon} from "@heroicons/vue/24/outline";
import Image from "@/Components/Image.vue";

export default {

  data() {
    return {chevronRotate: false, chevronShow: false, user: this.$page.props.auth.user}
  },
  components: {Link, UserIcon, ChevronDownIcon, Image, ArrowRightOnRectangleIcon},
  props: {},
  setup(props) {


  },
  computed: {
    selectable_locale() {
      if (this.$page.locale == 'fa') {
        return 'en';
      }
      if (this.$page.locale == 'en') {
        return 'ar';
      }
      return 'fa'
    },

  },
  watch: {},
  methods: {
    logout() {
      axios.post(route('logout')).then(() => {
        location.reload()
      })
    },
    profileLink() {
      if (this.$page.props.auth.user)
        return this.route('user.panel.index');
      else return this.route('login');
    },
  }
}

</script>
<style lang="scss" scoped>

li > ul {
  transform: translatex(100%) scale(0)
}

li:hover {
  > ul {
    transform: translatex(101%) scale(1);
  }
}

//.dropdown {
//    > button {
//        transform: rotate(0deg);
//
//        &:hover {
//            svg {
//                transition: all 500ms;
//                transform: rotate(90deg)
//            }
//        }
//    }
//
//    ul {
//        &:hover {
//            svg {
//                transition: all 500ms;
//                transform: rotate(90deg)
//            }
//        }
//    }
//
//}


/* Below styles fake what can be achieved with the tailwind config
   you need to add the group-hover variant to scale and define your custom
   min width style.
     See https://codesandbox.io/s/tailwindcss-multilevel-dropdown-y91j7?file=/index.html
     for implementation with config file
*/
//.group:hover .group-hover\:scale-100 {
//    transform: scale(1)
//}
//
//.group:hover .group-hover\:-rotate-180 {
//    transform: rotate(180deg)
//}
//
//.scale-0 {
//    transform: scale(0)
//}
//
.min-w {
  min-width: 20rem !important;
}
</style>
