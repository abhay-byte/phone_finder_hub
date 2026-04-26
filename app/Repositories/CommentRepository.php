<?php

namespace App\Repositories;

use App\Models\Comment;

class CommentRepository extends FirestoreRepository
{
    protected string $collection = 'comments';

    protected function makeModel(array $data): object
    {
        return new Comment($data);
    }

    protected function modelClass(): string
    {
        return Comment::class;
    }

    /**
     * Get comments for a specific phone.
     */
    public function forPhone(string $phoneId): array
    {
        $all = $this->all();
        $comments = array_filter($all, fn ($comment) => ($comment->phone_id ?? '') === $phoneId && empty($comment->parent_id));
        usort($comments, fn ($a, $b) => ($b->created_at ?? '') <=> ($a->created_at ?? ''));

        return $comments;
    }

    /**
     * Get replies for a specific comment.
     */
    public function repliesFor(string $commentId): array
    {
        $all = $this->all();
        $replies = array_filter($all, fn ($comment) => ($comment->parent_id ?? '') === $commentId);
        usort($replies, fn ($a, $b) => ($a->created_at ?? '') <=> ($b->created_at ?? ''));

        return $replies;
    }

    /**
     * Count comments for a phone.
     */
    public function countForPhone(string $phoneId): int
    {
        return count($this->where('phone_id', '==', $phoneId)->get());
    }
}
