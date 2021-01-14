<template>
    <v-app id="inspire">
        <Header :snackbars="snackbars"/>
        <v-main class="grey lighten-3 mt-2">
            <transition
                name="fade"
                mode="out-in"
            >
                <router-view :snackbars="snackbars"></router-view>
            </transition>
        </v-main>
        <VSnackbars :objects.sync="snackbars" :timeout="2000" color="primary" bottom right>
            <template v-slot:action="{ close }">
                <v-btn icon @click="close()">
                    <v-icon>close</v-icon>
                </v-btn>
            </template>
        </VSnackbars>
    </v-app>
</template>

<style>
.fade-enter, .fade-leave-to {
    opacity: 0;
    transform: translateX(2em);
}

.fade-enter-active, .fade-leave-active {
    transition: all .2s ease;
}
</style>

<script>
    import Header from './components/Header';

    export default {
        components: {
            Header
        },
        data: () => ({
            snackbars: []
        })
    }
</script>