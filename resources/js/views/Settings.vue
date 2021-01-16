<template>
    <v-container>
        <v-row 
            align="center"
            justify="center"
        >
            <v-col>
                <v-sheet
                    min-height="70vh"
                    rounded="lg"
                >
                    <v-row>
                        <v-col cols="4" class="pa-6">
                            <h3 class="mb-4">Change Pilot Name</h3>
                            <v-form
                                ref="pilotNameForm"
                                v-on:submit.prevent="changePilotName()"
                            >
                                <v-text-field
                                    label="Pilot Name"
                                    outlined
                                    clearable
                                    v-model="pilotNameForm.pilotName"
                                    :rules="[globalRules.required]"
                                ></v-text-field>
                                <v-btn
                                    block
                                    large
                                    color="primary"
                                    type="submit"
                                    :disabled="pilotNameForm.submitButtonLoading"
                                    :loading="pilotNameForm.submitButtonLoading"
                                >
                                Change
                                </v-btn>
                            </v-form>
                        </v-col>
                    </v-row>
                    <v-row>
                        <v-col cols="4" class="pa-6">
                            <h3 class="mb-4">Change Password</h3>
                            <v-form
                                ref="passwordChangeForm"
                                v-on:submit.prevent="changePassword()"
                            >
                                <v-text-field
                                    label="Old Password"
                                    outlined
                                    clearable
                                    v-model="passwordChangeForm.oldPassword"
                                    :rules="[globalRules.required]"
                                    :append-icon="passwordChangeForm.showOldPassword ? 'visibility' : 'visibility_off'"
                                    :type="passwordChangeForm.showOldPassword ? 'text' : 'password'"
                                    @click:append="passwordChangeForm.showOldPassword = !passwordChangeForm.showOldPassword"
                                ></v-text-field>
                                <v-text-field
                                    label="New Password"
                                    outlined
                                    clearable
                                    v-model="passwordChangeForm.newPassword"
                                    :rules="[globalRules.required]"
                                    :append-icon="passwordChangeForm.showNewPassword ? 'visibility' : 'visibility_off'"
                                    :type="passwordChangeForm.showNewPassword ? 'text' : 'password'"
                                    @click:append="passwordChangeForm.showNewPassword = !passwordChangeForm.showNewPassword"
                                ></v-text-field>
                                <v-text-field
                                    label="Confirm new password"
                                    outlined
                                    clearable
                                    v-model="passwordChangeForm.confirmNewPassword"
                                    :rules="[globalRules.required, globalRules.changePasswordForm.confirmPassword]"
                                    type="password"
                                ></v-text-field>
                                <v-btn
                                    block
                                    large
                                    color="primary"
                                    type="submit"
                                    :disabled="passwordChangeForm.submitButtonLoading"
                                    :loading="passwordChangeForm.submitButtonLoading"
                                >
                                Change
                                </v-btn>
                            </v-form>
                        </v-col>
                    </v-row>                    
                </v-sheet>
            </v-col>
        </v-row>
    </v-container>
</template>

<script>
    import { mapState, mapMutations } from 'vuex';

    export default {
        props: [
            'snackbars'
        ],
        data: () => ({
            pilotNameForm: {
                pilotName: '',
                submitButtonLoading: false
            },
            passwordChangeForm: {
                oldPassword: '',
                newPassword: '',
                confirmNewPassword: '',
                showOldPassword: false,
                showNewPassword: false,
                submitButtonLoading: false
            }
        }),
        created() {
            this.pilotNameForm.pilotName = this.user.pn;
        },
        computed: {
            ...mapState([
                'user'
            ]),            
            globalRules() {
                const rules = {
                    required: v => !!v || 'This field is required',
                    changePasswordForm: {
                        confirmPassword: v => v === this.passwordChangeForm.newPassword || 'Passwords does not match'
                    }
                };

                return rules;
            }
        },        
        methods: {
            ...mapMutations([
                'updateUser'
            ]),
            changePilotName() {
                if (this.$refs.pilotNameForm.validate() == false) {
                    return;
                }

                this.pilotNameForm.submitButtonLoading = true;

                axios.post('/user/change-pilot-name', {
                    pilotName: this.pilotNameForm.pilotName
                }).then(response => {
                    if (response.data.status === true) {
                        this.updateUser({ pn: this.pilotNameForm.pilotName });

                        this.snackbars.push({ header: 'Success', message: 'Pilot name has changed successfully.' });
                    } else {
                        if (response.data.message) {
                            this.snackbars.push({ color: 'red', header: 'Error', message: response.data.message });
                        }
                    }
                }).catch(error => {
                    this.snackbars.push({ color: 'red', header: 'Error', message: 'An error occurred while submitting the form.' });
                }).finally(() => {
                    this.pilotNameForm.submitButtonLoading = false;
                });
            },
            changePassword() {
                if (this.$refs.passwordChangeForm.validate() == false) {
                    return;
                }

                this.passwordChangeForm.submitButtonLoading = true;

                axios.post('/user/change-password', {
                    oldPassword: this.passwordChangeForm.oldPassword,
                    newPassword: this.passwordChangeForm.newPassword
                }).then(response => {
                    if (response.data.status === true) {
                        this.snackbars.push({ header: 'Success', message: 'Password has changed successfully.' });
                    } else {
                        if (response.data.message) {
                            this.snackbars.push({ color: 'red', header: 'Error', message: response.data.message });
                        }
                    }
                }).catch(error => {
                    this.snackbars.push({ color: 'red', header: 'Error', message: 'An error occurred while submitting the form.' });
                }).finally(() => {
                    this.passwordChangeForm.submitButtonLoading = false;
                });                
            }
        }
    }
</script>
