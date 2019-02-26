<template>
    <div id="app">

        <div class="filters container">
            <div class="columns">
                <div class="column">
                    <a class="btn btn-link btn-all-contacts float-right text-bold text-gray">All contacts</a>
                </div>
                <div class="divider-vert"></div>
                <div class="column">
                    <a class="btn btn-link btn-my-favourites float-left text-bold">My favourites</a>
                </div>
            </div>
        </div>

        <div class="breathing-room divider"></div>

        <div class="search container">
            <div class="columns">
                <div class="column col-4 col-mx-auto">
                    <div class="has-icon-left">
                        <input type="text" class="form-input input-lg" placeholder="">
                        <i class="form-icon icon icon-search"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="container">
            <div class="columns">
                <div class="contacts column col-8 col-mx-auto">

                    <button class="contact card" title="Add New" style="align-items: center;">
                        <div class="card-body" style="align-items: center;">
                            <i class="icon icon-plus"></i>
                            <div class="card-title h5 text-center">Add New</div>
                        </div>
                    </button>

                    <div class="contact card">
                        <div class="card-header">
                            <button class="btn btn-sm btn-action btn-favourite s-circle float-left"
                                    title="Favourite">
                                <i class="icon icon-bookmark"></i></button>
                            <button class="btn btn-sm btn-action btn-delete s-circle float-right" title="Delete">
                                <i class="icon icon-delete"></i></button>
                            <button class="btn btn-sm btn-action btn-edit s-circle float-right" title="Edit">
                                <i class="icon icon-edit"></i></button>
                        </div>
                        <div class="card-body">
                            <figure class="avatar avatar-xl p-centered" data-initial="YZ"
                                    style="background-color: #5755d9;"></figure>
                            <div class="card-title h5 text-center">Oscar Arnold</div>
                        </div>
                    </div>

                    <div class="contact card">
                        <div class="card-header">
                            <button class="btn btn-sm btn-action btn-favourite s-circle float-left"><i
                                    class="icon icon-bookmark"></i></button>
                            <button class="btn btn-sm btn-action btn-delete s-circle float-right"><i
                                    class="icon icon-delete"></i>
                            </button>
                            <button class="btn btn-sm btn-action btn-edit s-circle float-right"><i
                                    class="icon icon-edit"></i>
                            </button>
                        </div>
                        <div class="card-body">
                            <figure class="avatar avatar-xl p-centered" data-initial="YZ"
                                    style="background-color: #5755d9;"></figure>
                            <div class="card-title h5 text-center">Oscar Arnold</div>
                        </div>
                    </div>

                    <div class="contact card">
                        <div class="card-header">
                            <button class="btn btn-sm btn-action btn-favourite s-circle float-left"><i
                                    class="icon icon-bookmark"></i></button>
                            <button class="btn btn-sm btn-action btn-delete s-circle float-right"><i
                                    class="icon icon-delete"></i>
                            </button>
                            <button class="btn btn-sm btn-action btn-edit s-circle float-right"><i
                                    class="icon icon-edit"></i>
                            </button>
                        </div>
                        <div class="card-body">
                            <figure class="avatar avatar-xl p-centered" data-initial="YZ"
                                    style="background-color: #5755d9;"></figure>
                            <div class="card-title h5 text-center">Oscar Arnold</div>
                        </div>
                    </div>

                    <div class="contact card">
                        <div class="card-header">
                            <button class="btn btn-sm btn-action btn-favourite s-circle float-left"><i
                                    class="icon icon-bookmark"></i></button>
                            <button class="btn btn-sm btn-action btn-delete s-circle float-right"><i
                                    class="icon icon-delete"></i>
                            </button>
                            <button class="btn btn-sm btn-action btn-edit s-circle float-right"><i
                                    class="icon icon-edit"></i>
                            </button>
                        </div>
                        <div class="card-body">
                            <figure class="avatar avatar-xl p-centered" data-initial="YZ"
                                    style="background-color: #5755d9;"></figure>
                            <div class="card-title h5 text-center">Oscar Arnold</div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</template>

<script>
  //  import Auth from './components/Auth.vue'
  //  import Messages from './components/Messages.vue'

  export default {
    name: 'app',
    components: {
//      Auth,
//      Messages
    },
  };
</script>

<style>
    .header {
        margin-top: 2rem;
        margin-bottom: 1rem;
    }

    .breathing-room {
        border-color: #5755d9;
        margin: 1.4rem 0;
    }

    .search {
        margin-bottom: 2rem;
    }

    .contacts {
        display: flex;
        justify-content: space-between;
        flex-direction: row;
        flex-wrap: wrap;
    }

    .contact.card {
        width: 22%;
        margin-bottom: 2rem;
    }

    .contact.card .card-body {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    button.contact.card {
        border-style: dashed;
    }

    .contact .avatar {
        position: relative;
        bottom: .6rem;
    }

    .contact .btn {
        border-style: hidden;
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