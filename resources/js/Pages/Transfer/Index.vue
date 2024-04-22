<template>
  <Scaffold navbar-theme="dark">
    <template v-slot:header>
      <title>{{__('shop')}}</title>

    </template>

    <div class="relative  bg-gradient-to-t from-pink-300 via-purple-300 to-indigo-400">
      <!--Hero-->
      <div class="py-4         mx-auto    ">
        <div class="  px-3  sm:px-1  flex   flex-col md:flex-row items-center ">
          <!--                    Right Col-->
          <!--          <div class="  md:w-2/5 py-6 text-center">-->
          <!--            <img class="w-full xs:w-3/4 sm:w-3/4   mx-auto  z-50   " :src="heroImage"/>-->
          <!--          </div>-->
          <!--Left Col-->
          <div
              class="flex flex-col max-w-3xl text-white w-full   justify-center  mx-auto  text-center ">
            <h1 class="my-4 text-5xl font-bold leading-tight">

            </h1>
            <p class="leading-normal text-2xl mb-8" v-html="heroText">

            </p>
            <!--                        search-->

            <div class="  px-3    flex  items-stretch justify-center">
              <!--              <PrimaryButton class="mx-2 p-2 grow  ">{{ __('register_podcast') }}</PrimaryButton>-->
              <!--              <SecondaryButton @click="$inertia.visit(route('panel.podcast.create'))" class="mx-2 p-2  ">-->
              <!--                {{ __('register_podcast') }}-->
              <!--              </SecondaryButton>-->
              <SearchInput v-model="params.search" @search="getData(0)"/>
            </div>
          </div>

        </div>
      </div>
      <!--wave-->
      <div v-if="false" class="absolute  bottom-0 start-0 end-0">
        <svg viewBox="0 0 1428 174" xmlns="http://www.w3.org/2000/svg"
             xmlns:xlink="http://www.w3.org/1999/xlink">
          <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
            <g transform="translate(-2.000000, 44.000000)" fill="#FFFFFF" fill-rule="nonzero">
              <path
                  d="M0,0 C90.7283404,0.927527913 147.912752,27.187927 291.910178,59.9119003 C387.908462,81.7278826 543.605069,89.334785 759,82.7326078 C469.336065,156.254352 216.336065,153.6679 0,74.9732496"
                  opacity="0.100000001"></path>
              <path
                  d="M100,104.708498 C277.413333,72.2345949 426.147877,52.5246657 546.203633,45.5787101 C666.259389,38.6327546 810.524845,41.7979068 979,55.0741668 C931.069965,56.122511 810.303266,74.8455141 616.699903,111.243176 C423.096539,147.640838 250.863238,145.462612 100,104.708498 Z"
                  opacity="0.100000001"
              ></path>
              <path
                  d="M1046,51.6521276 C1130.83045,29.328812 1279.08318,17.607883 1439,40.1656806 L1439,120 C1271.17211,77.9435312 1140.17211,55.1609071 1046,51.6521276 Z"
                  id="Path-4" opacity="0.200000003"></path>
            </g>
            <g transform="translate(-4.000000, 76.000000)" fill="#FFFFFF" fill-rule="nonzero">
              <path
                  d="M0.457,34.035 C57.086,53.198 98.208,65.809 123.822,71.865 C181.454,85.495 234.295,90.29 272.033,93.459 C311.355,96.759 396.635,95.801 461.025,91.663 C486.76,90.01 518.727,86.372 556.926,80.752 C595.747,74.596 622.372,70.008 636.799,66.991 C663.913,61.324 712.501,49.503 727.605,46.128 C780.47,34.317 818.839,22.532 856.324,15.904 C922.689,4.169 955.676,2.522 1011.185,0.432 C1060.705,1.477 1097.39,3.129 1121.236,5.387 C1161.703,9.219 1208.621,17.821 1235.4,22.304 C1285.855,30.748 1354.351,47.432 1440.886,72.354 L1441.191,104.352 L1.121,104.031 L0.457,34.035 Z"
              ></path>
            </g>
          </g>
        </svg>
      </div>
    </div>

    <section class="flex justify-center  p-1 max-w-8xl  py-8  ">
      <!--      <div class=" w-80 p-3   mx-2  bg-white rounded-lg     lg:flex md:hidden sm:hidden xs:hidden"></div>-->


      <div
          class="   grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-3 xl:grid-cols-4  gap-4     max-w-6xl">
        <Link v-for="(d,idx) in data" :href="route(d.item_type,d.id)"
              class="max-w-xs flex-col relative items-stretch cursor-pointer hover:scale-[101%] duration-300 rounded-lg overflow-hidden shadow-lg">
          <Image :src="route(`storage.${d.item_type}s`)+`/${d.id}.jpg`"
                 classes="object-cover rounded-lg h-48   w-full"/>
          <div class="absolute text-gray-500 rounded-full p-4 mx-4 top-[10rem] bg-white   shadow-lg">
            <MicrophoneIcon v-if="d.item_type=='podcast'" class="w-5 h-5 "/>
            <PlayIcon v-if="d.item_type=='video'" class="w-5 h-5 "/>
            <PencilIcon v-if="d.item_type=='article'" class="w-5 h-5 "/>
            <PhotoIcon v-if="d.item_type=='banner'" class="w-5 h-5 "/>
          </div>
          <div
              class="absolute text-gray-500 rounded-lg text-white bg-rose-500 p-1 px-2 m-2  end-0 top-0     shadow-lg">
            {{ `${__(d.type)}` }}
            {{ d.type == 'auction' ? '⌛️' : d.type == 'private' ? '🔒' : '💵' }}
          </div>
          <div class="p-2 mt-4  text-gray-700">{{ cropText(d.item_name, 30) }}</div>
          <div class="px-2 py-2 text-sm   text-gray-400">
            <span class="text-gray-500">{{ __('expire') }}: </span>
            <span>  {{ toShamsi(d.expires_at, true) || __('unlimited') }}</span>
          </div>
          <div class="px-2 py-2 flex items-center justify-center text-xl   text-primary ">
            <span>  {{ asPrice(d.price,) }}</span>
            <span class="text-gray-500 text-sm mx-2 ">{{ __('currency') }}  </span>
          </div>

          <div class="flex justify-between  items-center p-4 text-sm text-gray-500">
            <div class="flex items-center grow">
              <!--              <EyeIcon class="w-4 h-4"/>-->
              <button type="button" @click.prevent="this.modal.show();getItemAuction(idx,d)"
                      data-te-toggle="modal"
                      data-te-target="#buyModal"
                      data-te-ripple-init
                      class="grow flex justify-center  items-center   py-2 cursor-pointer px-2 bg-success hover:bg-success-400 text-white rounded">
                {{ __('buy') }}

                <ShoppingBagIcon class="w-5 h-5 mx-2 "/>
              </button>
            </div>
            <div class=" border-s   py-4"></div>


          </div>
        </Link>
      </div>
    </section>
    <!-- Modal -->
    <div
        data-te-modal-init
        class="fixed left-0 top-0 backdrop-blur z-[1055] hidden h-full w-full overflow-y-auto overflow-x-hidden outline-none"
        id="buyModal"
        tabindex="-1"
        aria-labelledby="buyModalLabel"
        aria-hidden="true">
      <div
          data-te-modal-dialog-ref
          class="pointer-events-none relative w-auto translate-y-[-50px] opacity-0 transition-all duration-300 ease-in-out min-[576px]:mx-auto min-[576px]:mt-7 px-2 sm:px-4 md:px8 min-[576px]:max-w-5xl">
        <div
            class="min-[576px]:shadow-[0_0.5rem_1rem_rgba(#000, 0.15)] pointer-events-auto relative flex w-full flex-col rounded-md border-none bg-white bg-clip-padding text-current shadow-lg outline-none">
          <div
              class="flex flex-shrink-0 items-center justify-between rounded-t-md border-b-2 border-neutral-100 border-opacity-100 p-4">
            <!--Modal title-->
            <h5
                class="text-xl font-medium leading-normal text-neutral-800"
                id="buyModalLabel">
              {{ __('transfer') }}
            </h5>
            <!--Close button-->
            <button
                :class="`text-danger`"
                type="button"
                class="box-content rounded-none border-none hover:no-underline hover:opacity-75 focus:opacity-100 focus:shadow-none focus:outline-none"
                data-te-modal-dismiss
                aria-label="Close">
              <svg
                  xmlns="http://www.w3.org/2000/svg"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke-width="1.5"
                  stroke="currentColor"
                  class="h-6 w-6">
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>

          <!--Modal body-->
          <div v-show="selectedData" class="relative flex-auto p-4" data-te-modal-body-ref>
            <div
                class="flex items-center justify-start px-4 py-1 text-primary-500 border-b md:py-2">
              <!--              <FolderPlusIcon class="h-7 w-7 mx-3"/>-->

              <h5 class="text-lg font-semibold"> {{ selectedData.item_name }}</h5>

            </div>


            <div class="px-2  md:px-4">

              <div
                  class="    mx-auto md:max-w-3xl   mt-1 px-2 md:px-4 py-4 bg-white      rounded-lg  ">


                <div
                    class="flex flex-col mx-2   col-span-2 w-full     px-2"
                >

                  <LoadingIcon class="w-8 h-8  fill-primary-500 mx-auto" v-if=" modalLoading"/>
                  <ArrowPathIcon v-if="!selectedData.id && !modalLoading" @click.prevent="getItemAuction(selectedData)"
                                 class="w-10 mx-auto  h-10 cursor-pointer hover:scale-[110%]"/>
                  <form v-if=" selectedData.id && !modalLoading" class=" ">

                    <div class="flex flex-col mb-4">
                      <div class="flex items-center">
                        <div class="">{{ __('sell_type') }}:</div>
                        <div class="mx-2 text-primary">{{ __(selectedData.type) }}</div>
                      </div>
                      <div class="flex items-center">
                        <div class="">{{ __('item_type') }}:</div>
                        <div class="mx-2 text-primary">{{ __(selectedData.item_type) }}</div>
                      </div>
                      <div class="flex items-center">
                        <div class="">{{ __('id') }}:</div>
                        <div class="mx-2 text-primary">{{ selectedData.id }}</div>
                      </div>
                      <div class="flex items-center">
                        <div class="">{{ __('status') }}:</div>
                        <div class="mx-2 text-primary">{{ __(selectedData.status) }}</div>
                      </div>
                      <div v-if="selectedData.owner" class="flex items-center">
                        <div class="">{{ __('owner') }}:</div>
                        <div class="mx-2 text-primary">
                          {{ selectedData.owner.fullname }}
                          {{ selectedData.owner.phone }}
                        </div>
                      </div>
                      <div class="flex items-center">
                        <div class="">{{ __('price') }}:</div>
                        <div class="mx-2 text-primary">{{ asPrice(selectedData.price) }} {{ __('currency') }}</div>
                      </div>
                    </div>

                    <div v-if="selectedData.type=='auction' && selectedData.auction"
                         class="  px-2 md:px-4 py-1  border  overflow-hidden  rounded-lg  ">

                      <div v-if="selectedData.type=='auction' && selectedData.auction.length==0">
                        {{ __('no_auction_suggestion_yet') }}
                      </div>
                      <div v-else>
                        <div class="text-lg pb-3">{{ __('suggestions') }}</div>
                        <div v-for="(sug,idx) in selectedData.auction" class="flex flex-col py-2">

                          <div class="flex justify-center">
                <span class="rounded-s p-2"
                      :class="idx==0?'bg-green-200 text-green-700':'bg-primary-200 text-primary-700'">
                  {{ asPrice(sug.price) }}
                  {{ __('currency') }}
                </span>
                            <span class="rounded-e p-2 px-6 text-white  "
                                  :class="idx==0?'bg-green-500':'bg-primary-500'">
                      {{ toShamsi(sug.created_at, true) }}
                  </span>


                          </div>
                        </div>
                      </div>

                    </div>
                    <div v-if="selectedData.type=='auction' || selectedData.type=='private'"
                         class=" my-2 p-2 md:px-4 py-1  border     rounded-lg  ">
                      <div class="my-2" v-if="selectedData.type=='auction'">
                        <TextInput
                            id="price"
                            type="number"
                            :placeholder="`${__('suggestion_price')} ( ${__('currency')} )`"
                            classes="  "
                            v-model="params.price"
                            autocomplete="price"
                            :error="params.errors.price"
                        >
                          <template v-slot:prepend>
                            <div class="p-3">
                              <BanknotesIcon class="h-5 w-5"/>
                            </div>
                          </template>
                        </TextInput>
                      </div>
                      <div class="my-2" v-if="selectedData.type=='private'">
                        <TextInput
                            id="password"
                            type="text"
                            :placeholder="`${__('transfer_password')} ${__('transfer_password_label')}` "
                            classes=" "
                            v-model="params.password"
                            autocomplete="password"
                            :error="params.errors.password"
                        >
                          <template v-slot:prepend>
                            <div class="p-3">
                              <KeyIcon class="h-5 w-5"/>
                            </div>
                          </template>
                        </TextInput>

                      </div>
                    </div>

                    <div class="    mt-4">

                      <PrimaryButton @click.prevent="modal.hide(); showDialog('danger',selectedData.type == 'auction' ? __('subtract_wallet_if_win_auction') : __('subtract_wallet'),selectedData.type == 'auction' ? __('register_suggestion') : __('buy'),transfer,
                      {
                          id: selectedData.id,
                          price:  params.price,
                          password:  params.password
                        })" class="w-full  "
                                     :class="{ 'opacity-25': loading}"
                                     :disabled="loading">
                        <LoadingIcon class="w-4 h-4 mx-3 " v-if="  loading"/>
                        <span class=" text-lg  ">  {{
                            selectedData.type == 'auction' ? __('register_suggestion') : __('buy')
                          }}</span>
                      </PrimaryButton>

                    </div>

                  </form>
                </div>


              </div>
            </div>
          </div>


        </div>
      </div>
    </div>

    <LoadingIcon v-show="loading" ref="loader" type="linear"/>
  </Scaffold>

