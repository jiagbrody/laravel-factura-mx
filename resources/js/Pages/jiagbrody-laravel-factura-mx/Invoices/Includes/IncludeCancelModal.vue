<script setup>
import CompButtonSubmit from "@/Pages/jiagbrody-laravel-factura-mx/Components/CompButtonSubmit.vue";
import {useDeleteAjax} from "@/Pages/jiagbrody-laravel-factura-mx/Composables/useAjax.js";
import CompConfirmationModal from "@/Pages/jiagbrody-laravel-factura-mx/Components/CompConfirmationModal.vue";
import CompFormTextInput from "@/Pages/jiagbrody-laravel-factura-mx/Components/Form/CompFormTextInput.vue";
import CompFormSelectInput from "@/Pages/jiagbrody-laravel-factura-mx/Components/Form/CompFormSelectInput.vue";


const props = defineProps({
    cancelData: {
        type: Object,
        required: true
    },
    catInvoiceCfdiCancelTypes: {
        type: Array,
        default: [],
    },
})

const onSubmitStatus = () => {
    props.cancelData.formData.processing = true
    const response = useDeleteAjax(route('laravel-factura-mx.invoices.set-cancel', props.cancelData.formData.invoice.id), props.cancelData.formData.data())
    response.then(({data, validateErrors}) => {
        if(validateErrors){
            props.cancelData.formData.errors = validateErrors
        }

        if (data) {
            props.cancelData.response = data.pac_response
        }
    }).finally(() => {
        props.cancelData.formData.processing = false
    })
}

function isNotEmpty(obj) {
    return Object.keys(obj).length !== 0
}

function onCloseModal() {
    props.cancelData.formData.reset()
    props.cancelData.formData.clearErrors()
    props.cancelData.modal = false
}
</script>
<template>
    <CompConfirmationModal :show="cancelData.modal" @close="onCloseModal()">
        <template #title>
            Cancelar la factura electrónica
            <div>
                <template v-if="cancelData.formData.invoice.invoice_cfdi">
                    {{ cancelData.formData.invoice.invoice_cfdi.uuid }}
                </template>
                <template v-else>
                    No existe un UUID
                </template>
            </div>
        </template>
        <template #content>
            <template v-if="isNotEmpty(cancelData.response)">
                <table class="lyt-default-listing-table">
                    <tr>
                        <td class="w-1/2 font-bold">Detalles validación EFOS</td>
                        <td class="w-1/2">{{ cancelData.response.detallesValidacionEFOS }}</td>
                    </tr>
                </table>
            </template>
            <template v-else>
                <div class="grid grid-cols-1 gap-6">
                    <CompFormSelectInput label="Tipo de cancelación"
                                         v-model.number="cancelData.formData.invoice_cfdi_cancel_type_id"
                                         :options="catInvoiceCfdiCancelTypes"
                                         :error="cancelData.formData.errors.invoice_cfdi_cancel_type_id"/>

                    <div v-if="cancelData.formData.invoice_cfdi_cancel_type_id === 1">
                        <CompFormTextInput label="UUID a relacionar"
                                           v-model="cancelData.formData.uuid"
                                           :error="cancelData.formData.errors.uuid"/>
                    </div>
                </div>
            </template>
        </template>
        <template #footer>
            <div class="flex items-center justify-end">
                <button type="button" class="lyt-button lyt-button-style-default mr-4"
                        @click="onCloseModal()">
                    Cerrar
                </button>
                <template v-if=" ! isNotEmpty(cancelData.response)">
                    <CompButtonSubmit :is-loading="cancelData.formData.processing"
                                      @click="onSubmitStatus()"
                                      class="lyt-button lyt-button-style-danger">
                        Cancelar factura
                    </CompButtonSubmit>
                </template>
            </div>
        </template>
    </CompConfirmationModal>
</template>
