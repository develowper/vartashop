<template>
  <Scaffold navbar-theme="light">

    <template v-slot:header>
      <title>{{__('payment_info')}}</title>
    </template>
    <div class="mt-24">
      <div v-if="payment" class="container position-relative ">

        <div class=" rounded-lg shadow-lg bg-white  m-4 max-w-md mx-auto">
          <div class=" text-center text-white p-4 overflow-hidden rounded-t-lg " :class="`bg-${status}`">
            {{ message }}
          </div>
          <div class="p-4 ">
            <div class="card my-5 bg-light">


              <div class="card-body text-primary">
                <form method="POST">


                  <div class="form-group  ">
                    <span class="col-12 col-form-label text-md-right">{{ __('pay_time') }}</span>

                    <div class="col-12">
                      <div class="text-center font-weight-bold">{{ toShamsi(time, true) }}</div>
                    </div>

                    <hr class="my-1"/>
                  </div>
                  <div class="form-group  ">
                    <span class="col-12 col-form-label text-md-right">{{ __('product_type') }}</span>

                    <div class="col-12">
                      <div class="text-center font-weight-bold">{{ title }}</div>
                    </div>

                    <hr class="my-1"/>
                  </div>

                  <div class="form-group  ">
                    <span class="col-12 col-form-label text-md-right">{{ __('price') }}</span>

                    <div class="col-12">
                      <div class="text-center font-weight-bold">{{ amount }} {{ __('currency') }}</div>
                    </div>

                    <hr class="my-1"/>
                  </div>
                  <div class="form-group  ">
                    <span class="col-12 col-form-label text-md-right">{{ __('order_id') }}</span>

                    <div class="col-12">
                      <div class="text-center font-weight-bold">{{ order_id }}</div>
                    </div>

                    <hr class="my-1"/>
                  </div>
                  <div class="form-group  ">
                    <span class="col-12 col-form-label text-md-right">{{ __('track_id') }}</span>

                    <div class="col-12">
                      <div class="text-center font-weight-bold">{{ track_id }}</div>
                    </div>

                    <!--                      <hr class="my-1"/>-->
                  </div>


                  <!--                    <div class="form-group    row">-->
                  <!--                      <div class="col-sm-12 my-2  ">-->
                  <!--                        <a :href="url" type="submit"-->
                  <!--                           class="btn btn-primary btn-block py-3    font-weight-bold ">-->
                  <!--                          {{ __('return') }}-->
                  <!--                        </a>-->

                  <!--                      </div>-->

                  <!--                    </div>-->

                </form>
              </div>
            </div>
          </div>
        </div>
      </div>


    </div>
  </Scaffold>
</template>

<script>
import {
  CheckBadgeIcon,
  ExclamationTriangleIcon,

} from "@heroicons/vue/24/solid";

import Scaffold from "@/Layouts/Scaffold.vue";

export default {

  data() {
    return {
      payment: null,

    }
  },
  components: {
    Scaffold,
    CheckBadgeIcon,
    ExclamationTriangleIcon,
  },
  mounted() {

    /*
    * http://127.0.0.1:8000/payment/confirm?trans_id=778cd378-6c16-4a0f-818d-5b548587d2d5&order_id=1-1694458771737&amount=200&np_status=OK
    * */

  },
  created() {
    this.payment = this.$page.props.payment;
    this.title = this.$page.props.title;
    this.time = this.$page.props.time;
    this.amount = this.$page.props.amount;
    this.url = this.$page.props.url;
    this.order_id = this.$page.props.order_id;
    this.track_id = this.$page.props.track_id;
    this.status = this.$page.props.status;
    this.message = this.$page.props.message;

  }
}

</script>