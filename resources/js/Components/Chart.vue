<template>
  <div :key="id" class="     position-relative  ">

    <div class="    ">

      <div class="   px-0 p-sm-1  ">
        <!--chart section-->
        <div class="  ">
          <div class="row  align-items-center px-1">
            <div class="flex items-center flex-wrap gap-1  justify-around">
              <div class="flex flex-wrap items-center justify-between  gap-2   ">


                <RadioGroup @change.prevent="updateTimestamp" class="grow"
                            :name="`timestamp-${id}`"
                            :items="[__('daily'),__('monthly'),__('yearly')]"/>
                <RadioGroup @change.prevent="updateTime" class="grow" :name="`time-${id}`"
                            :before-selected="__('last_week')"
                            :items="[__('today'),__('yesterday'),__('last_week'),__('last_month')]"/>
                <div class="  flex flex-wrap   gap-2  justify-center w-full">
                  <date-picker :id="`from-${id}`" class="rounded    fromdate" inputClass="" :editable="true"

                               inputFormat="YYYY/MM/DD" :placeholder="__('from_date')" color="#00acc1"
                               v-model="params.dateFrom" @change="getData()"></date-picker>
                  <date-picker :id="`to-${id}`" class="rounded-2   todate" inputClass="" :editable="true"

                               inputFormat="YYYY/MM/DD" :placeholder="__('to_date')" color="#dd77dd"
                               v-model="params.dateTo" @change="getData()"></date-picker>


                </div>
              </div>

            </div>


          </div>
          <div class="relative">
            <div v-show="loading" class="  absolute top-[50%] start-[50%] flex justify-center items-center"
                 style="  z-index: 10">
              <LoadingIcon class=" w-10 h-10 fill-primary"/>

            </div>
            <canvas class="w-full px-0 p-sm-4" :class="{'opacity-50':loading}" :id="`chart-${id}`"></canvas>
          </div>
        </div>

      </div>
    </div>
  </div>
</template>

<script>
import Chart from 'chart.js/auto';
import VuePersianDatetimePicker from 'vue3-persian-datetime-picker';
import {shallowRef} from 'vue';
import LoadingIcon from "@/Components/LoadingIcon.vue";
import RadioGroup from "@/Components/RadioGroup.vue";

let colors = [
  'rgba(54, 162, 235, 1)',
  'rgba(255, 99, 132, 1)',
  'rgba(255, 206, 86, 1)',
  'rgba(75, 192, 192, 1)',
  'rgba(153, 102, 255, 1)',
  'rgba(255, 159, 64, 1)'];
