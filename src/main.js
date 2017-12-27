// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import Vue from 'vue'
import 'metro-dist/css/metro.min.css'
import 'metro-dist/css/metro-icons.min.css'
import 'metro-dist/js/metro.min.js'
import App from './App'
import router from './router'
import VueResource from 'vue-resource';
import VueLocalStorage from 'vue-localstorage'

Vue.use(VueResource);
Vue.use(VueLocalStorage, {createComputed: true});
Vue.http.options.root = process.env.NODE_ENV == 'production' ? '/api' : 'http://code.local/api';

Vue.config.productionTip = false;

/* eslint-disable no-new */
new Vue({
  el: '#app',
  router,
  template: '<App/>',
  components: { App }
});
