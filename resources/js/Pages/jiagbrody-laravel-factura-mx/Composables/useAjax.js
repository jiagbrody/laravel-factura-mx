// import useFlashMessage from "@/Composables/useFlashMessage.js";

export const useGetAjax = async (url, params) => {
    return await axios.get(url, {params}).then((res) => validateResponseJson(res)).catch(error => detectTypeError(error))
}

export const usePostAjax = async (url, params, options) => {
    return await axios.post(url, params, options).then((res) => validateResponseJson(res)).catch(error => detectTypeError(error))
}

export const usePutAjax = async (url, params, options) => {
    return await axios.put(url, params, options).then((res) => validateResponseJson(res)).catch(error => detectTypeError(error))
}

export const usePatchAjax = async (url, params, options) => {
    return await axios.patch(url, params, options).then((res) => validateResponseJson(res)).catch(error => detectTypeError(error))
}

export const useDeleteAjax = async (url, params) => {
    return await axios.delete(url, {data: params}).catch(error => detectTypeError(error))
}

function validateResponseJson(response) {
    if (response.headers['content-type'] === 'application/json') {
        response.validateErrors = false
        response.forbiddenError = false
        response.unauthorized = false
        // return {data: response.data, validateErrors: false, forbiddenError: false}
        return response
    }
    /*useFlashMessage({
        message: 'La respuesta del ajax no es json',
        description: `Status response http: ${response.status}`,
        status: 'warning'
    })*/
    console.warn('La respuesta del ajax no esta en formato json')
}

function FatalError(data) {
    Error.apply(this, arguments)
    this.name = "Error en componente AJAX"

    // return data
}


function detectTypeError(error) {
    if (error.response) {
        // console.log(error)
        // console.log(error.response.data)
        // console.log(error.response.status)
        // console.log(error.response.headers)

        console.warn(error.response.data.message)

        // Por Unauthorized (sin autorización)
        if (error.response.status === 401) {
            // useFlashMessage({message: error.response.data.message, status: 'info'})
            return {unauthorized: true}
        }


        // Errores de validación de Laravel
        if (error.response.status === 422) {
            if (error.response.data) {
                let laravelValidationErrors = error.response.data.errors

                if (laravelValidationErrors) {
                    for (const key of Object.entries(laravelValidationErrors)) {
                        laravelValidationErrors[key[0]] = (key[1].length > 0) ? key[1][0] : key[1]
                    }

                    // useFlashMessage({message: 'Existen errores en el formulario', status: 'info'})
                    return {validateErrors: laravelValidationErrors, message: error.response.data.message}
                }

                // useFlashMessage({message: error.response.data.message, status: 'danger'})
                return {validateErrors: {}, message: error.response.data.message}
            }
        }


        // Errores Forbidden (prohibicion)
        if (error.response.status === 403) {
            // useFlashMessage({message: error.response.data.message, status: 'info'})
            return {forbiddenError: true}
        }


        // 500 Internal Server Error
        /*if (error.response.status === 500) {
            useFlashMessage({
                message: error,
                description: `Error status: ${JSON.stringify(error.response.data.exception)}`,
                status: 'danger'
            })
            return {forbiddenError: true}
        }*/

        /*useFlashMessage({
            message: error,
            description: `Error status: ${JSON.stringify(error.response.data.exception)}`,
            status: 'danger'
        })*/

        console.dir(error)
        throw new Error()

    } else if (error.request) {
        // The request was made but no response was received
        // `error.request` is an instance of XMLHttpRequest in the browser and an instance of
        // http.ClientRequest in node.js
        // useFlashMessage({message: error, description: '', status: 'danger'})
    } else {
        // Something happened in setting up the request that triggered an Error
        // useFlashMessage({message: error, description: error.message, status: 'danger'})
    }

    throw new FatalError(error)
}
