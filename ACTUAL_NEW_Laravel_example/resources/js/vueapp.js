window.Vue = require('vue');

const files = require.context('./', true, /\.vue$/i)
files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

const appElement = document.getElementById('app');
if (appElement) {
    Vue.config.productionTip = false;
    const app = new Vue({
        el: appElement,
    });
}
