<?php
use App\Models\Chat;
use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('chat.{chatId}', function (User $user, int$chatId) {
    return Chat::where('id', $chatId)->whereHas('users', fn($q) => $q->where('users.id',$user->id))->exists();
});