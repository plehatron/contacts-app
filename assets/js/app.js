import Vue from 'vue';
import VueRouter from 'vue-router';
import VueProgressBar from 'vue-progressbar';

import 'babel-polyfill';
import 'whatwg-fetch';

import '../../node_modules/spectre.css/dist/spectre.css';
import '../../node_modules/spectre.css/dist/spectre-icons.css';
import '../../node_modules/spectre.css/dist/spectre-exp.css';
import '../css/app.css';

import App from './App.vue';

Vue.config.productionTip = false;

Vue.use(VueRouter);

Vue.use(VueProgressBar, {
  color: '#5755d9',
  failedColor: 'red',
  height: '6px'
});

Vue.mixin({
  data: function() {
    return {
      mediaPath: APP_MEDIA_PATH
    }
  }
});

new Vue({
  render: h => h(App)
}).$mount('#app');
