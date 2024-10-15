import '../css/main.css';

// @ts-ignore
//import Turbolinks from 'turbolinks';

//import {register} from "./Resgiter";
//import { initFlowbite } from 'flowbite'
import {dropdown} from "./Dropdown";

window.addEventListener('DOMContentLoaded', () => {
    //initFlowbite();
});

// Modules ayant besoin d'être rechargés au changement de page (liens de navigation)
document.addEventListener('turbolinks:load', () => {
   dropdown();
    //register();
});

//Turbolinks.start();