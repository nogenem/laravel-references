import axios from "axios";
import { ref } from "vue";

import getServerErrors from "~/shared/utils/getServerErrors";

export default function useSearchUsers() {
    const users = ref([]);
    const errors = ref({});

    function clearErrors() {
        errors.value = {};
    }

    async function searchUsers(searchValue) {
        try {
            const response = await axios.get(
                `/api/users/search?name=${searchValue}`
            );

            clearErrors();
            users.value = response.data.data;
        } catch (err) {
            errors.value = getServerErrors(err);
            console.error('Error while fetching users by name', err);
        }
    }

    return {
        users,
        errors,
        clearErrors,
        searchUsers,
    };
}
