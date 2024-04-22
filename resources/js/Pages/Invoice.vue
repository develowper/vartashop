<template>


  <div v-if="lang" class="container max-w-2xl mx-auto   ">

    <div class="flex flex-col shadow-md rounded-lg  " :dir="dir()">
      <div class=" ">
        <div class="   my-5 bg-gray-50">
          <div
              class="text-white text-center rounded-t-lg  py-2" :class="`bg-${status}`">
            {{ lang.title }}

          </div>
          <div class="p-3 flex flex-col items-stretch   text-sm">

            <div class="flex justify-center items-center py-2 border-b">
              <div>{{ message }}</div>
            </div>
            <div class="flex justify-center items-center py-2 border-b">
              <div>{{ lang.pay_time }}:</div>
              <div class="text-blue-500 mx-2">{{ now }}</div>
            </div>
            <div class="flex justify-center items-center py-2 border-b">
              <div>{{ lang.pay_type }}:</div>
              <div class="text-blue-500 mx-2">{{ type }}</div>
            </div>
            <div class="flex justify-center items-center py-2 border-b">
              <div>{{ lang.amount }}:</div>
              <div class="text-blue-500 mx-2">{{ `${amount} ${lang.currency}` }}</div>
            </div>
            <div class="flex justify-center items-center py-2 border-b">
              <div>{{ lang.pay_id }}:</div>
              <div class="text-blue-500 mx-2">{{ pay_id }}</div>
            </div>
            <div>
              <a :href="link" class=" p-2 text-white rounded flex justify-center cursor-pointer  "
                 :class="`bg-${status}-500 hover:bg-${status}-400`">
                {{ lang.return }}
              </a>
            </div>


          </div>

          <div class="  text-primary">
            <form method="POST">


              <div class="form-group    row">
                <div class="col-12 my-2  ">
                  <a :href="link" type="submit"
                     class="d-block btn btn-primary text-white   btn-block py-3    font-weight-bold ">
                    {{ lang.return_app }}
                  </a>

                </div>

              </div>


            </form>
          </div>
        </div>
      </div>
    </div>
  </div>


</template>

<script>
import Scaffold from "@/Layouts/Scaffold.vue";
import {Head, Link, usePage} from '@inertiajs/vue3';
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import {} from "@heroicons/vue/24/solid";
import {} from "@heroicons/vue/24/outline";
import {Dropdown, initTE, Modal} from "tw-elements";

export default {
  data() {
    return {
      lang: this.$page.props.lang,
      now: this.$page.props.now,
      status: this.$page.props.status,
      pay_id: this.$page.props.pay_id,
      amount: this.$page.props.amount,
      type: this.$page.props.type,
      link: this.$page.props.link,
      message: this.$page.props.message,

    }
  },
  props: [],
  components: {
    SecondaryButton, PrimaryButton, Scaffold, Head,

  },
  // mixins: [Mixin],
  setup(props) {

  },
  mounted() {


  },
  methods: {},

}
</script>

