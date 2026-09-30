@extends('layouts.app')
@section('content')
    <news-feed :current-user-id="{{ auth()->id() ?? 0 }}"></news-feed>
@endsection
@section('aside_right')
    <div style="background:#fff; border:1px solid #e2e8f0; padding:16px; border-radius:12px;">
        <h4 style="font-weight:700; margin-bottom:8px;">Интересные темы</h4>
        <p style="font-size:12px; color:#64748b;">Выберите категории для персонализации ленты.</p>
    </div>
@endsection