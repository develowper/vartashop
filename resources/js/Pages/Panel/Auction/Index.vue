<template>

    <Panel>
        <template v-slot:header>
            <title>{{__('panel')}}</title>
        </template>


        <template v-slot:content>
            <!-- Content header -->
            <div
                class="flex items-center justify-between px-4 py-4 text-primary-500 border-b lg:py-6">
                <h1 class="text-2xl font-semibold">{{ __('create_business') }}</h1>
                <Link href="" target="_blank"
                      class="px-4 py-2 text-sm text-white rounded-md bg-primary hover:bg-primary-dark focus:outline-none focus:ring focus:ring-primary focus:ring-offset-1 focus:ring-offset-white">

                </Link>
            </div>

            <!-- Content -->
            <div class="mt-2">

            </div>
        </template>


    </Panel>
</template>

<script>
import Scaffold from "@/Layouts/Scaffold.vue";
import Panel from "@/Layouts/Panel.vue";
import {Head, Link} from "@inertiajs/vue3";
import {ChevronDownIcon, HomeIcon} from "@heroicons/vue/24/outline";

export default {
    components: {
        Head, Link, HomeIcon, ChevronDownIcon, Panel
    },
    mounted() {

    }

}
</script>
