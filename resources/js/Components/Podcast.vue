<template>
  <div v-show="mode!='view' && !song"
       class=" flex  h-full md:min-w-[150px]  items-center justify-center  uploader-container  rounded-lg p-3 "

       :id="`container-podcast-${forId}`"
       style="border:dashed; "
       role="form" @mouseover="uploader.classList.add('hover');"
       @dragover.prevent="uploader.classList.add('hover');"
       @dragleave.prevent="uploader.classList.remove('hover');"
       @mouseleave=" uploader.classList.remove('hover');"
       @drop.prevent="uploader.classList.remove('hover');filePreview($event  ) "
       @click="openFileChooser($event,'podcast')">

    <div class="  ">
      <div class=" p-2  small text-center     ">
        {{ label }}
      </div>
    </div>
    <input v-show="false" :id="`podcast-${forId}`" class="w-full   " accept=".mp3," type="file"
           name="podcast" @input="  filePreview($event, 'file' )"/>


  </div>

  <div v-if="song && mode=='multi'" class="flex flex-col   items-center    ">
    <audio controls :autoplay="false" class="  ">
      <source :src="song.url" type="audio/ogg">
      Your browser does not support the audio element.
    </audio>
    <div class="p-2   flex items-center z-50">

      <div class="flex flex-col">
          <span data-amplitude-song-info="name"
                class="font-sans text-sm  text-slate-900 dark:text-white">{{ song.name }}
          </span>
        <span data-amplitude-song-info="artist"
              class="font-sans text-sm my-2  text-gray-500 dark:text-gray-400">
            {{ song.artist }}
          </span>
        <span data-amplitude-song-info="album"
              class="font-sans text-base font-medium leading-6 text-gray-500 dark:text-gray-400"></span>
      </div>
    </div>
  </div>
  <div v-else-if=" song  "
       class=" w-full    flex flex-col  ">
    <div class="flex  ">
      <div v-if="song.cover" class="hidden sm:block   ">
        <Image :src="preload.cover" classes="object-cover rounded-s  w-32 sm:w-48   lg:w-48 h-full"/>
      </div>
      <div
          :class="classes || 'rounded-xl'"
          class="relative w-player flex flex-col grow  shadow-player-light bg-player-light-background border border-player-light-border dark:shadow-player-dark dark:bg-player-dark-background dark:border-player-dark-border dark:backdrop-blur-xl">

        <!--      <div>-->
        <!--        <XCircleIcon @click="player.stop();song=null"-->
        <!--                     class="h-8 w-8 cursor-pointer text-danger  hover:text-danger-400 duration-200"/>-->
        <!--      </div>-->

        <div class="px-6 sm:px-4 pt-10 pb-4 flex items-center z-50">

          <div class="flex flex-col">
          <span data-amplitude-song-info="name"
                class="font-sans text-sm  text-slate-900 dark:text-white">{{ song.name }}
          </span>
            <span data-amplitude-song-info="artist"
                  class="font-sans text-sm my-2  text-gray-500 dark:text-gray-400">
            {{ song.artist }}
          </span>
            <span data-amplitude-song-info="album"
                  class="font-sans text-base font-medium leading-6 text-gray-500 dark:text-gray-400"></span>
          </div>
        </div>


        <div class="w-full flex flex-col px-6 sm:px-4 pb-6  " dir="ltr">

          <div :id="`progress-container`" class="progress-container relative   h-[20px] cursor-pointer ">
            <!--            <div class="amplitude-wave-form"></div>-->
            <div class="amplitude-visualization"></div>
            <input type="range"
                   class=" amplitude-song-slider absolute z-[999] top-[-7px] h-[20px] cursor-pointer bg-[inherit] appearance-none w-full my-[7.5px]"
                   :style="`background-size:${percentage}% 100%`"
                   step=".1" @change="changeTime "/>

            <progress :id="`song-played-progress`"
                      class="amplitude-song-played-progress w-full absolute left-0 top-[8px] right-0 z-[60] appearance-none h-[4px] rounded-[5px] bg-transparent"
                      value="0">

            </progress>
            <progress :id="`song-buffered-progress`"
                      class="amplitude-buffered-progress bg-[#D7DEE3] w-100 absolute left-0 top-[8px] right-0 z-[10] appearance-none h-[4px] rounded-[5px] bg-transparent"
                      value="0">
            </progress>
          </div>


          <div class="flex w-full justify-between ">
          <span
              class="mx-1 amplitude-current-time text-xs font-sans tracking-wide font-medium text-sky-500 dark:text-sky-300">
          </span>
            <span class="mx-1 amplitude-duration-time text-xs font-sans tracking-wide font-medium text-gray-500"></span>

          </div>

          <div class="flex w-full justify-center">
            <input type="range" @change="player.setVolume(volume)" v-model="volume" class="amplitude-volume-slider"/>
            <SpeakerXMarkIcon @click="volume=50;player.setVolume(volume)" v-if="volume==0"
                              class="w-5 h-5 cursor-pointer text-gray-500 hover:text-primary duration-200"/>
            <SpeakerWaveIcon @click="volume=0;player.setVolume(volume)" v-else
                             class="w-5 h-5 cursor-pointer text-gray-500 hover:text-primary duration-200"/>
          </div>
        </div>

        <div
            class="h-control-panel px-6 sm:px-4 rounded-b-xl bg-control-panel-light-background border-t border-gray-200 flex items-center justify-between z-50 dark:bg-control-panel-dark-background dark:border-gray-900">

          <!--        <div class="cursor-pointer amplitude-shuffle">-->
          <!--          <svg width="28" height="26" viewBox="0 0 28 26" fill="none" xmlns="http://www.w3.org/2000/svg">-->
          <!--            <path-->
          <!--                d="M1 20C0.447715 20 0 20.4477 0 21C0 21.5523 0.447715 22 1 22V20ZM7.75736 19.2426L8.46447 19.9497H8.46447L7.75736 19.2426ZM20.2426 6.75736L19.5355 6.05025L19.5355 6.05025L20.2426 6.75736ZM27 5L27.7071 5.70711C28.0976 5.31658 28.0976 4.68342 27.7071 4.29289L27 5ZM27 21L27.7071 21.7071C28.0976 21.3166 28.0976 20.6834 27.7071 20.2929L27 21ZM1 4C0.447715 4 0 4.44772 0 5C0 5.55228 0.447715 6 1 6V4ZM7.75736 6.75736L8.46447 6.05025L7.75736 6.75736ZM20.2426 19.2426L20.9497 18.5355L20.2426 19.2426ZM10.4645 10.8787C10.855 11.2692 11.4882 11.2692 11.8787 10.8787C12.2692 10.4882 12.2692 9.85499 11.8787 9.46447L10.4645 10.8787ZM17.5355 15.1213C17.145 14.7308 16.5118 14.7308 16.1213 15.1213C15.7308 15.5118 15.7308 16.145 16.1213 16.5355L17.5355 15.1213ZM23.7071 0.292893C23.3166 -0.0976311 22.6834 -0.0976311 22.2929 0.292893C21.9024 0.683417 21.9024 1.31658 22.2929 1.70711L23.7071 0.292893ZM22.2929 8.29289C21.9024 8.68342 21.9024 9.31658 22.2929 9.70711C22.6834 10.0976 23.3166 10.0976 23.7071 9.70711L22.2929 8.29289ZM23.7071 16.2929C23.3166 15.9024 22.6834 15.9024 22.2929 16.2929C21.9024 16.6834 21.9024 17.3166 22.2929 17.7071L23.7071 16.2929ZM22.2929 24.2929C21.9024 24.6834 21.9024 25.3166 22.2929 25.7071C22.6834 26.0976 23.3166 26.0976 23.7071 25.7071L22.2929 24.2929ZM1 22H3.51472V20H1V22ZM8.46447 19.9497L20.9497 7.46446L19.5355 6.05025L7.05025 18.5355L8.46447 19.9497ZM24.4853 6H27V4H24.4853V6ZM20.9497 7.46446C21.8874 6.52678 23.1592 6 24.4853 6V4C22.6288 4 20.8483 4.7375 19.5355 6.05025L20.9497 7.46446ZM3.51472 22C5.37123 22 7.15171 21.2625 8.46447 19.9497L7.05025 18.5355C6.11257 19.4732 4.8408 20 3.51472 20V22ZM27 20H24.4853V22H27V20ZM3.51472 4H1V6H3.51472V4ZM8.46447 6.05025C7.15171 4.7375 5.37123 4 3.51472 4V6C4.8408 6 6.11257 6.52678 7.05025 7.46446L8.46447 6.05025ZM24.4853 20C23.1592 20 21.8874 19.4732 20.9497 18.5355L19.5355 19.9497C20.8483 21.2625 22.6288 22 24.4853 22V20ZM11.8787 9.46447L8.46447 6.05025L7.05025 7.46446L10.4645 10.8787L11.8787 9.46447ZM20.9497 18.5355L17.5355 15.1213L16.1213 16.5355L19.5355 19.9497L20.9497 18.5355ZM22.2929 1.70711L26.2929 5.70711L27.7071 4.29289L23.7071 0.292893L22.2929 1.70711ZM26.2929 4.29289L22.2929 8.29289L23.7071 9.70711L27.7071 5.70711L26.2929 4.29289ZM22.2929 17.7071L26.2929 21.7071L27.7071 20.2929L23.7071 16.2929L22.2929 17.7071ZM26.2929 20.2929L22.2929 24.2929L23.7071 25.7071L27.7071 21.7071L26.2929 20.2929Z"-->
          <!--                fill="#94A3B8"/>-->
          <!--          </svg>-->
          <!--        </div>-->
          <span class="amplitude-playback-speed text-gray-500 cursor-pointer hover:text-primary duration-200">
          <span v-if="speed==1.5" @click="speed=2;player.setPlaybackSpeed(speed)">1.5x</span>
          <span v-else-if="speed==2" @click="speed=1;player.setPlaybackSpeed(speed)">2x</span>
          <span v-else @click="speed=1.5;player.setPlaybackSpeed(speed)">1x</span>
        </span>
          <div class="cursor-pointer amplitude-next group  hover:scale-[104%] duration-200">
            <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path class="fill-gray-500  group-hover:fill-primary-400  group-hover:fill-stroke-primary-400"
                    d="M6 7C6 5.76393 7.41115 5.05836 8.4 5.8L20.4 14.8C21.2 15.4 21.2 16.6 20.4 17.2L8.4 26.2C7.41115 26.9416 6 26.2361 6 25V7Z"
                    fill="#94A3B8" stroke="none" stroke-width="2" stroke-linejoin="round"/>
              <path d="M26 5L26 27" class="fill-gray-500 stroke-gray-500  " stroke="none" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </div>
          <div @click="!playing? this.player.play():this.player.pause()"
               class="cursor-pointer group hover:scale-[103%] hover:first:fill-primary duration-200 amplitude-play-pause w-16 h-16 my-2 rounded-full bg-white border border-play-pause-light-border shadow-xl flex items-center justify-center dark:bg-play-pause-dark-background dark:border-play-pause-dark-border">

            <svg v-if="playing" class="" id="pause-icon" width="24"
                 height="36" viewBox="0 0 24 36"
                 fill="none"
                 xmlns="http://www.w3.org/2000/svg">
              <rect width="6" height="36" rx="3" class="fill-slate-500 dark:fill-slate-400"/>
              <rect x="18" width="6" height="36" rx="3" class="fill-slate-500 dark:fill-slate-400"/>
            </svg>
            <svg v-else id="play-icon"
                 class="ml-[10px] "
                 width="31" height="37" viewBox="0 0 31 37"
                 fill="none"
                 xmlns="http://www.w3.org/2000/svg">
              <path fill-rule="evenodd" clip-rule="evenodd" c
                    d="M29.6901 16.6608L4.00209 0.747111C2.12875 -0.476923 0.599998 0.421814 0.599998 2.75545V33.643C0.599998 35.9728 2.12747 36.8805 4.00209 35.6514L29.6901 19.7402C29.6901 19.7402 30.6043 19.0973 30.6043 18.2012C30.6043 17.3024 29.6901 16.6608 29.6901 16.6608Z"
                    class="fill-gray-500 group-duration-200  group-hover:fill-primary-400  group-hover:fill-stroke-primary-400  "/>
            </svg>
          </div>
          <div class="cursor-pointer amplitude-prev group  hover:scale-[104%] duration-200">
            <svg class="" width="32" height="32" viewBox="0 0 32 32" fill="none"
                 xmlns="http://www.w3.org/2000/svg">
              <path
                  class="fill-gray-500  group-hover:fill-primary-400  group-hover:fill-stroke-primary-400  "
                  d="M26 7C26 5.76393 24.5889 5.05836 23.6 5.8L11.6 14.8C10.8 15.4 10.8 16.6 11.6 17.2L23.6 26.2C24.5889 26.9416 26 26.2361 26 25V7Z"
                  fill="none" stroke="none" stroke-width="2" stroke-linejoin="round"/>
              <path d="M6 5L6 27"
                    class="fill-gray-500 stroke-gray-500    "
                    fill="none" stroke="none" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round"/>
            </svg>
          </div>

          <div class="cursor-pointer amplitude-repeat-song group" @click="repeat=!repeat,player.setRepeatSong(repeat)">
            <svg width="26" height="24" viewBox="0 0 26 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path :class="{' fill-primary  stroke-primary-400  ':repeat}"
                    class="fill-gray-500 group-duration-200   group-hover:fill-primary-400  group-hover:fill-stroke-primary-400"
                    d="M17.7071 15.7071C18.0976 15.3166 18.0976 14.6834 17.7071 14.2929C17.3166 13.9024 16.6834 13.9024 16.2929 14.2929L17.7071 15.7071ZM13 19L12.2929 18.2929C11.9024 18.6834 11.9024 19.3166 12.2929 19.7071L13 19ZM16.2929 23.7071C16.6834 24.0976 17.3166 24.0976 17.7071 23.7071C18.0976 23.3166 18.0976 22.6834 17.7071 22.2929L16.2929 23.7071ZM19.9359 18.7035L19.8503 17.7072L19.9359 18.7035ZM8.95082 19.9005C9.50243 19.9277 9.97163 19.5025 9.99879 18.9509C10.026 18.3993 9.6008 17.9301 9.04918 17.9029L8.95082 19.9005ZM6.06408 18.7035L5.97851 19.6998L6.06408 18.7035ZM1.07501 13.4958L0.075929 13.5387L1.07501 13.4958ZM1.07501 6.50423L0.0759292 6.46127L1.07501 6.50423ZM6.06409 1.29649L6.14965 2.29282L6.06409 1.29649ZM19.9359 1.29649L19.8503 2.29283L19.9359 1.29649ZM24.925 6.50423L23.9259 6.54718L24.925 6.50423ZM24.925 13.4958L25.9241 13.5387V13.5387L24.925 13.4958ZM16.2929 14.2929L12.2929 18.2929L13.7071 19.7071L17.7071 15.7071L16.2929 14.2929ZM12.2929 19.7071L16.2929 23.7071L17.7071 22.2929L13.7071 18.2929L12.2929 19.7071ZM19.8503 17.7072C17.5929 17.901 15.3081 18 13 18V20C15.3653 20 17.7072 19.8986 20.0215 19.6998L19.8503 17.7072ZM9.04918 17.9029C8.07792 17.8551 7.1113 17.7898 6.14964 17.7072L5.97851 19.6998C6.96438 19.7845 7.95525 19.8515 8.95082 19.9005L9.04918 17.9029ZM2.07408 13.4528C2.02486 12.3081 2 11.157 2 10H0C0 11.1856 0.0254804 12.3654 0.075929 13.5387L2.07408 13.4528ZM2 10C2 8.84302 2.02486 7.69192 2.07408 6.54718L0.0759292 6.46127C0.0254806 7.63461 0 8.81436 0 10H2ZM6.14965 2.29282C8.4071 2.09896 10.6919 2 13 2V0C10.6347 0 8.29281 0.101411 5.97853 0.30016L6.14965 2.29282ZM13 2C15.3081 2 17.5929 2.09896 19.8503 2.29283L20.0215 0.30016C17.7072 0.101411 15.3653 0 13 0V2ZM23.9259 6.54718C23.9751 7.69192 24 8.84302 24 10H26C26 8.81436 25.9745 7.63461 25.9241 6.46127L23.9259 6.54718ZM24 10C24 11.157 23.9751 12.3081 23.9259 13.4528L25.9241 13.5387C25.9745 12.3654 26 11.1856 26 10H24ZM19.8503 2.29283C22.092 2.48534 23.8293 4.29889 23.9259 6.54718L25.9241 6.46127C25.7842 3.20897 23.2653 0.578736 20.0215 0.30016L19.8503 2.29283ZM6.14964 17.7072C3.90797 17.5147 2.17075 15.7011 2.07408 13.4528L0.075929 13.5387C0.215764 16.791 2.7347 19.4213 5.97851 19.6998L6.14964 17.7072ZM2.07408 6.54718C2.17075 4.29889 3.90798 2.48534 6.14965 2.29282L5.97853 0.30016C2.73471 0.578735 0.215764 3.20897 0.0759292 6.46127L2.07408 6.54718ZM20.0215 19.6998C23.2653 19.4213 25.7842 16.791 25.9241 13.5387L23.9259 13.4528C23.8292 15.7011 22.092 17.5147 19.8503 17.7072L20.0215 19.6998Z"
                    fill="none"/>
            </svg>
          </div>

        </div>
        <div
            class="hidden top-14 w-full absolute ml-auto mr-auto left-0 right-0 text-center max-w-lg h-72 rounded-full bg-highlight blur-2xl dark:block">

        </div>
        <div class="flex m-1    " role="group">
          <div v-if="mode=='edit' || mode=='create'"
               :class="mode=='edit'? file?'rounded-e':'rounded':''"
               class="text-center flex rounded-lg grow  cursor-pointer hover:bg-danger-600  p-2 bg-danger text-white   "
               :title="__('remove')"
               @click="song=null">
            <XMarkIcon class="w-4 h-4  mx-auto text-white  text-white"/>
          </div>
          <div v-if="mode=='edit' && file"

               class="cursor-pointer grow rounded-lg rounded-s hover:bg-success-600  p-2 bg-success text-white  "
               :title="__('upload')"
               @click="  showDialog('primary',__('new_file_replace_and_active_after_review'), __('upload') , uploadPodcast )">
            <CheckIcon class="w-4 h-4  mx-auto text-white   text-white" v-if="!uploading"/>
            <LoadingIcon class="w-4 h-4 mx-auto " v-if="uploading"/>
          </div>

        </div>
      </div>
    </div>


  </div>


