<template>
    <router-link
            tag="a"
            :to="{name: 'contactDetails', params: {id: contact.id}}">
        <div class="contact card">
            <div class="card-header">
                <ContactFavourite v-bind:contact="contact"/>
                <div class="dropdown dropdown-right float-right">
                    <button v-on:click.stop.prevent="event.preventDefault()"
                            class="btn btn-sm btn-action s-circle dropdown-toggle">
                        <i class="fas fa-ellipsis-v"></i>
                    </button>
                    <ul class="menu">
                        <li class="menu-item">
                            <div class="menu-item-content"
                                 v-on:click.stop.prevent="$router.push({name: 'contactEdit', params: {id: contact.id}})"
                                 title="Edit">
                                <i class="far fa-edit"></i> Edit
                            </div>
                        </li>
                        <li class="menu-item">
                            <a class="menu-item-content"
                               v-on:click.stop.prevent="$emit('confirmRemove')"
                               title="Delete">
                                <i class="far fa-trash-alt text-error"></i> Delete
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="card-body">
                <figure :data-initial="contact.firstName.charAt(0).toUpperCase() + contact.lastName.charAt(0).toUpperCase()"
                        class="avatar avatar-xl p-centered"
                        style="background-color: #5755d9;">
                    <img v-if="contact.profilePhoto" :src="profilePhotoPath + '/' + contact.profilePhoto.fileName">
                </figure>
                <div class="card-title h5 text-center">{{ contact.firstName }} {{ contact.lastName }}</div>
            </div>
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
