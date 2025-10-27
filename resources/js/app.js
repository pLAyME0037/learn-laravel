import './bootstrap';

import Alpine from 'alpinejs';
import Collapse from '@alpinejs/collapse'; // Import the Collapse plugin

window.Alpine = Alpine;

Alpine.plugin(Collapse); // Register the Collapse plugin

Alpine.start();

import './dashboard';
import './components/profile-image'; // Import the new profile-image component
