export const register = () => {
    const register_form: HTMLFormElement = document.querySelector("#register_form");
    const button_register_form: HTMLButtonElement = document.querySelector("#button_register_form");

    if (register_form && button_register_form) {
        button_register_form.addEventListener("click", event => {
            event.preventDefault();

            console.log('register');
            const formData = new FormData(register_form);
            const response = fetch(
                'http://localhost:8000/register',
                {
                    method: "POST",
                    body: formData,
                    headers: {
                        contentType: "application/json"
                    }
                })
                .then(res => res.json())
                .catch(err => console.log(err));

            console.log(response);
        });
    }
}
