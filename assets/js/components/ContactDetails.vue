<template>
    <div class="details">
        <div class="container grid-sm">
            <div v-if="contact" class="columns col-gapless">

                <div class="column col-12">
                    <div class="details-header columns col-gapless">
                        <div class="column col-1">
                            <router-link
                                    class="btn btn-sm btn-action btn-back s-circle"
                                    :to="{name: 'contactListAll'}"
                                    tag="button"
                                    title="Back">
                                <i class="fas fa-arrow-left"></i>
                            </router-link>
                        </div>

                        <div class="column col-9">
                            <h3 class="text-ellipsis">{{ contact.firstName }} {{ contact.lastName }}</h3>
                        </div>

                        <div class="column col-1">
                            <router-link
                                    class="btn btn-sm btn-action btn-edit s-circle float-right"
                                    :to="{name: 'contactEdit', params: {id: contact.id}}"
                                    tag="button"
                                    title="Edit">
                                <i class="far fa-edit"></i>
                            </router-link>
                        </div>
                        <div class="column col-1">
                            <ContactFavourite v-bind:contact="contact" v-bind:float="'float-right'" />
                        </div>
                    </div>
                </div>

                <div class="column col-4 col-sm-12 details-body details-photo">
                    <figure :data-initial="contact.firstName.charAt(0).toUpperCase() + contact.lastName.charAt(0).toUpperCase()"
                            class="avatar avatar-xxl"
                            style="background-color: #5755d9;">
                        <img v-if="contact.profilePhoto" :src="profilePhotoPath + '/' + contact.profilePhoto.fileName">
                    </figure>
                </div>

                <div class="column col-8 col-sm-12 details-body">

                    <div v-if="contact.emailAddress" class="columns">
                        <div class="column col-pad col-4 col-sm-5">
                            <span class="text-grayer"><i class="fas fa-envelope"></i> Email</span>
                        </div>
                        <div class="column col-pad col col-sm-7">
                            <a :href="'mailto:' + contact.emailAddress">{{ contact.emailAddress }}</a>
                        </div>
                    </div>

                    <div v-if="contact.phoneNumbers.length > 0" class="columns">
                        <div class="column col-pad col-4 col-sm-5">
                            <span class="text-grayer"><i class="fas fa-phone-square"></i> Numbers</span>
                        </div>
                        <div class="column col-pad col col-sm-7">
                            <div v-for="phoneNumber in contact.phoneNumbers" class="columns">
                                <div class="column col-pad-bottom col-auto">
                                    <a :href="'tel:' + phoneNumber.number">{{ phoneNumber.number }}</a> <span
                                        class="label label-rounded">{{ phoneNumber.label }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div v-if="error">
                <h3>{{ error['hydra:title'] }}</h3>
                <p>{{ error['hydra:description'] }}</p>
            </div>
        </div>
    </div>
</template>

<script>
  import ContactFavourite from './ContactFavourite';

  export default {
    name: 'ContactDetails',
    components: {
      ContactFavourite,
    },
    data() {
      return {
        contact: null,
        error: null,
      };
    },
    computed: {
      id() {
        return this.$route.params.id;
      },
    },
    created() {
      this.fetchItem();
    },
    methods: {
      fetchItem: function() {
        this.$Progress.start();
        this.error = null;
        let url = '/api/contacts/' + this.id;
        fetch(url).then(async response => {
          if (response.status !== 200) {
            this.error = await response.json();
          } else {
            this.contact = await response.json();
          }
          this.$Progress.finish();
        }).catch(error => {
          this.error = error.toString();
          this.$Progress.fail();
        });
      },
    },
  };
</script>

<style>
    .details {
        padding-top: 1.6rem;
        border-top: .05rem solid #5755d9;
    }

    .details .avatar {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
    }

    .details-header {
        margin-top: .8rem;
        padding-bottom: .2rem;
        border-bottom: .05rem solid #e5e5f9;
    }

    .details-header .btn {
        margin-top: .14rem;
    }

    .details-header {
        margin-left: 0;
        margin-right: 0;
    }

    .details-body {
        padding-top: 1rem;
    }

    .details-body .col-pad {
        padding: 1rem .5rem;
    }

    .details-body .col-pad-bottom {
        padding-bottom: 1rem;
    }

    .details-body i {
        margin-right: .4rem;
    }

    .details-body .label {
        margin-left: .2rem;
    }

    .details-body .text-grayer {
        color: #455060;
    }

    .details-photo {
        display: flex;
        justify-content: center;
    }
</style>