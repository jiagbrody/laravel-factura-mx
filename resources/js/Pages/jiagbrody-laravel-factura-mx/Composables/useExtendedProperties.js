import {computed} from "vue";

export function useExtendedProperties(props) {
    const getStyles = computed(() => {
        return {[props.getGlobalStylesForInputComponent]: true}
    })

    const isRequired = computed(() => {
        return (props.required !== null) ? '*' : '';
    })

    const renameStandardFormat = computed(() => {
        if (props.id) {
            return props.id;
        } else if (props.label) {
            return props.label.toLowerCase().replace(/[^\w ]+/g, '').replace(/ +/g, '-');
        }

        return Math.random().toString(20).substr(2, 6)
    })

    const getAutocomplete = computed(() => {
        return (props.autocomplete === true) ? 'on' : 'off'
    })

    return {
        getStyles,
        isRequired,
        renameStandardFormat,
        getAutocomplete
    }
}
