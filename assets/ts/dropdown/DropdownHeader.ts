export const dropdownHeader = () => {
    const button_dropdown = document.querySelector('#js_dropdown_user_button');
    const dropdown_menu = document.querySelector('#js_dropdown_user_menu');

    if (button_dropdown) {
        button_dropdown.addEventListener('click', (e) => {
            e.preventDefault();

            if (dropdown_menu) {
                dropdown_menu.classList.toggle('hidden');
            }
        })
    }
}