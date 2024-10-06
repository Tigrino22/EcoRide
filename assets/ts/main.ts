import '../css/main.css';

// @ts-ignore
import Turbolinks from 'turbolinks';

import 'flowbite';
import { initFlowbite } from 'flowbite'


window.addEventListener('DOMContentLoaded', () => {
    initFlowbite();
});

Turbolinks.start();