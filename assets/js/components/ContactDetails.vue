<template>
    <div class="container">
        <div class="columns">
            <div class="column col-8 col-mx-auto">

                <div class="details container">
                    <div v-if="contact" class="columns">
                        <div class="column col-auto">
                            <figure :data-initial="contact.firstName.charAt(0).toUpperCase() + contact.lastName.charAt(0).toUpperCase()"
                                    class="avatar avatar-xxl"
                                    style="background-color: #5755d9;">
                                <img v-if="contact.profilePhoto" :src="mediaPath + '/' + contact.profilePhoto ">
                            </figure>
                        </div>
                        <div class="column col">
                            <h3>
                                <router-link
                                        class="btn btn-sm btn-action btn-back s-circle"
                                        :to="{name: 'contactListAll'}"
                                        tag="button"
                                        title="Back"
                                >
                                    <i class="fas fa-arrow-left"></i>
                                </router-link>
                                {{ contact.firstName }} {{ contact.lastName }}

                                <router-link
                                        class="btn btn-sm btn-action btn-edit s-circle float-right"
                                        :to="{name: 'contactEdit', params: {id: contact.id}}"
                                        tag="button"
                                        title="Edit"
                                >
                                    <i class="far fa-edit"></i>
                                </router-link>

                                <ContactFavourite v-bind:contact="contact" v-bind:float="'float-right'" />
                            </h3>

                            <div class="divider"></div>

                            <div class="column col-9 col-sm-12">
                                <div class="form-horizontal">
                                    <div class="form-group">
                                        <div class="col-3 col-sm-12">
                                            <label class="form-label"><i class="fas fa-envelope"></i> Email</label>
                                        </div>
                                        <div class="col-9 col-sm-12">
                                            {{ contact.emailAddress }}
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-3 col-sm-12">
                                            <label class="form-label"><i class="fas fa-phone-square"></i> Numbers</label>
                                        </div>
                                        <div class="col-9 col-sm-12">
                                            <div v-for="phoneNumber in contact.phoneNumbers">
                                                {{ phoneNumber.label }}: {{ phoneNumber.number }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</template>

<script>
  import ContactFavourite from './ContactFavourite';

  export default {
    name: 'ContactDetails',
    props: ['id'],
    components: {
      ContactFavourite,
    },
    data() {
      return {
        contact: null,
        error: null,
      };
    },
    mounted() {
      this.fetchItem();
    },
    methods: {
      fetchItem: function() {
        this.$Progress.start();
        let url = '/api/contacts/' + this.id;
        fetch(url).then(async response => {
          this.contact = await response.json();
          this.$Progress.finish();
        }).catch(error => {
          this.error = error.toString();
          this.$Progress.fail();
        });
      },
    },
  };
</script>
