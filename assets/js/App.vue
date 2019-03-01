<template>
    <div id="app" class="container">
        <vue-progress-bar></vue-progress-bar>
        <router-view></router-view>
    </div>
</template>

<script>
  import ContactList from './components/ContactList.vue';
  import ContactDetails from './components/ContactDetails.vue';
  import ContactEdit from './components/ContactEdit.vue';
  import NotFoundComponent from './components/NotFound.vue';
  import VueRouter from 'vue-router';

  const router = new VueRouter({
    mode: 'history',
    base: '/',
    routes: [
      {name: 'contactListAll', path: '/(contacts)?', component: ContactList},
      {name: 'contactListFavourites', path: '/contacts/favourites', component: ContactList},
      {name: 'contactNew', path: '/contacts/new', component: ContactEdit, props: true},
      {name: 'contactDetails', path: '/contacts/:id', component: ContactDetails, props: true},
      {name: 'contactEdit', path: '/contacts/:id/edit', component: ContactEdit, props: true},
      {name: 'notFound', path: '*', component: NotFoundComponent},
    ],
    linkExactActiveClass: 'active',
  });

  export default {
    name: 'app',
    router: router,
  };
</script>
