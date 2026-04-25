import './bootstrap';
import './route-loader';
import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';
import { signInWithGoogle, firebaseSignOut, getIdToken } from './firebase';

Alpine.plugin(collapse);

window.Alpine = Alpine;

// Expose Firebase auth functions globally for Blade views
window.signInWithGoogle = signInWithGoogle;
window.firebaseSignOut = firebaseSignOut;
window.getFirebaseIdToken = getIdToken;

import comparisonPage from './components/comparison-page';

document.addEventListener('alpine:init', () => {
    // Theme Store
    Alpine.store('theme', {
        darkMode: localStorage.getItem('theme') === 'dark' || 
                 (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches),
        
        toggle() {
            this.darkMode = !this.darkMode;
            localStorage.setItem('theme', this.darkMode ? 'dark' : 'light');
        }
    });

    window.comparisonPage = comparisonPage;
    Alpine.data('comparisonPage', comparisonPage);

    Alpine.data('commentsManager', (fetchUrl, storeUrl, initialTotalComments) => ({
        sortBy: 'newest',
        commentsHtml: '',
        isLoading: false,
        isSubmitting: false,
        newCommentContent: '',
        errorMessage: '',
        totalComments: initialTotalComments,

        init() {
            // Initialize with server-rendered content
            this.commentsHtml = this.$refs.commentsContainer.innerHTML;
        },

        loadComments() {
            this.isLoading = true;
            const url = new URL(fetchUrl, window.location.origin);
            url.searchParams.set('sort', this.sortBy);

            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                this.commentsHtml = data.html;
                if (data.total_count !== undefined) {
                    this.totalComments = data.total_count;
                }
            })
            .catch(err => {
                console.error('Failed to load comments:', err);
            })
            .finally(() => {
                this.isLoading = false;
            });
        },

        submitRootComment() {
            if(!this.newCommentContent.trim()) return;
            
            this.isSubmitting = true;
            this.errorMessage = '';

            fetch(storeUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    content: this.newCommentContent
                })
            })
            .then(async (res) => {
                const data = await res.json();
                if (!res.ok) throw data;
                
                this.newCommentContent = '';
                this.loadComments();
            })
            .catch(err => {
                this.errorMessage = err.message || err.error || 'Failed to post comment. Please try again.';
                if(err.errors && err.errors.content) {
                    this.errorMessage = err.errors.content[0];
                }
            })
            .finally(() => {
                this.isSubmitting = false;
            });
        }
    }));
});

Alpine.start();

// Handle Firebase Google Sign-In
document.addEventListener('DOMContentLoaded', () => {
    const googleSignInBtn = document.getElementById('google-signin-btn');
    if (googleSignInBtn) {
        googleSignInBtn.addEventListener('click', async (e) => {
            e.preventDefault();
            googleSignInBtn.disabled = true;
            googleSignInBtn.innerHTML = '<span class="animate-spin inline-block mr-2">&#9696;</span> Signing in...';

            const result = await signInWithGoogle();
            if (result.success) {
                const response = await fetch('/auth/firebase/callback', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({ id_token: result.idToken }),
                });

                if (response.ok) {
                    window.location.href = '/';
                } else {
                    const data = await response.json();
                    alert(data.message || 'Authentication failed');
                    googleSignInBtn.disabled = false;
                    googleSignInBtn.innerHTML = 'Continue with Google';
                }
            } else {
                alert(result.error || 'Google sign-in failed');
                googleSignInBtn.disabled = false;
                googleSignInBtn.innerHTML = 'Continue with Google';
            }
        });
    }
});
