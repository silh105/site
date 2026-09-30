<?php
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => redirect('/feed'));
Route::get('/feed', fn() => view('feed'));
Route::get('/chats', fn() => view('chats'))->middleware('auth');