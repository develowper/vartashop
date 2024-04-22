<template>
  <div>
    <template v-if="percentage">
      <div class="text-center text-primary"> {{ percentage }}%</div>
    </template>
    <template v-if="!type" class="transition-all duration-500 ">
      <svg fill="#fff" :class="class" width="135" height="140" viewBox="0 0 135 140" xmlns="http://www.w3.org/2000/svg">
        <rect y="10" width="15" height="120" rx="6">
          <animate attributeName="height"
                   begin="0.5s" dur="1s"
                   values="120;110;100;90;80;70;60;50;40;140;120" calcMode="linear"
                   repeatCount="indefinite"/>
          <animate attributeName="y"
                   begin="0.5s" dur="1s"
                   values="10;15;20;25;30;35;40;45;50;0;10" calcMode="linear"
                   repeatCount="indefinite"/>
        </rect>
        <rect x="30" y="10" width="15" height="120" rx="6">
          <animate attributeName="height"
                   begin="0.25s" dur="1s"
                   values="120;110;100;90;80;70;60;50;40;140;120" calcMode="linear"
                   repeatCount="indefinite"/>
          <animate attributeName="y"
                   begin="0.25s" dur="1s"
                   values="10;15;20;25;30;35;40;45;50;0;10" calcMode="linear"
                   repeatCount="indefinite"/>
        </rect>
        <rect x="60" width="15" height="140" rx="6">
          <animate attributeName="height"
                   begin="0s" dur="1s"
                   values="120;110;100;90;80;70;60;50;40;140;120" calcMode="linear"
                   repeatCount="indefinite"/>
          <animate attributeName="y"
                   begin="0s" dur="1s"
                   values="10;15;20;25;30;35;40;45;50;0;10" calcMode="linear"
                   repeatCount="indefinite"/>
        </rect>
        <rect x="90" y="10" width="15" height="120" rx="6">
          <animate attributeName="height"
                   begin="0.25s" dur="1s"
                   values="120;110;100;90;80;70;60;50;40;140;120" calcMode="linear"
                   repeatCount="indefinite"/>
          <animate attributeName="y"
                   begin="0.25s" dur="1s"
                   values="10;15;20;25;30;35;40;45;50;0;10" calcMode="linear"
                   repeatCount="indefinite"/>
        </rect>
        <rect x="120" y="10" width="15" height="120" rx="6">
          <animate attributeName="height"
                   begin="0.5s" dur="1s"
                   values="120;110;100;90;80;70;60;50;40;140;120" calcMode="linear"
                   repeatCount="indefinite"/>
          <animate attributeName="y"
                   begin="0.5s" dur="1s"
                   values="10;15;20;25;30;35;40;45;50;0;10" calcMode="linear"
                   repeatCount="indefinite"/>
        </rect>
      </svg>
    </template>
    <template v-else-if="type=='audio'" class="transition-all duration-500">
      <!-- By Sam Herbert (@sherb), for everyone. More @ http://goo.gl/7AJzbL -->
      <svg fill="#fff" :class="class" width="55" height="80" viewBox="0 0 55 80" xmlns="http://www.w3.org/2000/svg">
        <g transform="matrix(1 0 0 -1 0 80)">
          <rect width="10" height="20" rx="3">
            <animate attributeName="height"
                     begin="0s" dur="4.3s"
                     values="20;45;57;80;64;32;66;45;64;23;66;13;64;56;34;34;2;23;76;79;20" calcMode="linear"
                     repeatCount="indefinite"/>
          </rect>
          <rect x="15" width="10" height="80" rx="3">
            <animate attributeName="height"
                     begin="0s" dur="2s"
                     values="80;55;33;5;75;23;73;33;12;14;60;80" calcMode="linear"
                     repeatCount="indefinite"/>
          </rect>
          <rect x="30" width="10" height="50" rx="3">
            <animate attributeName="height"
                     begin="0s" dur="1.4s"
                     values="50;34;78;23;56;23;34;76;80;54;21;50" calcMode="linear"
                     repeatCount="indefinite"/>
          </rect>
          <rect x="45" width="10" height="30" rx="3">
            <animate attributeName="height"
                     begin="0s" dur="2s"
                     values="30;45;13;80;56;72;45;76;34;23;67;30" calcMode="linear"
                     repeatCount="indefinite"/>
          </rect>
        </g>
      </svg>
    </template>
    <template v-else-if="type=='grid'" class="transition-all duration-500">
      <svg fill="#fff" :class="class" width="105" height="105" viewBox="0 0 105 105" xmlns="http://www.w3.org/2000/svg">
        <circle cx="12.5" cy="12.5" r="12.5">
          <animate attributeName="fill-opacity"
                   begin="0s" dur="1s"
                   values="1;.2;1" calcMode="linear"
                   repeatCount="indefinite"/>
        </circle>
        <circle cx="12.5" cy="52.5" r="12.5" fill-opacity=".5">
          <animate attributeName="fill-opacity"
                   begin="100ms" dur="1s"
                   values="1;.2;1" calcMode="linear"
                   repeatCount="indefinite"/>
        </circle>
        <circle cx="52.5" cy="12.5" r="12.5">
          <animate attributeName="fill-opacity"
                   begin="300ms" dur="1s"
                   values="1;.2;1" calcMode="linear"
                   repeatCount="indefinite"/>
        </circle>
        <circle cx="52.5" cy="52.5" r="12.5">
          <animate attributeName="fill-opacity"
                   begin="600ms" dur="1s"
                   values="1;.2;1" calcMode="linear"
                   repeatCount="indefinite"/>
        </circle>
        <circle cx="92.5" cy="12.5" r="12.5">
          <animate attributeName="fill-opacity"
                   begin="800ms" dur="1s"
                   values="1;.2;1" calcMode="linear"
                   repeatCount="indefinite"/>
        </circle>
        <circle cx="92.5" cy="52.5" r="12.5">
          <animate attributeName="fill-opacity"
                   begin="400ms" dur="1s"
                   values="1;.2;1" calcMode="linear"
                   repeatCount="indefinite"/>
        </circle>
        <circle cx="12.5" cy="92.5" r="12.5">
          <animate attributeName="fill-opacity"
                   begin="700ms" dur="1s"
                   values="1;.2;1" calcMode="linear"
                   repeatCount="indefinite"/>
        </circle>
        <circle cx="52.5" cy="92.5" r="12.5">
          <animate attributeName="fill-opacity"
                   begin="500ms" dur="1s"
                   values="1;.2;1" calcMode="linear"
                   repeatCount="indefinite"/>
        </circle>
        <circle cx="92.5" cy="92.5" r="12.5">
          <animate attributeName="fill-opacity"
                   begin="200ms" dur="1s"
                   values="1;.2;1" calcMode="linear"
                   repeatCount="indefinite"/>
        </circle>
      </svg>
    </template>

    <template v-else-if="type=='spin-dot'" class="transition-all duration-500">
      <!-- By Sam Herbert (@sherb), for everyone. More @ http://goo.gl/7AJzbL -->
      <svg fill="#fff" :class="class" width="58" height="58" viewBox="0 0 58 58" xmlns="http://www.w3.org/2000/svg">
        <g fill="none" fill-rule="evenodd">
          <g transform="translate(2 1)" stroke="#FFF" stroke-width="1.5">
            <circle cx="42.601" cy="11.462" r="5" fill-opacity="1" fill="#fff">
              <animate attributeName="fill-opacity"
                       begin="0s" dur="1.3s"
                       values="1;0;0;0;0;0;0;0" calcMode="linear"
                       repeatCount="indefinite"/>
            </circle>
            <circle cx="49.063" cy="27.063" r="5" fill-opacity="0" fill="#fff">
              <animate attributeName="fill-opacity"
                       begin="0s" dur="1.3s"
                       values="0;1;0;0;0;0;0;0" calcMode="linear"
                       repeatCount="indefinite"/>
            </circle>
            <circle cx="42.601" cy="42.663" r="5" fill-opacity="0" fill="#fff">
              <animate attributeName="fill-opacity"
                       begin="0s" dur="1.3s"
                       values="0;0;1;0;0;0;0;0" calcMode="linear"
                       repeatCount="indefinite"/>
            </circle>
            <circle cx="27" cy="49.125" r="5" fill-opacity="0" fill="#fff">
              <animate attributeName="fill-opacity"
                       begin="0s" dur="1.3s"
                       values="0;0;0;1;0;0;0;0" calcMode="linear"
                       repeatCount="indefinite"/>
            </circle>
            <circle cx="11.399" cy="42.663" r="5" fill-opacity="0" fill="#fff">
              <animate attributeName="fill-opacity"
                       begin="0s" dur="1.3s"
                       values="0;0;0;0;1;0;0;0" calcMode="linear"
                       repeatCount="indefinite"/>
            </circle>
            <circle cx="4.938" cy="27.063" r="5" fill-opacity="0" fill="#fff">
              <animate attributeName="fill-opacity"
                       begin="0s" dur="1.3s"
                       values="0;0;0;0;0;1;0;0" calcMode="linear"
                       repeatCount="indefinite"/>
            </circle>
            <circle cx="11.399" cy="11.462" r="5" fill-opacity="0" fill="#fff">
              <animate attributeName="fill-opacity"
                       begin="0s" dur="1.3s"
                       values="0;0;0;0;0;0;1;0" calcMode="linear"
                       repeatCount="indefinite"/>
            </circle>
            <circle cx="27" cy="5" r="5" fill-opacity="0" fill="#fff">
              <animate attributeName="fill-opacity"
                       begin="0s" dur="1.3s"
                       values="0;0;0;0;0;0;0;1" calcMode="linear"
                       repeatCount="indefinite"/>
            </circle>
          </g>
        </g>
      </svg>
    </template>

    <template v-else-if="type=='linear'" class="transition-all duration-500 ">
      <div class="progress-line mt-1"></div>
    </template>


    <template v-else-if="type=='line-dot'" class="transition-all duration-500 ">
      <!-- By Sam Herbert (@sherb), for everyone. More @ http://goo.gl/7AJzbL -->
      <svg fill="#fff" :class="class" width="120" height="30" viewBox="0 0 120 30" xmlns="http://www.w3.org/2000/svg">
        <circle cx="15" cy="15" r="15">
          <animate attributeName="r" from="15" to="15"
                   begin="0s" dur="0.8s"
                   values="15;9;15" calcMode="linear"
                   repeatCount="indefinite"/>
          <animate attributeName="fill-opacity" from="1" to="1"
                   begin="0s" dur="0.8s"
                   values="1;.5;1" calcMode="linear"
                   repeatCount="indefinite"/>
        </circle>
        <circle cx="60" cy="15" r="9" fill-opacity="0.3">
          <animate attributeName="r" from="9" to="9"
                   begin="0s" dur="0.8s"
                   values="9;15;9" calcMode="linear"
                   repeatCount="indefinite"/>
          <animate attributeName="fill-opacity" from="0.5" to="0.5"
                   begin="0s" dur="0.8s"
                   values=".5;1;.5" calcMode="linear"
                   repeatCount="indefinite"/>
        </circle>
        <circle cx="105" cy="15" r="15">
          <animate attributeName="r" from="15" to="15"
                   begin="0s" dur="0.8s"
                   values="15;9;15" calcMode="linear"
                   repeatCount="indefinite"/>
          <animate attributeName="fill-opacity" from="1" to="1"
                   begin="0s" dur="0.8s"
                   values="1;.5;1" calcMode="linear"
                   repeatCount="indefinite"/>
        </circle>
      </svg>
    </template>
  </div>