export default {
  props: ['id', 'logLink', 'parentParams', 'logTypes', 'units'],
  components: {
    datePicker: VuePersianDatetimePicker,
    LoadingIcon,
    RadioGroup,
  },
  data() {
    return {
      data: [],
      params: {
        timestamp: 'd',
        order_dir: 'DESC',
        order_by: 'created_at',
        // type: this.logTypes[0],
        type: null,
        unit: this.units[0],
        types: [],

      },
      link: null,
      loading: false,
      types: [],
      chart: null,
      table: [],
      toggleSort: true

    }
  },
  mounted() {

    this.types = [];
    // for (let k in this.logTypes)
    //   this.types.push({'name': this.logTypes[k], 'checked': true});

    document.querySelector(`#from-${this.id} label`).append(this.__('from_date'));
    document.querySelector(`#to-${this.id} label`).append(this.__('to_date'));

    document.querySelectorAll('.vpd-input-group').forEach((el) => {
      el.classList.add('flex');

    });
    document.querySelectorAll('.vpd-input-group input').forEach((el) => {
      el.classList.add('rounded');
    });
    document.querySelectorAll('.vpd-input-group label').forEach((el) => {
      el.classList.add('rounded-s');
    });
    // this.params.dateFrom = this.date('yesterday');
    this.params.dateFrom = this.date('last_week');
    this.params.dateTo = this.date();
    this.initChart();
    this.getData();
  },
  methods: {

    updateTime(event) {
      const val = event.target.value;
      var today = new Date().getTime();
      var date = new Date();
      date.setDate(date.getDate() - 1);
      var yesterday = date.getTime();

      var date = new Date();
      date.setDate(date.getDate() - 7);
      var lastWeek = date.getTime();

      var date = new Date();
      date.setMonth(date.getMonth() - 1);
      var lastMonth = date.getTime();

      // this.log(this.toShamsi(lastMonth));
      switch (val) {
        case this.__('today'):
          this.params.dateFrom = this.toShamsi(today);
          this.params.dateTo = this.params.dateFrom;
          this.params.timestamp = 'd';
          break;
        case this.__('yesterday'):
          this.params.dateFrom = this.toShamsi(yesterday);
          this.params.dateTo = this.toShamsi(yesterday);
          this.params.timestamp = 'd';
          break;
        case this.__('last_week'):
          this.params.dateFrom = this.toShamsi(lastWeek);
          this.params.dateTo = this.toShamsi(today);
          this.params.timestamp = 'd';
          break;
        case this.__('last_month'):
          this.params.dateFrom = this.toShamsi(lastMonth);
          this.params.dateTo = this.toShamsi(today);
          this.params.timestamp = 'd';
          break;
        default:


      }
      this.getData();
    },
    updateUnit(event) {
      const val = event.target.value;
      this.params.unit = val;

      this.getData();
    },
    updateTimestamp(event) {
      const val = event.target.value;

      switch (val) {
        case this.__('daily'):
          this.params.timestamp = 'd';
          break;
        case this.__('monthly'):
          this.params.timestamp = 'm';
          break;
        case this.__('yearly'):
          this.params.timestamp = 'y';
          break;
        default:
          this.params.timestamp = null;

      }
      this.getData();
    },
    updateTypes(event) {
      const val = event.target.value;
      this.params.type = val;

      // this.params.types = [];
      // for (let i in this.types)
      //   if (this.types[i].checked)
      //     this.params.types.push(this.types[i].name);
      // if (this.params.types.length == this.types.length)
      //   this.params.types = [];
      this.getData();
    },
    sort(by) {
      this.toggleSort = !this.toggleSort;
      this.table.sort(function (a, b) {
        return ('' + a[by]).localeCompare('' + b[by]);
//                    return a[by] - b[by];
      });
      if (this.toggleSort)
        this.table.reverse();
    },

    initChart() {
      const ctx = document.getElementById(`chart-${this.id}`).getContext('2d');
      this.chart = shallowRef(new Chart(ctx, {
        type: 'line',
        borderWidth: 50,
        options: {
          responsive: true,
          plugins: {
            title: {
              display: true,
              text: ''
            },
          },
          interaction: {
            intersect: false,
          },
          scales: {
            x: {
              display: true,
              title: {
                display: true
              }
            },
            y: {
              beginAtZero: true,
              display: true,
              title: {
                display: true,
                text: this.params.unit
              },
//                                suggestedMin: -10,
//                                suggestedMax: 200
            }
          }
        },
        data: {},

      }));
    },
    updateChart() {

      this.chart.data.datasets = [];

      let ix = -1;

      for (let i in this.data.datas) {
        ix++;
        let counts = [];

        for (let idx in this.data.dates) {

          if (!Object.keys(this.data.datas[i]).includes(this.data.dates[idx])) {
            this.data.datas[i][this.data.dates[idx]] = [];
          }
          if (this.params.unit === this.__('currency'))
            counts.push(this.data.datas[i][this.data.dates[idx]].reduce((partialSum, a) => partialSum + (!isNaN(a.sum) ? parseInt(a.sum) : 0), 0));

          else if (this.params.unit === this.__('meta'))
            counts.push(this.data.datas[i][this.data.dates[idx]].reduce((partialSum, a) => partialSum + (!isNaN(a.sum_meta) ? parseInt(a.sum_meta) : 0), 0));

          else if (this.params.unit === this.__('view'))
            counts.push(this.data.datas[i][this.data.dates[idx]].reduce((partialSum, a) => partialSum + (!isNaN(a.view) ? parseInt(a.view) : 0), 0));

        }

//                    var r = Math.floor(Math.random() * 255);
//                    var g = Math.floor(Math.random() * 255);
//                    var b = Math.floor(Math.random() * 255);

        this.chart.data.datasets.push({
          label: this.params.unit,
//                        cubicInterpolationMode: 'monotone',
          borderWidth: 2,
          data: counts,
//                        borderColor: "rgb(" + r + "," + g + "," + b + ")",
          borderColor: colors[ix],
          backgroundColor: colors[ix],
          tension: 0.4,
        });
      }
      this.chart.data.labels = e2f(this.data.dates);

//                this.chart.options.scales.y.title.text = this.params.type === 'مالی'
//                    ? 'پرداخت (تومان)' : 'تعداد';
      this.chart.options.scales.y.title.text = this.params.unit;
      this.chart.update();
    },
    getData() {
//                console.log({...this.params, ...JSON.parse(this.parentParams)});
      this.loading = true;
      // console.log(this.params);
      window.axios.post(this.logLink,
          {...this.params, ...this.parentParams}
      )

          .then((response) => {
//                            console.log(response.data);
                this.loading = false;

                if (response.status === 200) {
                  this.data = response.data;
                  this.updateChart();
                  this.table = [];

                  this.table = [];
                  for (let i in this.data.datas) {
                    for (let j in this.data.datas[i])
                      this.table.push(this.data.datas[i][j]);

                  }
                  this.table = this.table.flat();

                }
                //                            window.location.reload();


              }
          ).catch((error) => {
        this.loading = false;

        let errors = '';
        if (error.response && error.response.status === 422)
          for (let idx in error.response.data.errors)
            errors += error.response.data.errors[idx] + '<br>';
        else if (error.response && error.response.status === 403)
          errors = error.response.data.message;
        else {
          errors = error;
        }
        console.log(errors);

      });
    },
    date(day) {
      const t = new Date().getTime();
      let today;
      if (day && day === 'yesterday')
        today = new Date(t - 24 * 60 * 60 * 1000);

      else if (day && day === 'last_week')
        today = new Date(t - 24 * 7 * 60 * 60 * 1000);
      else
        today = new Date(t + 24 * 60 * 60 * 1000);
      let options = {
        hour12: false,

        year: 'numeric',
        month: '2-digit',
        day: '2-digit',

        calendar: 'persian',
      };
//                var dd = String(today.getDate()).padStart(2, '0');
//                var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
//                var yyyy = today.getFullYear();
//                return yyyy + '/' + mm + '/' + dd;

      return f2e(today.toLocaleDateString('fa-IR', options));
    }
    ,
    log(str) {
      console.log(str);
    },

  },
}
</script>
