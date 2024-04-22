<template>

  <Panel>
    <template v-slot:header>
      <title>{{__('new_order')}}</title>
    </template>


    <template v-slot:content>
      <!-- Content header -->
      <div
          class="flex items-center justify-start px-4 py-2 text-primary-500 border-b md:py-4">
        <FolderPlusIcon class="h-7 w-7 mx-3"/>

        <h1 class="text-2xl font-semibold">{{ __('new_order') }}</h1>

      </div>


      <div class="px-2  md:px-4">

        <div
            class="    mx-auto md:max-w-2xl lg:max-w-max   mt-6 px-2 md:px-4 py-4 bg-white shadow-md overflow-hidden  rounded-lg  ">


          <div
              class="flex flex-col mx-2   col-span-2 w-full     px-2"
          >

            <form @submit.prevent="submit">

              <div class="my-2">
                <UserSelector :colsData="['name','phone','agency_id']" :labelsData="['name','phone','agency_id']"
                              :callback="{'level':getAgency}" :error="form.errors.to_repo_id"
                              :link="route('admin.panel.repository.search')+(`?status=active` )"
                              :label="__('destination_repository')"
                              :id="'destination_repository'" v-model:selected="form.to_repo_id" :preload="null">
                  <template v-slot:selector="props">
                    <div :class="props.selectedText?'py-2':'py-2'"
                         class=" px-4 border border-gray-300 rounded hover:bg-gray-100 cursor-pointer flex items-center ">
                      <div class="grow">
                        {{ props.selectedText ?? __('select') }}
                      </div>
                      <div v-if="props.selectedText"
                           class="bg-danger rounded p-2   cursor-pointer text-white hover:bg-danger-400"
                           @click.stop="props.clear()">
                        <XMarkIcon class="w-5 h-5"/>

                      </div>
                    </div>
                  </template>
                </UserSelector>

              </div>

              <div class="my-4 ">
                <Tooltip v-if="hasAccess('create_variation')" class="  " :content="__('help_order_from')">
                  <div class="flex items-center">
                    <QuestionMarkCircleIcon class="text-gray-500 hover:bg-gray-50 w-4 h-4"/>
                    <InputLabel class="mx-1" for="order_from" :value="__('order_from')"/>
                  </div>
                </Tooltip>
                <div class="border p-2 rounded border-gray-300">
                  <RadioGroup v-if="hasAccess('create_variation')" :beforeSelected="form.order_type"
                              ref="orderFromSelector" class="grow" name="status"
                              @change="($e)=>{ if($e.target.value==__('external')){form.from_repo_id=null;form.from_repo=null;form.shipping_method_id=null};form.products=[ ] }"
                              v-model="form.order_type"
                              :items="orderFrom"/>
                  <div v-if="form.order_type==orderFrom[0]">

                    <UserSelector :colsData="['name','phone','agency_id','address']"
                                  :labelsData="['name','phone','agency_id','address']"
                                  :callback="{'level':getAgency}" :error="form.errors.from_repo_id"
                                  :link="route('admin.panel.repository.search')+(`?status=active&with=shipping_methods` )"
                                  :label="__('origin_repository')"
                                  @change="($e)=>{form.from_repo=$e;updateAddress($e)}"
                                  :id="'origin_repository'" v-model:selected="form.from_repo_id" :preload="null">
                      <template v-slot:selector="props">
                        <div :class="props.selectedText?'py-2':'py-2'"
                             class=" px-4 border border-gray-300 rounded hover:bg-gray-100 cursor-pointer flex items-center ">
                          <div class="grow">
                            {{ props.selectedText ?? __('select') }}
                          </div>
                          <div v-if="props.selectedText"
                               class="bg-danger rounded p-2   cursor-pointer text-white hover:bg-danger-400"
                               @click.stop="props.clear()">
                            <XMarkIcon class="w-5 h-5"/>

                          </div>
                        </div>
                      </template>
                    </UserSelector>

                    <div v-if="form.from_repo" class="my-2 border rounded p-2">
                      <InputLabel for="shipping_methods" :value="__('shipping_method')"/>
                      <InputError :message="form.errors.shipping_method"/>
                      <div v-for="(s,idx) in form.from_repo.shipping_methods">
                        <div @click="form.shipping_method_id=form.shipping_method_id==s.id?null:  s.id"
                             :class="{'bg-primary-200':form.shipping_method_id==s.id}"
                             class="border-b rounded text-gray-700 p-2 text-sm hover:bg-gray-200 cursor-pointer">
                          <div class="font-semibold">{{ s.name }}</div>
                          <div class="text-gray-400 text-xs">{{ s.description }}</div>
                        </div>
                      </div>
                    </div>

                  </div>

                  <AddressSelector v-if="form.from_repo_id || form.order_type==orderFrom[1]" ref="addressSelector"
                                   :clearable="form.order_type==orderFrom[1]"
                                   :editable="form.order_type==orderFrom[1]" class="my-2 " type="repo"
                                   :label="__('address')"
                                   @change="updateAddress($event) "
                                   :error="form.errors.from_address ||form.errors.from_postal_code || form.errors.from_province_id || form.errors.from_county_id|| form.errors.from_location "/>

                </div>
              </div>


              <div class="my-2">
                <div class="border p-2 rounded border-gray-300">
                  <InputLabel for="products" :value="__('products')"/>
                  <InputError :message="form.errors.products"/>
                  <div class="my-4">
                    <ProductSelector v-if="form.from_repo_id" ref="variationSelector"
                                     :link="route('admin.panel.product.tree')+`?repo_id=${form.from_repo_id}`"
                                     :multi="true" mode="count"
                                     @change="($e)=>form.products=$e.map(e=>{ e.qty=(form.products.filter(el=>el.id==e.id)[0] ||{qty:0}).qty; return e;})"
                                     :label="__('products')"
                                     :error="``"/>
                    <div class="     w-full overflow-x-auto   md:rounded-lg">
                      <table ref="tableRef "
                             class=" table-auto   text-sm   text-gray-500  ">
                        <thead
                            class="   sticky top-0 shadow-md   text-xs text-gray-700   bg-gray-50 ">
                        <!--         table header-->
                        <tr class="text-sm text-center ">

                          <th v-if="form.order_type==__('internal')" scope="col"
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
                            class="    overflow-y-scroll   text-xs   ">
                        <tr v-for="(d,idx) in form.products"
                            class="text-center border-b hover:bg-gray-50 " :class="idx%2==1?'bg-gray-50':'bg-white'">

                          <td v-if="form.order_type==__('internal')" class="px-2 py-4    ">
                            {{ d.id }}
                          </td>
                          <td
                              class="flex  text-xs items-center px-1 py-4 text-gray-900  ">
                            <div v-if="form.order_type==__('internal')" class=" font-semibold ">{{
                                cropText(d.name, 30)
                              }}
                            </div>
                            <div v-else class=" min-w-[10rem]">
                              <Selector ref="productSelector" v-model="d.id"
                                        :data="$page.props.products"
                                        :error="form.errors[`products.${idx}.id`]"
                                        :label="__('')" classes=""
                                        :id="`id${idx}`">

                              </Selector>
                            </div>
                          </td>


                          <td class="px-2 py-4    ">

                            <div v-if="form.order_type==__('internal')" class=" font-semibold ">{{
                                d.grade
                              }}
                            </div>
                            <div v-else class=" min-w-[8rem]">
                              <Selector ref="gradeSelector" v-model="d.grade"
                                        :data="$page.props.grades.map(e=>{return{id:e,name:e}})"
                                        :error="form.errors[`products.${idx}.grade`]"
                                        :label="__('')" classes=""
                                        :id="`grade${idx}`">

                              </Selector>
                            </div>
                          </td>

                          <td class="px-2 py-4    ">
                            <div v-if="form.order_type==__('internal')" class=" font-semibold ">{{
                                getPack(d.pack_id)
                              }}
                            </div>
                            <div v-else class=" min-w-[10rem]">
                              <Selector ref="packSelector" v-model="d.pack_id"
                                        :data="$page.props.packs"
                                        @change="($e)=> {if(d.pack_id==1)d.weight=1}"
                                        :error="form.errors[`products.${idx}.pack_id`]"
                                        :label="__('')" classes=""
                                        :id="`pack${idx}`">

                              </Selector>
                            </div>

                          </td>
                          <td class="px-2 py-4    ">
                            <div v-if="form.order_type==__('internal')" class=" font-semibold ">{{
                                parseFloat(d.weight)
                              }}
                            </div>
                            <TextInput v-else
                                       :id="`weight${d.id}`"
                                       type="number"
                                       :placeholder="``"
                                       :disabled="d.pack_id==1? true:false"
                                       classes=" p-2   min-w-[5rem]"
                                       v-model="d.weight"
                                       autocomplete="weight"
                                       :error="form.errors[`products.${idx}.weight`]">

                            </TextInput>


                          </td>
                          <td class="px-2 py-4    ">
                            <div v-if="form.order_type==__('internal')" class=" font-semibold ">{{
                                asPrice(d.price)
                              }}
                            </div>
                            <TextInput v-else
                                       :id="`price${d.id}`"
                                       type="number"
                                       :placeholder="``"
                                       classes=" p-2   min-w-[5rem]"
                                       v-model="d.price"
                                       autocomplete="price"
                                       :error="form.errors[`products.${idx}.price`]">

                            </TextInput>
                          </td>


                          <td class="px-2 py-4   ">
                            <div v-if="form.order_type==__('internal')" class="flex items-center font-semibold ">{{
                                d.in_repo ? parseFloat(d.in_repo) : 0
                              }}/
                              <TextInput
                                  :id="`qty${d.id}`"
                                  type="number"
                                  :placeholder="``"
                                  classes=" p-0 max-w-[5rem]"
                                  v-model="d.qty"
                                  autocomplete="in_repo"
                                  :error="form.errors[`products.${idx}.qty`]">

                              </TextInput>
                            </div>
                            <TextInput v-else
                                       :id="`qty${d.id}`"
                                       type="number"
                                       :placeholder="``"
                                       classes=" p-2   min-w-[5rem]"
                                       v-model="d.qty"
                                       autocomplete="in_repo"
                                       :error="form.errors[`products.${idx}.qty`]">

                            </TextInput>
                          </td>

                          <td class="px-2 py-4">
                            <!-- Actions Group -->
                            <div
                                class=" inline-flex rounded-md shadow-sm transition duration-150 ease-in-out hover:bg-primary-600 hover:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.3),0_4px_18px_0_rgba(59,113,202,0.2)] focus:bg-primary-600 focus:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.3),0_4px_18px_0_rgba(59,113,202,0.2)] focus:outline-none focus:ring-0 active:bg-primary-700 active:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.3),0_4px_18px_0_rgba(59,113,202,0.2)]"
                                role="group">
                              <PrimaryButton type="button"
                                             @click="form.products.splice(idx,1);$refs.variationSelector.selecteds.splice(idx,1) "
                                             class="bg-red-500 hover:bg-red-400 text-sm  ms-auto">
                                <TrashIcon class="w-4 h-4 "/>
                              </PrimaryButton>

                            </div>
                          </td>
                        </tr>

                        </tbody>
                      </table>
                      <PrimaryButton v-if="form.order_type==__('external')" type="button"
                                     @click="form.products.push({})"
                                     class="bg-green-500 hover:bg-green-400 text-sm  my-2">
                        <PlusIcon class="w-4 h-4 mx-4"/>
                      </PrimaryButton>
                    </div>

                  </div>

                </div>

                <div class="flex flex-col space-y-2 text-sm text-gray-600 my-2 p-2 border rounded">
                  <div class="flex items-center">
                    <div>{{ __('count') }}:</div>
                    <div class="font-semibold mx-1">{{
                        asPrice(mySum(form.products.map(e => parseFloat(e.qty))))
                      }}
                    </div>
                  </div>
                  <div class="flex items-center">
                    <div>{{ __('price') }}:</div>
                    <div class="font-semibold mx-1">{{ asPrice(mySum(form.products.map(e => e.qty * e.price))) }}</div>
                  </div>
                  <div class="flex items-center">
                    <div>{{ __('shipping_price') }}:</div>
                    <TextInput
                        id="shipping_price"
                        type="number"
                        placeholder=""
                        classes=" p-1 mx-1   "
                        v-model="form.total_shipping_price"
                        autocomplete="total_shipping_price"
                        :error="form.errors.total_shipping_price">
                    </TextInput>
                  </div>
                  <div class="flex items-center">
                    <div>{{ __('discount') }}:</div>
                    <TextInput
                        id="discount"
                        type="number"
                        placeholder=""
                        classes=" p-1 mx-1   "
                        v-model="form.total_discount"
                        autocomplete="total_discount"
                        :error="form.errors.total_discount">
                    </TextInput>
                  </div>
                  <div class="flex items-center border-t py-2">
                    <div class="font-bold">{{ __('sum') }}:</div>
                    <div class="font-semibold mx-1">{{
                        asPrice(mySum([mySum(form.products.map(e => e.qty * e.price)), Math.abs(form.total_shipping_price), -Math.abs(form.total_discount)]))
                      }}
                    </div>
                  </div>
                </div>
              </div>


              <div class="my-2">
                <Selector ref="statusSelector" v-model="form.status"
                          :data="$page.props.statuses.map((e)=>{return {id:e,name:e}})"
                          :error="form.errors.status"
                          :label="__('status')"
                          id="status">
                  <template v-slot:append>
                    <div class="  p-3">
                      <Squares2X2Icon class="h-5 w-5"/>
                    </div>
                  </template>
                </Selector>
              </div>


              <div v-if="form.progress" class="shadow w-full bg-grey-light m-2   bg-gray-200 rounded-full">
                <div
                    class=" bg-primary rounded  text-xs leading-none py-[.1rem] text-center text-white duration-300 "
                    :class="{' animate-pulse': form.progress.percentage <100}"
                    :style="`width: ${form.progress.percentage }%`">
                  <span class="animate-bounce">{{ form.progress.percentage }}</span>
                </div>
              </div>

              <div class="    mt-4">

                <PrimaryButton @click="submit" type="button" class="w-full flex items-center justify-center"
                               :class="{ 'opacity-25': form.processing }"
                               :disabled="form.processing">
                  <LoadingIcon class="w-4 h-4 mx-3 " v-if="  form.processing"/>
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