</template>

<script>
import LoadingIcon from "@/Components/LoadingIcon.vue";
import Image from "@/Components/Image.vue";
import Scaffold from "@/Layouts/Scaffold.vue";
import {Head, Link} from '@inertiajs/vue3';
import heroImage from '@/../images/hero.jpg';
import {loadScript} from "vue-plugin-load-script";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import {EyeIcon} from "@heroicons/vue/24/outline";
import {
  MicrophoneIcon,
  PlayIcon,
  PencilIcon,
  PhotoIcon,
  ShoppingBagIcon,
  ArrowPathIcon,
  BanknotesIcon,
  KeyIcon,
} from "@heroicons/vue/24/solid";
import SearchInput from "@/Components/SearchInput.vue";
import TextInput from "@/Components/TextInput.vue";
import {Modal} from "tw-elements";

export default {
  data() {
    return {
      heroImage,
      loading: false,
      modalLoading: false,
      total: 0,
      data: [],
      selectedData: {},
      params: {
        page: 0,
        search: null,
        order_by: null,
        dir: null,
        errors: {},
        price: null,
        password: null,
      }
    }
  },
  props: ['heroText'],
  components: {
    TextInput,
    SearchInput,
    SecondaryButton,
    PrimaryButton,
    Scaffold,
    Head,
    LoadingIcon,
    Image,
    EyeIcon,
    Link,
    MicrophoneIcon,
    PlayIcon,
    PencilIcon,
    PhotoIcon,
    ShoppingBagIcon,
    ArrowPathIcon,
    BanknotesIcon,
    KeyIcon,
  },
  // mixins: [Mixin],
  setup(props) {

  }, mounted() {
    // this.setScroll(this.$refs.loader.$el);
    this.getData();
    const modalEl = document.getElementById('buyModal');
    this.modal = new Modal(modalEl);
  },
  methods: {
    getItemAuction(idx, data) {
      this.selectedData.item_name = data.item_name;
      this.selectedData.item_type = data.item_type;

      this.modalLoading = true;
      window.axios.get(route('transfer.show'), {
        params: {id: data.id}
      })
          .then((response) => {
            this.selectedData = response.data.data;
            // this.log(response.data.data);
          })
          .catch((error) => {
            this.error = this.getErrors(error);

            this.showToast('danger', this.error)
          })
          .finally(() => {
            this.modalLoading = false;
          });
    },
    getData(page) {
      if (page == 0) {
        this.params.page = 1;
        this.data = [];
      }

      if (this.total > 0 && this.total <= this.data.length) return;
      this.loading = true;

      window.axios.get(route('transfer.search'), {
        params: this.params
      })
          .then((response) => {
            this.data = this.data.concat(response.data.data);
            this.total = response.data.total;
            this.params.page = response.data.current_page + 1;

            // this.modal.show();
            // this.getItemAuction(1, this.data[1]);
          })
          .catch((error) => {
            this.error = this.getErrors(error);

            this.showToast('danger', this.error)
          })
          .finally(() => {
            // always executed
            this.loading = false;
          });
    },
    setScroll(el) {
      window.onscroll = () => {
//                    const {top, bottom, height} = this.loader.getBoundingClientRect();

        let top_of_element = el.offsetTop;
        let bottom_of_element = el.offsetTop + el.offsetHeight;
        let bottom_of_screen = window.pageYOffset + window.innerHeight;
        let top_of_screen = window.pageYOffset;

        if ((bottom_of_screen + 300 > top_of_element) && (top_of_screen < bottom_of_element + 200) && !this.loading) {

          this.getData();
          // scrolled = true;
//                        console.log('visible')
          // the element is visible, do something
        } else {
//                        console.log('invisible')
          // the element is not visible, do something else
        }
      };
    },
    transfer(params) {
      this.isLoading(true);

      window.axios.post(route('transfer.transfer'), params,
          {
            onUploadProgress: function (axiosProgressEvent) {
            },
            onDownloadProgress: function (axiosProgressEvent) {
            }
          })
          .then((response) => {
            if (response.data && response.data.message) {
              this.showToast('success', response.data.message);
              location.reload();
              // this.$inertia.visit('panel.transfer.index');
            }
          })

          .catch((error) => {
            this.error = this.getErrors(error);
            if (error.response && error.response.data) {

            }
            this.showToast('danger', this.error);
          })
          .finally(() => {
            // always executed
            this.isLoading(false);
          });
    },
    edit(params) {
      this.isLoading(true);
      params.id = data.id;
      params.item_type = data.item_type;
      params.item_id = data.item_id;
      window.axios.patch(route('transfer.update'), params,
          {
            onUploadProgress: function (axiosProgressEvent) {

            },

            onDownloadProgress: function (axiosProgressEvent) {

            }
          })
          .then((response) => {
            if (response.data && response.data.message) {
              this.showToast('success', response.data.message);

            }

            if (response.data.status) {
              this.status = response.data.status;
            }


          })

          .catch((error) => {
            this.error = this.getErrors(error);
            if (error.response && error.response.data) {


            }
            this.showToast('danger', this.error);
          })
          .finally(() => {
            // always executed
            this.isLoading(false);
          });
    },
  }

}
</script>
<style type="text/css">.turbo-progress-bar {
  position: fixed;
  display: block;
  top: 0;
  left: 0;
  height: 3px;
  background: #32CD32;
  z-index: 9999;
  transition: width 300ms ease-out,
  opacity 150ms 150ms ease-in;
  transform: translate3d(0, 0, 0);
}
</style>
