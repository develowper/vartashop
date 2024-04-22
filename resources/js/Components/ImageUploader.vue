<template>

  <div class="  h-fit ">

    <div class=" h-fit">

      <label :for="id"
             class=" text-center d-none  "> </label>

      <!--           :style="'width:'+(cropRatio*height)+'rem;height:'+height+'rem'"-->
      <div v-show="!croppedImage"
           class=" flex  h-full md:min-w-[150px]  items-center justify-center  uploader-container  rounded-lg p-3 "

           :id="'container-' + this.id"
           style="border:dashed; "
           role="form" @mouseover="uploader.classList.add('hover');"
           @dragover.prevent="uploader.classList.add('hover');"
           @dragleave.prevent="uploader.classList.remove('hover');"
           @mouseleave=" uploader.classList.remove('hover');"
           @drop.prevent="uploader.classList.remove('hover');filePreview($event  ) "
           @click="openFileChooser($event,id+'-file')">

        <div class="  ">
          <div class=" p-2  small text-center     ">
            {{ label }}
          </div>
        </div>

        <input v-show="false" :id="id+'-file'" class="w-full   " accept=".png, .jpg, .jpeg" type="file"
               :key="id"
               :name="id+'-file'" @input="  filePreview($event,id )"/>
        <input :id="id" class="col-12" :name="id" type="hidden"/>


      </div>


      <div v-show="croppedImage" class="cursor-pointer  rounded  flex flex-col justify-between   "
           :style="`width:${width}`"
           @click="modal.show();initCropper()"
      >

        <img v-show="croppedImage" class="flex max-w-full rounded    " :class="classes"
             @error="errorImage"
             :src="croppedImage"

             alt=""/>

        <div class="flex my-1 w-full " role="group">
          <div v-if="mode=='edit'"
               class="text-center flex rounded  grow  cursor-pointer hover:bg-danger-600  p-2 bg-danger text-white grow "
               :title="__('remove')"
               @click.stop="!preload || replace ? clearImage():  showDialog('danger',__('remove_image?'), __('remove') , removeImage )  ;">
            <XMarkIcon class="w-4 h-4  mx-auto text-white  text-white" v-if="!removing"/>
            <LoadingIcon class="w-4 h-4 mx-auto " v-if="removing"/>
          </div>
          <div v-if=" false &&  mode=='edit'"
               class="cursor-pointer grow rounded-e hover:bg-success-600  p-2 bg-success text-white grow"
               :title="__('upload')"
               @click="  showDialog('primary',__('new_file_replace'), __('upload') , uploadImage )">
            <CheckIcon class="w-4 h-4  mx-auto text-white   text-white" v-if="!uploading"/>
            <LoadingIcon class="w-4 h-4 mx-auto " v-if="uploading"/>
          </div>
          <div v-if="false && mode!='edit'"
               class="cursor-pointer hover:bg-success-600 p-2 bg-success text-white grow rounded-s "
               :title="__('crop')"
               @click="cropImage()">
            <CheckIcon class="w-4 h-4   mx-auto text-white bg-success text-white"/>
          </div>
          <div v-if="mode!='edit'"
               class="  cursor-pointer bg-danger hover:bg-danger-600 p-2  text-white grow rounded-lg"
               :title="__('remove')"
               @click="refresh()">
            <XMarkIcon class="w-4 h-4  mx-auto text-white   text-white"/>
          </div>
        </div>
      </div>

    </div>


    <!-- Modal -->
    <div
        data-te-modal-init
        class="fixed left-0 top-0 z-[1055] hidden h-full w-full overflow-y-auto overflow-x-hidden outline-none"
        :id="`cropperModal${id}`"
        tabindex="-1"
        :aria-labelledby="`cropperModal${id}Label`"
        aria-hidden="true">
      <div
          data-te-modal-dialog-ref
          class="pointer-events-none relative w-auto translate-y-[-50px] opacity-0 transition-all duration-300 ease-in-out min-[0px]:m-0 min-[0px]:h-full min-[0px]:max-w-none">
        <div
            class="pointer-events-auto relative flex w-full flex-col rounded-md bg-white bg-clip-padding text-current shadow-lg outline-none dark:bg-neutral-600 min-[0px]:h-full min-[0px]:rounded-none min-[0px]:border-0">
          <div
              class="flex flex-shrink-0 items-center justify-between rounded-t-md border-b-2 border-neutral-100 border-opacity-100 p-4 dark:border-opacity-50 min-[0px]:rounded-none">
            <!-- Modal title -->
            <h5
                class="flex text-xl font-medium leading-normal text-neutral-800 dark:text-neutral-200"
                :id="`cropperModal${id}Label`">
              <PhotoIcon class="h-7 w-7 mx-3"/>
              {{ __('crop_image') }}
            </h5>
            <!-- Close button -->
            <button @click="modal.hide()"
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

          <!-- Modal body -->
          <div class="relative p-4 pb-0 min-[0px]:overflow-y-auto">

            <div v-show="mainDoc" class="h-full  rounded-lg flex flex-col justify-between   "
                 :style="`width:${width}`">

              <img :id="'img-'+id" class="flex max-w-full    " :class="classes"
                   @error="errorImage"
                   @load="  uploadContainer.classList.add('d-none');initCropper()"
                   :src="mainDoc"

                   alt=""/>

            </div>


          </div>

          <!-- Modal footer -->
          <div
              class="mt-auto flex flex-shrink-0 flex-wrap items-center justify-end rounded-b-md border-t-2 border-neutral-100 border-opacity-100 p-4 dark:border-opacity-50 min-[0px]:rounded-none">
            <div class="flex my-1 w-full " role="group">
              <div v-if="mode=='edit'"
                   class="text-center flex rounded-s grow  cursor-pointer hover:bg-danger-600  p-3 bg-danger text-white grow "
                   :title="__('remove')"
                   @click="!preload || replace ? clearImage('modal',$event):  showDialog('danger',__('remove_image?'), __('remove') , removeImage )  ;">
                <XMarkIcon class="w-4 h-4  mx-auto text-white  text-white" v-if="!removing"/>
                <LoadingIcon class="w-4 h-4 mx-auto " v-if="removing"/>
              </div>
              <div v-if="  mode=='edit'"
                   class="cursor-pointer grow rounded-e hover:bg-success-600  p-3 bg-success text-white grow"
                   :title="__('upload')"
                   @click="  showDialog('primary',__('new_file_replace'), __('upload') , uploadImage )">
                <CheckIcon class="w-4 h-4  mx-auto text-white   text-white" v-if="!uploading"/>
                <LoadingIcon class="w-4 h-4 mx-auto " v-if="uploading"/>
              </div>
              <div v-if=" mode!='edit'"
                   class="cursor-pointer hover:bg-success-600 p-3 bg-success text-white grow rounded-s "
                   :title="__('crop')"
                   @click="cropImage()">
                <CheckIcon class="w-4 h-4   mx-auto text-white  text-white"/>
              </div>
              <div v-if="mode!='edit'"
                   class="  cursor-pointer bg-danger hover:bg-danger-600 p-3  text-white grow rounded-e"
                   :title="__('remove')"
                   @click="refresh()">
                <XMarkIcon class="w-4 h-4  mx-auto text-white   text-white"/>
              </div>
            </div>

          </div>
        </div>
      </div>
    </div>

  </div>


