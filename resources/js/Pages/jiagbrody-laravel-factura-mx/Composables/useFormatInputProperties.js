export function useFormInputProperties() {
    const state = {
        getGlobalStylesForInputComponent: {
            type: String,
            default: 'w-full'
        },
        sizeStyle: {
            type: String,
            default: 'md',
        },
        id: {
            type: String,
            default: '',
        },
        label: {
            type: String,
            default: null,
        },
        required: {
            type: String,
            default: null,
        },
        readonly: {
            type: Boolean,
            default: null,
        },
        disabled: {
            type: Boolean,
            default: false,
        },
        autocomplete: {
            type: Boolean,
            default: false,
        },
        placeHolder: {
            type: String,
            default: '',
        },
        error: {
            type: String,
            default: null,
        },
    }

    return {
        ...state
    }
}
