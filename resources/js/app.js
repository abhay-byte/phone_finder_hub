import './bootstrap';
import './route-loader';
import Alpine from 'alpinejs';

window.Alpine = Alpine;
import comparisonPage from './components/comparison-page';

document.addEventListener('alpine:init', () => {
    Alpine.data('comparisonPage', comparisonPage);
});

Alpine.start();
