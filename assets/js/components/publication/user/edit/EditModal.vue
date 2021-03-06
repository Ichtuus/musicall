<template>
    <b-modal id="modal-publication-properties" ref="modal-publication-properties" size="lg"
             title="Propriété de la publication">

        <div>
            <b-form-group description="Le titre de votre publication">
                <b-form-input v-model="currentTitle" :state="validation.title.state"
                              placeholder="Votre titre ici"></b-form-input>
                <b-form-invalid-feedback :state="validation.title.state">
                    {{ validation.title.message }}
                </b-form-invalid-feedback>
            </b-form-group>

            <b-form-group description="Cette courte description apparaitra sur la page d'accueil">
                <b-form-textarea
                        v-model="currentDescription"
                        id="textarea"
                        placeholder="Une courte description de l'article"
                        rows="3"
                ></b-form-textarea>
            </b-form-group>

            <p class="alert alert-info">
                Attention, l'image doit faire max 4000px de largeur ou hauteur. (max 4mb)
            </p>
            <b-alert v-show="errors.length" variant="danger" show>
                <span v-for="error in errors" class="d-block">{{ error }}</span>
            </b-alert>
            <b-row>
                <b-col v-if="currentCover">
                    <b-img :src="currentCover" fluid-grow></b-img>
                </b-col>
                <b-col v-else>
                    Il n'y a pas encore de cover pour cette publication
                </b-col>
                <b-col>
                    <vue-dropzone
                            v-if="dropzoneOptions"
                            ref="myVueDropzone"
                            id="dropzone"
                            @vdropzone-success="vfileUploaded"
                            @vdropzone-error="error"
                            @vdropzone-file-added="start"
                            :options="dropzoneOptions"
                    >
                    </vue-dropzone>

                </b-col>
            </b-row>
        </div>


        <template slot="modal-footer" slot-scope="{ ok, cancel, hide }">
            <b-button variant="default" @click="cancel()">
                Annuler
            </b-button>

            <b-button variant="outline-success" @click="save" class="float-right" :disabled="submitted">
                <b-spinner small v-if="submitted"></b-spinner>
                <i class="far fa-save" v-else></i>
                Enregistrer
            </b-button>
        </template>
    </b-modal>
</template>

<script>
  import vue2Dropzone from "vue2-dropzone";

  export default {
    components: {
      vueDropzone: vue2Dropzone
    },
    props: ['id', 'title', 'description', 'validation', 'cover', 'submitted'],
    data() {
      return {
        currentTitle: '',
        currentDescription: '',
        currentCover: '',
        dropzoneOptions: null,
        errors: [],
      }
    },
    mounted() {
      this.currentTitle = this.title;
      this.currentDescription = this.description;
      this.currentCover = this.cover;

      this.$refs['modal-publication-properties'].$on('show', async () => {
        this.dropzoneOptions = {
          url: Routing.generate('api_user_publication_upload_cover', {id: this.id}),
          headers: {'Authorization': 'Bearer ' + await this.$store.dispatch('security/getAuthToken', {displayLoading: false})},
          paramName: 'image_upload[imageFile][file]',
          thumbnailWidth: 200,
          maxFilesize: 4,
          dictFileTooBig: 'Le fichier est trop volumineux ({{filesize}}M). Sa taille ne doit pas dépasser {{maxFilesize}} M.',
          dictDefaultMessage: "<i class=\"fas fa-cloud-upload-alt\"></i> Uploader une cover"
        };
      });

    },
    methods: {
      save() {
        this.$emit('saveProperties', {title: this.currentTitle, description: this.currentDescription});
      },
      start() {
        this.errors = [];
      },
      vfileUploaded(file, resp) {
        if (resp.error) {
          alert('Erreur lors de l\'upload');
        } else {
          this.currentCover = resp.data.uri;
        }
      },
      error(error, messages) {
        if (!Array.isArray(messages)) {
          this.errors.push(messages);
          return;
        }

        for (const message of messages) {
          this.errors.push(message.message);
        }
      },
    }
  }
</script>