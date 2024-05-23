<script setup>
import LaravelFacturaMxLayout from "@/Pages/laravel-factura-mx/Layouts/LaravelFacturaMxLayout.vue";
import {router} from "@inertiajs/vue3";

defineProps({
    invoices: Object
})

const onCancel = (id) => {
    router.delete(route('laravel-factura-mx.invoices.destroy', id))
}

const onStatus = (id) => {
    router.post(route('laravel-factura-mx.invoices.status', id), {}, {
        preserveScroll: true,
        onSuccess: (page, ok, zaz) => {
            console.log(page)
            console.log(ok)
            console.log(zaz)
        }
    })
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
                <th>Empresa<br>a facturar</th>
                <th>Estatus</th>
                <th class="space_actions"></th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="invoice in invoices">
                <th class="space_id">{{ invoice.id }}</th>
                <td class="space_date">{{ invoice.created_at }}</td>
                <td>{{ invoice.invoice_type.name }}</td>
                <td>{{ invoice.invoice_company.name }}</td>
                <td>{{ invoice.invoice_status.name }}</td>
                <td class="space_actions">
                    <div class="flex gap-2">
                        <a :href="route('laravel-factura-mx.invoices.show', invoice.id )"
                           class="lyt-button-sm lyt-button-style-primary">
                            Mostrar
                        </a>
                        <button @click="onStatus(invoice.id)"
                                type="button"
                                class="lyt-button-sm lyt-button-style-info">
                            Checar estatus
                        </button>
                        <button @click="onCancel(invoice.id)"
                                type="button"
                                class="lyt-button-sm lyt-button-style-danger">
                            Cancelar
                        </button>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
    </LaravelFacturaMxLayout>
</template>
