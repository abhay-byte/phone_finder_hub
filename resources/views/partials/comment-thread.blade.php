<div x-data="{ 
        replying: false, 
        editing: false,
        isSubmittingReply: false,
        isSubmittingEdit: false,
        replyContent: '',
        editContent: {{ json_encode($comment->content) }},
        errorMessage: '',
        upvoted: {{ Auth::check() && $comment->upvotes()->where('user_id', Auth::id())->exists() ? 'true' : 'false' }},
        upvotesCount: {{ $comment->upvotes_count }},
        toggleUpvote() {
            @if(Auth::check())
                fetch('{{ route('comments.upvote.toggle', $comment) }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content'),
                        'Accept': 'application/json',
                    }
                })
                .then(res => res.json())
                .then(data => {
                    this.upvoted = (data.status === 'added');
                    this.upvotesCount = data.upvotes_count;
                })
                .catch(err => console.error(err));
            @else
                window.location.href = '{{ route('login') }}';
            @endif
        },
        submitEdit() {
            if (!this.editContent.trim()) return;
            this.isSubmittingEdit = true;
            this.errorMessage = '';
            fetch('{{ route('comments.update', $comment) }}', {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ content: this.editContent })
            })
            .then(res => {
                if (!res.ok) throw res;
                return res.json();
            })
            .then(() => {
                this.editing = false;
                window.dispatchEvent(new CustomEvent('comment-added'));
            })
            .catch(err => {
                this.errorMessage = 'Failed to update. Please try again.';
            })
            .finally(() => this.isSubmittingEdit = false);
        },
        submitDelete() {
            if (!confirm('Delete this comment?')) return;
            fetch('{{ route('comments.destroy', $comment) }}', {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(res => {
                if (!res.ok) throw res;
                window.dispatchEvent(new CustomEvent('comment-deleted'));
            })
            .catch(err => console.error(err));
        },
        submitReply() {
            if (!this.replyContent.trim()) return;
            this.isSubmittingReply = true;
            this.errorMessage = '';
            fetch('{{ route('comments.store', $phone) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ 
                    content: this.replyContent,
                    parent_id: {{ $comment->id }}
                })
            })
            .then(res => {
                if (!res.ok) throw res;
                return res.json();
            })
            .then(() => {
                this.replying = false;
                this.replyContent = '';
                window.dispatchEvent(new CustomEvent('comment-added'));
            })
            .catch(err => {
                this.errorMessage = 'Failed to post reply.';
            })
            .finally(() => this.isSubmittingReply = false);
        }
     }" 
     class="flex gap-4 {{ $comment->parent_id ? 'mt-4' : 'mt-6 pt-6 border-t border-gray-100 dark:border-white/5' }}">
    
    <!-- Avatar -->
    <div class="flex-shrink-0">
        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-teal-400 to-emerald-600 flex items-center justify-center text-white font-bold text-lg shadow-inner">
            {{ strtoupper(substr($comment->author_name, 0, 1)) }}
        </div>
    </div>

    <!-- Content Area -->
    <div class="flex-1 min-w-0">
        <div class="bg-gray-50 dark:bg-[#1a1a1a] rounded-2xl rounded-tl-none p-4 border border-gray-100 dark:border-white/5 relative group">
            <div class="flex items-baseline gap-2 mb-1">
                <span class="font-bold text-gray-900 dark:text-gray-100">{{ $comment->author_name }}</span>
                @if($comment->user && $comment->user->isSuperAdmin())
                    <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-teal-100 dark:bg-teal-900/30 text-teal-700 dark:text-teal-400 uppercase tracking-widest">Admin</span>
                @endif
                <span class="text-xs text-gray-400" title="{{ $comment->created_at->format('M d, Y h:i A') }}">
                    {{ $comment->created_at->diffForHumans() }}
                </span>
                @if($comment->created_at != $comment->updated_at)
                    <span class="text-[10px] text-gray-400 italic">(edited)</span>
                @endif
            </div>

            <!-- View Mode -->
            <div x-show="!editing">
                <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap leading-relaxed">{{ $comment->content }}</p>
            </div>

            <!-- Edit Mode -->
            @if(Auth::check() && Auth::id() === $comment->user_id && $comment->user_id !== null)
                <div x-show="editing" x-cloak class="mt-2">
                    <form @submit.prevent="submitEdit" hx-boost="false">
                        <textarea x-model="editContent" rows="2" class="w-full bg-white dark:bg-black/50 border border-teal-200 dark:border-teal-500/30 rounded-xl p-3 focus:ring-2 focus:ring-teal-500/50 focus:border-teal-500 transition-all text-sm text-gray-900 dark:text-gray-100 mb-2" required></textarea>
                        <p x-show="errorMessage" x-text="errorMessage" class="text-xs text-red-500 mb-2"></p>
                        <div class="flex gap-2 justify-end items-center">
                            <span x-show="isSubmittingEdit" class="text-xs text-gray-400 animate-pulse">Saving...</span>
                            <button type="button" @click="editing = false" class="px-3 py-1.5 text-xs font-semibold text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors">Cancel</button>
                            <button type="submit" :disabled="isSubmittingEdit" class="bg-teal-600 hover:bg-teal-700 disabled:opacity-50 text-white px-4 py-1.5 rounded-lg text-xs font-bold shadow-md shadow-teal-500/20 transition-all">Save Edit</button>
                        </div>
                    </form>
                </div>
            @endif

            <!-- Quick Actions (Hover visible on desktop) -->
            @auth
                @if((Auth::id() === $comment->user_id && $comment->user_id !== null) || Auth::user()->isSuperAdmin())
                    <div class="absolute top-3 right-3 opacity-0 group-hover:opacity-100 transition-opacity flex items-center gap-1 bg-gray-50/90 dark:bg-[#1a1a1a]/90 backdrop-blur-sm px-2 py-1 rounded-lg border border-gray-200 dark:border-white/10 shadow-sm" x-show="!editing">
                        @if(Auth::id() === $comment->user_id && $comment->user_id !== null)
                            <button @click="editing = true" class="p-1 text-gray-400 hover:text-blue-500 transition-colors" title="Edit">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                            </button>
                        @endif
                        <form @submit.prevent="submitDelete" hx-boost="false" class="inline">
                            <button type="submit" class="p-1 text-gray-400 hover:text-red-500 transition-colors" title="Delete">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                            </button>
                        </form>
                    </div>
                @endif
            @endauth
        </div>

        <!-- Action Bar (Upvote & Reply) -->
        <div class="flex items-center gap-4 mt-2 ml-2">
            <button @click="toggleUpvote" 
                    class="flex items-center gap-1.5 text-xs font-bold transition-colors"
                    :class="upvoted ? 'text-teal-600 dark:text-teal-400' : 'text-gray-500 dark:text-gray-400 hover:text-teal-600 dark:hover:text-teal-400'">
                <svg class="w-4 h-4" :class="upvoted ? 'fill-current' : 'fill-none'" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.514" />
                </svg>
                <span x-text="upvotesCount"></span>
            </button>

            <!-- Anyone can reply -->
            <button @click="replying = !replying" class="flex items-center gap-1.5 text-xs font-bold text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                </svg>
                Reply
            </button>
        </div>

        <!-- Reply Form Box -->
        <div x-show="replying" x-collapse x-cloak class="mt-3">
            <form @submit.prevent="submitReply" hx-boost="false" class="ml-4 border-l-2 border-gray-100 dark:border-white/5 pl-4">
                <div class="relative">
                    <textarea x-model="replyContent" rows="2" class="w-full bg-white dark:bg-[#121212] border border-gray-200 dark:border-white/10 rounded-xl p-3 focus:ring-2 focus:ring-teal-500/50 focus:border-teal-500 transition-all text-sm placeholder-gray-400 text-gray-900 dark:text-gray-100" placeholder="Replying to {{ rtrim($comment->author_name, 's') }}'s comment..." required></textarea>
                </div>
                <p x-show="errorMessage" x-text="errorMessage" class="text-xs text-red-500 mt-1"></p>
                <div class="flex justify-end items-center gap-2 mt-2">
                    <span x-show="isSubmittingReply" class="text-xs text-gray-400 animate-pulse">Posting...</span>
                    <button type="button" @click="replying = false" class="px-3 py-1.5 text-xs font-semibold text-gray-500 hover:text-gray-900 dark:hover:text-white transition-colors">Cancel</button>
                    <button type="submit" :disabled="isSubmittingReply" class="bg-gray-900 dark:bg-white text-white disabled:opacity-50 dark:text-gray-900 hover:bg-teal-600 dark:hover:bg-teal-500 hover:text-white px-4 py-1.5 rounded-lg text-xs font-bold shadow-md transition-all">Reply</button>
                </div>
            </form>
        </div>

        <!-- Nested Replies Recursive Render -->
        @if($comment->replies->isNotEmpty())
            <div class="mt-2 ml-4 md:ml-6 pl-4 md:pl-6 border-l-2 border-gray-100 dark:border-white/5 space-y-4">
                @foreach($comment->replies as $reply)
                    @include('partials.comment-thread', ['comment' => $reply, 'phone' => $phone])
                @endforeach
            </div>
        @endif
    </div>
</div>
