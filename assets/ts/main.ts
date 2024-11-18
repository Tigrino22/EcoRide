import '../css/main.css';

import { initFlowbite } from 'flowbite'
import {bootDropdown} from "./dropdown/Dropdown";
import {confimModal} from "./Modals/ConfimModal";
import {toggleDriver} from "./Profile/ToggleDriver";
import {togglePassenger} from "./Profile/TogglePassenger";

window.addEventListener('DOMContentLoaded', () => {
    initFlowbite();
    bootDropdown();
    confimModal();

    // API
    toggleDriver();
    togglePassenger();
});