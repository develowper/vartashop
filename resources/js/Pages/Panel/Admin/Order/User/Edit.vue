<template>

  <Panel>
    <template v-slot:header>
      <title>{{__('edit_order')}}</title>
    </template>


    <template v-slot:content>
      <!-- Content header -->
      <div
          class="flex items-center justify-start px-4 py-2 text-primary-500 border-b md:py-4">
        <FolderPlusIcon class="h-7 w-7 mx-3"/>

        <h1 class="text-2xl font-semibold">{{ __('edit_order') }}</h1>

      </div>


      <div class="px-2  md:px-4" v-if="data">

        <div
            class="    mx-auto md:max-w-2xl lg:max-w-3xl   mt-6 px-2 md:px-4 py-4 bg-white shadow-md overflow-hidden  rounded-lg  ">


          <div
              class="flex flex-col mx-2   col-span-2 w-full     px-2"
          >

            <form @submit.prevent="edit">


              <div class="my-4 ">

                <div class="border p-2 rounded border-gray-300">

                  <div>

                    <UserSelector :colsData="['name','phone','agency_id' ]"
                                  :labelsData="['name','phone','agency_id' ]"
                                  :callback="{'agency_id':(e)=>`${__('agency')} ${e}`}" :error="errors.repo_id"
                                  :link="route('admin.panel.repository.search')+(`?status=active&with=shipping_methods` )"
                                  :label="__('origin_repository')"
                                  :editable="false"
                                  :id="'origin_repository'" v-model:selected="data.repo_id"
                                  :preload="data.repository">
                      <template v-slot:selector="props">
                        <div :class="props.selectedText?'py-2':'py-2'"
                             class=" px-4 border border-gray-300 rounded hover:bg-gray-100 cursor-pointer flex items-center ">
                          <div class="grow">
                            {{ props.selectedText ?? __('select') }}
                          </div>

                        </div>
                      </template>
                    </UserSelector>

                    <div v-if="data.repository" class="my-2 border rounded p-2">
                      <InputLabel for="shipping_methods" :value="__('shipping_method')"/>
                      <InputError :message="errors.shipping_method"/>
                      <div v-for="(s,idx) in data.repository.shipping_methods">
                        <div @click=" data.shipping_method_id=s.id "
                             :class="{'bg-primary-200':data.shipping_method_id==s.id}"
                             class="border-b rounded text-gray-700 p-2 text-sm hover:bg-gray-200 cursor-pointer">
                          <div class="font-semibold">{{ s.name }}</div>
                          <div class="text-gray-400 text-xs">{{ s.description }}</div>
                        </div>
                      </div>
                      <div class="my-2 flex flex-col space-y-1">

                        <date-picker :id="`delivery_date`" class="rounded   fromdate  " inputClass=""
                                     :editable="true"
                                     inputFormat="YYYY/MM/DD" :placeholder="__('delivery_time')" color="#00acc1"
                                     v-model="data.delivery_date"></date-picker>
                        <TextInput
                            id="delivery_timestamp"
                            type="text"
                            :placeholder="null"
                            classes="p-1  "
                            v-model="data.delivery_timestamp"
                            autocomplete="fullname"
                            :error=" errors.delivery_timestamp"
                        >
                          <template v-slot:prepend>
                            <div class="p-1 px-2">
                              {{ __('delivery_hours') }}
                            </div>
                          </template>

                        </TextInput>
                      </div>
                    </div>

                  </div>

                  <AddressSelector ref="addressSelector"
                                   :clearable="false"
                                   :editable="true" class="my-2 " type="repo"
                                   :label="__('address')"
                                   @change="updateAddress($event) "
                                   :error="errors.from_address ||errors.from_postal_code || errors.from_province_id || errors.from_county_id|| errors.from_location "/>

                </div>
              </div>


              <div class="my-2">
                <div class="border p-2 rounded border-gray-300">
                  <InputError :message="errors.products"/>
                  <div class="my-4">
                    <ProductSelector v-if="data.repo_id" ref="variationSelector"
                                     :link="route('admin.panel.product.tree')+`?repo_id=${data.repo_id}`"
                                     :multi="true" mode="count"
                                     :preload="data.products "
                                     @change="($e)=> data.products=$e.map(e=>{e.qty=(data.items.filter(el=>el.variation_id==e.id)[0]||{qty:0}).qty ;return e;})"
                                     :label="__('products')"
                                     :error="``"/>
                    <div class="     w-full overflow-x-auto   md:rounded-lg">
                      <table ref="tableRef "
                             class=" table-auto   text-sm   text-gray-500  ">
                        <thead
                            class="   sticky top-0 shadow-md   text-xs text-gray-700   bg-gray-50 ">
                        <!--         table header-->
                        <tr class="text-sm text-center ">

                          <th scope="col"
                              class="px-2 py-3   cursor-pointer duration-300 hover:text-gray-500 hover:scale-[99%]">
                            <div class="flex items-center justify-center">
                              <span class="px-0">    {{ __('id') }} </span>
                            </div>
                          </th>
                          <th scope="col"
                              class="px-4 py-3   cursor-pointer duration-300 hover:text-gray-500 hover:scale-[99%]">
                            <div class="flex items-center justify-center">
                              <span class="px-2">  {{ __('name') }}</span>
                            </div>
                          </th>


                          <th scope="col"
                              class=" py-3   cursor-pointer duration-300 hover:text-gray-500 hover:scale-[99%]">
                            <div class="flex items-center justify-center">
                              <span class=" ">    {{ __('grade') }} </span>
                            </div>
                          </th>

                          <th scope="col"
                              class="px-2 py-3   cursor-pointer duration-300 hover:text-gray-500 hover:scale-[99%]">
                            <div class="flex items-center justify-center">
                              <span class="px-2">    {{ __('pack') }} </span>
                            </div>
                          </th>

                          <th scope="col"
                              class="px-2 py-3   cursor-pointer duration-300 hover:text-gray-500 hover:scale-[99%]">
                            <div class="flex items-center justify-center">
                              <span class=" ">    {{ __('weight') }} </span>
                            </div>
                          </th>

                          <th scope="col"
                              class="px-2 py-3   cursor-pointer duration-300 hover:text-gray-500 hover:scale-[99%]">
                            <div class="flex items-center justify-center">
                              <span class="px-2">    {{ __('fee') }} </span>
                            </div>
                          </th>


                          <th scope="col"
                              class="px-2 py-3   cursor-pointer duration-300 hover:text-gray-500 hover:scale-[99%]">
                            <div class="flex items-center justify-center">
                              <span class="px-2">    {{ __('count') }} </span>
                            </div>
                          </th>

                          <th scope="col" class="px-2 py-3">
                            {{ __('actions') }}
                          </th>
                        </tr>
                        </thead>
                        <tbody
                            class="       text-xs   ">
                        <tr v-for="(d,idx) in   data.products  "
                            class="text-center border-b hover:bg-gray-50 " :class="idx%2==1?'bg-gray-50':'bg-white'">

                          <td class="px-2 py-4    ">
                            {{ d.id }}
                          </td>
                          <td
                              class="flex  text-xs items-center px-1 py-5 text-gray-900  ">
                            <div class=" font-semibold ">{{
                                cropText(d.name, 30)
                              }}
                            </div>

                          </td>


                          <td class="px-2 py-4    ">

                            <div class=" font-semibold ">{{
                                d.grade
                              }}
                            </div>

                          </td>

                          <td class="px-2 py-4    ">
                            <div class=" font-semibold ">{{
                                getPack(d.pack_id)
                              }}
                            </div>


                          </td>
                          <td class="px-2 py-4    ">
                            <div class=" font-semibold ">{{
                                parseFloat(d.weight)
                              }}
                            </div>


                          </td>
                          <td class="px-2 py-4    ">
                            <div class=" font-semibold ">{{
                                asPrice(d.price)
                              }}
                            </div>

                          </td>


                          <td class="px-2 py-4   ">
                            <div class="flex items-center font-semibold ">
                              <TextInput
                                  :id="`qty${d.id}`"
                                  type="number"
                                  :placeholder="``"
                                  classes=" p-0 max-w-[5rem]"
                                  v-model="d.qty"
                                  autocomplete="qty"
                                  :error="errors[`products.${idx}.qty`]">

                              </TextInput>
                            </div>

                          </td>

                          <td class="px-2 py-4">
                            <!-- Actions Group -->
                            <div
                                class=" inline-flex rounded-md shadow-sm transition duration-150 ease-in-out hover:bg-primary-600 hover:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.3),0_4px_18px_0_rgba(59,113,202,0.2)] focus:bg-primary-600 focus:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.3),0_4px_18px_0_rgba(59,113,202,0.2)] focus:outline-none focus:ring-0 active:bg-primary-700 active:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.3),0_4px_18px_0_rgba(59,113,202,0.2)]"
                                role="group">
                              <PrimaryButton type="button"
                                             @click="data.products.splice(idx,1);$refs.variationSelector.selecteds.splice(idx,1) "
                                             class="bg-red-500 hover:bg-red-400 text-sm  ms-auto">
                                <TrashIcon class="w-4 h-4 "/>
                              </PrimaryButton>

                            </div>
                          </td>
                        </tr>

                        </tbody>
                      </table>

                    </div>

                  </div>

                </div>

                <div class="flex flex-col space-y-2 text-sm text-gray-600 my-2 p-2 border rounded">
                  <div class="flex items-center">
                    <div>{{ __('count') }}:</div>
                    <div class="font-semibold mx-1">{{
                        asPrice(mySum(data.products.map(e => parseFloat(e.qty))))
                      }}
                    </div>
                  </div>
                  <div class="flex items-center">
                    <div>{{ __('distance') }}:</div>
                    <div class="font-semibold mx-1">{{
                        data.distance ? `${data.distance} ${__('km')}` : '?'
                      }}
                    </div>
                  </div>
                  <div class="flex items-center">
                    <div>{{ __('discount') }}:</div>
                    <div class="font-semibold mx-1 flex items-center">
                      <span> {{ asPrice(data.total_discount) }}</span>
                      <TomanIcon class="mx-1"/>
                    </div>
                  </div>
                  <div class="flex items-center">
                    <div>{{ __('change_price') }}:</div>
                    <div class="font-semibold mx-1 flex items-center">
                      <span> {{ asPrice(data.change_price) }}</span>
                      <TomanIcon class="mx-1"/>
                    </div>
                  </div>
                  <div class="flex items-center">
                    <div>{{ __('items_price') }}:</div>
                    <TextInput
                        id="items_price"
                        type="number"
                        placeholder=""
                        classes=" p-1 mx-1   "
                        v-model="data.total_items_price"
                        autocomplete="change_price"
                        :error="errors.total_items_price">
                    </TextInput>
                    <TomanIcon class="mx-1"/>
                  </div>
                  <div class="flex items-center">
                    <div>{{ __('shipping_price') }}:</div>
                    <TextInput
                        id="total_shipping_price"
                        type="number"
                        placeholder=""
                        classes=" p-1 mx-1   "
                        v-model="data.total_shipping_price"
                        autocomplete="total_shipping_price"
                        :error="errors.total_shipping_price">
                    </TextInput>
                    <TomanIcon class="mx-1"/>
                  </div>


                  <div class="flex items-center border-t py-2">
                    <div class="font-bold">{{ __('sum') }}:</div>
                    <div class="font-semibold mx-1">{{
                        asPrice(mySum([parseInt(data.total_items_price), Math.abs(data.total_shipping_price) || 0, -Math.abs(data.total_discount) || 0,]))
                      }}
                    </div>
                    <TomanIcon class="mx-1"/>
                  </div>
                </div>
              </div>


              <div class="    mt-4">

                <PrimaryButton @click="edit" type="button" class="w-full flex items-center justify-center"
                               :class="{ 'opacity-25': loading }"
                               :disabled="loading">
                  <LoadingIcon class="w-4 h-4 mx-3 " v-if="  loading"/>
                  <span class=" text-lg  ">  {{ __('register_info') }} </span>
                </PrimaryButton>

              </div>

            </form>
          </div>


        </div>
      </div>
    </template>


  </Panel>
