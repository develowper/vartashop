<template>
  <div class="fixed  bottom-1 end-1" style="z-index:10000 !important;">
    <!--:class="maximize?' left-2 top-2 bottom-2 right-2  ':'bottom-1 right-1'"-->
    <div v-show="showChat" class="bg-white overflow-hidden shadow-md   rounded-lg "
         :style=" maximize?'height: 90vh;width: 90vw; ':'height:  75vh; ;min-width: 20rem;width:40vw; '">
      <div class="flex flex-col  items-stretch  h-full   ">

        <div class="p-2  flex items-center justify-between  bg-primary-500   flex  ">
          <div @click="maximize=!maximize" class="text-white  text-sm ">
            پشتیبان آنلاین
            <i class="fa fa-headset text-success" aria-hidden="true"></i>
            ({{ supporterName }})
          </div>
          <div class="flex items-center justify-center ">

            <div @click="maximize=!maximize" class="mx-1 cursor-pointer  ">
              <ArrowsPointingInIcon v-if="maximize" class="w-5 text-white"/>
              <ArrowsPointingOutIcon v-else class="w-5 text-white"/>
            </div>
            <div @click="showChat=false" class="mx-1 cursor-pointer">
              <XMarkIcon class="w-5 text-white"/>
            </div>
            <!--            <div @click="clearHistory()" class="text-dark  hoverable-light">-->
            <!--              <i class="fa fa-trash text-danger move-on-hover" aria-hidden="true"></i>-->
            <!--            </div>-->
          </div>


        </div>
        <div id="chats-container" class="     z-10    overflow-y-scroll">

          <div v-for="msg in messages   " class="flex-row flex text-sm">
            <div v-html="renderHTML(msg.message)"
                 class=" text-right  p-2 my-1   bg-white text-dark shadow-md  "
                 :class="msg.from!=null && msg.from.includes('support')? 'me-1 ms-auto rounded-e-lg':'ms-1 me-auto rounded-s-lg'">
            </div>
            <div v-if="msg.from!=null && msg.from.includes('support')"
                 class="rounded-circle shadow-card p-2 align-self-center">
              <i class="fa fa-headset text-success   " aria-hidden="true"></i>
            </div>
          </div>

          <!--<div class="opacity-2 position-fixed top-0 left-0 right-0 bottom-0 w-100 h-100 bg-success "></div>-->
        </div>
        <div class=" p-2    flex flex-row   items-center">
          <LoadingIcon class="w-4 h-5 mx-3 fill-primary-500" v-if="loading"/>
          <PaperAirplaneIcon v-else @click="sendMessage"
                             class="text-primary-500 w-8  cursor-pointer hover:text-primary-400"/>
          <input :disabled="loading?true:null" type="text" v-model="msg"
                 class="px-1 grow rounded-lg border-gray-500 mx-1"
                 v-on:keyup.enter="sendMessage( )">
        </div>
      </div>

    </div>
    <div v-show="!showChat"
         class="rounded-full shadow-md  bg-primary-500 hover:bg-primary-400  cursor-pointer"
         style="height: 4rem;width: 4rem;"
         @click="showChat=!showChat;chatContainer.scrollTop = chatContainer.scrollHeight;">
      <ChatBubbleLeftEllipsisIcon class="text-white p-3"/>

    </div>
  </div>
</template>

<script>

import Echo from 'laravel-echo';
import {
  XMarkIcon,
  ChatBubbleLeftEllipsisIcon,
  ArrowsPointingOutIcon,
  ArrowsPointingInIcon,
  PaperAirplaneIcon,

} from "@heroicons/vue/24/solid";
import LoadingIcon from "@/Components/LoadingIcon.vue";
import Pusher from 'pusher-js';
import icon from "@/../images/logo.png";

