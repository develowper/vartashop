<template>


  <div class=" ">
    <div>
      <SearchInput class="w-full  " v-model="search" @input="searchMap" @search="searchMap"/>

      <LoadingIcon class="w-6 mx-auto  mx-3 fill-primary-500 " type="line-dot" v-show="loading"/>
      <ul class=" border boredr-t-0 shadow-lg rounded mb-1">
        <!--        <li v-if="searchMapResults.length==0">{{ __('no_result') }}</li>-->
        <li class="py-3 px-1 text-neutral-600 cursor-pointer hover:bg-neutral-100 rounded"
            @click="setLocation({y: res.location.y, x: res.location.x}); getAddressFromLocation({lat: res.location.y, lng: res.location.x});"
            v-for="res in searchMapResults">
          {{ res.address }}
        </li>
      </ul>
    </div>
    <div class=" " id="map"></div>
    <!--    <NeshanMap-->
    <!--        :mapKey="mapKey"-->
    <!--    />-->
  </div>
</template>

<script>

// import L from "leaflet";
// import L from '../neshan1.9.4'
import 'leaflet.fullscreen';
// import 'leaflet-bing-layer/leaflet-bing-layer.min';
import {
  MapPinIcon,
} from "@heroicons/vue/24/outline";
import {GeoSearchControl, OpenStreetMapProvider} from 'leaflet-geosearch';
import SearchInput from "@/Components/SearchInput.vue";
import LoadingIcon from "@/Components/LoadingIcon.vue";
import {Head, Link} from '@inertiajs/vue3';

