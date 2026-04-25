<div x-data="commentsManager('{{ route('phones.comments.index', $phone) }}', '{{ route('comments.store', $phone) }}', {{ $totalComments }})" 
     class="space-y-6" 
     @comment-added.window="loadComments"
     @comment-deleted.window="loadComments">
    
    <!-- Header & Sorting -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-gray-100 dark:border-white/5 pb-4 transition-colors duration-300">
        <h3 class="font-bold text-gray-900 dark:text-white flex items-center gap-2 transition-colors duration-300">
            Comments <span class="text-xs bg-gray-100 dark:bg-slate-800 px-2.5 py-0.5 rounded-full text-gray-600 dark:text-slate-400 font-medium transition-colors duration-300" x-text="totalComments">{{ $totalComments }}</span>
        </h3>
        
        <div class="flex items-center gap-2">
            <label class="text-sm text-gray-500 dark:text-slate-400 font-medium transition-colors duration-300">Sort by:</label>
            <div class="relative">
                <select x-model="sortBy" 
                        @change="loadComments"
                        class="appearance-none bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-white/10 text-gray-900 dark:text-white text-sm rounded-xl focus:ring-teal-500 focus:border-teal-500 block py-2.5 pl-3 pr-10 cursor-pointer font-medium hover:border-teal-500/50 transition-colors w-full">
                    <option value="top">Top Comments</option>
                    <option value="newest">Newest First</option>
                    <option value="oldest">Oldest First</option>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 dark:text-slate-400">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Root Comment Form -->
    <form @submit.prevent="submitRootComment" hx-boost="false" class="mb-8">
        <div class="relative">
            <textarea x-model="newCommentContent" rows="3" class="w-full bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-white/10 rounded-2xl p-4 focus:ring-2 focus:ring-teal-500/50 focus:border-teal-500 transition-all text-sm placeholder-gray-400 dark:placeholder-slate-500 text-gray-900 dark:text-white transition-colors duration-300" placeholder="What do you think about the {{ $phone->name }}?" required></textarea>
            <div class="absolute bottom-3 right-3 flex items-center gap-2">
                <span x-show="isSubmitting" class="text-xs text-gray-400 dark:text-slate-500 font-medium animate-pulse">Posting...</span>
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
        <div x-show="isLoading" class="absolute inset-0 flex items-center justify-center bg-white/50 dark:bg-slate-900/50 backdrop-blur-sm z-10 rounded-xl transition-colors duration-300" style="display: none;">
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
