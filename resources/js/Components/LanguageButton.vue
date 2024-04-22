<template>
    <div class="">
        <Link :href="route('language', [selectable_locale])"
              class="flex mx-1   border-2 text-primary-500 bg-primary-100 hover:bg-primary-200
            border-transparent text-sm font-medium leading-5
            focus:outline-none focus:text-gray-700 focus:border-
            gray-300 transition duration-300 ease-in-out border-primary-500 p-2 rounded-lg">
            <div v-if="selectable_locale == 'fa'" class="">Fa</div>
            <div v-else-if="selectable_locale == 'ar'">AR</div>
            <div v-else>EN</div>
        </Link>
    </div>
</template>

<script>
import {Link} from '@inertiajs/vue3';

export default {


    components: {Link},
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
}

</script>