let self;
export default {
  name: "Map",
  props: ['mode', 'preload'],
  emits: ['change'],
  components: {
    SearchInput,
    MapPinIcon,
    LoadingIcon,
    Head,
  },
  data() {
    return {
      mapKey: import.meta.env.VITE_MAP_API,
      mapServiceKey: import.meta.env.VITE_MAP_SERVICE_API,
      bingMapsKey: 'AnG2T50AOKMBYM0Qe3UEoyYYGlEpRswFDz7IGeH9-w4-hekJeRCOPaWwgF88AOwX',
      mapAddress: null,
      loading: false,
      search: null,
      map: null,
      marker: null,
      location: null,
      clearBtn: null,
      label: '',
      iranLoc: [32.4279, 53.6880],
      myIcon: null,
      mapLayers:
          {
            google: "https://mt.google.com/vt/lyrs=m&x={x}&y={y}&z={z}&s=IR&hl=fa",
            osm: "http://{s}.tile.osm.org/{z}/{x}/{y}.png",
            parsimap: "https://vt.parsimap.com/comapi.svc/tile/parsimap/%7Bx%7D/%7By%7D/%7Bz%7D.jpg?token=ee9e06b3-dcaa-4a45-a60c-21ae72dca0bb",
            air: "https://api.ellipsis-drive.com/v3/ogc/wcs/93e1c322-f21e-4395-9566-51abf473d2b9?request=getCapabilities",
            bing: "https://www.bing.com/api/maps/mapcontrol?key=AnG2T50AOKMBYM0Qe3UEoyYYGlEpRswFDz7IGeH9-w4-hekJeRCOPaWwgF88AOwX&callback=loadMapScenario",
            METADATA_URL: `https://dev.virtualearth.net/REST/v1/Imagery/Metadata/{'imagerySet'}?key={bingMapsKey}&include=ImageryProviders&uriScheme=https&c={'culture'}`,
            POINT_METADATA_URL: `https://dev.virtualearth.net/REST/v1/Imagery/Metadata/{'imagerySet'}/{lat},{lng}?zl={z}&key={bingMapsKey}&uriScheme=https&c={'culture'}`

          },
      searchLayer: null,
      mapSearchProvider: null,
      searchMapResults: [],
    }
  },
  mounted() {
    self = this;
    let fullScreenLabel = this.__('full_screen');
    let clearLabel = this.__('clear');


    // this.mapSearchProvider = new GoogleProvider();

    document.getElementById("map").style.height = "14rem";
    this.location = (this.location && this.location.split(',').length === 2 && this.location.split(',')[0] !== 'undefined') ? [parseFloat(this.location.split(',')[0]), parseFloat(this.location.split(',')[1])] : this.iranLoc;
    this.myIcon = L.icon({
      iconUrl: '/assets/images/marker-icon.png',
      shadowUrl: '/assets/images/marker-shadow.png',

    });
    // loc = iranLoc;

    this.map = new L.Map('map', {
      fullscreenControl: true,
      key: this.mapKey,
      maptype: "neshan",
      center: [35.699756, 51.338076],
      poi: false,
      traffic: false,
      fullscreenControlOptions: {
        position: 'bottomleft',
        pseudoFullscreen: true,
        title: fullScreenLabel,

      }
    }).setView(this.location, this.location ? 16 : 8);

    // L.control.scale().addTo(map);

    L.tileLayer(this.mapLayers.google, {
      // attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
      // maxZoom: 18,
      id: 'mapbox/streets-v11',
      // tileSize: 512,
      attribution: null,
      maxNativeZoom: 19, // OSM max available zoom is at 19.
      maxZoom: 22, // Match the map maxZoom, or leave map.options.maxZoom undefined.
      // zoomOffset: -1,
      accessToken: this.mapKey,
    }).addTo(this.map);
    // L.tileLayer.bing({
    //   bingMapsKey: this.bingMapsKey,
    //   imagerySet: 'RoadOnDemand',
    //   culture: 'fa-IR',
    //   minZoom: 1,
    //   minNativeZoom: 1,
    //   maxNativeZoom: 19,
    //   attribution: null,
    // }).addTo(this.map);
    // L.tileLayer('https://static.neshan.org/sdk/openlayers/5.3.0/ol.js?callback=initMyMap&v=5', {
    //   // attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
    //   // maxZoom: 18,
    //   // tileSize: 512,
    //   attribution: null,
    //   maxNativeZoom: 19, // OSM max available zoom is at 19.
    //   maxZoom: 22, // Match the map maxZoom, or leave map.options.maxZoom undefined.
    //   // zoomOffset: -1,
    //   key: this.mapKey,
    //
    // }).addTo(this.map);
    //geosearch
    this.mapSearchProvider = new OpenStreetMapProvider();
    // const searchControl = new GeoSearchControl({
    //   provider: this.mapSearchProvider,
    // });
    // this.map.addControl(searchControl);


    this.marker = L.marker(this.location, {icon: this.myIcon}).addTo(this.map);


    //add clear button
    if (this.mode !== 'view') {
      this.clearBtn = L.control({
        position: 'topleft',
        title: clearLabel,
      });

      this.clearBtn.onAdd = function (src) {

        this._div = L.DomUtil.create('div', 'leaflet-touch  leaflet-bar ');
        let el = `<a class="d-block  hoverable-dark "    title="${clearLabel}"   ><image  src="/assets/images/toggle-marker.png" class="h-full w-full p-1  " aria-hidden="true"></image></a>`;

        this._div.innerHTML = el;
        this._div.onclick = function (e) {
          e.stopPropagation();
          self.removeMarker();
        };
        return this._div;

      };
      this.clearBtn.addTo(this.map);
    }

    if (this.label)
      this.marker.bindPopup(`<small>${this.label}</small>`)/*.openPopup()*/;
    this.map.on('click', this.onMapClick);

    if (this.mode === 'edit') {
      this.marker.dragging.enable();
      this.marker.on('dragend', function (event) {
        var marker = event.target;
        var result = marker.getLatLng();
        self.setLocation({x: result.lng, y: result.lat});
        self.getAddressFromLocation({lat: result.lat, lng: result.lng});

      });
    }
    if (this.mode === 'create') {
    }

    let fullScreenEl = document.querySelector('.leaflet-control-zoom-fullscreen');
    if (fullScreenEl)
      fullScreenEl.style.backgroundImage = "url('/assets/images/vendor/leaflet.fullscreen/icon-fullscreen.svg')";

    if (this.preload) {
      this.setLocation({x: this.preload.lat, y: this.preload.lon});
    }
  },
  methods: {
    setCurrentAddress(data) {
      if (!data) {
        this.mapAddress = null;
        this.$emit('change', this.mapAddress);
        return;
      }

      let province = data.address && data.address.state ? data.address.state.replace('استان ', '') : data.address.province ? data.address.province.replace('استان ', '') : null;
      let county = data.address && data.address.county ? data.address.county.replace('شهرستان ', '') : null;
      if (province)
        province = this.$page.props.cities.filter((e) => e.parent_id == 0 && e.name == province)[0];
      province = province ? province.id : null;
      if (county && province)
        county = this.$page.props.cities.filter((e) => e.parent_id == province && e.name == county)[0];
      county = county ? county.id : null;

      this.mapAddress = {
        address: data.display_name,
        province_id: province,
        county_id: county,
        lat: data.lat,
        lon: data.lon,
      };

      this.$emit('change', this.mapAddress);

    },
    setLocation(res) {
      this.searchMapResults = [];
      if (!res) return;
      if (this.marker) {
        if (!this.marker._map) {
          this.marker.addTo(this.map);
        }

      } else {
        this.marker = L.marker(e.latlng, {icon: myIcon}).addTo(this.map);
        this.marker.dragging.enable();
      }
      this.marker.setLatLng([res.y, res.x]).update();
      // this.map.flyTo([res.y, res.x], 10);
      // this.map.panTo([res.y, res.x], 10);
      this.map.setView([res.y, res.x], this.map.getZoom(), {animation: true});


    },
    searchMap() {
      this.mapAddress = null;
      this.$emit('change', this.mapAddress);
      this.loading = true;
      this.searchNeshan({search: this.search});
      // this.mapSearchProvider.search({query: this.search}).then(function (result) {
      //   // console.log(result);
      //   self.searchMapResults = result;
      //   // if (self.searchMapResults.length == 1)
      //   //   self.setLocation(self.searchMapResults[0]);
      // });
      this.loading = false;
    },
    searchNeshan(param, type = 'geo') {
      if (type == 'geo' && (!param.search || param.search.length < 3)) return;

      this.loading = true;

      var mapLoc = this.map.getCenter();
      var term = param.search || '';
      var lat = param.lat || mapLoc.lat || this.iranLoc[0];
      var lon = param.lng || mapLoc.lng || this.iranLoc[1];

      //making url
      if (type == 'rgeo')
        var url = `	https://api.neshan.org/v5/reverse?lat=${lat}&lng=${lon}`;
      else
        var url = `https://api.neshan.org/v1/search?term=${term}&lat=${lat}&lng=${lon}`;
      //add your api key
      var params = {
        headers: {
          'Api-Key': import.meta.env.VITE_MAP_SERVICE_API
        },

      };
      //sending get request
      window.axios.get(url, params)
          .then(response => {
            //set center of map to marker location
            // console.log(response.data);
            if (type == 'rgeo' && response.data.status == 'OK') {
              response.data.address = response.data;
              response.data.display_name = response.data.formatted_address;
              response.data.lat = lat;
              response.data.lon = lon;
              this.search = response.data.formatted_address;
              this.setCurrentAddress(response.data);
              this.map.eachLayer((layer) => {
                if (layer instanceof L.Marker)
                  layer.bindPopup(`<small>${response.data.formatted_address}</small>`);
                // layer.openPopup();
              });
            } else
              this.searchMapResults = response.data.items;

          }).catch(error => {
        console.log(error.response);
      }).finally(() => {
        // always executed
        this.loading = false;

      });
      ;
    },
    removeMarker() {
      this.map.eachLayer((layer) => {
        if (layer instanceof L.Marker)
          layer.remove();
      });
      this.search = null;
      this.setCurrentAddress(null);

      return this.map;
    },
    getLocation() {
      let res = null;
      this.map.eachLayer((layer) => {
        if (layer instanceof L.Marker) {
          res = layer.getLatLng().lat + "," + layer.getLatLng().lng;
          return false;
        }
      });

      return res;
    },
    onMapClick(e) {
      // this.log(e);
      this.setLocation({x: e.latlng.lng, y: e.latlng.lat});
      this.getAddressFromLocation({lat: e.latlng.lat, lng: e.latlng.lng})
      return;
      if (this.marker) {
        if (!this.marker._map) {
          this.marker.addTo(this.map);
        }

      } else {
        this.marker = L.marker(e.latlng, {icon: myIcon}).addTo(this.map);
        this.marker.dragging.enable();
      }
      this.marker.setLatLng(e.latlng);
      this.getAddressFromLocation(e.latlng);
      // L.popup()
      //     .setLatLng(e.latlng)
      //     .setContent("You clicked the map at " + e.latlng.toString())
      //     .openOn(this.map);
    },
    getAddressFromLocation(latLon) {

      if (!latLon) return null;

      if (!latLon.lat) return null;

      this.searchNeshan(latLon, 'rgeo');
      return;

      this.loading = true;
      window.axios.get("https://nominatim.openstreetmap.org/reverse", {
        params: {
          lat: latLon.lat,
          lon: latLon.lng,
          format: "json",
          'accept-language': 'fa'
        }
      }).then((response) => {

        if (response.status !== 200) return;
        // let addressEl = document.getElementById('address');
        if (response.data && response.data.display_name) {
          // self.log(response.data);
          self.search = response.data.display_name;
          self.setCurrentAddress(response.data);
          this.map.eachLayer((layer) => {
            if (layer instanceof L.Marker)
              layer.bindPopup(`<small>${response.data.display_name}</small>`);
            // layer.openPopup();
          });
        }
      }).finally(() => {
        // always executed
        this.loading = false;

      });
    }
  }
}
</script>

<style scoped>
/*@import url("@neshan-maps-platform/vue3-openlayers/dist/style.css");*/
</style>
