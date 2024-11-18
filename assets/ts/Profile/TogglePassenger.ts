import {CookieManager} from "../Services/CookieManager";

export const togglePassenger = () => {
    const toggle_driver_button: HTMLInputElement = document.querySelector('#js_toggle_passenger');

    const id = CookieManager.get('user_id');

    toggle_driver_button.addEventListener('change', function () {
        fetch(`/profile/toggle-passenger/${id}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            }
        })
            .then(response => {
                console.log(response);
                if (response.ok) {
                    return response.json();
                }
            })
            .then(data => {
                console.log(data);
            })
            .catch(error => console.error('Error:', error));
    });
}