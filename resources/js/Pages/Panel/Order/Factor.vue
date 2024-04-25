<template>

  <Head :title="__('factor')">

  </Head>
  <div class="w-full">
    <div class="my-4  p-2 sm:p-4 mx-auto overflow-x-scroll    max-w-3xl" :dir="dir()" v-if="data">


      <table class="w-full    ">
        <thead>
        <tr>

        </tr>
        </thead>
        <tbody
            class="    overflow-y-scroll   text-xs   ">
        <tr>
          <th
              class="px-2 border w-[50vw]">
            <div class="flex items-center justify-center">
              <ApplicationLogo type="outline-dark" class="  h-12 fill-current text-gray-500"/>
              <span>{{ __('app_name') }}</span>
            </div>
          </th>

          <th class="px-2 border w-[50vw] ">
            <div class="flex flex-col items-start">
              <div class="flex items-center justify-between">
                <div class="font-bold">{{ __('factor_id') }}:</div>
                <div class="px-2">{{ data.order_id }}</div>
              </div>
              <div class="flex items-center">
                <div class="font-bold">{{ __('created_at') }}:</div>
                <div class="px-2">{{ toShamsi(data.created_at, true) }}</div>
              </div>
              <div v-if="data.delivery_date" class="flex items-center">
                <div class="font-bold">{{ __('delivery_time') }}:</div>
                <div class="px-2">{{ toShamsi(data.delivery_date, true) }}</div>
                <div class="px-2">{{ data.delivery_timestamp }}</div>
              </div>
              <div class="text-end w-full ">
                <vue3-barcode :width="1" :display-value="false" class=" "
                              :value="data.order_id"
                              :height="20"/>
              </div>
            </div>
          </th>
        </tr>


        </tbody>
      </table>
      <table class="w-full   ">
        <thead>
        <tr class="bg-gray-100">
          <th class="border">{{ __('seller') }}</th>
          <th class="border">{{ __('buyer') }}</th>
        </tr>
        </thead>
        <tbody>
        <tr>
          <td class="border p-2 w-[50vw]">
            <div v-if="data.from" class="flex gap-2 flex-wrap w-full font-normal text-sm">
              <div class="flex items-center">
                <div class="font-bold">{{ __('seller') }}:</div>
                <div class=" ms-1">{{ data.from.name }}</div>
              </div>
              <div class="flex items-center">
                <div class="font-bold">{{ __('province') }}:</div>
                <div class=" ms-1">{{ getCityName(data.from.province_id) }}</div>
              </div>
              <div class="flex items-center">
                <div class="font-bold">{{ __('county') }}:</div>
                <div class=" ms-1">{{ getCityName(data.from.county_id) }}</div>
                <div class=" ms-1">{{ data.from.district_id ? ',' + getCityName(data.from.district_id) : '' }}</div>
              </div>
              <div v-if="data.from.postal_code" class="flex items-center">
                <div class="font-bold">{{ __('postal_code') }}:</div>
                <div class=" ms-1">{{ data.from.postal_code }}</div>
              </div>
              <div v-if="data.from.phone" class="flex items-center">
                <div class="font-bold">{{ __('phone') }}:</div>
                <div class=" ms-1">{{ data.from.phone }}</div>
              </div>
              <div v-if="data.from.address" class="flex  items-center">
                <div class="font-bold">{{ __('address') }}:</div>
                <div class=" ms-1">{{ data.from.address }}</div>
              </div>
            </div>
          </td>
          <td class="border p-2 w-[50vw]">
            <div v-if="data.to" class="flex gap-2 flex-wrap w-full font-normal text-sm">
              <div class="flex items-center">
                <div class="font-bold">{{ __('receiver') }}:</div>
                <div class=" ms-1">{{ data.to.name }}</div>
              </div>
              <div class="flex items-center">
                <div class="font-bold">{{ __('province') }}:</div>
                <div class=" ms-1">{{ getCityName(data.to.province_id) }}</div>
              </div>
              <div class="flex items-center">
                <div class="font-bold">{{ __('county') }}:</div>
                <div class=" ms-1">{{ getCityName(data.to.county_id) }}</div>
                <div class=" ms-1">{{ data.to.district_id ? ',' + getCityName(data.to.district_id) : '' }}</div>
              </div>
              <div v-if="data.to.postal_code" class="flex items-center">
                <div class="font-bold">{{ __('postal_code') }}:</div>
                <div class=" ms-1">{{ data.to.postal_code }}</div>
              </div>
              <div v-if="data.to.phone" class="flex items-center">
                <div class="font-bold">{{ __('phone') }}:</div>
                <div class=" ms-1">{{ data.to.phone }}</div>
              </div>
              <div v-if="data.to.address" class="flex  items-center">
                <div class="font-bold">{{ __('address') }}:</div>
                <div class=" ms-1">{{ data.to.address }}</div>
              </div>
            </div>
          </td>
        </tr>
        </tbody>
      </table>
      <table class="w-full    ">
        <thead>
        <tr class="bg-gray-100">
          <th scope="col" class="border">{{ __('row') }}</th>
          <th class="border">{{ __('product_id') }}</th>
          <th class="border">{{ __('description') }}</th>
          <th class="border">{{ `${__('qty')}/${__('total_weight')}` }}</th>
          <th class="border">{{ `${__('unit_price')}` }}</th>
          <th class="border">{{ `${__('price')}(${__('currency')})` }}</th>
        </tr>
        </thead>
        <tbody class="text-sm">
        <tr v-for="(item,idx) in data.items">
          <td class="border text-center p-2  ">
            {{ idx + 1 }}
          </td>
          <td class="border text-center p-2  ">
            {{ item.variation_id }}
          </td>
          <td class="border text-center p-2  ">

            {{ item.title }}
          </td>
          <td class="border text-center p-2  ">
            {{
              `${(parseFloat(item.qty) || 0)}`
            }}
          </td>

          <td class="border text-center p-2  ">
            {{
              `${asPrice(Math.round((item.total_price || 0) / ((item.qty || 1))))}`
            }}
          </td>
          <td class="border text-center p-2  ">
            {{
              `${asPrice(item.total_price) || ''}`
            }}
          </td>
        </tr>
        <tr>
          <td colspan="5" class="border text-center p-2">{{ __('total_items_price') }}</td>
          <td colspan="1" class="border text-center p-2">{{ asPrice(data.total_items_price) }}</td>
        </tr>
        <tr>
          <td colspan="5" class="border text-center p-2">{{ __('tax') }}</td>
          <td colspan="1" class="border text-center p-2">{{ asPrice(data.tax_price) }}</td>
        </tr>
        <tr>
          <td colspan="5" class="border text-center p-2">{{ __('shipping_price') }}</td>
          <td colspan="1" class="border text-center p-2">{{
              data.shipping_method && data.shipping_method.pay_type == 'local' ? __('local') : asPrice(data.total_shipping_price)
            }}</td>
        </tr>

        <tr v-if="data.change_price">
          <td colspan="5" class="border text-center p-2">{{ __('change_price') }}</td>
          <td colspan="1" class="border text-center p-2">{{ asPrice(data.change_price) }}</td>
        </tr>


        <tr>
          <td colspan="3" class="border text-center p-2">{{
              `${__('pay_id')}${data.transaction && data.transaction.pay_gate ? `(${data.transaction.pay_gate})` : ''}`
            }}
          </td>
          <td colspan="3" class="border text-sm text-center p-2">{{
              data.transaction && data.transaction.pay_id ? data.transaction.pay_id : '_'
            }}
          </td>
        </tr>
        <tr class="bg-gray-100">
          <td colspan="5" class="border text-center p-2">{{ __('final_price') }}
          </td>
          <td colspan="1" class="border text-sm text-center p-2">{{ asPrice(data.total_price) }}</td>
        </tr>

        </tbody>
      </table>
      <div class="w-full flex py-2">
        <button
            type="button" @click="print"
            class="flex mx-1 rounded  bg-indigo-500 text-white px-6  py-2 text-xs font-medium   leading-normal text-white transition duration-150 ease-in-out hover:bg-indigo-400   focus:outline-none focus:ring-0  "
            data-te-ripple-init
            data-te-ripple-color="light">
          <PrinterIcon class="h-4 w-4"/>
          <span class="mx-1">{{ __('print') }}</span>
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import {Head} from "@inertiajs/vue3";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import Vue3Barcode from 'vue3-barcode';
import {

  PrinterIcon,

} from "@heroicons/vue/24/outline";

export default {
  name: "Factor",
  data() {
    return {
      data: this.$page.props.data
    }
  },
  components: {
    ApplicationLogo,
    Head,
    Vue3Barcode,
    PrinterIcon,
  },
  props: [],
  mounted() {

  },
  methods: {
    print() {
      window.print();
    }
  }

}
</script>

<style scoped>

</style>