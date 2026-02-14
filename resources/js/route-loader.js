document.addEventListener('DOMContentLoaded', () => {

    // Config
    const MAIN_CONTENT_ID = 'main-content';
    const LOADING_CLASS = 'opacity-50'; // Simple fade effect
    const TRANSITION_DURATION = 300; // ms

    const handleGlobalNavigation = async (url) => {
        if (url === window.location.href) return;

        const mainContent = document.getElementById(MAIN_CONTENT_ID);
        if (!mainContent) {
            window.location.href = url; // Fallback if wrapper missing
            return;
        }

        try {
            // 1. Start Transition (Fade Out)
            mainContent.style.transition = `opacity ${TRANSITION_DURATION}ms ease-out`;
            mainContent.style.opacity = '0';

            // Wait for fade out
            await new Promise(r => setTimeout(r, TRANSITION_DURATION));

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

            // 3. Extract New Content
            const newContent = doc.getElementById(MAIN_CONTENT_ID);
            if (!newContent) throw new Error('New content wrapper not found');

            // 4. Update DOM
            mainContent.innerHTML = newContent.innerHTML;
            document.title = doc.title;

            // 5. Update Navbar Active States (Simple implementation)
            updateNavbarState(url);

            // 6. Execute Scripts in New Content
            // Standard innerHTML doesn't run scripts. We must manually re-add them.
            executeScripts(mainContent);

            // 7. Push History
            window.history.pushState({}, '', url);

            // 8. End Transition (Fade In)
            // Small delay to ensure DOM render
            requestAnimationFrame(() => {
                mainContent.style.opacity = '1';
            });

        } catch (error) {
            console.error('Global Navigation Error:', error);
            window.location.href = url; // Robust fallback
        }
    };

    const updateNavbarState = (url) => {
        const navLinks = document.querySelectorAll('nav a');
        navLinks.forEach(link => {
            // Logic to highlight active link based on URL match
            // This depends on how your navbar styles 'active' state.
            // For now, simpler to just let the user see the page change.
            // Complex active state logic can be added here.
        });
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
        handleGlobalNavigation(href);
    });

    // Handle History (Back/Forward)
    window.addEventListener('popstate', () => {
        // We can either reload or try to fetch. 
        // Reload is safer for history traversal unless we cache states.
        // For "Robustness", fetch is better than reload if speedy.
        handleGlobalNavigation(window.location.href);
    });
});
