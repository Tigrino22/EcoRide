import '../css/main.css';

import { initFlowbite } from 'flowbite'
import {bootDropdown} from "./dropdown/Dropdown";
import {confimModal} from "./Modals/ConfimModal";

window.addEventListener('DOMContentLoaded', () => {
    initFlowbite();
    bootDropdown();
    confimModal();
});