<template>

  <Panel>
    <template v-slot:header>
      <title>{{__('new_product')}}</title>
    </template>


    <template v-slot:content>
      <!-- Content header -->
      <div
          class="flex items-center justify-start px-4 py-2 text-primary-500 border-b md:py-4">
        <FolderPlusIcon class="h-7 w-7 mx-3"/>

        <h1 class="text-2xl font-semibold">{{ __('new_product') }}</h1>

      </div>


      <div class="px-2  md:px-4">

        <div
            class="    mx-auto md:max-w-2xl   mt-6 px-2 md:px-4 py-4 bg-white shadow-md overflow-hidden  rounded-lg  ">


          <div
              class="flex flex-col mx-2   col-span-2 w-full     px-2"
          >
            <div class="flex-col   m-2 items-center rounded-lg max-w-xs  w-full mx-auto    ">
              <div class="my-2">
                <ImageUploader ref="imageCropper" :label="__('product_image_jpg')" id="img"
                               height="10" class="grow " :crop-ratio="null"/>
                <InputError class="mt-1 " :message="form.errors.img"/>
              </div>

            </div>
            <form @submit.prevent="submit">

              <div class="my-2">

                <UserSelector :multi="true" :colsData="['id','name','phone','agency_id']"
                              :labelsData="['id','name','phone','agency_id']"
                              :callback="{'level':getAgency}" :error="null"
                              :link="route('admin.panel.repository.search')+(`?status=active` )"
                              :label="__('repositories')"
                              :id="'repository'" v-model:selected="form.repo_ids" :preload="null">
                  <template v-slot:selector="props">
                    <div v-if="(props.selectedText || []).length==0" :class=" 'py-2'"
                         class=" px-4 border border-gray-300 rounded hover:bg-gray-100 cursor-pointer flex items-center ">
                      <div class="grow">
                        {{ __('select') }}
                      </div>
                    </div>
                    <template v-for="(text,idx) in props.selectedText">
                      <div :class=" 'py-2 my-1'"
                           class=" px-4 border border-gray-300 rounded hover:bg-gray-100 cursor-pointer flex items-center ">
                        <div class="grow">
                          {{ text }}
                        </div>
                        <div
                            class="bg-danger rounded p-2   cursor-pointer text-white hover:bg-danger-400"
                            @click.stop="props.clear(props.selectedItem[idx]  )">
                          <XMarkIcon class="w-5 h-5"/>

                        </div>
                      </div>
                      <InputError
                          :message="form.errors && form.errors[`repo_ids.${idx}`]?form.errors[`repo_ids.${idx}`]:null"/>
                    </template>
                  </template>
                </UserSelector>
              </div>
              <Selector ref="productSelector" v-model="form.product_id"
                        :data="$page.props.products"
                        :error="form.errors.product_id"
                        @change="el=>form.name=$page.props.products.filter((e)=>e.id==form.product_id)[0].name "
                        :label="__('product')" classes=""
                        :id="`product_id`">

              </Selector>
              <div class="my-2">
                <TextInput
                    :id="`name`"
                    type="name"
                    :placeholder="`${__('name')}`"
                    classes=" p-2   min-w-[5rem]"
                    v-model="form.name"
                    autocomplete="name"
                    :error="form.errors.name">

                </TextInput>
              </div>

              <div class="my-2">
                <Selector ref="gradeSelector" v-model="form.grade"
                          :data="$page.props.grades.map(e=>{return{id:e,name:e}})"
                          :error="form.errors.grade"
                          :label="__('grade')" classes=""
                          :id="`grade`">

                </Selector>
              </div>
              <div class="my-2">
                <Selector ref="packSelector" v-model="form.pack_id"
                          :data="$page.props.packs"
                          @change="($e)=> {if(form.pack_id==1)form.weight=1}"
                          :error="form.errors.pack_id"
                          :label="__('pack')" classes=""
                          :id="`pack`">

                </Selector>
              </div>

              <div class="my-2">
                <TextInput
                    :id="`weight`"
                    type="number"
                    :placeholder="`${__('pack_weight')} (${__('kg')})`"
                    :disabled="form.pack_id==1? true:false"
                    classes=" p-2   min-w-[5rem]"
                    v-model="form.weight"
                    autocomplete="weight"
                    :error="form.errors.weight">

                </TextInput>
              </div>

              <div class="my-2">
                <TextInput
                    :id="`price`"
                    type="number"
                    :placeholder="form.pack_id==1?__('kg_price'):`${__('pack_price')} (${__('currency')})`"
                    classes=" p-2   min-w-[5rem]"
                    v-model="form.price"
                    autocomplete="price"
                    :error="form.errors.price">

                </TextInput>
              </div>

              <div class="my-2">
                <TextInput
                    :id="`in_repo`"
                    type="number"
                    :placeholder="`${__('repository_count')} (${form.pack_id==1?__('kg'):__('pack_count')})`"
                    classes="    "
                    v-model="form.in_repo"
                    autocomplete="in_repo"
                    :error="form.errors.in_repo">

                </TextInput>
              </div>

              <div class="my-2">
                <TextInput
                    :id="`in_shop`"
                    type="number"
                    :placeholder="`${__('shop_count')} (${form.pack_id==1?__('kg'):__('pack_count')})`"
                    classes=" "
                    v-model="form.in_shop"
                    autocomplete="in_shop"
                    :error="form.errors.in_shop">

                </TextInput>
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


export default {

  data() {
    return {
      form: useForm({

        name: null,
        product_id: null,
        repo_ids: null,
        weight: null,
        grade: null,
        price: null,
        pack_id: null,
        in_repo: null,
        in_shop: null,
        uploading: false,

      }),
      img: null,

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

  },
  mounted() {
    // this.log(this.$page.props)

  },
  watch: {
    form(_new, _old) {


    }
  },
  methods: {

    submit() {
      // this.img = this.$refs.imageCropper.getCroppedData();
      this.img = this.$refs.imageCropper.getCroppedData();
      this.form.uploading = false;
      this.form.clearErrors();
      // this.isLoading(true, this.form.progress ? this.form.progress.percentage : null);

      this.form.post(route('admin.panel.variation.create'), {
        preserveScroll: false,

        onSuccess: (data) => {

          if (!this.form.uploading) {
            this.form.uploading = true;

            this.form.transform((data) => ({
              ...data,
              uploading: true,
              img: this.img,

            }))
                .post(route('admin.panel.variation.create'), {
                  preserveScroll: false,
                  onSuccess: (data) => {
                    // else {
                    if (this.$page.props.flash.status)
                      this.showAlert(this.$page.props.flash.status, this.$page.props.flash.message);
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
