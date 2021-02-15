/*require('./bootstrap');

window.Vue = require('vue');*/

Vue.component('answer', require('./components/question/answer.vue'));
Vue.component('question', require('./components/question/question.vue'));

const app = new Vue({
    el: '#main',
    created: function(){
        console.log('test');
    }
});