</template>

<script>
import * as Amplitude from 'amplitudejs';
import 'amplitudejs/dist/visualizations/bar.js';
import LoadingIcon from "@/Components/LoadingIcon.vue";
import Image from "@/Components/Image.vue";
import {
  SpeakerXMarkIcon,
  SpeakerWaveIcon,
  XCircleIcon,
  XMarkIcon,
  CheckIcon,

} from '@heroicons/vue/24/solid';

export default {
  name: "Podcast",
  props: ['view', 'preload', 'label', 'mode', 'link', 'forId', 'classes',],
  components: {
    SpeakerXMarkIcon,
    SpeakerWaveIcon,
    XCircleIcon,
    XMarkIcon,
    CheckIcon,
    LoadingIcon,
    Image,
  },
  data() {
    return {
      url: null,
      file: null,
      uploader: null,
      uploadContainer: null,
      player: null,
      playing: false,
      repeat: false,
      speed: 1,
      song: null,
      volume: 50,
      uploading: false,
      percentage: 0,
      duration: 0,
    }
  },
  watch: {
    song() {

      if (!this.song && this.player)
        this.player.pause();
    },

  },
  mounted() {

    this.uploader = document.querySelector(`#podcast-${this.forId}`);
    this.uploadContainer = document.querySelector(`#container-podcast-${this.forId}`);

    this.player = Amplitude;
    if (this.preload)
      this.song = this.preload;
    if (this.song && this.mode != 'multi')
      this.initPlayer();

    this.$inertia.on('start', (event) => {
      // this.destroyPlayer();
    })
    // this.player.registerVisualization("bar_visualization");

  },
  updated() {
    // console.log(`updated ${this.forId}`)
    // console.log(`updated ${this.song.url}`)
  },

  beforeUnmount() {
    this.destroyPlayer();
  },

  methods: {
    destroyPlayer() {

      let anode = this.player.getAnalyser();

      // this.log(anode.context.state);

      this.player.stop();
      this.playing = false;
      if (anode && anode.context.state != 'closed') {
        anode.context.close().then(() => {
          this.player = null;
        });
      } else {
        this.player = null;
      }
    },
    initPlayer() {

      const songEl = document.getElementById('song-percentage-played');
      this.player.init({
        "bindings": {
          37: 'prev',
          39: 'next',
          32: 'play_pause'
        },
        visualization: 'bar_visualization',
        "callbacks": {
          timeupdate: function () {
            return;
            this.percentage = this.player.getSongPlayedPercentage();


            if (isNaN(this.percentage)) {
              this.percentage = 0;
            }
            /**
             * Massive Help from: https://nikitahl.com/style-range-input-css
             */
            let slider = songEl;
            slider.style.backgroundSize = this.percentage + '% 100%';
          }.bind(this),
          stop: function () {
            // console.log("  stopped.")
            this.playing = false;
          }.bind(this),
          initialized: function () {
            // console.log("  initialized.")
          }.bind(this),
          next: function () {
            // console.log("  next.")
          }.bind(this),
          prev: function () {
            // console.log("  prev.")
          }.bind(this),
          song_repeated: function () {
            // console.log("  song_repeated")
          }.bind(this),
          play: function () {
            // console.log("  play")
            this.playing = true;
          }.bind(this),
          pause: function () {
            // console.log("  pause")
            this.playing = false;
          }.bind(this),

        },
        "songs": [
          this.song,
          // {
          //   "name": "Risin' High (feat Raashan Ahmad)",
          //   "artist": "Ancient Astronauts",
          //   "album": "We Are to Answer",
          //   "url": this.file,
          //   "cover_art_url": null,
          //   "visualization": "bar_visualization",
          // }
        ],
        waveforms: {
          sample_rate: 50
        },
        // visualizations: [
        //   {
        //     object: window.BarVisualization,
        //     params: {}
        //   }]
      });

      window.onkeydown = function (e) {
        return !(e.keyCode == 32);
      };
      /*
        Handles a click on the song played progress bar.
      */

    },
    changeTime(e) {
      this.percentage = parseFloat(e.target.value);
      this.player.setSongPlayedPercentage(this.percentage);

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
      this.file = file;
      this.url = URL.createObjectURL(file);

      this.song = {
        'name': file.name,
        'url': this.url,
      };
      let anode = this.player.getAnalyser();

      // this.log(this.file);

      this.player.stop();
      this.playing = false;
      if (anode && anode.context) {
        anode.context.close().then(() => {
          this.initPlayer();
          this.player.play();
        });
      } else {
        this.initPlayer();

      }
      // this.log(this.player.getSongDuration());
      // this.creating = false;
      // this.$forceUpdate();


    },
    async uploadPodcast() {
//                console.log(this.$refs.dropdown.selected.length);

      this.uploading = true;
      var fd = new FormData();
      fd.append('podcast', this.file);
      fd.append('id', this.forId);
      fd.append('duration', parseInt(this.player.getSongDuration()));
      fd.append('cmnd', 'upload-podcast');
      fd.append('_method', 'PATCH')

      // let fd = {
      //   'podcast': this.file,
      //   'id': this.forId,
      //   'cmnd': 'upload-podcast',
      // };
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

  },
}
</script>

<style scoped lang="scss">

$primary: #00A0FF;

input[type="range"] {
  color: #ef233c;
  --thumb-height: 1.125em;
  --track-height: 0.125em;
  --track-color: rgba(0, 0, 0, 0.2);
  --brightness-hover: 180%;
  --brightness-down: 80%;
  --clip-edges: 0.125em;
}

div#progress-container:hover input[type=range].amplitude-song-slider::-webkit-slider-thumb {
  display: block;
}

