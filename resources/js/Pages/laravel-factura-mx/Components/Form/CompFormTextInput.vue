<script setup>
import {useFormInputProperties} from "@/Pages/laravel-factura-mx/Composables/useFormatInputProperties.js";
import {useExtendedProperties} from "@/Pages/laravel-factura-mx/Composables/useExtendedProperties.js";

const props = defineProps({
    modelValue: {
        type: String,
        default: null,
    },
    minlength: {
        type: String,
        default: null,
    },
    maxlength: {
        type: String,
        default: null,
    },
    ...useFormInputProperties()
})

const {
    getStyles,
    isRequired,
    renameStandardFormat,
    getAutocomplete
} = useExtendedProperties(props);

</script>
<template>
    <div class="w-full">
        <label v-if="label"
               class="block text-gray-700 text-sm font-bold mb-2"
               :class="{ 'text-red-400': error, 'lyt-disabled': disabled, 'lyt-readonly': readonly }"
               :for="renameStandardFormat">
            {{ label }} <span class="text-red-500 font-bold">{{ isRequired }}</span>
        </label>
        <div class="flex w-full">
            <input type="text"
                   :id="renameStandardFormat"
                   :class="{...getStyles, 'border-red-400': error}"
                   :minlength="minlength"
                   :maxlength="maxlength"
                   :placeholder="placeHolder"
                   :disabled="disabled"
                   :autocomplete="getAutocomplete"
                   :value="modelValue"
                   @input="$emit('update:modelValue', $event.target.value)"
                   :required="required"
                   :readonly="readonly"
            />
        </div>
        <div v-if="error" class="text-sm text-red-500" :class="{'lyt-disabled': disabled}">{{ error }}</div>
    </div>
</template>
