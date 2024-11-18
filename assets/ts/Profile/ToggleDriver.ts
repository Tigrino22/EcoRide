import {CookieManager} from "../Services/CookieManager";
import {chauffeurDropdown} from "../dropdown/ChauffeurDropdown";

export const toggleDriver = () => {
    const toggle_driver_button: HTMLInputElement = document.querySelector('#js_toggle_driver');

    const id = CookieManager.get('user_id');

    toggle_driver_button.addEventListener('change', function () {
        fetch(`/profile/toggle-driver/${id}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            }
        })
            .then(response => {
                console.log(response);
                if (response.ok) {
                    // Affichage du dropdown
                    chauffeurDropdown();
                }
            })
            .catch(error => console.error('Error:', error));
    });
}