div#progress-container:hover input[type=range].amplitude-song-slider::-moz-range-thumb {
  visibility: visible;
}


@media all and (-ms-high-contrast: none) {
  #progress-container *::-ms-backdrop, #progress-container progress#song-played-progress {
    color: $primary;
    border: none;
    background-color: #CFD8DC;
  }
}

@supports (-ms-ime-align: auto) {
  #progress-container progress#song-played-progress {
    color: $primary;
    border: none;
  }
}

#progress-container progress#song-played-progress[value]::-webkit-progress-bar {
  background: none;
  border-radius: 5px;
}

#progress-container progress#song-played-progress[value]::-webkit-progress-value {
  background-color: $primary;
  border-radius: 5px;
}

#progress-container progress#song-played-progress::-moz-progress-bar {
  background: none;
  border-radius: 5px;
  background-color: $primary;
  height: 5px;
  margin-top: -2px;
}


#progress-container progress#song-buffered-progress[value]::-webkit-progress-bar {
  background-color: #CFD8DC;
  border-radius: 5px;
}

#progress-container progress#song-buffered-progress[value]::-webkit-progress-value {
  background-color: #78909C;
  border-radius: 5px;
  transition: width .1s ease;
}

#progress-container progress#song-buffered-progress::-moz-progress-bar {
  background: none;
  border-radius: 5px;
  background-color: #78909C;
  height: 5px;
  margin-top: -2px;
}

