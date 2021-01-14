import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex)

const store = new Vuex.Store({
    state: {
        user: null
    },
    getters: {
        authenticated(state) {
            return state.user !== null
        }
    },
    mutations: {
        updateUser(state, data) {
            if (typeof data === 'object' && data !== null) {
                state.user = Object.assign({}, state.user, data);
            } else {
                state.user = null;
            }
        }
    },
    actions: {
        checkAuth({ dispatch }) {
            return new Promise((resolve, reject) => {
                axios.post('/auth/check').then(async response => { 
                    if (response.data.status === true) {
                        await dispatch('loadUser');
                    }
                    
                    resolve(response);
                }).catch(error => {
                    reject(error);
                });
            });
        },
        loadUser({ commit }) {
            return new Promise((resolve, reject) => {
                axios.post('/auth/get-user').then(async response => { 
                    if (response.data.status === true) {
                        commit('updateUser', response.data.data);
                    }
                    
                    resolve(response);
                }).catch(error => {
                    reject(error);
                });
            });
        }
    }
})

export default store