<template>
    <div class="favourite-container">
        <button v-on:click.stop.prevent="favourite"
                :class="'btn btn-sm btn-action btn-favourite s-circle ' + floatClass"
                title="Favourite">
            <i v-if="contact.favourite" class="fas fa-heart"></i>
            <i v-else class="far fa-heart"></i>
        </button>
    </div>
</template>

<script>
  export default {
    name: 'ContactFavourite',
    props: ['contact', 'float'],
    data() {
      return {
        floatClass: this.float ? this.float : 'float-left',
      };
    },
    methods: {
      favourite: function() {
        this.contact.favourite = (!this.contact.favourite);

        this.$Progress.start();
        this.error = null;
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
          if (!response.ok) {
            this.error = await response.json();
          }
          this.$Progress.finish();
        }).catch(error => {
          this.$Progress.fail();
        });
      },
    },
  };
</script>
