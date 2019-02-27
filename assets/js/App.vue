<template>
    <div id="app">
        <vue-progress-bar></vue-progress-bar>
        <router-view></router-view>
    </div>
</template>

<script>
  import ContactList from './components/ContactList.vue';
  import ContactEdit from './components/ContactEdit.vue';
  import NotFoundComponent from './components/NotFound.vue';
  import VueRouter from 'vue-router';

  const router = new VueRouter({
    mode: 'history',
    base: '/',
    routes: [
      {name: 'contactList', path: '/(contacts)?', component: ContactList, props: (route) => ({ favourites: route.query.favourites, searchQuery: route.query.query })},
      {name: 'contactDetails', path: '/contacts/:id', props: true},
      {name: 'contactEdit', path: '/contacts/:id/edit', component: ContactEdit, props: true},
      {name: 'contactNew', path: '/contacts/new'},
      {name: 'notFound', path: '*', component: NotFoundComponent }
    ],
    linkExactActiveClass: 'active',
  });

  export default {
    name: 'app',
    router: router
  };
</script>