</template>

<script>


import {Modal} from "tw-elements";

let input = null;

let self;
let canvas;


import Cropper from 'cropperjs';
import Tooltip from "@/Components/Tooltip.vue";
import {
  XMarkIcon,
  CheckIcon,
} from "@heroicons/vue/24/solid";
import {
  PhotoIcon,
} from "@heroicons/vue/24/outline";
import LoadingIcon from "@/Components/LoadingIcon.vue";

export default {


  props: ['link', 'classes', 'id', 'type', 'replace', 'height', 'width', 'required', 'preload', 'label', 'mode', 'forId', 'callback', 'images', 'limit', 'cropRatio'],
  components: {
    Tooltip, XMarkIcon, CheckIcon,
    LoadingIcon,
    PhotoIcon,
  },
  data() {
    return {
      modal: null,
      componentKey: 1,
      star: null,
      mainDoc: this.preload,
      doc: this.preload,
      croppedImage: this.preload,
      reader: null,
      cropper: null,

      loading: false,
      uploading: false,


      creating: false,
      removing: false,
      uploader: null,

      errors: "",
    }
  },
  watch: {
    doc: function (val) {
//                console.log(val);
      if (val) {
//                    this.initCropper();

      } else {

      }
    },
    loading: function (val) {
//                console.log(val);
      if (val) {
        document.querySelector('#loading').classList.remove('d-none');
      } else
        document.querySelector('#loading').classList.add('d-none');
    },
  },
  beforeDestroy() {
//            console.log("beforeDestroy")
  },
  computed: {
//            get_noe_faza: () => {
//                return Vue.noe_faza;
//            }
  },
  mounted() {
    self = this;
    this.image = document.getElementById('img-' + this.id);
//            console.log(image);
//            $(".point-sw")

    this.uploader = document.querySelector('#' + this.id + '-file');
    this.uploadContainer = document.querySelector('#container-' + this.id);

    const modalEl = document.getElementById(`cropperModal${this.id}`);
    this.modal = new Modal(modalEl);
  }
  ,
  created() {

  }
  ,
  updated() {


    if (!this.creating) {
      this.initCropper();
//                console.log('update');
    }
//            this.AwesomeQRCode = AwesomeQRCode;
//            console.log(window.AwesomeQRCode)
  },
  beforeUpdate() {
  }
  ,
  methods: {

    errorImage() {

      this.doc = null;
      this.uploadContainer.classList.remove('d-none');

    },
    getCroppedData() { //input id=img value=cropped data
      //not upload file
      return this.croppedImage;
      document.querySelector("#" + this.id + '-file').value = null;
      if (!this.cropper) return null;
      let c = this.cropper.getCroppedCanvas();
      if (c) {
        document.querySelector('#' + this.id).value = c.toDataURL();
        return document.querySelector('#' + this.id).value;
      }
      return null

    },
    clearImage(_from, $event) {
      this.doc = null;
      // this.image.src = null;
      this.croppedImage = null;
      document.querySelector("#" + this.id + '-file').value = null;
      document.querySelector('#' + this.id).value = null;
      if (_from == 'modal') {
        // this.openFileChooser($event, this.id + '-file')
        this.modal.hide();

      }

      return null

    },
    refresh() {
      this.uploadContainer.classList.remove('d-none');
      this.doc = null;
      // this.image.src = null;
      this.croppedImage = null;
//                document.getElementById('img').value = null;
//                this.cropper.destroy();
//                this.componentKey++;
//                this.$forceUpdate();

      this.creating = false;
      this.initCropper();
      this.modal.hide();


    },
    cropImage() {
      this.loading = true;

      this.cropper.crop();
      this.loading = false;
      let img = this.cropper.getCroppedCanvas().toDataURL();
      this.croppedImage = img;
      if (this.mode === 'multi') {
        if (this.images.length >= this.limit) {
          window.showToast('danger', 'تعداد تصاویر بیش از حد مجاز است', onclick = null);
          return;
        }
        this.images.push({id: this.images.length, src: img});
        this.doc = null;

        this.initCropper();
      } else {
        document.querySelector('#' + self.id).value = img;
        this.showToast('success', 'تصویر آماده ارسال است', onclick = null);
      }
      this.modal.hide();
//                this.cropper.getCroppedCanvas().toBlob((blob) => {
//                    this.loading = false;
//                    if (blob) {
//
//
//                        $('#' + self.id).val(blob);
//
//                        window.showDialog('success', 'تصویر آماده ارسال است', onclick = null);
//                    }
//
//
//                });

    },
    removeImage() {
      this.removing = true;

      axios.patch(this.link, {
        'name': this.id,
        'id': this.forId,
        'cmnd': 'delete-img',
        'path': this.preload,
      },)
          .then((response) => {
//                        console.log(response);

            if (response.status === 200)
              window.location.reload();
            else {
              this.showToast('danger', response, onclick = null);

            }
          }).catch((error) => {
        this.errors = this.getErrors(error);
        this.showToast('danger', this.errors, onclick = null);
      }).finally(() => {
        this.removing = false;
      })
    }
    ,

    async uploadImage() {
//                console.log(this.$refs.dropdown.selected.length);
      this.uploading = true;
      this.isLoading(true);
      this.modal.hide();
      const canvas = this.cropper.getCroppedCanvas();
//                this.cropper.crop();
      if (!canvas)
        this.initCropper();
      let fd = {
        'name': this.id,
        'img': this.cropper.getCroppedCanvas().toDataURL(),
        'id': this.forId,
        'path': this.preload,
        'cmnd': 'upload-img',
      };
      axios.patch(this.link, fd,)
          .then((response) => {
//                        console.log(response);
            if (response.status === 200) {
              // window.location.reload();
              this.showToast('success', response.data.message, onclick = null);
              window.location.reload();
            } else {
              this.showToast('danger', response.data, onclick = null);
            }
          }).catch((error) => {
        this.errors = this.getErrors(error);
        this.showToast('danger', this.errors, onclick = null);

      }).finally(() => {
        this.uploading = false;
        this.isLoading(false);

      });


    },

    initCropper() {

//                Cropper.noConflict();
      if (this.cropper)
        this.cropper.destroy();

      this.cropper = new Cropper(this.image, {
//                    autoCrop: false,
        autoCropArea: 1,
        viewMode: 0, responsive: false,
//                    autoCrop: true,
//         style: {height: '200px', width: '100px' /*,'overflow-x': 'auto'*/},
        aspectRatio: this.cropRatio,
        initialAspectRatio: this.cropRatio,
        minContainerWidth: 150,
        // minCanvasWidth: 150,
        // minCropBoxWidth: 150,
        highlight: true,
        restore: true,
        cropBoxMovable: true,
        dragMode: 'move',
        zoomable: true,
//                    dragMode: 'move',
        cropBoxResizable: true,
        toggleDragModeOnDblclick: false,
        crop(event) {
//                        console.log('crop');
          this.creating = true;

        },
        cropend(event) {
//                        console.log('croped');
        },
        ready(e) {

//                        if (self.mode !== 'edit') {
          this.cropper.crop();

          document.querySelector('#' + this.id).value = this.cropper.getCroppedCanvas().toDataURL();
//                        }

          // this.cropper[method](argument1, , argument2, ..., argumentN);
//                        this.cropper.move(1, -1);

          // Allows chain composition
//                        this.cropper.move(1, -1).rotate(45).scale(1, -1);
        },
      });


    }
    ,

    openFileChooser(event, from) {
//                send fake click for browser file
      let image_input = document.getElementById(from);
      if (document.createEvent) {
        let evt = document.createEvent("MouseEvents");
        evt.initEvent("click", false, true);
        image_input.dispatchEvent(evt);

      } else {
        let evt = new Event("click", {"bubbles": false, "cancelable": true});
        image_input.dispatchEvent(evt);
      }
    }
    ,
    filePreview(event, id) {
      let file;
      this.modal.show();

      if (event.dataTransfer) {
        file = event.dataTransfer.files[0];
      } else if (event.target.files) {
        file = event.target.files[0];
      }
      event.target.value = '';
//                    console.log(files.length);
//                        console.log(files.length);
//                this.reader = new FileReader();
//                this.reader = new FileReader();

//                this.reader.onload = (e,  ) => {
////
//                    console.log(e);
//                    self.doc = e.target.result;
//
//                    self.loading = false;
//                    self.creating = false;
//
//                    self.uploadContainer.classList.add('d-none');
//                };

//                this.reader.readAsDataURL(file);

      this.mainDoc = URL.createObjectURL(file);
      this.creating = false;
      this.$forceUpdate();
      this.uploadContainer.classList.add('d-none');


    },

    log(s) {
      console.log(s);
    }


  }
}


</script>

<style type="text/css" lang="scss">
@import "cropperjs/dist/cropper.min.css";


$color: #6f42c1;
.uploader-container {
  /*display: flex;*/
  /*justify-content: center;*/
  /*vertical-align: middle;*/
  /*align-items: center;*/
  /*text-align: right;*/
  /*min-height: 8rem;*/
  &:hover, &.hover {
    color: rgba($color, 20%);
    cursor: pointer;
  }

  //.cropper-container {
  //  width: 100px !important;
  //  display: block;
  //}

}


</style>