#progress-container progress::-ms-fill {
  border: none;
}

@-moz-document url-prefix() {
  #progress-container progress#song-buffered-progress {
    top: 9px;
    border: none;
  }
}

@media all and (-ms-high-contrast: none) {
  #progress-container *::-ms-backdrop, #progress-container progress#song-buffered-progress {
    color: #78909C;
    border: none;
  }
}

@supports (-ms-ime-align: auto) {
  #progress-container progress#song-buffered-progress {
    color: #78909C;
    border: none;
  }
}


#progress-container input[type=range]:focus {
  outline: none;
}

#progress-container input[type=range]::-webkit-slider-runnable-track {
  width: 100%;
  height: 0px;
  cursor: pointer;
  box-shadow: 0px 0px 0px rgba(0, 0, 0, 0), 0px 0px 0px rgba(13, 13, 13, 0);
  background: #0075a9;
  border-radius: 0px;
  border: 0px solid #010101;

}

#progress-container input[type=range]::-webkit-slider-thumb {
  box-shadow: 0px 0px 0px #000000, 0px 0px 0px #0d0d0d;
  border: 1px solid $primary;
  height: 15px;
  width: 15px;
  border-radius: 16px;
  background: $primary;
  cursor: pointer;
  -webkit-appearance: none;
  margin-top: -7.5px;
}

