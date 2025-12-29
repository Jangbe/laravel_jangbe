import { ref } from "vue";

const toastVisible = ref(false);
const toastTitle = ref('');
const toastMessage = ref('');
const toastType = ref<'success' | 'error'>('success');

export const useToast = () => {
    const showToast = (title: string, message: string, type: 'success' | 'error' = 'success') => {
        toastTitle.value = title;
        toastMessage.value = message;
        toastType.value = type;
        toastVisible.value = true;

        setTimeout(() => {
            hideToast();
        }, 5000);
    };

    const hideToast = () => {
        toastVisible.value = false;
    };

    return { showToast, hideToast, toastVisible, toastTitle, toastMessage, toastType }
}