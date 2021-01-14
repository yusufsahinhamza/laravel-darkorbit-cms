<template>
    <v-container>
        <v-row
            align="center"
            justify="center"
        >
            <v-col>
                <v-sheet rounded="lg">
                    <v-row class="text-center">
                        <v-col
                            v-for="company in companies"
                            :key="company.text"
                            :cols="(12 / companies.length).toString()"
                        >
                            <h1>{{ company.text }}</h1>
                            <v-img
                                class="ma-5 cursor-pointer"
                                :src="company.img_src"
                                @click="ask(company.text)"
                            ></v-img>
                        </v-col>
                    </v-row>
                </v-sheet>
            </v-col>
        </v-row>
        <v-dialog
            v-model="dialog"
            max-width="290"
        >
            <v-card>
                <v-card-title class="headline">
                Are you sure?
                </v-card-title>
                <v-card-text>Are you sure that you really want to choose <b>{{ selectedCompany }}</b> company?</v-card-text>
                <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn
                    text
                    @click="selectedCompany = 'NONE'; dialog = false;"
                >
                No
                </v-btn>
                <v-btn
                    text
                    @click="select()"
                >
                Yes
                </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-container>
</template>

<style>
.cursor-pointer {
    cursor: pointer;
}
</style>

<script>
    import { mapMutations } from 'vuex';

    export default {
        props: [
            'snackbars'
        ],
        data: () => ({
            dialog: false,
            selectedCompany: 'NONE',
            companies: [
                {
                    text: 'MMO',
                    img_src: '/img/companies/mmo.jpg'
                },
                {
                    text: 'EIC',
                    img_src: '/img/companies/eic.jpg'
                },
                {
                    text: 'VRU',
                    img_src: '/img/companies/vru.jpg'
                }
            ]
        }),
        methods: {
            ...mapMutations([
                'updateUser'
            ]),
            ask(company) {
                this.selectedCompany = company;
                this.dialog = true;
            },
            select() {
                this.dialog = false;

                axios.post('/user/select-company', {
                    company: this.selectedCompany
                }).then(response => {
                    if (response.data.status === true) {
                        this.updateUser({ f: this.selectedCompany });
                        this.$router.push({ name: 'Index' });
                    } else {
                        if (response.data.message) {
                            this.snackbars.push({ color: 'red', header: 'Error', message: response.data.message });
                        }
                    }
                }).catch(error => {
                    this.snackbars.push({ color: 'red', header: 'Error', message: 'An error occurred while submitting the form.' });
                });
            }
        }
    }
</script>
