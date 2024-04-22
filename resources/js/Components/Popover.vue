<template>
<span class="cursor-pointer"
      data-te-toggle="popover"
      data-te-placement="top"
      data-te-ripple-init
      data-te-ripple-color="light"
      :data-te-title="content? title:null"
      :data-te-content="content || title"
>
    <QuestionMarkCircleIcon class="text-gray-500 hover:bg-gray-50 w-4 h-4"/>
    <slot></slot>

</span>
</template>
<script>
import {QuestionMarkCircleIcon} from "@heroicons/vue/24/solid";
import {initTE, Ripple, Popover} from "tw-elements";


export default {
    data() {
        return {}
    },
    props: ['title', 'content'],
    components: {
        QuestionMarkCircleIcon,
    },
    mounted() {
        // initTE({Ripple, Popover});
    }
}
</script>