</template>

<script>
export default {
  data() {
    return {}
  },
  props: ['type', 'class', 'percentage']
}
</script>
<style scoped lang="scss">
$border-radius: .25rem;
$gray-400: #ced4da;
$purple: #32cd32;
$indigo: #1d8d1d;
.progress-line {
  border-radius: $border-radius;
  background-color: $gray-400;
  display: -webkit-flex;
  display: flex;

  &:before {
    border-radius: $border-radius;
    height: .5rem;
    width: 100%;
    margin: 0;
    background-color: $indigo;
    content: '';
    -webkit-animation: running-progress 2s cubic-bezier(0.4, 0, 0.2, 1) infinite;
    animation: running-progress 2s cubic-bezier(0.4, 0, 0.2, 1) infinite;
  }

  @-webkit-keyframes running-progress {
    0% {
      margin-left: 0px;
      margin-right: 100%;
    }
    50% {
      margin-left: 25%;
      margin-right: 0%;
    }
    100% {
      margin-left: 100%;
      margin-right: 0;
    }
  }

  @keyframes running-progress {
    0% {
      margin-left: 0px;
      margin-right: 100%;
    }
    50% {
      margin-left: 25%;
      margin-right: 0%;
    }
    100% {
      margin-left: 100%;
      margin-right: 0;
    }
  }
}
</style>