export default {

  data() {
    return {
      orderFrom: [this.__('internal'), this.__('external')],
      form: useForm({

        status: 'pending',
        to_repo_id: null,
        from_repo_id: null,
        from_repo: null,
        order_type: this.__('internal'),
        pay_timeout: this.$page.props.pay_timeout,
        products: [],
        shipping_method_id: null,
        total_discount: 0,
        total_shipping_price: 0,
        from_address: null,
        from_province_id: null,
        from_county_id: null,
        from_district_id: null,
        from_lat: null,
        from_lon: null,
        from_location: null,
        from_postal_code: null,
        from_fullname: null,
        from_phone: null,
      }),

    }
  },
  components: {
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

  },
  mounted() {
    // this.log(this.$page.props)


  },
  watch: {
    form(_new, _old) {


    }
  },
  methods: {
    updateAddress(address) {
      address = address || {};
      this.$nextTick(() => {
        this.form.from_address = address.address;
        this.form.from_province_id = address.province_id;
        this.form.from_county_id = address.county_id;
        this.form.from_district_id = address.district_id;
        this.form.from_lat = address.lat;
        this.form.from_lon = address.lon;
        this.form.from_location = address.lat && address.lon ? `${address.lat},${address.lon}` : null;
        this.form.from_postal_code = this.f2e(address.postal_code);
        this.form.from_fullname = address.receiver_fullname;
        this.form.from_phone = address.receiver_phone;

        this.$refs.addressSelector.preload(address);
      })

    },
    submit() {
      // this.img = this.$refs.imageCropper.getCroppedData();

      this.form.clearErrors();
      // this.isLoading(true, this.form.progress ? this.form.progress.percentage : null);

      this.form.post(route('admin.panel.repository.order.create'), {
        preserveScroll: false,

        onSuccess: (data) => {

          if (this.$page.props.flash.status)
            this.showAlert(this.$page.props.flash.status, this.$page.props.flash.message);

          // else {
          //   this.showAlert(this.$page.props.flash.status, this.$page.props.flash.message);
          //   this.form.reset();
          // }
        },
        onError: () => {
          this.showToast('danger', Object.values(this.form.errors).join("<br/>"));
        },
        onFinish: (data) => {
          // this.isLoading(false,);
        },
      });
    }
  },

}
</script>
