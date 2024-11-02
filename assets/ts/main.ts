import '../css/main.css';

import { initFlowbite } from 'flowbite'
import {bootDropdown} from "./dropdown/Dropdown";

window.addEventListener('DOMContentLoaded', () => {
    initFlowbite();
    bootDropdown()
});