<?php

namespace App\Repositories;

use App\Models\CommentUpvote;

class CommentUpvoteRepository extends FirestoreRepository
{
    protected string $collection = 'comment_upvotes';

    protected function makeModel(array $data): object
    {
        return new CommentUpvote($data);
    }

    protected function modelClass(): string
    {
        return CommentUpvote::class;
    }

    public function forComment(string $commentId): array
    {
        return $this->where('comment_id', '==', $commentId)->get();
    }

    public function hasUpvoted(string $commentId, string $userId): bool
    {
        return $this->where('comment_id', '==', $commentId)
            ->where('user_id', '==', $userId)
            ->exists();
    }

    public function removeUpvote(string $commentId, string $userId): bool
    {
        $upvotes = $this->where('comment_id', '==', $commentId)
            ->where('user_id', '==', $userId)
            ->get();

        foreach ($upvotes as $upvote) {
            $this->delete($upvote->id);
        }

        return true;
    }
}
