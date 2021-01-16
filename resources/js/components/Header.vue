<template>
    <v-app-bar
        app
        color="white"
    >
        <v-container class="py-0 fill-height">
            <v-btn
                text
                exact
                v-for="link in links"
                :key="link.text"
                :to="{ name: link.routeName }"
            >
            {{ link.text }}
            </v-btn>

            <v-spacer></v-spacer>

            <div v-if="authenticated">
                <v-menu
                    offset-y
                    transition="slide-y-transition"
                    bottom
                    dark
                >
                    <template
                        v-slot:activator="{ on, attrs }"
                    >
                        <v-btn
                            class="ml-5 mr-5"
                            icon
                            v-bind="attrs"
                            v-on="on"
                        >
                            <v-icon>person</v-icon>
                        </v-btn>
                    </template>
                    <v-list>
                        <v-list-item :to="{ name: 'Settings' }">
                            <v-list-item-title>Settings</v-list-item-title>
                        </v-list-item>
                        <v-divider></v-divider>
                        <v-list-item
                            @click="logout()"
                        >
                            <v-list-item-icon>
                                <v-icon>exit_to_app</v-icon>
                            </v-list-item-icon>
                            <v-list-item-title>Logout</v-list-item-title>
                        </v-list-item>
                    </v-list>
                </v-menu>
            </div>
            <div v-else>
                <v-btn 
                    text
                    :to="{ name: 'Login' }"
                >Login</v-btn>
                <v-btn
                    class="ml-2"
                    text
                    :to="{ name: 'Register' }"
                >Register</v-btn>
            </div>
        </v-container>
    </v-app-bar>
</template>

<script>
    import { mapGetters, mapState, mapMutations } from 'vuex';

    export default {
        props: [
            'snackbars'
        ],
        data: () => ({
            links: [
                {
                    text: 'Homepage',
                    routeName: 'Index'
                }
            ]
        }),
        computed: {
            ...mapState([
                'user'
            ]),
            ...mapGetters([
                'authenticated'
            ])
        },
        methods: {
            ...mapMutations([
                'updateUser'
            ]),
            logout() {
                axios.post('/auth/logout').then(response => {
                    if (response.data.status === true) {
                        this.updateUser(null);

                        if (this.$route.name !== 'Index') {
                            this.$router.push({ name: 'Index' });
                        }
                    }
                }).catch(error => {
                    this.snackbars.push({ color: 'red', header: 'Error', message: 'An error occurred while logging out.' });
                });
            }
        }
    }
</script>