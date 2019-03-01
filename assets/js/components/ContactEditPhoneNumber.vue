<template>
    <div class="edit-phone-number columns">
        <div class="column col-6 col-no-lp">
            <div v-bind:class="{ 'has-error': phone._validation.number }" class="form-group">
                <input v-model="phone.number" class="form-input" type="tel" placeholder="+385 91 234567">
                <p v-if="phone._validation.number" class="form-input-hint">
                    {{ phone._validation.number }}</p>
            </div>
        </div>
        <div class="column col-5">
            <div v-bind:class="{ 'has-error': phone._validation.label }" class="form-group">
                <input v-model="phone.label" class="form-input" type="text" placeholder="Label">
                <p v-if="phone._validation.label" class="form-input-hint">
                    {{ phone._validation.label }}</p>
            </div>
        </div>
        <div class="column col-1">
            <button v-on:click.stop="$emit('removePhoneNumber')"
                    type="button"
                    class="btn btn-sm btn-action btn-remove-number s-circle"
                    title="Delete">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
</template>

<script>
  export default {
    name: 'ContactEditPhoneNumber',
    props: ['phoneNumber'],
    data() {
      return {
        phone: this.phoneNumber,
      };
    },
    methods: {},
  };
</script>

<style>
    .col-no-lp {
        padding-left: 0;
    }

    .col-no-p {
        padding: 0;
    }

    .btn-remove-number {
        margin-top: .2rem;
    }
</style>