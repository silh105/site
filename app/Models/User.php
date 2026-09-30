<?php
namespace App\Models;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable {
    use Notifiable;
    protected $fillable = ['login', 'email', 'password', 'role'];
    protected $hidden = ['password', 'remember_token'];

    public function profile(): HasOne { return $this->hasOne(UserProfile::class); }
    public function categories(): BelongsToMany { return $this->belongsToMany(Category::class, 'user_categories'); }
    public function posts(): HasMany { return $this->hasMany(Post::class); }
    public function chats(): BelongsToMany { return $this->belongsToMany(Chat::class, 'chat_participants'); }
    public function isAdmin(): bool { return $this->role === 'admin'; }
}