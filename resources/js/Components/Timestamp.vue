<template>
  <div v-if="mode!='view'" class="border rounded p-2">
    <InputLabel :for="id" :value="label"/>
    <div class="       overflow-x-auto   md:rounded-lg">
      <table ref="tableRef "
             class=" table-auto   text-sm   text-gray-500  ">
        <thead
            class="   sticky top-0 shadow-md   text-xs text-gray-700   bg-gray-50 ">
        <!--         table header-->
        <tr class="text-sm text-center ">

          <th scope="col"
              class="px-4 py-3 min-w-[8.5rem]  cursor-pointer duration-300 hover:text-gray-500 hover:scale-[99%]">
            <div class="  items-center justify-center">
              <span class="px-2">  {{ __('from_hour') }}</span>
            </div>
          </th>


          <th scope="col"
              class=" py-3 min-w-[8.5rem]  cursor-pointer duration-300 hover:text-gray-500 hover:scale-[99%]">
            <div class="  items-center justify-center">
              <span class=" ">    {{ __('to_hour') }} </span>
            </div>
          </th>

          <th scope="col"
              class="px-2 py-3   cursor-pointer duration-300 hover:text-gray-500 hover:scale-[99%]">
            <div class="  items-center justify-center">
              <span class="px-2">    {{ __('active') }} </span>
            </div>
          </th>
        </tr>
        </thead>
        <tbody
            class="    overflow-y-scroll   text-xs   ">
        <tr v-for="(d,idx) in data"
            class="text-center border-b hover:bg-gray-50 " :class="idx%2==1?'bg-gray-50':'bg-white'">

          <td
              class="     text-xs items-center px-1  text-gray-900  ">
            <Selector @change="changed" ref="fromSelector" v-model="d.from"
                      :data="$page.props.hours"
                      :error="errors [`timestamps.${idx}.from`]"
                      class=" "
                      :label="__('')" classes="w-full  "
                      :id="`from${idx}`">

            </Selector>
          </td>
          <td
              class="   text-xs items-center px-1  text-gray-900  ">
            <Selector @change="changed" ref="toSelector" v-model="d.to"
                      :data="$page.props.hours"
                      :error="errors[`timestamps.${idx}.to`] "
                      :label="__('')" classes="w-full "
                      :id="`to${idx}`">

            </Selector>
          </td>
          <td class="p-4">
            <div class="  items-center">
              <input @change="changed" :id="`active${idx}`" type="checkbox" v-model="d.active"
                     class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
              <label :for="`active${idx}`" class="sr-only">checkbox</label>
            </div>
          </td>

          <td class="px-2 py-4">
            <!-- Actions Group -->
            <div
                class=" inline-flex rounded-md shadow-sm transition duration-150 ease-in-out hover:bg-primary-600 hover:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.3),0_4px_18px_0_rgba(59,113,202,0.2)] focus:bg-primary-600 focus:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.3),0_4px_18px_0_rgba(59,113,202,0.2)] focus:outline-none focus:ring-0 active:bg-primary-700 active:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.3),0_4px_18px_0_rgba(59,113,202,0.2)]"
                role="group">
              <PrimaryButton type="button"
                             @click="data.splice(idx,1); changed()  "
                             class="bg-red-500 hover:bg-red-400 text-sm  ms-auto">
                <TrashIcon class="w-4 h-4 "/>
              </PrimaryButton>

            </div>
          </td>
        </tr>

        </tbody>
      </table>
      <PrimaryButton type="button"
                     @click="data.push({})"
                     class="bg-green-500 hover:bg-green-400 text-sm  my-2">
        <PlusIcon class="w-4 h-4 mx-4"/>
      </PrimaryButton>
    </div>
  </div>
  <div v-else class="border p-2 rounded overflow-x-scroll " :class="`${errors?'bg-red-50':''}`">
    <InputLabel :for="id" :value="label"/>
    <div class="flex items-center gap-1" v-for="(row,idx) in modelValue">
      <div class="w-[4rem]">{{ row[0].day }}</div>
      <div class="flex flex-col w-[7rem] p-2   m-[.2rem] border rounded  "
           @click="$emit('change',(idx*row.length)+(ix))"
           :class=" (col.active?'cursor-pointer ':'opacity-50 cursor-default ')+(col.selected? ' shadow-md ':'  ') "
           v-for="(col,ix) in row">
        <div>{{ `${padDigits(col.from, 2)}:00 ${__('until')} ${padDigits(col.to, 2)}:00` }}</div>
        <div class="flex items-center mb-4">
          <input @click="$emit('change',(ix+1)*(idx+1))"
                 :id="`check${idx}${ix}`" type="checkbox" v-model="col.selected" :value="col.selected"
                 class="w-4 h-4 mx-auto text-blue-600 bg-gray-100 border-gray-300 rounded-full focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
        </div>
      </div>
    </div>
    <InputError :message="errors"/>
  </div>
</template>

<script>
import InputLabel from "@/Components/InputLabel.vue";
import Selector from "@/Components/Selector.vue";
import {

  ClockIcon,
  TrashIcon,
  PlusIcon,

} from "@heroicons/vue/24/outline";
import {useForm} from "@inertiajs/vue3";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import InputError from "@/Components/InputError.vue";

export default {
  name: "Timestamp",
  components: {
    InputError,
    PrimaryButton,
    InputLabel,
    Selector,
    ClockIcon,
    TrashIcon,
    PlusIcon,
  },
  emits: ['update:modelValue', 'change'],
  props: ['mode', 'label', 'modelValue', 'id', 'errors',],
  data() {
    return {
      data: [],
    }
  },
  mounted() {
    this.data = this.modelValue || this.$page.props.default_timestamps;
    this.$emit('update:modelValue', this.data);
  },
  methods: {
    changed() {
      this.$emit('update:modelValue', this.data);
    },
    padDigits(number, digits) {
      return Array(Math.max(digits - String(number).length + 1, 0)).join(0) + number;
    }
  },
}
</script>

<style scoped>

</style>