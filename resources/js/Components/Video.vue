<template>
  <div v-if="mode!='view' && !file"
       class=" flex  h-full md:min-w-[150px]  items-center justify-center  uploader-container  rounded-lg p-3 "

       :id="`container-video-${forId}`"
       style="border:dashed; "
       role="form"
       @drop.prevent=" filePreview($event  ) "
       @click="openFileChooser($event,`video-${forId}`)">

    <div class="  ">
      <div class=" p-2  small text-center     ">
        {{ label }}
      </div>
    </div>
    <input v-show="false" :id="`video-${forId}`" class="w-full   " accept=".mp4," type="file"
           name="video" @input="  filePreview($event, 'file' )"/>


  </div>

  <div v-if="file" class=" ">
    <video

        :id="`video-${forId}`"
        :ref="`video-${forId}`"
        :class="classes"
        class="video-js   "
        preload="auto"
        controls
        :autoplay="false"
        :poster="poster"
        data-setup='{}'
    >
      <!--      <source v-if="file" :src="url" :type="file.type"/>-->
      <!--       <source src="MY_VIDEO.webm" type="video/webm" />-->
      <p class="vjs-no-js">
        To view this video please enable JavaScript, and consider upgrading to a
        web browser that
        <a href="https://videojs.com/html5-video-support/" target="_blank"
        >supports HTML5 video</a
        >
      </p>
    </video>
  </div>
  <div class="flex my-1 w-100   " role="group">
    <div v-if="mode=='edit'"
         class="text-center flex rounded-s grow  cursor-pointer hover:bg-danger-600  p-2 bg-danger text-white grow "
         :title="__('remove')"
         @click="!preload || replace ? destroyPlayer() :  showDialog('danger',__('remove_image?'), __('remove') , removeImage )  ;">
      <XMarkIcon class="w-4 h-4  mx-auto text-white  text-white" v-if="!removing"/>
      <LoadingIcon class="w-4 h-4 mx-auto " v-if="removing"/>
    </div>
    <div v-if="  mode=='edit'"
         class="cursor-pointer grow rounded-e hover:bg-success-600  p-2 bg-success text-white grow"
         :title="__('upload')"
         @click="  showDialog('primary',__('new_file_replace_and_active_after_review'), __('upload') , uploadVideo )">
      <CheckIcon class="w-4 h-4  mx-auto text-white   text-white" v-if="!uploading"/>
      <LoadingIcon class="w-4 h-4 mx-auto " v-if="uploading"/>
    </div>

    <div v-if="mode!='edit' && mode!='view' && file"
         class="  cursor-pointer bg-danger hover:bg-danger-600 p-2  text-white grow rounded-lg"
         :title="__('remove')"
         @click="destroyPlayer()">
      <XMarkIcon class="w-4 h-4  mx-auto text-white   text-white"/>
    </div>
  </div>

</template>

<script>


import LoadingIcon from "@/Components/LoadingIcon.vue";
import {
  SpeakerXMarkIcon,
  SpeakerWaveIcon,
  XCircleIcon,
  XMarkIcon,
  CheckIcon,

} from '@heroicons/vue/24/solid';

import videojs from 'video.js';
import 'video.js/dist/video-js.css';

