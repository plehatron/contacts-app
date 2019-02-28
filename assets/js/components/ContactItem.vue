<template>
    <router-link
            class="contact card"
            tag="div"
            :to="{name: 'contactDetails', params: {id: contact.id}}"
    >
        <div class="card-header">

            <ContactFavourite v-bind:contact="contact"/>

            <button v-on:click.stop="$emit('confirmRemove')"
                    class="btn btn-sm btn-action btn-delete s-circle float-right"
                    title="Delete">
                <i class="far fa-trash-alt"></i>
            </button>

            <router-link
                    class="btn btn-sm btn-action btn-edit s-circle float-right"
                    :to="{name: 'contactEdit', params: {id: contact.id}}"
                    tag="button"
                    title="Edit"
            >
                <i class="far fa-edit"></i>
            </router-link>

        </div>
        <div class="card-body">
            <figure :data-initial="contact.firstName.charAt(0).toUpperCase() + contact.lastName.charAt(0).toUpperCase()"
                    class="avatar avatar-xl p-centered"
                    style="background-color: #5755d9;">
                <img v-if="contact.profilePhoto" :src="mediaPath + '/' + contact.profilePhoto ">
            </figure>
            <div class="card-title h5 text-center">{{ contact.firstName }} {{ contact.lastName }}</div>
        </div>
    </router-link>
</template>

<script>
  import ContactFavourite from './ContactFavourite';

  export default {
    name: 'ContactList',
    props: ['contact'],
    components: {
      ContactFavourite,
    },
  };
</script>
