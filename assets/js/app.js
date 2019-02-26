import Vue from 'vue';
import App from './App.vue';
import 'whatwg-fetch';

import '../../node_modules/spectre.css/dist/spectre.css';
import '../../node_modules/spectre.css/dist/spectre-icons.css';
import '../../node_modules/spectre.css/dist/spectre-exp.css';
import '../css/app.css';

Vue.config.productionTip = false;

new Vue({
  render: h => h(App),
}).$mount('#app');