export default {
  mounted() {

    this.initPusher();
    this.chatContainer = document.getElementById('chats-container');
  },
  props: ['broadcastLink', 'key', 'cluster', 'ip', 'supportHistoryLink'],

  components: {
    XMarkIcon,
    ChatBubbleLeftEllipsisIcon,
    ArrowsPointingOutIcon,
    ArrowsPointingInIcon,
    PaperAirplaneIcon,
    LoadingIcon,
  },
  watch: {
    showChat: (newVal, oldVal) => {
      if (newVal) {
        // $('#chats-container').animate({scrollTop: 100000}, 100);


      }
    }
  },
  data() {
    return {
      msg: null,
      messages: [],
      showChat: false,
      maximize: false,
      supporterName: null,
      container: null,
      loading: false,
    }
  },
  methods: {

    sendMessage() {
      if (this.msg) {
        this.loading = true;
        window.axios.post(this.broadcastLink, {
          message: this.msg,
          from: this.ip,
          to: null,
          msgId: new Date().getTime(),
          chatId: this.ip,
        },).then((resp) => {

          this.msg = null;
          this.chatContainer.scrollTop = this.chatContainer.scrollHeight;

        }).finally(() => {
          this.loading = false;
        });
      }
//                $('#chat-container').scrollTop($('#chat-container').height());

//                chatContainer.scrollTop(chatContainer.prop("scrollHeight"));
    },
    initPusher() {
      this.fetchHistory();

      window.Pusher.logToConsole = true;


//                Sometimes you may wish to broadcast an event to other connected clients without hitting your Laravel application at all. This can be particularly useful for things like "typing" notifications
      this.Echo = new Echo({
        broadcaster: 'pusher',
        key: import.meta.env.VITE_PUSHER_APP_KEY,
        cluster: "ap2",
        forceTLS: true,
        logToConsole: true,
//                    authEndpoint: '/custom/endpoint/auth',
//                    authorizer: (channel, options) => {
//                        return {
//                            authorize: (socketId, callback) => {
//                                window.axios.post('/api/broadcasting/auth', {
//                                    socket_id: socketId,
//                                    channel_name: channel.name
//                                })
//                                    .then(response => {
//                                        callback(false, response.data);
//                                    })
//                                    .catch(error => {
//                                        callback(true, error);
//                                    });
//                            }
//                        };
//                    },
      });
//                this.setPusherEvents();

      this.Echo.channel/*private*/(`support.${this.ip}`)

          .listen('.chat', (e) => {

            this.messages.push({
              message: e.message,
              from: e.from,
              to: e.to,
              chatId: e.chatId,
            });
            this.addToHistory(e);

            if (e.from !== null && e.from.includes('support'))
              this.supporterName = e.from;
            // this.container.animate({scrollTop: this.container.prop('scrollHeight') + 100}, 200);
            if ('Notification' in window) {

              if (Notification.permission === "granted") {

                let notification = new Notification('پیام از پشتیبان', {
                  body: e.message, // content for the alert
                  icon: this.icon // optional image url
                });

                // link to page on clicking the notification
                notification.onclick = () => {
                  window.open(window.location.href);
                };
              } else if (Notification.permission !== "denied") {

                Notification.requestPermission().then(permission => {
                  let notification = new Notification('پیام از پشتیبان', {
                    body: e.message, // content for the alert
                    icon: this.icon  // optional image url
                  });

                  // link to page on clicking the notification
                  notification.onclick = () => {
                    window.open(window.location.href);
                  };
                });
              }


            }

          }).listenForWhisper('typing', () => {
        console.log('typing');
      }).subscribed(() => {
        console.log('subscribed');
      }).error((e) => {
        console.log('error: ' + e);
      })/*.whisper('typing', {
                    msg: 'typing'
                })*/;


//
//                window.Echo.join('support')
//                    .here(users => {
//                        console.log('here');
//                        console.log(users);
//                    })
//                    .joining(user => {
//                        console.log('joining');
//                        console.log(user);
//                    })
//                    .leaving(user => {
//                        console.log('leaving');
//                        console.log(user);
//                    })
//                    .listen('.chat', (e) => {
//                        console.log('listen');
//                        console.log(user);
//                    })

    },
    leaveChannel() {
      Echo.leaveChannel(`orders.1`);

//                If you would like to leave a channel and also its associated private and presence channels, you may call the leave method:
//                    Echo.leave(`orders.${this.order.id}`);
    },
    addToHistory(message) {

      window.axios.post(this.supportHistoryLink, {cmnd: 'add', data: message}).then(response => {
//                    console.log(response.data);

      });
    },
    fetchHistory() {

      window.axios.post(this.supportHistoryLink, {cmnd: 'get'}).then(response => {
//                    console.log(response.data);
        this.messages = response.data;
      });

    },
    clearHistory() {

      window.axios.post(this.supportHistoryLink, {cmnd: 'clear'}).then(response => {
        this.messages = response.data;
      });
    },


    renderHTML(text) {
      let rawText = this.strip(text);
      let urlRegex = /(((https?|ftp|file):\/\/|www.)[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|](.com|.ir|.net|.org)*)/gi;

      return rawText.replace(urlRegex, function (url) {
        let url2 = url;
        if (!url.startsWith('http') && !url.startsWith('ftp') && !url.startsWith('file'))
          url2 = 'http://' + url;
        if ((url.indexOf(".jpg") > 0) || (url.indexOf(".png") > 0) || (url.indexOf(".gif") > 0)) {
          return ' <img src="' + url2 + '" class="w-100 img-thumbnail rounded-2 "> ' + '<br/>' +
              ' <a href="' + url2 + '" class="text-info" target="_blank">' + url + '</a> '
        } else {

          return ' <a href="' + url2 + '" class="text-info" target="_blank">' + url + '</a> '
        }
      })
    },
    strip(html) {
      let tmp = document.createElement("DIV");
      tmp.innerHTML = html;
      let urlRegex = /(((https?|ftp|file):\/\/|www.)[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|](.com|.ir|.net|.org)*)/gi;
      return tmp.innerText.replace(urlRegex, function (url) {
        return url
      })
    },
    setPusherEvents() {
      this.Echo.connector.pusher.connection.bind('connecting', (payload) => {

        /**
         * All dependencies have been loaded and Channels is trying to connect.
         * The connection will also enter this state when it is trying to reconnect after a connection failure.
         */

        console.log('connecting...');

      });

      this.Echo.connector.pusher.connection.bind('connected', (payload) => {

        /**
         * The connection to Channels is open and authenticated with your app.
         */

        console.log('***connected!', payload);

      });

      this.Echo.connector.pusher.connection.bind('unavailable', (payload) => {

        /**
         *  The connection is temporarily unavailable. In most cases this means that there is no internet connection.
         *  It could also mean that Channels is down, or some intermediary is blocking the connection. In this state,
         *  pusher-js will automatically retry the connection every 15 seconds.
         */

        console.log('unavailable', payload);
      });

      this.Echo.connector.pusher.connection.bind('failed', (payload) => {

        /**
         * Channels is not supported by the browser.
         * This implies that WebSockets are not natively available and an HTTP-based transport could not be found.
         */

        console.log('failed', payload);

      });

      this.Echo.connector.pusher.connection.bind('disconnected', (payload) => {

        /**
         * The Channels connection was previously connected and has now intentionally been closed
         */

        console.log('disconnected', payload);

      });

      this.Echo.connector.pusher.connection.bind('message', (payload) => {

        /**
         * Ping received from server
         */

        console.log('message', payload);
      });
      this.Echo.connector.pusher.connection.bind('succeeded', (payload) => {

        /**
         * Ping received from server
         */

        console.log('******hi', payload);
      });
    }
  },
}
</script>