#progress-container input[type=range]:focus::-webkit-slider-runnable-track {
  background: #00adfb;

}


#progress-container input[type=range]::-moz-range-track {
  width: 100%;
  height: 0px;
  cursor: pointer;
  box-shadow: 0px 0px 0px rgba(0, 0, 0, 0), 0px 0px 0px rgba(13, 13, 13, 0);
  background: #0075a9;
  border-radius: 0px;
  border: 0px solid #010101;
}

#progress-container input[type=range]::-moz-range-thumb {
  box-shadow: 0px 0px 0px #000000, 0px 0px 0px #0d0d0d;
  border: 1px solid $primary;
  height: 15px;
  width: 15px;
  border-radius: 16px;
  background: $primary;
  cursor: pointer;
}

#progress-container input[type=range]::-ms-track {
  width: 100%;
  height: 0px;
  cursor: pointer;
  background: transparent;
  border-color: transparent;
  color: transparent;
}

#progress-container input[type=range]::-ms-fill-lower {
  background: #003d57;
  border: 0px solid #010101;
  border-radius: 0px;
  box-shadow: 0px 0px 0px rgba(0, 0, 0, 0), 0px 0px 0px rgba(13, 13, 13, 0);
}

#progress-container input[type=range]::-ms-fill-upper {
  background: #0075a9;
  border: 0px solid #010101;
  border-radius: 0px;
  box-shadow: 0px 0px 0px rgba(0, 0, 0, 0), 0px 0px 0px rgba(13, 13, 13, 0);
}

