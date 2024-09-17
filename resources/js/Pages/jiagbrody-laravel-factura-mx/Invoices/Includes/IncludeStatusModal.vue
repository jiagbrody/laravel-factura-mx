<script setup>
import CompDialogModal from "@/Pages/jiagbrody-laravel-factura-mx/Components/CompDialogModal.vue";
import CompButtonSubmit from "@/Pages/jiagbrody-laravel-factura-mx/Components/CompButtonSubmit.vue";
import {usePostAjax} from "@/Pages/jiagbrody-laravel-factura-mx/Composables/useAjax.js";

const props = defineProps({
    status: {
        type: Object,
        required: true
    }
})

const onSubmitStatus = () => {
    props.status.formData.processing = true
    const response = usePostAjax(route('laravel-factura-mx.invoices.status', props.status.formData.invoice.id), props.status.formData.data())
    response.then(({data}) => {
        if (data) {
            props.status.response = data.pac_response
        }
    }).finally(() => {
        props.status.formData.processing = false
    })
}

function isNotEmpty(obj) {
    return Object.keys(obj).length !== 0
}
</script>
<template>
    <CompDialogModal :show="status.modal" @close="status.modal = false">
        <template #title>
            Mostrar el estatus de la factura electrónica
            <div>
                <template v-if="status.formData.invoice.invoice_cfdi">
                    {{ status.formData.invoice.invoice_cfdi.uuid }}
                </template>
                <template v-else>
                    No existe un UUID
                </template>
            </div>
        </template>
        <template #content>
            <template v-if="isNotEmpty(status.response)">
                <table class="lyt-default-listing-table">
                    <tbody>
                    <tr>
                        <td class="w-1/2 font-bold">Detalles validación EFOS</td>
                        <td class="w-1/2">{{ status.response.detallesValidacionEFOS }}</td>
                    </tr>
                    <tr>
                        <td class="w-1/2 font-bold">Es cancelable</td>
                        <td class="w-1/2">{{ status.response.esCancelable }}</td>
                    </tr>
                    <tr>
                        <td class="w-1/2 font-bold">Código Estatus</td>
                        <td class="w-1/2">{{ status.response.codigoEstatus }}</td>
                    </tr>
                    <tr>
                        <td class="w-1/2 font-bold">Estado</td>
                        <td class="w-1/2">{{ status.response.estado }}</td>
                    </tr>
                    <tr>
                        <td class="w-1/2 font-bold">Estatus cancelación</td>
                        <td class="w-1/2">{{ status.response.estatusCancelacion }}</td>
                    </tr>
                    </tbody>
                </table>
            </template>
        </template>
        <template #footer>
            <div class="flex items-center justify-end">
                <template v-if="isNotEmpty(status.response)">
                    <button type="button" class="lyt-button lyt-button-style-default mr-4"
                            @click="status.modal = false">
                        Cerrar
                    </button>
                </template>
                <template v-else>
                    <button type="button" class="lyt-button lyt-button-style-default mr-4"
                            @click="status.modal = false">
                        Cancelar
                    </button>
                    <CompButtonSubmit :is-loading="status.formData.processing"
                                      @click="onSubmitStatus()"
                                      class="lyt-button lyt-button-style-primary">
                        Obtener estatus
                    </CompButtonSubmit>
                </template>
            </div>
        </template>
    </CompDialogModal>
</template>