</template>

<script>
import Scaffold from "@/Layouts/Scaffold.vue";
import Panel from "@/Layouts/Panel.vue";
import {Head, Link, useForm} from "@inertiajs/vue3";
import {
  ChevronDownIcon,
  HomeIcon,
  UserIcon,
  EyeIcon,
  FolderPlusIcon,
  Bars2Icon,
  ChatBubbleBottomCenterTextIcon,
  Squares2X2Icon,
  SignalIcon,
  PencilIcon,
  XMarkIcon,
  CurrencyDollarIcon,
  ClockIcon,
  TrashIcon,
  PlusIcon,

} from "@heroicons/vue/24/outline";
import {QuestionMarkCircleIcon,} from "@heroicons/vue/24/solid";
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import RadioGroup from '@/Components/RadioGroup.vue'
import LoadingIcon from "@/Components/LoadingIcon.vue";
import Popover from "@/Components/Popover.vue";
import Tooltip from "@/Components/Tooltip.vue";
import TagInput from "@/Components/TagInput.vue";
import ImageUploader from "@/Components/ImageUploader.vue";
import Selector from "@/Components/Selector.vue";
import ProvinceCounty from "@/Components/ProvinceCounty.vue";
import PhoneFields from "@/Components/PhoneFields.vue";
import SocialFields from "@/Components/SocialFields.vue";
import TextEditor from "@/Components/TextEditor.vue";
import UserSelector from "@/Components/UserSelector.vue";
import AddressSelector from "@/Components/AddressSelector.vue";
import CitySelector from "@/Components/CitySelector.vue";
import ProductSelector from "@/Components/ProductSelector.vue";
import TomanIcon from "@/Components/TomanIcon.vue";
import Timestamp from "@/Components/Timestamp.vue";
import VuePersianDatetimePicker from 'vue3-persian-datetime-picker';

