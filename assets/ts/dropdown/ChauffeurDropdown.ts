export const chauffeurDropdown = () => {
    const $target: HTMLInputElement | null = document.querySelector('#js_toggle_driver');
    const $trigger: HTMLDivElement | null = document.querySelector('#chauffeur_dropdown');

    if ($target.checked) {

        $trigger.classList.remove('hidden');

        setTimeout(() => {
            if ($target.checked) {
                $trigger.classList.remove('scale-y-0', 'opacity-0');
                $trigger.classList.add('scale-y-100', 'opacity-100');
            }
        }, 300);

    } else {
        $trigger.classList.remove('scale-y-100', 'opacity-100');
        $trigger.classList.add('scale-y-0', 'opacity-0');

        setTimeout(() => {
            if (!$target.checked) {
                $trigger.classList.add('hidden');
            }
        }, 300);
    }
};
