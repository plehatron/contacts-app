<template>
    <div class="edit columns">
        <div class="column col-6 col-mx-auto">
            <div v-if="error" class="toast toast-error m-2">
                <button v-on:click.stop="error = null" type="button"
                        class="btn btn-clear float-right"></button>
                <p>{{ error }}</p>
            </div>
            <form v-if="!loading" @submit.prevent="handleSubmit" class="columns">
                <div class="column col-auto">
                    <input type="file"
                           class="input-file"
                           id="input-photo-upload"
                           v-on:change="uploadProfilePhoto"
                           accept=".jpg,.png">
                    <label class="avatar avatar-xxl photo-upload"
                           v-bind:class="{'loading loading-lg': profilePhotoLoading}"
                           for="input-photo-upload">
                        <i class="fas fa-upload"></i>
                        <img id="photo-preview"
                             :src="profilePhoto ? profilePhotoPath + '/' + profilePhoto.fileName : false">
                    </label>
                </div>

                <div class="column col-8">

                    <div class="edit-header columns">
                        <div class="column col-1">

                            <router-link
                                    v-if="id"
                                    class="btn btn-sm btn-action btn-back s-circle"
                                    :to="{name: 'contactDetails', params: {id: id}}"
                                    tag="button"
                                    title="Back to contact details"
                                    type="button">
                                <i class="fas fa-arrow-left"></i>
                            </router-link>
                            <router-link
                                    v-else
                                    class="btn btn-sm btn-action btn-back s-circle"
                                    :to="{name: 'contactListAll'}"
                                    tag="button"
                                    title="Back all contacts"
                                    type="button">
                                <i class="fas fa-arrow-left"></i>
                            </router-link>

                        </div>

                        <div class="column col-10">
                            <h4 v-if="id">Edit contact #{{ id }}</h4>
                            <h4 v-else>Create contact</h4>
                        </div>

                        <div class="column col-1">
                            <button v-on:click.stop="confirmRemove"
                                    class="btn btn-sm btn-action btn-delete s-circle"
                                    title="Delete"
                                    type="button">
                                <i class="far fa-trash-alt"></i>
                            </button>
                        </div>
                    </div>

                    <div class="edit-body">

                        <h6><i class="fas fa-user-circle"></i> First and last name</h6>
                        <div class="columns">
                            <div class="column col-6">
                                <div v-bind:class="{ 'has-error': contact._validation.firstName }" class="form-group">
                                    <input v-model="contact.firstName"
                                           class="form-input"
                                           type="text"
                                           placeholder="First name">
                                    <p v-if="contact._validation.firstName" class="form-input-hint">
                                        {{ contact._validation.firstName }}</p>
                                </div>
                            </div>
                            <div class="column col-6">
                                <div v-bind:class="{ 'has-error': contact._validation.lastName }" class="form-group">
                                    <input v-model="contact.lastName"
                                           class="form-input"
                                           type="text"
                                           placeholder="Last name">
                                    <p v-if="contact._validation.lastName" class="form-input-hint">
                                        {{ contact._validation.lastName }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="divider"></div>
                        <h6><i class="fas fa-envelope"></i> Email</h6>
                        <div v-bind:class="{ 'has-error': contact._validation.emailAddress }" class="form-group">
                            <input v-model="contact.emailAddress" class="form-input" type="email" placeholder="Email">
                            <p v-if="contact._validation.emailAddress" class="form-input-hint">
                                {{ contact._validation.emailAddress }}</p>
                        </div>
                        <div class="divider"></div>
                        <h6><i class="fas fa-phone-square"></i> Phone numbers</h6>

                        <div class="edit-phone-numbers">
                            <ContactEditPhoneNumber
                                    v-for="(phoneNumber, index) in contact.phoneNumbers"
                                    :key="phoneNumber.id"
                                    v-bind:phoneNumber="phoneNumber"
                                    v-on:removePhoneNumber="removePhoneNumber(phoneNumber, index)"
                            />
                        </div>

                        <button v-on:click.stop="addPhoneNumber"
                                class="btn btn-link btn-add-phone-number"
                                title="Add phone number"
                                type="button">
                            <i class="fas fa-plus"></i> Add phone number
                        </button>

                        <div v-if="submitError" class="toast toast-error">
                            <button v-on:click.stop="submitError = null" type="button"
                                    class="btn btn-clear float-right"></button>
                            <p>{{ submitError }}</p>
                        </div>

                        <div class="divider"></div>

                        <div class="columns">
                            <div class="column col-6">
                                <router-link
                                        class="btn btn-cancel float-left"
                                        :to="{name: 'contactDetails', params: {id: this.id}}"
                                        tag="button"
                                        title="Cancel edit"
                                        type="button">
                                    Cancel
                                </router-link>
                            </div>
                            <div class="column col-6">
                                <button class="btn btn-primary btn-save float-right" type="submit">Save</button>
                            </div>
                        </div>

                    </div>
                </div>
            </form>
        </div>

        <ConfirmDialog
                v-if="showConfirmDialog"
                v-bind:title="'Delete contact \'' + contact.firstName + ' ' + contact.lastName + '\'?'"
                v-bind:actionTitle="'Delete'"
                v-on:confirm="remove()"
                v-on:cancel="showConfirmDialog = false"
        />
    </div>
</template>

<script>
  import ContactEditPhoneNumber from './ContactEditPhoneNumber';
  import ConfirmDialog from './ConfirmDialog';
  import set from 'lodash.set';

  export default {
    name: 'ContactEdit',
    components: {
      ContactEditPhoneNumber,
      ConfirmDialog
    },
    data() {
      return {
        contact: {
          id: null,
          firstName: '',
          lastName: '',
          emailAddress: '',
          profilePhoto: null, // ProfilePhoto IRI from @id
          phoneNumbers: [],
          _validation: {
            firstName: null,
            lastName: null,
            emailAddress: null,
          },
        },
        profilePhoto: null, // full ProfilePhoto object
        loading: true,
        profilePhotoLoading: false,
        error: null,
        submitError: null,
        showConfirmDialog: false
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
      getPhoneNumberModel: function(id, number, label) {
        return {
          id: id,
          number: number,
          label: label,
          _validation: {
            number: null,
            label: null,
          },
        };
      },
      fetchItem: function() {
        if (!this.id) {
          this.loading = false;
          return;
        }
        this.$Progress.start();
        let url = '/api/contacts/' + this.id;
        fetch(url).then(async response => {
          if (response.ok) {
            let data = await response.json();
            this.contact.id = data.id;
            this.contact.firstName = data.firstName;
            this.contact.lastName = data.lastName;
            this.contact.emailAddress = data.emailAddress;
            if (data.profilePhoto) {
              this.contact.profilePhoto = data.profilePhoto['@id'];
              this.profilePhoto = data.profilePhoto;
            }
            this.contact.phoneNumbers = [];
            data.phoneNumbers.forEach((pn) => {
              this.contact.phoneNumbers.push(this.getPhoneNumberModel(pn.id, pn.number, pn.label));
            });
          } else {
            this.error = response.json()['hydra:description'];
          }
          this.$Progress.finish();
          this.loading = false;
        }).catch(error => {
          this.error = error.toString();
          this.$Progress.fail();
          this.loading = false;
        });
      },
      handleSubmit() {
        let bntSave = document.querySelector('.edit form .btn-save');
        if (bntSave.classList.contains('loading')) {
          return;
        }
        bntSave.classList.add('loading');
        this.submitError = null;
        this.resetValidationViolations();

        this.$Progress.start();

        let url = '/api/contacts';
        let method = 'POST';
        if (this.contact.id) {
          url += '/' + this.contact.id;
          method = 'PUT';
        }

        // Clone contact object
        let contact = JSON.parse(JSON.stringify(this.contact));

        // Delete _validation on contact and phone numbers
        delete contact._validation;
        contact.phoneNumbers.forEach((phoneNumber) => delete phoneNumber._validation);

        let jsonData = JSON.stringify(contact);
        fetch(url, {
          method: method,
          headers: {
            'Accept': 'application/ld+json',
            'Content-Type': 'application/json',
          },
          body: jsonData,
        }).then(async response => {
          this.$Progress.finish();
          bntSave.classList.remove('loading');

          let data = await response.json();

          if (response.ok) {
            this.$router.push({name: 'contactDetails', params: {id: data.id}});
          } else if ('violations' in data) {
            this.handleValidationViolations(data);
          } else {
            this.submitError = data['hydra:description'];
          }

        }).catch(error => {
          this.$Progress.fail();
          bntSave.classList.remove('loading');
          this.submitError = error.toString();
        });
      },
      resetValidationViolations() {
        const restPropValidation = (validation) => {
          for (let prop in validation) {
            if (validation.hasOwnProperty(prop)) {
              validation[prop] = null;
            }
          }
        };
        restPropValidation(this.contact._validation);
        this.contact.phoneNumbers.forEach((pn) => {
          restPropValidation(pn._validation);
        });
      },
      handleValidationViolations(data) {
        data.violations.forEach((violation) => {
          let pathParts = violation.propertyPath.split('.');
          if (pathParts.length > 1) {
            pathParts.splice(1, 0, '_validation');
          } else {
            pathParts.unshift('_validation');
          }
          let propertyPath = pathParts.join('.');
          set(this.contact, propertyPath, violation.message);
        });
      },
      addPhoneNumber() {
        this.contact.phoneNumbers.push(this.getPhoneNumberModel(null, '', ''));
      },
      removePhoneNumber(phoneNumber, index) {
        this.contact.phoneNumbers.splice(index, 1);
      },
      uploadProfilePhoto() {
        let input = document.querySelector('#input-photo-upload');
        if (input.files && input.files[0]) {

          this.$Progress.start();
          this.profilePhotoLoading = true;

          let reader = new FileReader();
          reader.onload = function(e) {
            document.querySelector('#photo-preview').src = e.target.result;
          };
          reader.readAsDataURL(input.files[0]);

          let formData = new FormData();
          formData.append('file', input.files[0]);

          fetch('/api/profile-photos', {
            method: 'POST',
            headers: {
              'Accept': 'application/ld+json',
            },
            body: formData,
          }).then(async response => {

            let data = await response.json();

            if (response.ok) {
              this.contact.profilePhoto = data['@id'];
              this.profilePhoto = data;
            } else {
              this.submitError = data['hydra:description'];
            }

            this.$Progress.finish();
            this.profilePhotoLoading = false;

          }).catch(error => {
            this.error = error.toString();

            this.$Progress.finish();
            this.profilePhotoLoading = false;
          });

        }
      },
      confirmRemove: function() {
        this.showConfirmDialog = true;
      },
      remove: function() {
        this.showConfirmDialog = false;
        this.$Progress.start();
        fetch('/api/contacts/' + this.contact.id, {
          method: 'DELETE',
          headers: {
            'Accept': 'application/ld+json',
          },
        }).then(async response => {
          this.$Progress.finish();
          if (response.ok) {
            this.$router.push({name: 'contactListAll'});
          } else {
            let data = await response.json();
            this.submitError = data['hydra:description'];
          }
        }).catch(error => {
          this.$Progress.fail();
        });
      },
    },
  };
</script>

<style>
    .edit {
        padding-top: 2rem;
        border-top: .05rem solid #5755d9;
    }

    .edit-header {
        margin-top: .8rem;
        padding-bottom: .2rem;
        border-bottom: .05rem solid #e5e5f9;
    }

    .edit-header .btn {
        margin-top: .14rem;
    }

    .edit-body {
        padding-top: 1rem;
    }

    .edit-body .col-pad {
        padding: 1rem .5rem;
    }

    .edit-body .col-pad-bottom {
        padding-bottom: 1rem;
    }

    .edit-body h6 {
        margin-top: 1rem;
        margin-bottom: .5rem;
    }

    .edit-body h6 i {
        margin-right: .4rem;
    }

    .edit-body .text-grayer {
        color: #455060;
    }

    .edit-body .divider {
        border-color: #e5e5f9;
        margin: 1.4rem 0;
    }

    .edit-body .edit-phone-number {
        margin: .7rem 0;
    }

    .edit-body .btn-add-phone-number i {
        margin-right: .2rem;
    }

    .edit .avatar {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
    }

    .edit .avatar i {
        position: absolute;
        z-index: 2;
        bottom: 3.4rem;
    }

    .edit .form-input-hint {
        margin: .2rem 0;
    }

    .input-file {
        width: 0.1px;
        height: 0.1px;
        opacity: 0;
        overflow: hidden;
        position: absolute;
        z-index: -1;
    }

    .photo-upload {
        cursor: pointer;
    }

    .photo-upload.loading::after {
        border-color: #fff;
        border-right-color: transparent;
        border-top-color: transparent;
    }
</style>