<template>
    <div>
        <div v-if="hasError" class="row">
            <div class="col-12 col-lg-4 offset-lg-4">
                <div class="alert alert-danger" role="alert">
                    {{ error.message }}
                </div>
            </div>
        </div>
        <div class="row">
            <form method="post" class="col-lg-4 col-12 offset-lg-4 form-signin">

                <b-alert show variant="info">
                    MusicAll a été mise à jour.<br/>
                    Vous pouvez toujours utiliser votre précédent login/email.<br/>
                    Vous allez devoir simplement <router-link :to="{name: 'user_request_lost_password'}" class="mt-3">reset votre password</router-link> la première fois
                </b-alert>
                <h1 class="h3 mb-3 font-weight-normal">Login</h1>
                <label for="inputUsername" class="sr-only">Username</label>
                <b-form-input
                        id="inputUsername"
                        v-model="username"
                        required autofocus
                        size="lg"
                        placeholder="nom d'utilisateur"
                ></b-form-input>

                <label for="inputPassword" class="sr-only">Password</label>
                <b-form-input
                        id="inputPassword"
                        v-model="password"
                        type="password"
                        size="lg"
                        required
                        placeholder="mot de passe"
                ></b-form-input>

                <b-button
                        variant="primary" block
                        size="lg"
                        class="mt-3 mb-4"
                        :disabled="!username.length || !password.length || isLoading"
                        type="submit" @click.prevent @click="login">
                    <b-spinner small type="grow" v-if="isLoading"></b-spinner>
                    me connecter
                </b-button>

                <router-link :to="{name: 'user_request_lost_password'}" class="mt-3">Mot de passe oublié ?</router-link>
            </form>
        </div>
    </div>
</template>

<script>
  import {mapGetters} from 'vuex';

  export default {
    data() {
      return {
        username: '',
        password: '',
      }
    },
    computed: {
      ...mapGetters('security', [
        'isLoading',
        'hasError',
        'error'
      ])
    },
    methods: {
      async login() {
        const username = this.username;
        const password = this.password;
        const redirect = this.$route.query.redirect;

        await this.$store.dispatch('security/login', {username, password});

        this.$nextTick(() => {
          if (!this.hasError) {
            if (typeof redirect !== "undefined") {
              this.$router.push({path: redirect});
            } else {
              this.$router.push({name: "home"});
            }
          }
        })
      },
    }
  }
</script>