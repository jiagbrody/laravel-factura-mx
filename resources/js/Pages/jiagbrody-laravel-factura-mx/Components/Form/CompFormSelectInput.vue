<!-- DOCS:
<base-form-select-input label="mietiqueta"
                        v-model.number="valorEntero"
                        :error="errors[`varEtiqueta`]"
                        :options="cat_catalogos"/>

<base-form-select-input id="miid"
                        label="mietiqueta"
                        v-model.number="valorEntero"
                        :error="errors[`varEtiqueta`]"
                        :options="cat_catalogos"
                        key-value="miId"
                        :name-value="['vertexto1', 'vertexto2']"
                        :show-empty-first="false"
                        :disabled="true"
                        autocomplete="true"
                        place-holder="Texto default del campo"/>
                        -->
<script setup>
import {useFormInputProperties} from "@/Pages/jiagbrody-laravel-factura-mx/Composables/useFormatInputProperties.js";
import {useExtendedProperties} from "@/Pages/jiagbrody-laravel-factura-mx/Composables/useExtendedProperties.js";

const props = defineProps({
    modelValue: {
        type: [Number, String, null],
        default: null,
    },
    options: {
        default: {},
        type: Object
    },
    keyValue: {
        default: 'id',
        type: String,
    },
    nameValue: {
        default: 'name',
        type: [String, Array],
    },
    showEmptyFirst: {
        default: true,
        type: Boolean,
    },
    makeOptgroup: {
        type: Boolean,
        default: false,
    },
    ...useFormInputProperties()
})

const emit = defineEmits(['update:modelValue', 'onChange'])

const {
    getStyles,
    isRequired,
    renameStandardFormat,
    getAutocomplete
} = useExtendedProperties(props);

const displayOptionName = (option) => {
    if (Array.isArray(props.nameValue)) {
        let words = "";
        for (let i = 0; i < props.nameValue.length; i++) {
            if (i === 0) {
                words = option[props.nameValue[i]];
            } else {
                words = words.concat(' - ', option[props.nameValue[i]]);
            }
        }

        return words
    }

    return option[props.nameValue]
}

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
            <select
                :id="renameStandardFormat"
                :class="{...getStyles, 'border-red-400': error}"
                :disabled="disabled"
                :autocomplete="getAutocomplete"
                :value="modelValue"
                @input="$emit('update:modelValue', ($event.target.value) ? $event.target.value : null)"
                @change="$emit('onChange', ($event.target.value) ? $event.target.value : null)"
                :required="required">
                <option v-if="showEmptyFirst" value="">Seleccionar</option>
                <template v-if="makeOptgroup">
                    <optgroup v-for="(groups, label) in options" :label="label">
                        <option v-for="option in groups"
                                :key="option[keyValue]"
                                :value="option[keyValue]"
                                :disabled="disabled">
                            {{ displayOptionName(option) }}
                        </option>
                    </optgroup>
                </template>
                <template v-else>
                    <option v-for="option in options"
                            :key="option[keyValue]"
                            :value="option[keyValue]"
                            :disabled="disabled">
                        {{ displayOptionName(option) }}
                    </option>
                </template>
            </select>
        </div>
        <div v-if="error" class="text-sm text-red-500" :class="{'lyt-disabled': disabled}">{{ error }}</div>
    </div>
</template>
