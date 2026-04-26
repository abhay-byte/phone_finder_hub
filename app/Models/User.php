<?php

namespace App\Models;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Notifications\RoutesNotifications;
use Illuminate\Support\Collection;

class User extends FirestoreModel implements Authenticatable, MustVerifyEmail
{
    use RoutesNotifications;

    protected array $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function getAuthIdentifierName(): string
    {
        return 'id';
    }

    public function getAuthIdentifier(): string
    {
        return $this->attributes['id'] ?? '';
    }

    public function getKey(): string
    {
        return $this->attributes['id'] ?? '';
    }

    public function getEmailForVerification(): string
    {
        return $this->attributes['email'] ?? '';
    }

    public function hasVerifiedEmail(): bool
    {
        return ! empty($this->attributes['email_verified_at']);
    }

    public function markEmailAsVerified(): bool
    {
        $this->attributes['email_verified_at'] = now()->format('c');
        app(\App\Repositories\UserRepository::class)
            ->update($this->attributes['id'], ['email_verified_at' => $this->attributes['email_verified_at']]);

        return true;
    }

    public function sendEmailVerificationNotification(): void
    {
        \Illuminate\Support\Facades\Notification::send($this, new \Illuminate\Auth\Notifications\VerifyEmail);
    }

    public function update(array $attributes): bool
    {
        app(\App\Repositories\UserRepository::class)
            ->update($this->attributes['id'], $attributes);
        $this->attributes = array_merge($this->attributes, $attributes);

        return true;
    }

    public function refresh(): static
    {
        $fresh = app(\App\Repositories\UserRepository::class)
            ->find($this->attributes['id']);

        if ($fresh) {
            $this->attributes = $fresh->getAttributes();
        }

        return $this;
    }

    public function fresh(): ?static
    {
        return app(\App\Repositories\UserRepository::class)
            ->find($this->attributes['id'] ?? '');
    }

    public function getAuthPassword(): string
    {
        return $this->attributes['password'] ?? '';
    }

    public function getRememberToken(): string
    {
        return $this->attributes['remember_token'] ?? '';
    }

    public function setRememberToken($value): void
    {
        $this->attributes['remember_token'] = $value;
    }

    public function getRememberTokenName(): string
    {
        return 'remember_token';
    }

    public function getAuthPasswordName(): string
    {
        return 'password';
    }

    public function isSuperAdmin(): bool
    {
        return ($this->attributes['role'] ?? '') === 'super_admin';
    }

    public function isUser(): bool
    {
        return ($this->attributes['role'] ?? '') === 'user';
    }

    public function isAuthor(): bool
    {
        return ($this->attributes['role'] ?? '') === 'author';
    }

    public function isMaintainer(): bool
    {
        return ($this->attributes['role'] ?? '') === 'maintainer';
    }

    public function isModerator(): bool
    {
        return ($this->attributes['role'] ?? '') === 'moderator';
    }

    public function hasAdminAccess(): bool
    {
        return in_array($this->attributes['role'] ?? '', ['super_admin', 'maintainer', 'moderator', 'author']);
    }

    public function blogs(): Collection
    {
        return collect(app(\App\Repositories\BlogRepository::class)
            ->where('user_id', '==', $this->attributes['id'] ?? '')
            ->get());
    }

    public function comments(): Collection
    {
        return collect(app(\App\Repositories\CommentRepository::class)
            ->where('user_id', '==', $this->attributes['id'] ?? '')
            ->get());
    }

    public function upvotes(): Collection
    {
        return collect(app(\App\Repositories\CommentUpvoteRepository::class)
            ->where('user_id', '==', $this->attributes['id'] ?? '')
            ->get());
    }

    public function forumPosts(): Collection
    {
        return collect(app(\App\Repositories\ForumPostRepository::class)
            ->where('user_id', '==', $this->attributes['id'] ?? '')
            ->get());
    }

    public function forumComments(): Collection
    {
        return collect(app(\App\Repositories\ForumCommentRepository::class)
            ->where('user_id', '==', $this->attributes['id'] ?? '')
            ->get());
    }
}
