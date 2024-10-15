export const dropdown = () => {
    const button_dropdown = document.querySelector('#js_dropdown_user_button');
    const dropdown_menu = document.querySelector('#js_dropdown_user_menu');

    if (button_dropdown) {
        button_dropdown.addEventListener('click', (e) => {
            e.preventDefault();

            if (dropdown_menu) {
                console.log('click');
                dropdown_menu.classList.toggle('hidden');
                dropdown_menu.classList.toggle('translate-y-0');
                dropdown_menu.classList.toggle('opacity-100');
                dropdown_menu.classList.toggle('h-auto');
            }
        })
    }
}