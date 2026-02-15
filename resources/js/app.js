import './bootstrap';
import './route-loader';
import Alpine from 'alpinejs';

window.Alpine = Alpine;
import comparisonPage from './components/comparison-page';

document.addEventListener('alpine:init', () => {
    window.comparisonPage = comparisonPage; // Expose to window for inline usage if needed
    Alpine.data('comparisonPage', comparisonPage);
});

Alpine.start();