#progress-container input[type=range]::-ms-thumb {
  box-shadow: 0px 0px 0px #000000, 0px 0px 0px #0d0d0d;
  border: 1px solid #00a0ff;
  height: 15px;
  width: 15px;
  border-radius: 16px;
  background: #00a0ff;
  cursor: pointer;
  height: 0px;
  display: block;
}

@media all and (-ms-high-contrast: none) {
  #progress-container *::-ms-backdrop, #progress-container input[type="range"].amplitude-song-slider {
    padding: 0px;
  }

  #progress-container *::-ms-backdrop, #progress-container input[type=range].amplitude-song-slider::-ms-thumb {
    height: 15px;
    width: 15px;
    border-radius: 10px;
    cursor: pointer;
    margin-top: -8px;
  }

  #progress-container *::-ms-backdrop, #progress-container input[type=range].amplitude-song-slider::-ms-track {
    border-width: 15px 0;
    border-color: transparent;
  }

  #progress-container *::-ms-backdrop, #progress-container input[type=range].amplitude-song-slider::-ms-fill-lower {
    background: #CFD8DC;
    border-radius: 10px;
  }

  #progress-container *::-ms-backdrop, #progress-container input[type=range].amplitude-song-slider::-ms-fill-upper {
    background: #CFD8DC;
    border-radius: 10px;
  }
}

@supports (-ms-ime-align: auto) {
  #progress-container input[type=range].amplitude-song-slider::-ms-thumb {
    height: 15px;
    width: 15px;
    margin-top: 3px;
  }
}

#progress-container input[type=range]:focus::-ms-fill-lower {
  background: #0075a9;
}

#progress-container input[type=range]:focus::-ms-fill-upper {
  background: #00adfb;
}


</style>