export default function getServerErrors(error) {
    if (error.response) {
        if (error.response.status === 422 && error.response.data.errors) {
            return error.response.data.errors;
        } else if (error.response.data.code) {
            return { global: [error.response.data.message] };
        }
    }
    return { global: ['An error occurred, please try again later.'] };
}
