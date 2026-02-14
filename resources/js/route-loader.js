document.addEventListener('DOMContentLoaded', () => {

    // Config
    const LOADING_CLASS = 'opacity-50'; // Simple fade effect
    const TRANSITION_DURATION = 300; // ms

    // Force manual scroll restoration to prevent browser interference
    if ('scrollRestoration' in history) {
        history.scrollRestoration = 'manual';
    }

    const handleGlobalNavigation = async (url, pushState = true) => {
        // If pushing state, don't navigate to same URL unless it's a hash change
        if (pushState && url === window.location.href) return;

        try {
            // 1. Start Transition (Fade Out Body)
            document.body.style.transition = `opacity ${TRANSITION_DURATION}ms ease-out`;
            document.body.style.opacity = '0';

            // Wait for fade out
            await new Promise(r => setTimeout(r, TRANSITION_DURATION));

            // Scroll to top
            window.scrollTo(0, 0);

            // 2. Fetch Content
            const response = await fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!response.ok) throw new Error('Network response was not ok');

            const text = await response.text();
            const parser = new DOMParser();
            const doc = parser.parseFromString(text, 'text/html');

            // 3. Update Body Class (for layout differences)
            document.body.className = doc.body.className;

            // 4. Swap Body Content
            // This is a drastic but effective way to ensure everything (nav, footer, scripts) matches the new page
            document.body.innerHTML = doc.body.innerHTML;
            document.title = doc.title;

            // 5. Re-initialize Alpine.js
            // Laravel Breeze/Jetstream uses Alpine.js. We need to tell it to rescan the DOM.
            if (window.Alpine) {
                // Alpine V3
                window.Alpine.initTree(document.body);
            }

            // 6. Execute Scripts (if any inline scripts exist)
            executeScripts(document.body);

            // 7. Push History
            if (pushState) {
                window.history.pushState({}, '', url);
            }

            // 8. End Transition (Fade In)
            // Restore transition property to ensure fade in works, then clear it
            requestAnimationFrame(() => {
                document.body.style.opacity = '1';
                setTimeout(() => {
                    document.body.style.transition = '';
                }, TRANSITION_DURATION);
            });

        } catch (error) {
            console.error('Global Navigation Error:', error);
            window.location.href = url; // Robust fallback to full reload
        }
    };

    const executeScripts = (element) => {
        const scripts = element.querySelectorAll('script');
        scripts.forEach(oldScript => {
            const newScript = document.createElement('script');
            Array.from(oldScript.attributes).forEach(attr => newScript.setAttribute(attr.name, attr.value));
            newScript.appendChild(document.createTextNode(oldScript.innerHTML));
            oldScript.parentNode.replaceChild(newScript, oldScript);
        });
    };

    // Attach Click Listeners
    document.addEventListener('click', (e) => {
        const link = e.target.closest('a');
        if (!link) return;

        // Ignore external links, anchors, or special targets
        if (link.target === '_blank' ||
            link.hasAttribute('download') ||
            link.getAttribute('href').startsWith('#') ||
            link.getAttribute('href').startsWith('javascript:')) {
            return;
        }

        const href = link.href;
        // Ensure same origin
        if (new URL(href).origin !== window.location.origin) return;

        // Check if it's a "data-no-ajax" link (escape hatch)
        if (link.hasAttribute('data-no-ajax')) return;

        e.preventDefault();
        handleGlobalNavigation(href, true);
    });

    // Handle History (Back/Forward)
    window.addEventListener('popstate', () => {
        // Handle popstate navigation without pushing new state
        handleGlobalNavigation(window.location.href, false);
    });
});
