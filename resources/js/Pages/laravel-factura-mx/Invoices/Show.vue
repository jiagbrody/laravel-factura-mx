<script setup>
import LaravelFacturaMxLayout from "@/Pages/laravel-factura-mx/Layouts/LaravelFacturaMxLayout.vue";
import {router} from "@inertiajs/vue3";

const props = defineProps({
    invoice: Object
})

const onCancel = () => {
    router.delete(route('laravel-factura-mx.invoices.destroy', props.invoice.id ))
}
</script>

<template>
    <LaravelFacturaMxLayout title="Dashboard">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Facturas
            </h2>
        </template>

        <button @click="onCancel"
                class="lyt-button-sm lyt-button-style-primary">
            Cancelar
        </button>

        {{ invoice }}
    </LaravelFacturaMxLayout>
</template>