videojs.addLanguage('fa', {
  "Audio Player": "پخش‌کنندهٔ صوت",
  "Video Player": "پخش‌کنندهٔ ویدیو",
  "Play": "پخش",
  "Pause": "توقف",
  "Replay": "پخش مجدد",
  "Current Time": "زمان فعلی",
  "Duration": "مدت",
  "Remaining Time": "زمان باقی‌مانده",
  "Stream Type": "نوع استریم",
  "LIVE": "زنده",
  "Seek to live, currently behind live": "پخش زنده، هم‌اکنون عقب‌تر از پخش زنده",
  "Seek to live, currently playing live": "پخش زنده، در حال پخش زنده",
  "Loaded": "بارگیری‌شده",
  "Progress": "پیشرفت",
  "Progress Bar": "نوار پیشرفت",
  "progress bar timing: currentTime={1} duration={2}": "{1} از {2}",
  "Fullscreen": "تمام‌صفحه",
  "Exit Fullscreen": "غیر تمام‌صفحه",
  "Mute": "بی‌صدا",
  "Unmute": "صدادار",
  "Playback Rate": "سرعت پخش",
  "Subtitles": "زیرنویس‌ها",
  "subtitles off": "بدون زیرنویس",
  "Captions": "توضیحات",
  "captions off": "بدون توضیحات",
  "Chapters": "بخش‌ها",
  "Descriptions": "توصیفات",
  "descriptions off": "بدون توصیفات",
  "Audio Track": "ترَک صوتی",
  "Volume Level": "سطح صدا",
  "You aborted the media playback": "شما پخش رسانه را قطع نمودید",
  "A network error caused the media download to fail part-way.": "وقوع مشکلی در شبکه باعث اختلال در دانلود رسانه شد.",
  "The media could not be loaded, either because the server or network failed or because the format is not supported.": "رسانه قابل بارگیری نیست. ممکن است مشکلی در شبکه یا سرور رخ داده باشد یا قالب رسانه در دستگاه شما پشتیبانی نشود.",
  "The media playback was aborted due to a corruption problem or because the media used features your browser did not support.": "پخش  رسانه به‌علت اشکال در آن یا عدم پشتیبانی مرورگر شما قطع شد.",
  "No compatible source was found for this media.": "هیچ منبع سازگاری برای پخش این رسانه پیدا نشد.",
  "The media is encrypted and we do not have the keys to decrypt it.": "این رسانه رمزنگاری شده‌است و کلیدهای رمزگشایی آن موجود نیست.",
  "Play Video": "پخش ویدیو",
  "Close": "بستن",
  "Close Modal Dialog": "بستن پنجره",
  "Modal Window": "پنجرهٔ محاوره",
  "This is a modal window": "این پنجره قابل بستن است",
  "This modal can be closed by pressing the Escape key or activating the close button.": "این پنجره با کلید Escape یا دکمهٔ بستن قابل بسته‌شدن است.",
  ", opens captions settings dialog": "، تنظیمات توضیجات را باز می‌کند",
  ", opens subtitles settings dialog": "، تنظیمات زیرنویس را باز می‌کند",
  ", opens descriptions settings dialog": "، تنظیمات توصیفات را باز می‌کند",
  ", selected": "، انتخاب شد",
  "captions settings": "تنظیمات توضیحات",
  "subtitles settings": "تنظیمات زیرنویس",
  "descriptions settings": "تنظیمات توصیفات",
  "Text": "متن",
  "White": "سفید",
  "Black": "سیاه",
  "Red": "قرمز",
  "Green": "سبز",
  "Blue": "آبی",
  "Yellow": "زرد",
  "Magenta": "ارغوانی",
  "Cyan": "فیروزه‌ای",
  "Background": "پس‌زمینه",
  "Window": "پنجره",
  "Transparent": "شفاف",
  "Semi-Transparent": "نیمه‌شفاف",
  "Opaque": "مات",
  "Font Size": "اندازهٔ قلم",
  "Text Edge Style": "سبک لبهٔ متن",
  "None": "هیچ",
  "Raised": "برجسته",
  "Depressed": "فرورفته",
  "Uniform": "یکنواخت",
  "Drop shadow": "سایه‌دار",
  "Font Family": "نوع قلم",
  "Proportional Sans-Serif": "Sans-Serif متناسب",
  "Monospace Sans-Serif": "Sans-Serif هم‌عرض",
  "Proportional Serif": "Serif متناسب",
  "Monospace Serif": "Serif هم‌عرض",
  "Casual": "فانتزی",
  "Script": "دست‌خط",
  "Small Caps": "حروف بزرگ کوچک",
  "Reset": "تنظیم مجدد",
  "restore all settings to the default values": "بازنشانی همهٔ تنظیمات به مقادیر پیش‌فرض",
  "Done": "انجام",
  "Caption Settings Dialog": "پنجرهٔ تنظیمات توضیحات",
  "Beginning of dialog window. Escape will cancel and close the window.": "شروع پنجرهٔ محاوره‌ای. دکمهٔ Escape عملیات را لغو کرده و پنجره را می‌بندد.",
  "End of dialog window.": "پایان پنجرهٔ محاوره‌ای.",
  "{1} is loading.": "{1} در حال بارگیری است.",
  "Exit Picture-in-Picture": "خروج از حالت تصویر در تصویر",
  "Picture-in-Picture": "تصویر در تصویر",
  "Skip forward {1} seconds": "{1} ثانیه بعد",
  "Skip backward {1} seconds": "{1} ثانیه قبل",
  "No content": "بدون محتوا",
  "Color": "رنگ",
  "Opacity": "شفافیت",
  "Text Background": "رنگ پس زمینه",
  "Caption Area Background": "رنگ پس زمینه عنوان",
  "Playing in Picture-in-Picture": "پخش در حالت تصویر در تصویر"
});

