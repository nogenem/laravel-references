import { defineStore } from 'pinia';

export const useSignedInUserStore = defineStore('signedInUser', {
    state: () => ({
        signedInUser: {},
    }),
    actions: {
        setSignedInUser(signedInUser) {
            this.signedInUser = signedInUser;
        },
    }
});
