import Vue from 'vue';
import BootstrapVue from 'bootstrap-vue';

import VueGoodTablePlugin from 'vue-good-table';
import vSelect from 'vue-select';

import CountryFlag from 'vue-country-flag';

import L from 'leaflet';
import * as Vue2Leaflet from 'vue2-leaflet';
//import * as EsriLeaflet from 'esri-leaflet';
import _ from 'lodash';
import axios from 'axios';

import App from './App.vue';
import router from './router';
import store from './store';

import 'bootstrap/dist/css/bootstrap.css';
import 'bootstrap-vue/dist/bootstrap-vue.css';
import 'vue-good-table/dist/vue-good-table.css';
import 'vue-select/dist/vue-select.css'
import 'leaflet/dist/leaflet.css';


import { library } from '@fortawesome/fontawesome-svg-core';
import {
  faChartPie, faChartBar, faTable, faSpinner, faQuestionCircle, faInfoCircle, faDownload,
} from '@fortawesome/free-solid-svg-icons';

library.add(faChartPie, faChartBar, faTable, faSpinner, faQuestionCircle, 
faInfoCircle, faDownload );

import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';

delete L.Icon.Default.prototype._getIconUrl;

L.Icon.Default.mergeOptions({
  iconRetinaUrl: require('leaflet/dist/images/marker-icon-2x.png'),
  iconUrl: require('leaflet/dist/images/marker-icon.png'),
  shadowUrl: require('leaflet/dist/images/marker-shadow.png'),
});

Vue.use(BootstrapVue);
Vue.use(VueGoodTablePlugin);
//Vue.use(EsriLeaflet);

Vue.component('fa-icon', FontAwesomeIcon);
Vue.component('v-select', vSelect);
Vue.component('v-flag', CountryFlag);

Vue.filter('toCurrency', function (value) {
    value = parseFloat(value);
    if (typeof value !== "number") {
        return value;
    }
    var formatter = new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
        minimumFractionDigits: 2
    });
    return formatter.format(value);
});

Vue.config.productionTip = false;

Object.defineProperty(Vue.prototype, '$_', { value: _ });
Object.defineProperty(Vue.prototype, '$axios', { value: axios });

var app = new Vue({
  router,
  store,
  render: h => h(App)
}).$mount('#app');
