import './bootstrap';

import Alpine from 'alpinejs';
import Collapse from '@alpinejs/collapse'; // Import the Collapse plugin
import { Livewire } from '../../vendor/livewire/livewire/dist/livewire.esm';

Alpine.plugin(Collapse); // Register the Collapse plugin
Livewire.start();

import './dashboard';
import './components/profile-image'; // Import the new profile-image component
