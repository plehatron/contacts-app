<template>
    <button v-on:click="favourite"
            class="btn btn-sm btn-action btn-favourite s-circle float-left"
            title="Favourite">
        <i v-if="contact.favourite" class="fas fa-heart"></i>
        <i v-else class="far fa-heart"></i>
    </button>
</template>

<script>
  export default {
    name: 'ContactFavourite',
    props: ['contact'],
    methods: {
      favourite: function() {
        this.contact.favourite = (!this.contact.favourite);

        this.$Progress.start();
        let data = {
          favourite: this.contact.favourite,
        };
        fetch('/api/contacts/' + this.contact.id, {
          method: 'PUT',
          headers: {
            'Accept': 'application/ld+json',
            'Content-Type': 'application/json',
          },
          body: JSON.stringify(data),
        }).then(async response => {
          this.$Progress.finish();
        }).catch(error => {
          this.$Progress.fail();
        });

      },
    },
  };
</script>
