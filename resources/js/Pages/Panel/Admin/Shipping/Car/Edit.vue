<template>

  <Panel>
    <template v-slot:header>
      <title>{{__('edit_car')}}</title>
    </template>


    <template v-slot:content>
      <!-- Content header -->
      <div
          class="flex items-center justify-start px-4 py-2 text-primary-500 border-b md:py-4">
        <FolderPlusIcon class="h-7 w-7 mx-3"/>

        <h1 class="text-2xl font-semibold">{{ __('edit_car') }}</h1>

      </div>


      <div class="px-2  md:px-4">

        <div
            class="    mx-auto md:max-w-2xl   mt-6 px-2 md:px-4 py-4 bg-white shadow-md overflow-hidden  rounded-lg  ">


          <div
              class="flex flex-col mx-2   col-span-2 w-full     px-2"
          >
            <div class="flex-col   m-2 items-center rounded-lg max-w-xs  w-full mx-auto    ">
              <div class="my-2">
                <ImageUploader :replace="true"
                               :preload="route('storage.cars')+`/${$page.props.data.id}.jpg`"
                               mode="edit" :for-id="$page.props.data.id"
                               :link="route('admin.panel.shipping.car.update')"
                               ref="imageCropper" :label="__('car_image_jpg')" :cropRatio="1.25" id="img"
                               height="10" class="grow "/>
                <InputError class="mt-1 " :message="form.errors.img"/>
              </div>

            </div>
            <form @submit.prevent="submit">

              <div class="my-2" v-if="$page.props.agency && $page.props.agency.level<3">
                <UserSelector :colsData="['name','phone','level']" :labelsData="['name','phone','type']"
                              :callback="{'level':getAgency}" :error="form.errors.agency_id"
                              :link="route('admin.panel.agency.search')" :label="__('agency')"
                              :id="'agency'" v-model:selected="form.agency_id" :preload="$page.props.data.agency">
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
              <div class="my-2">
                <UserSelector :colsData="['fullname','phone','agency' ]"
                              :labelsData="['name','phone','agency' ]"
                              :callback="{'level':getAgency,'agency':(e)=>`${e.name||''} (${e.id||''})`}"
                              :error="form.errors.driver_id"
                              :link="`${route('admin.panel.shipping.driver.search')}?agency_id=${form.agency_id}`"
                              :label="__('driver')"
                              :id="'driver'" v-model:selected="form.driver_id" :preload="$page.props.data.driver">
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
              <div class="my-2">
                <TextInput
                    id="name"
                    type="text"
                    :placeholder="__('name')"
                    classes="  "
                    v-model="form.name"
                    autocomplete="name"
                    :error="form.errors.name"
                >
                  <template v-slot:prepend>
                    <div class="p-3">
                      <Bars2Icon class="h-5 w-5"/>
                    </div>
                  </template>
                </TextInput>
              </div>

              <div class="my-2">
                <TextInput
                    id="plate_number"
                    type="text"
                    :placeholder="__('plate_number')"
                    classes="  "
                    v-model="form.plate_number"
                    autocomplete="plate_number"
                    :error="form.errors.plate_number"
                >
                  <template v-slot:prepend>
                    <div class="p-3">
                      <Bars2Icon class="h-5 w-5"/>
                    </div>
                  </template>
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
  LinkIcon,
  Squares2X2Icon,
  PencilSquareIcon,
  SignalIcon,
  ChatBubbleBottomCenterTextIcon,
  PencilIcon,
  XMarkIcon,
  PhoneIcon,
  IdentificationIcon,
  CreditCardIcon,
} from "@heroicons/vue/24/outline";
import {QuestionMarkCircleIcon,} from "@heroicons/vue/24/solid";
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import LoadingIcon from "@/Components/LoadingIcon.vue";
import ImageUploader from "@/Components/ImageUploader.vue";
import Selector from "@/Components/Selector.vue";
import UserSelector from "@/Components/UserSelector.vue";


export default {

  data() {
    return {
      data: this.$page.props.data || {},
      form: useForm({
        id: null,
        agency_id: null,
        car_id: null,
        driver_id: null,
        name: null,
        plate_number: null,
        uploading: false,


      }),
      img: null,
    }
  },
  components: {
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
    UserIcon,
    EyeIcon,
    Checkbox,
    FolderPlusIcon,
    Bars2Icon,
    LinkIcon,
    QuestionMarkCircleIcon,
    Selector,
    Squares2X2Icon,
    PencilSquareIcon,
    SignalIcon,
    ChatBubbleBottomCenterTextIcon,
    PencilIcon,
    UserSelector,
    XMarkIcon,
    PhoneIcon,
    IdentificationIcon,
    CreditCardIcon,
  },
  created() {

  },
  mounted() {

    // console.log(this.data);


    this.form.id = this.data.id;
    this.form.agency_id = this.data.agency_id;
    this.form.driver_id = this.data.driver_id;
    this.form.name = this.data.name;
    this.form.plate_number = this.data.plate_number;

  },
  methods: {


    submit() {


      // this.form.category_id = this.$refs.categorySelector.selected;
      this.form.clearErrors();

      // this.isLoading(true, this.form.progress ? this.form.progress.percentage : null);
      // this.images = [];
      // for (let i = 0; i < this.$page.props.max_images_limit; i++) {
      //   let tmp = this.$refs.imageCropper[i].getCroppedData();
      //   if (tmp) this.images.push(tmp);
      // }
      this.form.patch(route('admin.panel.shipping.car.update'), {
        preserveScroll: false,

        onSuccess: (data) => {
          if (this.$page.props.flash.status)
            this.showAlert(this.$page.props.flash.status, this.$page.props.flash.message);

        },
        onError: () => {
          this.showToast('danger', Object.values(this.form.errors).join("<br/>"));
        },
        onFinish: (data) => {
          // this.isLoading(false,);
          if (this.$page.props.flash.status)
            this.showAlert(this.$page.props.flash.status, this.$page.props.flash.message);
        },
      });
    },

  },

}
</script>
