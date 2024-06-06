<script setup>
import LaravelFacturaMxLayout from "@/Pages/jiagbrody-laravel-factura-mx/Layouts/LaravelFacturaMxLayout.vue";
import {router, Link} from "@inertiajs/vue3";

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

        <div class="flex gap-6">
            <Link :href="route('laravel-factura-mx.invoices.index')" class="lyt-button-sm lyt-button-style-primary">
                Todas las facturas
            </Link>

            <button @click="onCancel"
                    class="lyt-button-sm lyt-button-style-primary">
                Cancelar
            </button>
        </div>

        <div class="mt-6">
            {{ invoice }}
        </div>
    </LaravelFacturaMxLayout>
</template>
