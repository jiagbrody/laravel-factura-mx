<script setup>
import LaravelFacturaMxLayout from "@/Pages/jiagbrody-laravel-factura-mx/Layouts/LaravelFacturaMxLayout.vue";
import {useForm} from "@inertiajs/vue3";
import {reactive} from "vue";
import IncludeStatusModal from "@/Pages/jiagbrody-laravel-factura-mx/Invoices/Includes/IncludeStatusModal.vue";
import IncludeCancelModal from "@/Pages/jiagbrody-laravel-factura-mx/Invoices/Includes/IncludeCancelModal.vue";

defineProps({
    invoices: Object,
    cat_invoice_cfdi_cancel_types: Object,
})

const state = reactive({
    status: {
        formData: useForm({
            invoice: {}
        }),
        response: {},
    },
    cancel: {
        formData: useForm({
            invoice: {},
            invoice_id: null,
            invoice_cfdi_cancel_type_id: null,
            uuid: null
        }),
        response: {},
    }
})

function onOpenModalStatus(invoice) {
    state.status.response = {}
    state.status.formData.invoice = invoice;
    state.status.modal = true;
}

function onOpenModalCancel(invoice) {
    state.cancel.response = {}
    state.cancel.formData.invoice = invoice;
    state.cancel.formData.invoice_id = invoice.id;
    state.cancel.modal = true;
}
</script>
<template>
    <LaravelFacturaMxLayout title="Dashboard">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Facturas
            </h2>
        </template>

        <table class="lyt-default-listing-table">
            <thead>
            <tr>
                <th class="space_id">#</th>
                <th class="space_date">Fecha</th>
                <th>Tipo</th>
                <th>UUID</th>
                <th>Empresa<br>a facturar</th>
                <th>Estatus</th>
                <th class="space_actions"></th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="invoice in invoices">
                <th class="space_id">{{ invoice.id }}</th>
                <td class="space_date">
                    <div class="text-sm">{{ invoice.created_at_format }}</div>
                    <div class="text-gray-400 text-xs leading-none">{{ invoice.created_at_human }}</div>
                </td>
                <td>{{ invoice.invoice_type.name }}</td>
                <td>
                    <span v-if="invoice.invoice_cfdi" class="text-sm">{{ invoice.invoice_cfdi.uuid }}</span>
                </td>
                <td>{{ invoice.invoice_company.name }}</td>
                <td>{{ invoice.invoice_status.name }}</td>
                <td class="space_actions">
                    <div class="flex gap-2">
                        <a :href="route('laravel-factura-mx.invoices.show', invoice.id )"
                           class="lyt-button-sm lyt-button-style-primary">
                            Mostrar
                        </a>
                        <button @click="onOpenModalStatus(invoice)"
                                type="button"
                                class="lyt-button-sm lyt-button-style-info">
                            Checar estatus
                        </button>
                        <button @click="onOpenModalCancel(invoice)"
                                type="button"
                                class="lyt-button-sm lyt-button-style-danger">
                            Cancelar
                        </button>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>


        <IncludeCancelModal :cancel-data="state.cancel" :cat-invoice-cfdi-cancel-types="cat_invoice_cfdi_cancel_types"/>

        <IncludeStatusModal :status="state.status"/>


    </LaravelFacturaMxLayout>
</template>
