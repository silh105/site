<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserProfile extends Model {
    protected $fillable = ['user_id', 'first_name', 'last_name', 'avatar_url'];
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function getFullNameAttribute(): string { return "{$this->last_name} {$this->first_name}"; }
}