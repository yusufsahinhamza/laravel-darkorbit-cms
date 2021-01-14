<template>
    <v-container>
        <v-row
            align="center"
            justify="center"
        >
            <v-col cols="5">
                <v-sheet rounded="lg">
                    <v-form
                        ref="form"
                        class="pa-6"
                    >
                        <v-text-field
                            label="Username"
                            outlined
                            clearable
                            v-model="form.username"
                            :rules="[globalRules.required]"
                        ></v-text-field>
                        <v-text-field
                            label="Password"
                            outlined
                            clearable
                            v-model="form.password"
                            :rules="[globalRules.required]"
                            :append-icon="form.showPassword ? 'visibility' : 'visibility_off'"
                            :type="form.showPassword ? 'text' : 'password'"
                            @click:append="form.showPassword = !form.showPassword"
                        ></v-text-field>
                        <v-btn
                            block
                            large
                            color="primary"
                            :disabled="form.submitButtonLoading"
                            :loading="form.submitButtonLoading"
                            @click="submitForm()"
                        >
                        Login
                        </v-btn>
                    </v-form>
                </v-sheet>
            </v-col>
        </v-row>
    </v-container>
</template>

<script>
    import { mapActions, mapState } from 'vuex';

    export default {
        props: [
            'snackbars'
        ],
        data: () => ({
            form: {
                username: '',
                password: '',
                submitButtonLoading: false,
                showPassword: false
            }
        }),
        computed: {
            ...mapState([
                'user'
            ]),
            globalRules() {
                const rules = {
                    required: v => !!v || 'This field is required'
                };

                return rules;
            }
        },
        methods: {
            ...mapActions([
                'loadUser'
            ]),
            submitForm() {
                if (this.$refs.form.validate() == false) {
                    return;
                }

                this.form.submitButtonLoading = true;

                axios.post('/auth/login', {
                    username: this.form.username,
                    password: this.form.password
                }).then(response => {
                    if (response.data.status === true) {
                        this.loadUser().then(() => {
                            this.$router.push({ name: this.user.f === 'NONE' ? 'SelectCompany' : 'Index' });
                        });
                    } else {
                        if (response.data.message) {
                            this.snackbars.push({ color: 'red', header: 'Error', message: response.data.message });
                        }
                    }
                }).catch(error => {
                    this.snackbars.push({ color: 'red', header: 'Error', message: 'An error occurred while submitting the form.' });
                }).finally(() => {
                    this.form.submitButtonLoading = false;
                });
            }
        }
    }
</script>
