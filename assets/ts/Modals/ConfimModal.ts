export const confimModal = ()  => {

    const modal = document.querySelector('#confirmationModal');
    const confirmButton = document.querySelector('#confirmButton');
    const cancelButton = document.querySelector('#cancelButton');
    let formToSubmit;

    function showModal(form) {
        modal.classList.add('flex');
        modal.classList.remove('hidden');
        formToSubmit = form;
    }

    function hideModal() {
        modal.classList.remove('flex');
        modal.classList.add('hidden');
        formToSubmit = null;
    }

    cancelButton.addEventListener('click', (event) => {
        event.preventDefault();
        hideModal();
    });

    confirmButton.addEventListener('click', (event) => {
        event.preventDefault();
        if (formToSubmit) formToSubmit.submit();
        hideModal();

    });

    // Intercept le click des button ayant besoin d'une cofirmation
    document.querySelectorAll('.need-confirmation').forEach( button => {
        button.addEventListener('click', (event) => {
            event.preventDefault();
            const form = button.closest('form');
            showModal(form);
        });
    });
}