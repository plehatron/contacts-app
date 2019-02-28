<template>
    <div>
        <div class="filters container">
            <div class="columns">
                <div class="column">
                    <a class="btn btn-link float-right"
                       v-on:click="navigateRoute('contactListAll')"
                       v-bind:class="{ active: isCurrentRoute('contactListAll') }"
                    >
                        All contacts
                    </a>
                </div>
                <div class="divider-vert"></div>
                <div class="column">
                    <a class="btn btn-link float-left"
                       v-on:click="navigateRoute('contactListFavourites')"
                       v-bind:class="{ active: isCurrentRoute('contactListFavourites') }"
                    >
                        My favourites
                    </a>
                </div>
            </div>
        </div>

        <div class="search container">
            <div class="columns">
                <div class="column col-4 col-mx-auto">
                    <div class="has-icon-left">
                        <input v-on:keyup="searchDelayed"
                               v-model="searchQueryData"
                               type="text"
                               class="form-input input-lg"
                               placeholder="Search contacts..."
                        >
                        <i class="form-icon fas fa-search"></i>
                    </div>
                </div>
            </div>
        </div>


        <div class="container">
            <div class="columns">
                <div class="contacts column col-8 col-mx-auto">

                    <button class="contact card bnt-new-contact" title="Add New">
                        <div class="card-body">
                            <i class="fas fa-plus"></i>
                            <div class="card-title h5 text-center">Add New</div>
                        </div>
                    </button>

                    <ContactItem
                            v-for="(contact, index) in contacts"
                            v-bind:contact="contact"
                            v-bind:index="index"
                            v-bind:key="contact.id"
                            v-on:confirmRemove="confirmRemove(contact, index)"
                            v-on:remove="remove(contact, index)"
                            v-on:favourite="favourite(contact)"
                    />
                </div>
            </div>
        </div>

        <ConfirmDialog
                v-if="showConfirmDialog"
                v-bind:title="'Delete contact \'' + selectedForRemove.firstName + ' ' + selectedForRemove.lastName + '\'?'"
                v-bind:actionTitle="'Delete'"
                v-on:confirm="remove(selectedForRemove, selectedForRemoveIndex)"
                v-on:cancel="showConfirmDialog = false; selectedForRemove = null; selectedForRemoveIndex = null"
        />
    </div>
</template>

<script>
  import ContactItem from './ContactItem.vue';
  import ConfirmDialog from './ConfirmDialog.vue';
  import debounce from 'debounce';

  export default {
    name: 'ContactList',
    components: {
      ContactItem,
      ConfirmDialog,
    },
    data() {
      return {
        contacts: null,
        error: null,
        showConfirmDialog: false,
        selectedForRemove: null,
        selectedForRemoveIndex: null,
        searchQueryData: null,
      };
    },
    mounted() {
      this.searchQueryData = this.$route.query.query;
      this.fetchList();
    },
    watch: {
      '$route': 'fetchList',
    },
    methods: {
      isCurrentRoute: function(routeName) {
        return this.$route.name === routeName;
      },
      navigateRoute: function(routeName) {
        if (this.searchQueryData) {
          this.$router.push({name: routeName, query: {query: this.searchQueryData}});
        } else {
          this.$router.push({name: routeName});
        }
      },
      getUrlParams: function() {
        let urlParams = {};
        if (this.$route.name === 'contactListFavourites') {
          urlParams.favourite = 'true';
        }
        if (this.searchQueryData) {
          urlParams.query = this.searchQueryData;
        }
        return urlParams;
      },
      fetchList: function() {

        this.$Progress.start();

        let url = new URL('/api/contacts', window.location.protocol + '//' + window.location.host);

        let urlParams = this.getUrlParams();
        Object.keys(urlParams).forEach(key => url.searchParams.append(key, urlParams[key]));

        fetch(url).then(async response => {
          let data = await response.json();
          this.contacts = data['hydra:member'];
          this.$Progress.finish();

        }).catch(error => {
          this.error = error.toString();
          this.$Progress.fail();
        });
      },
      confirmRemove: function(contact, index) {
        this.selectedForRemove = contact;
        this.selectedForRemoveIndex = index;
        this.showConfirmDialog = true;
      },
      remove: function(contact, index) {
        this.contacts.splice(index, 1);
        this.showConfirmDialog = false;
        this.$Progress.start();
        fetch('/api/contacts/' + contact.id, {
          method: 'DELETE',
          headers: {
            'Accept': 'application/ld+json',
          },
        }).then(async response => {
          this.$Progress.finish();
        }).catch(error => {
          this.$Progress.fail();
        });
      },
      searchDelayed: debounce(function(e) {
        this.search();
      }, 800),
      search: function() {
        this.navigateRoute(this.$route.name);
      },
    },
  };
</script>

<style>

    .filters {
        padding-bottom: .6rem;
        border-bottom: .05rem solid #5755d9;
    }

    .filters .btn {
        color: #cbd0d9;
        font-weight: bold;
    }

    .filters .btn.active {
        color: #302ecd;
    }

    .search {
        margin-bottom: 2rem;
        margin-top: 1.4rem;
    }

    .search .form-icon {
        left: .5rem;
    }

    .search .has-icon-left .form-input {
        padding-left: 2rem;
    }

    .contacts {
        display: grid;
        grid-gap: 1rem;
        grid-template-columns: 1fr 1fr 1fr 1fr;
    }

    .contact.card {
        width: 100%;
        min-height: 170px;
        background-color: inherit;
    }

    .contact.card .card-header {
        padding: .4rem .4rem 0;
    }

    .contact.card .card-body {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    .bnt-new-contact {
        border-style: dashed;
        align-items: center;
    }

    .btn-new-contact .card-body {
        align-items: center;
    }

    .contact .avatar {
        position: relative;
        bottom: .8rem;
    }

    .contact .btn {
        border-style: hidden;
        background-color: inherit;
    }

    .contact .btn:hover {
        border-style: solid;
    }

    .contact.card .btn-edit {
        margin-right: .6rem;
    }

    .contact.card .btn-edit, .contact.card .btn-delete {
        visibility: hidden;
    }

    .contact.card:hover {
        border-color: #5755d9;
    }

    .contact.card:hover .btn-edit, .contact.card:hover .btn-delete {
        visibility: visible;
    }

    .filters .btn.text-gray:hover {
        color: #5755d9 !important;
    }
</style>