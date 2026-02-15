document.addEventListener('DOMContentLoaded', () => {

    // Config
    const LOADING_CLASS = 'opacity-50'; // Simple fade effect
    const TRANSITION_DURATION = 300; // ms

    // Force manual scroll restoration to prevent browser interference
    if ('scrollRestoration' in history) {
        history.scrollRestoration = 'manual';
    }

    // Create or get loading overlay
    const getLoadingOverlay = () => {
        let overlay = document.getElementById('page-loading-overlay');
        if (!overlay) {
            overlay = document.createElement('div');
            overlay.id = 'page-loading-overlay';
            const isDark = document.documentElement.classList.contains('dark');
            const logoUrl = '/assets/logo.png';
            
            overlay.innerHTML = `
                <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 1.5rem;">
                    <div style="position: relative;">
                        <div style="width: 80px; height: 80px; display: flex; align-items: center; justify-content: center; background: ${isDark ? 'rgba(26, 26, 26, 0.95)' : 'rgba(255, 255, 255, 0.95)'}; border-radius: 50%; box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1); border: 2px solid ${isDark ? 'rgba(255, 255, 255, 0.1)' : 'rgba(229, 231, 235, 0.5)'};">
                            <img src="${logoUrl}" alt="Loading" style="width: 56px; height: 56px; object-fit: contain;">
                        </div>
                        <svg class="spinner-circle" style="position: absolute; top: -8px; left: -8px; width: 96px; height: 96px;" viewBox="0 0 50 50">
                            <circle cx="25" cy="25" r="20" fill="none" stroke="#14b8a6" stroke-width="3" stroke-dasharray="31.4 31.4" stroke-linecap="round"></circle>
                        </svg>
                    </div>
                    <div style="text-align: center;">
                        <p style="font-size: 1rem; font-weight: 600; color: ${isDark ? '#ffffff' : '#111827'}; margin-bottom: 0.25rem;">PhoneFinderHub</p>
                        <p style="font-size: 0.875rem; font-weight: 500; color: ${isDark ? '#9ca3af' : '#6b7280'};">Loading...</p>
                    </div>
                </div>
            `;
            overlay.style.cssText = `
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: ${isDark ? 'rgba(17, 24, 39, 0.85)' : 'rgba(249, 250, 251, 0.85)'};
                backdrop-filter: blur(16px);
                -webkit-backdrop-filter: blur(16px);
                display: flex;
                align-items: center;
                justify-content: center;
                z-index: 9999;
                opacity: 0;
                pointer-events: none;
                transition: opacity 0.2s ease;
            `;
            
            document.body.appendChild(overlay);
        }
        return overlay;
    };

    const showLoadingOverlay = () => {
        const overlay = getLoadingOverlay();
        overlay.style.pointerEvents = 'auto';
        requestAnimationFrame(() => {
            overlay.style.opacity = '1';
        });
    };

    const hideLoadingOverlay = () => {
        const overlay = document.getElementById('page-loading-overlay');
        if (overlay) {
            overlay.style.opacity = '0';
            setTimeout(() => {
                overlay.style.pointerEvents = 'none';
            }, 200);
        }
    };

    const handleGlobalNavigation = async (url, pushState = true) => {
        // If pushing state, don't navigate to same URL unless it's a hash change
        if (pushState && url === window.location.href) return;

        try {
            // 1. Show loading overlay
            showLoadingOverlay();

            // 2. Start Transition (No delay, immediate load)
            document.body.style.transition = `opacity 150ms ease-out`;
            document.body.style.opacity = '0.7';

            // 2. Fetch Content (parallel with fade)
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

            // Preserve the loading overlay before body swap
            const loadingOverlay = document.getElementById('page-loading-overlay');

            // 4. Swap Body Content
            document.body.innerHTML = doc.body.innerHTML;
            document.title = doc.title;

            // Re-append loading overlay after body swap
            if (loadingOverlay) {
                document.body.appendChild(loadingOverlay);
            }

            // Scroll to top after content is loaded
            window.scrollTo(0, 0);

            // 5. Re-initialize Alpine.js
            if (window.Alpine) {
                window.Alpine.initTree(document.body);
            }

            // 6. Execute Scripts and Styles
            executeScripts(document.body);
            executeStyles(document.body);

            // 7. Lazy load images
            lazyLoadImages();

            // 8. Push History
            if (pushState) {
                window.history.pushState({}, '', url);
            }

            // 9. End Transition - show content immediately
            document.body.style.opacity = '1';
            document.body.style.transition = '';
            
            // Hide overlay after content is visible
            requestAnimationFrame(() => {
                hideLoadingOverlay();
            });

        } catch (error) {
            console.error('Global Navigation Error:', error);
            hideLoadingOverlay();
            // Show a brief error message to user
            const errorDiv = document.createElement('div');
            errorDiv.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                background: #ef4444;
                color: white;
                padding: 1rem 1.5rem;
                border-radius: 0.5rem;
                box-shadow: 0 10px 25px rgba(0,0,0,0.2);
                z-index: 10000;
                font-size: 0.875rem;
            `;
            errorDiv.textContent = 'Navigation failed, reloading...';
            document.body.appendChild(errorDiv);
            
            setTimeout(() => {
                window.location.href = url; // Robust fallback to full reload
            }, 1000);
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

    const executeStyles = (element) => {
        const styles = element.querySelectorAll('style');
        styles.forEach(oldStyle => {
            const newStyle = document.createElement('style');
            Array.from(oldStyle.attributes).forEach(attr => newStyle.setAttribute(attr.name, attr.value));
            newStyle.appendChild(document.createTextNode(oldStyle.innerHTML));
            oldStyle.parentNode.replaceChild(newStyle, oldStyle);
        });
    };

    const lazyLoadImages = () => {
        const images = document.querySelectorAll('img[src]');
        
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    if (img.dataset.src) {
                        img.src = img.dataset.src;
                        delete img.dataset.src;
                    }
                    img.classList.add('loaded');
                    observer.unobserve(img);
                }
            });
        }, {
            rootMargin: '50px'
        });

        images.forEach(img => {
            // Add loading attribute for native lazy loading
            if (!img.hasAttribute('loading')) {
                img.setAttribute('loading', 'lazy');
            }
            imageObserver.observe(img);
        });
    };

    // Attach Click Listeners
    document.addEventListener('click', (e) => {
        const link = e.target.closest('a');
        if (!link) return;

        const href = link.getAttribute('href');
        if (!href) return;

        // Ignore external links, anchors, or special targets
        if (link.target === '_blank' ||
            link.hasAttribute('download') ||
            href.startsWith('#') ||
            href.startsWith('javascript:') ||
            href.startsWith('mailto:') ||
            href.startsWith('tel:')) {
            return;
        }

        // Ensure same origin
        try {
            const url = new URL(href, window.location.origin);
            if (url.origin !== window.location.origin) return;
            
            // Check if it's a "data-no-ajax" link (escape hatch)
            if (link.hasAttribute('data-no-ajax')) return;

            e.preventDefault();
            handleGlobalNavigation(url.href, true);
        } catch (err) {
            // If URL parsing fails, let it navigate normally
            console.warn('Failed to parse URL:', href, err);
            return;
        }
    });

    // Handle History (Back/Forward)
    window.addEventListener('popstate', () => {
        // Handle popstate navigation without pushing new state
        handleGlobalNavigation(window.location.href, false);
    });

    // Initialize lazy loading on first page load
    lazyLoadImages();
});
