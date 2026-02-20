<div x-data="commentsManager('{{ route('phones.comments.index', $phone) }}', '{{ route('comments.store', $phone) }}', {{ $comments->count() }})" 
     class="space-y-6" 
     @comment-added.window="loadComments"
     @comment-deleted.window="loadComments">
