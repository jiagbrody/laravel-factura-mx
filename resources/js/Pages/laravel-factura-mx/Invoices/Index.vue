<script setup>
import LaravelFacturaMxLayout from "@/Pages/laravel-factura-mx/Layouts/LaravelFacturaMxLayout.vue";

defineProps({
    invoices: Object
})
</script>

<template>
    <LaravelFacturaMxLayout title="Dashboard">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Dashboard
            </h2>
        </template>

        <div v-for="invoice in invoices">
            <div> {{ invoice }}</div>
        </div>
    </LaravelFacturaMxLayout>
</template>
