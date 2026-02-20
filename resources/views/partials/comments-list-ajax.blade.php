@if($comments->isEmpty())
    <div class="text-center py-8 opacity-50">
        <svg class="w-12 h-12 mx-auto mb-3 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
        </svg>
        <p class="text-sm">No comments yet. Be the first!</p>
    </div>
@else
    @foreach($comments as $comment)
        @include('partials.comment-thread', ['comment' => $comment, 'phone' => $phone])
    @endforeach
@endif
