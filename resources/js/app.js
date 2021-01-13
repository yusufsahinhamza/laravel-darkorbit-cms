/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

import vuetify from './plugins/vuetify'
import router from './plugins/vue-router';
import store from './plugins/vuex';

import App from './App.vue';

import VSnackbars from 'v-snackbars';

window.Vue = require('vue').default;

Vue.mixin({
    components: {
        VSnackbars
    }
});

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

Promise.all([
    store.dispatch('checkAuth')
]).finally(() => {
    new Vue({
        vuetify,
        router,
        store,
        render: h => h(App)
    }).$mount('#app');
});