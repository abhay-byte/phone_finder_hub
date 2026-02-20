<div x-data="commentsManager('{{ route('phones.comments.index', $phone) }}', '{{ route('comments.store', $phone) }}')" 
     class="space-y-6" 
     @comment-added.window="loadComments"
     @comment-deleted.window="loadComments">
    
    <!-- Header & Sorting -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-gray-100 dark:border-white/5 pb-4">
        <h3 class="font-bold text-gray-900 dark:text-gray-100 flex items-center gap-2">
            Comments <span class="text-xs bg-gray-100 dark:bg-white/10 px-2.5 py-0.5 rounded-full text-gray-600 dark:text-gray-400 font-medium" x-text="totalComments">{{ $phone->comments()->count() }}</span>
        </h3>
        
        <div class="flex items-center gap-2">
            <label class="text-sm text-gray-500 dark:text-gray-400 font-medium">Sort by:</label>
            <select x-model="sortBy" 
                    @change="loadComments"
                    class="bg-gray-50 dark:bg-[#1a1a1a] border border-gray-200 dark:border-white/10 text-gray-900 dark:text-gray-100 text-sm rounded-xl focus:ring-teal-500 focus:border-teal-500 block py-2.5 pl-3 pr-8 cursor-pointer font-medium hover:border-teal-500/50 transition-colors">
                <option value="top">Top Comments</option>
                <option value="newest">Newest First</option>
                <option value="oldest">Oldest First</option>
            </select>
        </div>
    </div>

    <!-- Root Comment Form -->
    <form @submit.prevent="submitRootComment" class="mb-8">
        <div class="relative">
            <textarea x-model="newCommentContent" rows="3" class="w-full bg-gray-50 dark:bg-black/50 border border-gray-200 dark:border-white/10 rounded-2xl p-4 focus:ring-2 focus:ring-teal-500/50 focus:border-teal-500 dark:focus:ring-teal-400/50 dark:focus:border-teal-400 transition-all text-sm placeholder-gray-400 dark:placeholder-gray-500 text-gray-900 dark:text-gray-100" placeholder="What do you think about the {{ $phone->name }}?" required></textarea>
            <div class="absolute bottom-3 right-3 flex items-center gap-2">
                <span x-show="isSubmitting" class="text-xs text-gray-400 font-medium animate-pulse">Posting...</span>
                <button type="submit" 
                        :disabled="isSubmitting || !newCommentContent.trim()"
                        class="bg-teal-600 disabled:opacity-50 disabled:cursor-not-allowed hover:bg-teal-700 text-white px-5 py-2 rounded-xl text-sm font-bold shadow-lg shadow-teal-500/20 transition-all cursor-pointer">
                    Post Comment
                </button>
            </div>
        </div>
        <p x-show="errorMessage" x-text="errorMessage" x-cloak class="text-red-500 text-xs mt-2 font-medium"></p>
    </form>

    <!-- Comments List Area -->
    <div class="relative min-h-[100px]">
        <!-- Loading State -->
        <div x-show="isLoading" class="absolute inset-0 flex items-center justify-center bg-white/50 dark:bg-[#121212]/50 backdrop-blur-sm z-10 rounded-xl" style="display: none;">
            <svg class="animate-spin -ml-1 mr-3 h-8 w-8 text-teal-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </div>

        <!-- Injected Comments HTML -->
        <div x-ref="commentsContainer" x-html="commentsHtml" class="transition-opacity duration-300" :class="isLoading ? 'opacity-30' : 'opacity-100'">
            <!-- Initial content loaded server-side for SEO & quick display -->
            @include('partials.comments-list-ajax', ['comments' => $comments, 'phone' => $phone])
        </div>
    </div>
</div>

@push('scripts')
<script>
    const initCommentsManager = () => {
        // Prevent double-registration if possible, though Alpine.data usually overwrites cleanly
        Alpine.data('commentsManager', (fetchUrl, storeUrl) => ({
            sortBy: 'newest',
            commentsHtml: '',
            isLoading: false,
            isSubmitting: false,
            newCommentContent: '',
            errorMessage: '',
            totalComments: {{ $phone->comments()->count() }},

            init() {
                // Initialize with server-rendered content, don't fetch immediately unless sorting
                this.commentsHtml = this.$refs.commentsContainer.innerHTML;
            },

            loadComments() {
                this.isLoading = true;
                
                // Keep the URL updated for shareability/refreshing if wanted, though we load via AJAX
                const url = new URL(fetchUrl, window.location.origin);
                url.searchParams.set('sort', this.sortBy);

                fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest', // Important: tells Laravel wantsJson() is true
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
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        content: this.newCommentContent
                    })
                })
                .then(async (res) => {
                    const data = await res.json();
                    if (!res.ok) throw data;
                    
                    // Success
                    this.newCommentContent = '';
                    this.loadComments(); // Refresh list to show new comment and apply current sort
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
    };

    if (window.Alpine) {
        initCommentsManager();
    } else {
        document.addEventListener('alpine:init', initCommentsManager);
    }
</script>
@endpush
