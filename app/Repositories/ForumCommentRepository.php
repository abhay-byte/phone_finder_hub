<?php

namespace App\Repositories;

use App\Models\ForumComment;

class ForumCommentRepository extends FirestoreRepository
{
    protected string $collection = 'forum_comments';

    protected function makeModel(array $data): object
    {
        return new ForumComment($data);
    }

    protected function modelClass(): string
    {
        return ForumComment::class;
    }

    public function forPost(string $postId): array
    {
        $all = $this->all();
        $comments = array_filter($all, fn ($comment) => ($comment->forum_post_id ?? '') === $postId);
        usort($comments, fn ($a, $b) => ($a->created_at ?? '') <=> ($b->created_at ?? ''));

        return $comments;
    }
}
