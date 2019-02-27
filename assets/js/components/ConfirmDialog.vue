<template>
    <div class="modal modal-sm active">
        <a v-on:click="$emit('cancel')" class="modal-overlay" aria-label="Close"></a>
        <div class="modal-container" role="document">
            <div class="modal-header">
                <a v-on:click="$emit('cancel')" class="btn btn-clear float-right" aria-label="Close"></a>
                <div class="modal-title h5">{{ title }}</div>
            </div>
            <div v-if="description" class="modal-body">
                <div class="content">
                    <p>{{ description }}</p>
                </div>
            </div>
            <div class="modal-footer">
                <button v-on:click="$emit('confirm')" class="btn btn-primary btn-error">{{ actionTitle }}</button>
                <a v-on:click="$emit('cancel')" class="btn btn-link" aria-label="Close">Cancel</a>
            </div>
        </div>
    </div>
</template>

<script>
  export default {
    name: 'ConfirmDialog',
    props: ['title', 'description', 'actionTitle'],
  };
</script>
