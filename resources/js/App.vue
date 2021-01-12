<template>
    <v-app id="inspire">
        <Header/>
        <v-main class="grey lighten-3">
            <transition
                name="fade"
                mode="out-in"
            >
                <router-view></router-view>
            </transition>
        </v-main>
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
            links: [
                'Dashboard',
                'Messages',
                'Profile',
                'Updates',
            ]
        })
    }
</script>