export default {

  data() {
    return {
      data: this.$page.props.data,
      errors: {},
      loading: false,

    }
  },
  components: {
    Timestamp,
    TomanIcon,
    AddressSelector,
    UserSelector,
    ImageUploader,
    LoadingIcon,
    Head,
    Link,
    HomeIcon,
    ChevronDownIcon,
    Panel,
    InputLabel,
    TextInput,
    InputError,
    PrimaryButton,
    RadioGroup,
    UserIcon,
    EyeIcon,
    Checkbox,
    Popover,
    Tooltip,
    FolderPlusIcon,
    Bars2Icon,
    ChatBubbleBottomCenterTextIcon,
    TagInput,
    QuestionMarkCircleIcon,
    Selector,
    Squares2X2Icon,
    ProvinceCounty,
    PhoneFields,
    SocialFields,
    SignalIcon,
    TextEditor,
    PencilIcon,
    XMarkIcon,
    CitySelector,
    CurrencyDollarIcon,
    ClockIcon,
    ProductSelector,
    TrashIcon,
    PlusIcon,
    datePicker: VuePersianDatetimePicker,
  },
  mounted() {

    this.initDatePicker();

    this.loadAddress();

  },
  watch: {
    form(_new, _old) {


    }
  },
  methods: {
    loadAddress() {
      this.preloadAddress = {
        address: this.data.address,
        postal_code: this.data.postal_code,
        province_id: this.data.province_id,
        county_id: this.data.county_id,
        district_id: this.data.district_id,
        receiver_fullname: this.data.receiver_fullname,
        receiver_phone: this.data.receiver_phone,
        lat: this.data.location && this.data.location.indexOf(',') > -1 ? this.data.location.split(',')[0] : null,
        lon: this.data.location && this.data.location.indexOf(',') > -1 ? this.data.location.split(',')[1] : null,

      };
      this.$nextTick(() => {
        this.$refs.addressSelector.preload(this.preloadAddress);
        this.updateAddress(this.preloadAddress);

      });
    },
    updateAddress(address) {
      address = address || {};
      this.data.address = address.address;
      this.data.province_id = address.province_id;
      this.data.county_id = address.county_id;
      this.data.district_id = address.district_id;
      this.data.lat = address.lat;
      this.data.lon = address.lon;
      this.data.location = `${address.lat},${address.lon}`;
      this.data.postal_code = this.f2e(address.postal_code);
      this.data.receiver_fullname = address.receiver_fullname;
      this.data.receiver_phone = address.receiver_phone;
    },
    initDatePicker() {
      this.$nextTick(() => {
        document.querySelectorAll('.vpd-input-group').forEach((el) => {
          el.classList.add('flex');

        });
        document.querySelectorAll('.vpd-input-group input').forEach((el) => {
          el.classList.add('rounded');
        });
        document.querySelectorAll('.vpd-input-group label').forEach((el) => {
          el.classList.add('rounded-s');
          el.append(` ${this.__('delivery_time')} `)
        });
      });
    },
    edit(params = {}) {
      params.id = this.data.id;
      params.agency_id = this.data.agency_id;
      params._method = 'PATCH';
      params.products = this.data.products.map(e => {
        return {id: e.id, qty: e.qty}
      });
      params.delivery_date = this.data.delivery_date;
      params.delivery_timestamp = this.data.delivery_timestamp;

      params.receiver_fullname = this.data.receiver_fullname;
      params.receiver_phone = this.data.receiver_phone;

      params.address = this.data.address;
      params.province_id = this.data.province_id;
      params.county_id = this.data.county_id;
      params.district_id = this.data.district_id;
      params.location = this.data.location;
      params.lat = this.data.lat;
      params.lon = this.data.lon;
      params.postal_code = this.data.postal_code;
      params.change_price = this.data.change_price;
      params.shipping_method_id = this.data.shipping_method_id;
      params.total_shipping_price = this.data.total_shipping_price;
      params.total_items_price = this.data.total_items_price;

      this.isLoading(true);
      this.errors = {};
      window.axios.post(route('admin.panel.order.user.update'), params,
          {})
          .then((response) => {
            if (response.data && response.data.message) {
              this.showToast('success', response.data.message);

            }

            if (response.data.order) {
              this.data = null;
              this.data = response.data.order;
              this.loadAddress();
            }


          })

          .catch((error) => {
            this.error = this.getErrors(error);
            if (error.response && error.response.data) {
              this.errors = error.response.data.errors || {};


              if (error.response.data.meta) {
                this.data[params.idx].meta = error.response.data.meta;
              }
            }
            this.showToast('danger', this.error);
          })
          .finally(() => {
            // always executed
            this.isLoading(false);
          });
    },
  },

}
</script>
