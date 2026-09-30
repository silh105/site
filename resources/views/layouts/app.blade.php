<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pulse Messenger</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/layout.css">
    @vite(['resources/js/app.js'])
</head>
<body>
<div id="app" class="app-wrapper">
    <header class="mobile-header">
        <div style="display: flex; align-items: center;">
            <span class="logo-mark"></span>
            <span class="logo-text">Pulse</span>
        </div>
        <button @click="openAuthModal" style="background: none; border: 1px solid #e2e8f0; border-radius: 50%; width: 34px; height: 34px;">👤</button>
    </header>

    <div class="layout-container">
        <aside class="desktop-sidebar">
            <div class="sidebar-brand">
                <span class="logo-mark"></span>
                <span class="brand-title">Pulse Connect</span>
            </div>
            <nav class="sidebar-nav">
                <a href="/feed" class="nav-item active">Новости</a>
                <a href="/chats" class="nav-item">Сообщения</a>
                <a href="/friends" class="nav-item">Друзья</a>
                <a href="/categories" class="nav-item">Категории</a>
            </nav>
            <div style="padding-top: 14px; border-top: 1px solid #e2e8f0;">
                <button class="nav-item" style="width:100%; border:none; cursor:pointer;" @click="openAuthModal">Войти в аккаунт</button>
            </div>
        </aside>

        <main class="main-content">
            @yield('content')
        </main>

        <aside class="desktop-aside-right">
            @yield('aside_right')
        </aside>
    </div>

    <nav class="mobile-bottom-nav">
        <a href="/feed" class="bottom-nav-item active"><span>Лента</span></a>
        <a href="/chats" class="bottom-nav-item"><span>Чаты</span></a>
        <a href="/friends" class="bottom-nav-item"><span>Друзья</span></a>
        <a href="/categories" class="bottom-nav-item"><span>Темы</span></a>
    </nav>

    <auth-modal :is-open="isAuthModalOpen" @close="closeAuthModal"></auth-modal>
</div>
</body>
</html>