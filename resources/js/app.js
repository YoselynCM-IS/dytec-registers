/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

import Vue from 'vue'


import { BootstrapVue, IconsPlugin } from 'bootstrap-vue'
// Import Bootstrap an BootstrapVue CSS files (order is important)
import 'bootstrap/dist/css/bootstrap.css'
import 'bootstrap-vue/dist/bootstrap-vue.css'
// Make BootstrapVue available throughout your project
Vue.use(BootstrapVue)
// Optionally install the BootstrapVue icon components plugin
Vue.use(IconsPlugin)


Vue.use(require('vue-moment'));


import vueNumeralFilterInstaller from 'vue-numeral-filter';
Vue.use(vueNumeralFilterInstaller, { locale: 'en-gb' });

// import VueResource from 'vue-resource';
// Vue.use(VueResource)
// Vue.http.interceptors.push((request, next) => {
//     const token = document.querySelector('#token').getAttribute('content')
  
//     if (token) {
//       request.headers.set('X-CSRF-TOKEN', token)
//     }
  
//     next()
//   })
// Vue.use(require('vue-resource')); 
// Vue.http.headers.common['X-CSRF-TOKEN'] = document.getElementById('csrf_token').value;

import VueResource from 'vue-resource';
Vue.use(VueResource);

//necesario para http post, put, delete channel routes
Vue.http.interceptors.push((request, next) => {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    request.headers.set('X-CSRF-TOKEN', csrfToken);
    next();
});

// PAGINATION
Vue.component('pagination', require('laravel-vue-pagination'));
/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

// COMPONENTES DE VAUCHERS
Vue.component('vouchers-component', require('./components/vouchers/VouchersComponent.vue').default);

// COMPONENTES PARA EL REGISTRO
Vue.component('edit-register', require('./components/register/EditRegister.vue').default);

Vue.component('registers-component', require('./components/register/RegistersComponent.vue').default);
Vue.component('pre-register-component', require('./components/register/PreRegisterComponent.vue').default);
Vue.component('adm-preregister', require('./components/register/AdmPreRegister.vue').default);
Vue.component('search-folio', require('./components/register/SearchFolio.vue').default);
Vue.component('information-student', require('./components/register/InformationStudent.vue').default);

// INFORMACION DEL ESTUDIANTE
Vue.component('edit-register', require('./components/register/EditRegister.vue').default);

Vue.component('consult-register-component', require('./components/register/ConsultRegisterComponent.vue').default);

// COMPONENTES PARA LOS CODIGOS
Vue.component('codes-component', require('./components/codes/CodesComponent.vue').default);

// COMPONENTES PARA SUBIR FOLIOS A LA BASE DE DATOS
Vue.component('files-component', require('./components/manager/FilesComponent.vue').default);


Vue.component('folios-component', require('./components/FoliosComponent.vue').default);


// SCHOOL
Vue.component('schools-component', require('./components/schools/SchoolsComponent.vue').default);
Vue.component('new-edit-school', require('./components/schools/NewEditSchool.vue').default);

// BOOKS
Vue.component('books-component', require('./components/books/BooksComponent.vue').default);
Vue.component('new-edit-book', require('./components/books/NewEditBook.vue').default);

// COMPONENTES DE MOVIMIENTOS
Vue.component('movimientos-component', require('./components/movimientos/MovimientosComponent.vue').default);

// COMPONENTES DE REVISIONES
Vue.component('revisions-component', require('./components/revisions/RevisionsComponent.vue').default);
Vue.component('ne-categorie-component', require('./components/revisions/categories/NECategorieComponent.vue').default);
Vue.component('rev-list-categories-component', require('./components/revisions/categories/ListCategoriesComponent.vue').default);
Vue.component('pagos-categories-component', require('./components/revisions/pagos/PagosComponent.vue').default);


/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: '#app',
});
