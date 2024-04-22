<template>

  <div class="  h-full ">

    <div class=" h-full">


      <!--           :style="'width:'+(cropRatio*height)+'rem;height:'+height+'rem'"-->
      <div v-show="!doc && (mode=='edit' || mode=='create')"
           class=" flex  h-full md:min-w-[150px]  items-center justify-center  uploader-container  rounded-lg p-3 "
           :id="'banner-container-' + this.id"
           style="border:dashed; "
           role="form"
           @drop.prevent=" filePreview($event  ) "
           @click="openFileChooser($event,'banner-'+id+'-file')">

        <div class="  ">
          <div class=" p-2  small text-center     ">
            {{ label }}
          </div>
        </div>

        <input v-show="false" :id="'banner-'+id+'-file'" class="w-full   " accept=".png, .jpg, .jpeg" type="file"
               :key="id"
               :name="'banner-'+id+'-file'" @input="  filePreview($event,'banner-'+id+'-file' )"/>
        <input :id="id" class="col-12" :name="id" type="hidden"/>


      </div>
      <div v-show=" mode=='lock'"
           @click=" showDialog('danger',__('call_owner_for_receive_file'), __('remove') , removeImage )"
           class="text-primary backdrop-blur relative flex justify-center p-2 items-center h-full cursor-pointer hover:bg-gray-200">
        <div class="absolute bg-black opacity-10 w-full h-full rounded"></div>
        <FolderIcon class="w-10 h-10 "/>

      </div>
      <div v-show="doc && mode=='view'" class="h-[inherit]">
        <img :id="'banner-'+id"
             class="     "
             :class="classes"
             @error="errorImage"
             @load="  uploadContainer.classList.add('d-none'); "
             :src="doc"
             alt=""/>
      </div>
      <div v-show="doc && mode !='view'" class="  rounded-lg flex flex-col justify-between   "
           :style="`width:${width}`">

        <div class="backdrop-blur relative flex justify-center p-2 items-center h-32">
          <img v-show="doc && mode !='view'" :id="'banner-'+id"
               class="block   absolute w-full h-full opacity-50 p-2"
               :class="classes"
               @error="errorImage"
               @load="  uploadContainer.classList.add('d-none'); "
               :src="doc"
               alt=""/>
          <div class="absolute bg-black opacity-10 w-full h-full rounded"></div>
          <FolderIcon class="w-10 h-10 opacity-50 text-[#fff]"/>
        </div>

        <!--        <div v-show="doc && mode=='view'" :class="classes"-->
        <!--             class=" backdrop-blur relative flex justify-center p-2 items-center">-->
        <!--          <div class="absolute bg-black opacity-10 w-full h-full rounded"></div>-->
        <!--          <FolderIcon class="w-10 h-10 "/>-->

        <!--        </div>-->

        <div class="flex my-1 w-100   " role="group">
          <div v-if="mode=='edit'" :class="file? ' rounded-s' :'rounded'"
               class="text-center flex grow  cursor-pointer hover:bg-danger-600  p-2 bg-danger text-white grow "
               :title="__('remove')"
               @click="!preload || replace ? clearImage():  showDialog('danger',__('remove_image?'), __('remove') , removeImage )  ;">
            <XMarkIcon class="w-4 h-4  mx-auto text-white  text-white" v-if="!removing"/>
            <LoadingIcon class="w-4 h-4 mx-auto " v-if="removing"/>
          </div>
          <div v-if="  mode=='edit' && file"
               class="cursor-pointer grow rounded-e hover:bg-success-600  p-2 bg-success text-white grow"
               :title="__('upload')"
               @click="  showDialog('primary',__('new_file_replace_and_active_after_review'), __('upload') , uploadImage )">
            <CheckIcon class="w-4 h-4  mx-auto text-white   text-white" v-if="!uploading"/>
            <LoadingIcon class="w-4 h-4 mx-auto " v-if="uploading"/>
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


  </div>


</template>

<script>


let input = null;

let self;
let canvas;


import Tooltip from "@/Components/Tooltip.vue";
import {XMarkIcon, CheckIcon, FolderIcon,} from "@heroicons/vue/24/solid";
import LoadingIcon from "@/Components/LoadingIcon.vue";

export default {


  props: ['link', 'id', 'type', 'replace', 'height', 'width', 'required', 'preload', 'label', 'mode', 'forId', 'callback', 'images', 'limit', 'cropRatio', 'classes'],
  components: {Tooltip, XMarkIcon, CheckIcon, LoadingIcon, FolderIcon},
  data() {
    return {
      componentKey: 1,
      star: null,
      doc: null,
      file: null,
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
  watch: {},
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
    this.image = document.getElementById('banner-' + this.id);
//            console.log(image);
//            $(".point-sw")

    this.uploader = document.querySelector('#banner-' + this.id + '-file');
    this.uploadContainer = document.querySelector('#banner-container-' + this.id);
    if (this.preload) {
      this.doc = this.preload.url;
    }
  }
  ,
  created() {

  }
  ,
  updated() {


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

    clearImage() {
      this.doc = null;
      this.image.src = null;
      this.file = null;


      return null

    },
    refresh() {

      this.uploadContainer.classList.remove('d-none');
      this.image.src = null;
//                document.getElementById('banner').value = null;
//                this.cropper.destroy();
//                this.componentKey++;
//                this.$forceUpdate();


    },

    removeImage() {
      this.removing = true;

      axios.patch(this.link, {
        'id': this.forId,
        'cmnd': 'delete-banner',
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
      // console.log(this.file);

      this.uploading = true;
      var fd = new FormData();
      fd.append('banner', this.file);
      fd.append('id', this.forId);
      fd.append('cmnd', 'upload-banner');
      fd.append('_method', 'PATCH')

//                this.cropper.crop();


      axios.post(this.link, fd,)
          .then((response) => {
            console.log(response.status);
            if (response.status === 200) {
              // window.location.reload();
              this.showToast('success', response.data.message, onclick = null);
            } else {
              this.showToast('danger', response.data, onclick = null);
            }

          }).catch((error) => {
        this.errors = this.getErrors(error);
        this.showToast('danger', this.errors, onclick = null);

      }).finally(() => {
        this.uploading = false;
      });


    },


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

      this.file = file;
      this.doc = URL.createObjectURL(file);

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