export default {
  name: "Video",
  props: ['view', 'poster', 'preload', 'label', 'mode', 'link', 'forId', 'classes', 'removing', 'lang'],
  components: {
    SpeakerXMarkIcon,
    SpeakerWaveIcon,
    XCircleIcon,
    XMarkIcon,
    CheckIcon,
    LoadingIcon,
  },
  data() {
    return {
      url: null,
      file: null,
      uploadContainer: null,
      player: null,
      playing: false,
      repeat: false,
      speed: 1,
      video: null,
      volume: 50,
      uploading: false,
      percentage: 0,
      duration: 0,
      container: null,
      replace: !!this.preload,
    }
  },
  watch: {
    // video() {

    // if (!this.video && this.player)
    //   this.player.pause();
    // },

  },
  mounted() {
    this.container = this.$refs[`video-${this.forId}`];
    this.uploadContainer = document.querySelector(`#container-video-${this.forId}`);

    if (this.preload) {

      this.file = this.preload;
      this.url = this.preload.url;
    }
    if (this.file)
      this.$nextTick(() => {
        this.initPlayer();
      });

  },
  updated() {
  },

  beforeUnmount() {

    this.destroyPlayer();
  },

  methods: {
    destroyPlayer() {
      //
      if (this.player) {
        this.player.reset();
        // this.player.dispose();
        this.file = null;
        this.url = null;
      }
    },
    initPlayer() {
      var options = {language: this.lang || 'en'};
      this.player = videojs(this.$refs[`video-${this.forId}`], options, () => {
        // console.log('ready');
        this.player.src({
          type: this.file.type,
          src: this.url
        })
        this.player.load();
      });


      this.player.on('ended', function () {
        // console.log('Awww...over so soon?!');
      });


      // this.player.on('ready', () => {
      //   console.log('ready');
      // });
      this.player.on('loadedmetadata', () => {
        // console.log('loadedmetadata');
        this.duration = parseInt(this.player.duration());

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


      if (event.dataTransfer) {
        file = event.dataTransfer.files[0];
      } else if (event.target.files) {
        file = event.target.files[0];
      }
      event.target.value = '';
      this.file = file;
      this.url = URL.createObjectURL(file);

      this.$nextTick(() => {
        this.initPlayer();
      });

    }
    ,
    async uploadVideo() {
//                console.log(this.$refs.dropdown.selected.length);

      this.uploading = true;
      var fd = new FormData();
      fd.append('video', this.file);
      fd.append('id', this.forId);
      fd.append('duration', parseInt(this.player.duration()));
      fd.append('cmnd', 'upload-video');
      fd.append('_method', 'PATCH')

      axios.post(this.link, fd, {
        headers: {
          'Content-Type': 'multipart/form-data'
        }
      })
          .then((response) => {
//                        console.log(response);
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
    getDuration() {
      if (!this.player) return 0;
      return parseInt(this.player.duration())
    }

  },
}
</script>

<style lang="css">
@font-face {
  font-family: 'VideoJS';
  src: url('https://vjs.zencdn.net/f/1/vjs.eot');
  src: url('https://vjs.zencdn.net/f/1/vjs.eot?#iefix') format('embedded-opentype'),
  url('https://vjs.zencdn.net/f/1/vjs.woff') format('woff'),
  url('https://vjs.zencdn.net/f/1/vjs.ttf') format('truetype');
}

.video-js .vjs-play-control.vjs-playing .vjs-icon-placeholder:before, .vjs-icon-pause:before {
  content: "\f103";
  font-family: 'VideoJS';
}

.video-js .vjs-mute-control .vjs-icon-placeholder:before, .vjs-icon-volume-high:before {
  content: "\f107";
  font-family: 'VideoJS';
}

.video-js .vjs-big-play-button .vjs-icon-placeholder:before, .video-js .vjs-play-control .vjs-icon-placeholder:before, .vjs-icon-play:before {
  content: "\f101";
  font-family: 'VideoJS';
}

.video-js .vjs-picture-in-picture-control .vjs-icon-placeholder:before, .vjs-icon-picture-in-picture-enter:before {
  content: "\f121";
  font-family: 'VideoJS';
}


.video-js .vjs-fullscreen-control .vjs-icon-placeholder:before, .vjs-icon-fullscreen-enter:before {
  content: "\f108";
  font-family: 'VideoJS';
}

.video-js .vjs-menu-button-inline.vjs-slider-active,
.video-js .vjs-menu-button-inline:focus,
.video-js .vjs-menu-button-inline:hover,
.video-js.vjs-no-flex .vjs-menu-button-inline {
  width: 10em
}

.video-js .vjs-controls-disabled .vjs-big-play-button {
  display: none !important
}

.video-js .vjs-control {
  width: 3em
}

.video-js .vjs-menu-button-inline:before {
  width: 1.5em
}

.vjs-menu-button-inline .vjs-menu {
  left: 3em
}

.video-js.vjs-paused .vjs-big-play-button,
.vjs-paused.vjs-has-started.video-js .vjs-big-play-button {
  display: block
}

.video-js .vjs-load-progress div,
.vjs-seeking .vjs-big-play-button,
.vjs-waiting .vjs-big-play-button {
  display: none !important
}

.video-js .vjs-mouse-display:after,
.video-js .vjs-play-progress:after {
  padding: 0 .4em .3em
}

.video-js.vjs-ended .vjs-loading-spinner {
  display: none
}

.video-js.vjs-ended .vjs-big-play-button {
  display: block !important
}

.video-js *,
.video-js:after,
.video-js:before {
  box-sizing: inherit;
  font-size: inherit;
  color: inherit;
  line-height: inherit
}

.video-js.vjs-fullscreen,
.video-js.vjs-fullscreen .vjs-tech {
  width: 100% !important;
  height: 100% !important
}

.video-js {
  font-size: 14px;
  overflow: hidden
}

.video-js .vjs-control {
  color: inherit
}

.video-js .vjs-menu-button-inline:hover,
.video-js.vjs-no-flex .vjs-menu-button-inline {
  width: 8.35em
}

.video-js .vjs-volume-menu-button.vjs-volume-menu-button-horizontal:hover .vjs-menu .vjs-menu-content {
  height: 3em;
  width: 6.35em
}

.video-js .vjs-control:focus:before,
.video-js .vjs-control:hover:before {
  text-shadow: 0 0 1em #fff, 0 0 1em #fff, 0 0 1em #fff
}

.video-js .vjs-spacer,
.video-js .vjs-time-control {
  display: -webkit-box;
  display: -moz-box;
  display: -ms-flexbox;
  display: -webkit-flex;
  display: flex;
  -webkit-box-flex: 1 1 auto;
  -moz-box-flex: 1 1 auto;
  -webkit-flex: 1 1 auto;
  -ms-flex: 1 1 auto;
  flex: 1 1 auto
}

.video-js .vjs-time-control {
  -webkit-box-flex: 0 1 auto;
  -moz-box-flex: 0 1 auto;
  -webkit-flex: 0 1 auto;
  -ms-flex: 0 1 auto;
  flex: 0 1 auto;
  width: auto
}

.video-js .vjs-time-control.vjs-time-divider {
  width: 14px
}

.video-js .vjs-time-control.vjs-time-divider div {
  width: 100%;
  text-align: center
}

.video-js .vjs-time-control.vjs-current-time {
  margin-left: 1em
}

.video-js .vjs-time-control .vjs-current-time-display,
.video-js .vjs-time-control .vjs-duration-display {
  width: 100%
}

.video-js .vjs-time-control .vjs-current-time-display {
  text-align: right
}

.video-js .vjs-time-control .vjs-duration-display {
  text-align: left
}

.video-js .vjs-play-progress:before,
.video-js .vjs-progress-control .vjs-play-progress:before,
.video-js .vjs-remaining-time,
.video-js .vjs-volume-level:after,
.video-js .vjs-volume-level:before,
.video-js.vjs-live .vjs-time-control.vjs-current-time,
.video-js.vjs-live .vjs-time-control.vjs-duration,
.video-js.vjs-live .vjs-time-control.vjs-time-divider,
.video-js.vjs-no-flex .vjs-time-control.vjs-remaining-time {
  display: none
}

.video-js.vjs-no-flex .vjs-time-control {
  display: table-cell;
  width: 4em
}

.video-js .vjs-progress-control {
  position: absolute;
  left: 0;
  right: 0;
  width: 100%;
  height: .5em;
  top: -.5em
}

.video-js .vjs-progress-control .vjs-load-progress,
.video-js .vjs-progress-control .vjs-play-progress,
.video-js .vjs-progress-control .vjs-progress-holder {
  height: 100%
}

.video-js .vjs-progress-control .vjs-progress-holder {
  margin: 0
}

.video-js .vjs-progress-control:hover {
  height: 1.5em;
  top: -1.5em
}

.video-js .vjs-control-bar {
  -webkit-transition: -webkit-transform .1s ease 0s;
  -moz-transition: -moz-transform .1s ease 0s;
  -ms-transition: -ms-transform .1s ease 0s;
  -o-transition: -o-transform .1s ease 0s;
  transition: transform .1s ease 0s
}

.video-js.not-hover.vjs-has-started.vjs-paused.vjs-user-active .vjs-control-bar,
.video-js.not-hover.vjs-has-started.vjs-paused.vjs-user-inactive .vjs-control-bar,
.video-js.not-hover.vjs-has-started.vjs-playing.vjs-user-active .vjs-control-bar,
.video-js.not-hover.vjs-has-started.vjs-playing.vjs-user-inactive .vjs-control-bar,
.video-js.vjs-has-started.vjs-playing.vjs-user-inactive .vjs-control-bar {
  visibility: visible;
  opacity: 1;
  -webkit-backface-visibility: hidden;
  -webkit-transform: translateY(3em);
  -moz-transform: translateY(3em);
  -ms-transform: translateY(3em);
  -o-transform: translateY(3em);
  transform: translateY(3em);
  -webkit-transition: -webkit-transform 1s ease 0s;
  -moz-transition: -moz-transform 1s ease 0s;
  -ms-transition: -ms-transform 1s ease 0s;
  -o-transition: -o-transform 1s ease 0s;
  transition: transform 1s ease 0s
}

.video-js.not-hover.vjs-has-started.vjs-paused.vjs-user-active .vjs-progress-control,
.video-js.not-hover.vjs-has-started.vjs-paused.vjs-user-inactive .vjs-progress-control,
.video-js.not-hover.vjs-has-started.vjs-playing.vjs-user-active .vjs-progress-control,
.video-js.not-hover.vjs-has-started.vjs-playing.vjs-user-inactive .vjs-progress-control,
.video-js.vjs-has-started.vjs-playing.vjs-user-inactive .vjs-progress-control {
  height: .25em;
  top: -.25em;
  pointer-events: none;
  -webkit-transition: height 1s, top 1s;
  -moz-transition: height 1s, top 1s;
  -ms-transition: height 1s, top 1s;
  -o-transition: height 1s, top 1s;
  transition: height 1s, top 1s
}

.video-js.not-hover.vjs-has-started.vjs-paused.vjs-user-active.vjs-fullscreen .vjs-progress-control,
.video-js.not-hover.vjs-has-started.vjs-paused.vjs-user-inactive.vjs-fullscreen .vjs-progress-control,
.video-js.not-hover.vjs-has-started.vjs-playing.vjs-user-active.vjs-fullscreen .vjs-progress-control,
.video-js.not-hover.vjs-has-started.vjs-playing.vjs-user-inactive.vjs-fullscreen .vjs-progress-control,
.video-js.vjs-has-started.vjs-playing.vjs-user-inactive.vjs-fullscreen .vjs-progress-control {
  opacity: 0;
  -webkit-transition: opacity 1s ease 1s;
  -moz-transition: opacity 1s ease 1s;
  -ms-transition: opacity 1s ease 1s;
  -o-transition: opacity 1s ease 1s;
  transition: opacity 1s ease 1s
}

.video-js.vjs-live .vjs-live-control {
  margin-left: 1em
}

.video-js .vjs-big-play-button {
  top: 50%;
  left: 50%;
  margin-left: -1em;
  width: 1.5em;
  border: none;
  color: #fff;
  -webkit-transition: border-color .4s, outline .4s, background-color .4s;
  -moz-transition: border-color .4s, outline .4s, background-color .4s;
  -ms-transition: border-color .4s, outline .4s, background-color .4s;
  -o-transition: border-color .4s, outline .4s, background-color .4s;
  transition: border-color .4s, outline .4s, background-color .4s;
  background-color: rgba(0, 0, 0, .45);
  font-size: 3.5em;
  border-radius: 50%;
  height: 1.5em !important;
  line-height: 1.7em !important;
  margin-top: -1em !important
}

.video-js .vjs-menu-button-popup .vjs-menu {
  left: -3em
}

.video-js .vjs-menu-button-popup .vjs-menu .vjs-menu-content {
  background-color: transparent;
  width: 12em;
  left: -1.5em;
  padding-bottom: .5em
}

.video-js .vjs-menu-button-popup .vjs-menu .vjs-menu-item,
.video-js .vjs-menu-button-popup .vjs-menu .vjs-menu-title {
  background-color: #151b17;
  margin: .3em 0;
  padding: .5em;
  border-radius: .3em
}

.video-js .vjs-menu-button-popup .vjs-menu .vjs-menu-item.vjs-selected {
  background-color: #2483d5
}

.video-js .vjs-big-play-button:active,
.video-js .vjs-big-play-button:focus,
.video-js:hover .vjs-big-play-button {
  background-color: rgba(36, 131, 213, .9)
}

.video-js .vjs-loading-spinner {
  border-color: rgba(36, 131, 213, .8)
}

.video-js .vjs-control-bar2 {
  background-color: #000
}

.video-js .vjs-control-bar {
  background-color: rgba(0, 0, 0, .3) !important;
  color: #fff;
  font-size: 14px
}

.video-js .vjs-play-progress,
.video-js .vjs-volume-level {
  background-color: #2483d5
}
</style>