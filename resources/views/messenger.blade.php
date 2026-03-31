<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'Messenger') }} | Messenger</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap">
        <link rel="stylesheet" href="{{ asset('assets/css/template.bundle.css') }}">
        <style>
            [v-cloak] {
                display: none;
            }

            body {
                min-height: 100vh;
                font-family: 'Manrope', sans-serif;
                color: #0f172a;
                background:
                    radial-gradient(circle at top, rgba(14, 165, 233, 0.16), transparent 28%),
                    linear-gradient(180deg, #f4f8ff 0%, #eef4ff 42%, #f8fbff 100%);
            }

            .page-shell {
                min-height: 100vh;
                display: flex;
                flex-direction: column;
            }

            .topbar {
                position: sticky;
                top: 0;
                z-index: 20;
                backdrop-filter: blur(14px);
                background: rgba(255, 255, 255, 0.94);
                border-bottom: 1px solid rgba(148, 163, 184, 0.14);
            }

            .topbar-inner {
                max-width: 1400px;
                margin: 0 auto;
                padding: 1rem 1.5rem;
                display: flex;
                gap: 1rem;
                align-items: center;
                justify-content: space-between;
            }

            .brand-mark {
                width: 2.75rem;
                height: 2.75rem;
                border-radius: 1rem;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                background: linear-gradient(135deg, #0ea5e9, #2563eb);
                color: #fff;
                font-weight: 700;
                box-shadow: 0 16px 35px rgba(37, 99, 235, 0.28);
            }

            .topbar-nav {
                display: flex;
                flex-wrap: wrap;
                gap: 0.75rem;
                align-items: center;
            }

            .topbar-link {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                border-radius: 999px;
                padding: 0.7rem 1rem;
                font-size: 0.96rem;
                font-weight: 700;
                color: #0f172a;
                transition: all 0.2s ease;
            }

            .topbar-link:hover,
            .topbar-link.active {
                background: #0f172a;
                color: #fff;
                box-shadow: 0 14px 28px rgba(15, 23, 42, 0.18);
            }

            .logout-btn {
                border: 0;
                background: #0ea5e9;
                color: #082f49;
            }

            .workspace {
                flex: 1;
                width: 100%;
                max-width: 1400px;
                margin: 0 auto;
                padding: 1.5rem;
            }

            .workspace-grid {
                display: grid;
                grid-template-columns: 360px minmax(0, 1fr);
                gap: 1.5rem;
                min-height: calc(100vh - 130px);
            }

            .glass-panel {
                border: 1px solid rgba(148, 163, 184, 0.12);
                background: rgba(255, 255, 255, 0.95);
                box-shadow: 0 24px 60px rgba(15, 23, 42, 0.06);
                border-radius: 2rem;
                overflow: hidden;
            }

            .sidebar-shell {
                display: flex;
                flex-direction: column;
                min-height: 0;
            }

            .sidebar-head {
                padding: 1.5rem;
                border-bottom: 1px solid rgba(148, 163, 184, 0.12);
                background: linear-gradient(180deg, rgba(239, 246, 255, 0.95), rgba(255, 255, 255, 0.95));
            }

            .sidebar-meta,
            .friend-list {
                padding: 1.25rem 1.5rem 1.5rem;
            }

            .friend-list {
                border-top: 1px solid rgba(148, 163, 184, 0.12);
                background: rgba(248, 250, 252, 0.82);
            }

            .stats-grid {
                display: grid;
                grid-template-columns: repeat(3, minmax(0, 1fr));
                gap: 0.75rem;
                margin-top: 1rem;
            }

            .stat-card {
                border-radius: 1.25rem;
                padding: 0.85rem 0.95rem;
                background: rgba(255, 255, 255, 0.9);
                border: 1px solid rgba(148, 163, 184, 0.14);
            }

            .stat-card .small {
                color: #64748b !important;
                font-size: 0.78rem;
                font-weight: 700;
                letter-spacing: 0.02em;
            }

            .stat-card .fw-bold {
                color: #0f172a !important;
                font-size: 1.15rem;
            }

            .search-panel {
                padding: 0 1.5rem 1rem;
            }

            .search-panel label {
                color: #475569 !important;
                font-size: 0.88rem;
                font-weight: 700;
            }

            .search-panel .form-control {
                min-height: 3rem;
                border-radius: 1rem;
                border-color: rgba(148, 163, 184, 0.25);
                background: #f8fafc;
                color: #0f172a;
                font-size: 0.95rem;
            }

            .search-panel .form-control::placeholder {
                color: #94a3b8;
            }

            .friend-item {
                display: flex;
                align-items: center;
                gap: 0.75rem;
                padding: 0.75rem 0;
            }

            .sidebar-head .text-muted,
            .sidebar-meta .text-muted,
            .friend-list .text-muted {
                color: #64748b !important;
            }

            .sidebar-head .fw-bold,
            .sidebar-meta h1,
            .friend-list h2 {
                color: #0f172a !important;
            }

            .friend-avatar {
                width: 2.75rem;
                height: 2.75rem;
                border-radius: 999px;
                object-fit: cover;
                border: 2px solid rgba(14, 165, 233, 0.14);
            }

            .messenger-stage {
                min-height: 0;
                display: flex;
                flex-direction: column;
            }

            .media-preview-overlay {
                position: fixed;
                inset: 0;
                z-index: 9999;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 2rem;
                background: rgba(15, 23, 42, 0.84);
                backdrop-filter: blur(10px);
            }

            .media-preview-dialog {
                width: min(980px, 100%);
                max-height: 90vh;
                overflow: hidden;
                border-radius: 2rem;
                background: #0f172a;
                color: #fff;
                box-shadow: 0 40px 80px rgba(15, 23, 42, 0.35);
            }

            .media-preview-head {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 1rem;
                padding: 1rem 1.25rem;
                border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            }

            .media-preview-body {
                max-height: calc(90vh - 82px);
                overflow: auto;
                padding: 1.25rem;
                display: flex;
                align-items: center;
                justify-content: center;
                background: linear-gradient(180deg, #0f172a 0%, #111827 100%);
            }

            .media-preview-image {
                max-width: 100%;
                max-height: calc(90vh - 140px);
                border-radius: 1.25rem;
                object-fit: contain;
            }

            .media-preview-close,
            .media-preview-download {
                border: 1px solid rgba(255, 255, 255, 0.14);
                background: rgba(255, 255, 255, 0.08);
                color: #fff;
                border-radius: 999px;
                padding: 0.6rem 1rem;
                font-size: 0.9rem;
                font-weight: 700;
                text-decoration: none;
            }

            .media-preview-embed {
                width: 100%;
                min-height: 72vh;
                border: 0;
                border-radius: 1rem;
                background: #fff;
            }

            .media-preview-empty {
                width: 100%;
                min-height: 260px;
                border-radius: 1.25rem;
                display: flex;
                align-items: center;
                justify-content: center;
                background: rgba(255, 255, 255, 0.08);
                color: rgba(255, 255, 255, 0.72);
                font-weight: 600;
                text-align: center;
                padding: 2rem;
            }

            @media (max-width: 991.98px) {
                .workspace {
                    padding: 1rem;
                }

                .workspace-grid {
                    grid-template-columns: 1fr;
                }
            }
        </style>
    </head>
    <body>
        <div id="chat-app" class="page-shell" v-cloak>
            <header class="topbar">
                <div class="topbar-inner">
                    <div class="d-flex align-items-center gap-3">
                        <a href="{{ route('home') }}" class="text-decoration-none d-flex align-items-center gap-3 text-dark">
                            <span class="brand-mark">M</span>
                            <div>
                                <div class="fw-bold fs-5 lh-1">{{ config('app.name', 'Messenger') }}</div>
                                <div class="text-muted small">Portfolio demo workspace</div>
                            </div>
                        </a>
                    </div>

                    <div class="topbar-nav">
                        <a href="{{ route('home') }}" class="topbar-link">Home</a>
                        <a href="{{ route('dashboard') }}" class="topbar-link">Dashboard</a>
                        <a href="{{ route('messenger') }}" class="topbar-link active">Messenger</a>
                        <form method="POST" action="{{ route('logout') }}" class="m-0">
                            @csrf
                            <button type="submit" class="topbar-link logout-btn">Log out</button>
                        </form>
                    </div>
                </div>
            </header>

            <main class="workspace">
                <div class="workspace-grid">
                    <aside class="glass-panel sidebar-shell">
                        <div class="sidebar-head">
                            <div class="d-flex align-items-center gap-3">
                                <img src="{{ Auth::user()->avatar_url }}" alt="{{ Auth::user()->name }}" class="friend-avatar">
                                <div>
                                    <div class="fw-bold text-dark">{{ Auth::user()->name }}</div>
                                    <div class="text-muted small">Signed in and ready to present</div>
                                </div>
                            </div>
                        </div>

                        <div class="sidebar-meta pb-0">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <h1 class="h4 m-0">Conversations</h1>
                                <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-2">Live demo</span>
                            </div>
                            <p class="text-muted mb-0">Clear navigation, readable content, and polished demo data ready for presentation.</p>
                            <div class="stats-grid">
                                <div class="stat-card">
                                    <div class="small text-muted">Chats</div>
                                    <div class="fw-bold text-dark">{{ $stats['conversations'] }}</div>
                                </div>
                                <div class="stat-card">
                                    <div class="small text-muted">Contacts</div>
                                    <div class="fw-bold text-dark">{{ $stats['contacts'] }}</div>
                                </div>
                                <div class="stat-card">
                                    <div class="small text-muted">Unread</div>
                                    <div class="fw-bold text-dark">{{ $stats['unread'] }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="search-panel">
                            <label for="conversation-search" class="form-label small text-muted">Search conversations</label>
                            <input
                                id="conversation-search"
                                v-model="conversationSearch"
                                type="search"
                                class="form-control"
                                placeholder="Search by name, label, or preview"
                            >
                        </div>

                        <chat-list></chat-list>

                        <div class="friend-list">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <h2 class="h6 text-uppercase text-muted m-0">Demo Contacts</h2>
                                <span class="small text-muted">{{ $friends->count() }}</span>
                            </div>

                            @foreach ($friends->take(4) as $friend)
                                <div class="friend-item">
                                    <img src="{{ $friend->avatar_url }}" alt="{{ $friend->name }}" class="friend-avatar">
                                    <div class="min-w-0">
                                        <div class="fw-semibold text-dark text-truncate">{{ $friend->name }}</div>
                                        <div class="small text-muted text-truncate">{{ $friend->email }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </aside>

                    <section class="glass-panel messenger-stage">
                        <messenger :conversation="conversation"></messenger>
                    </section>
                </div>
            </main>

            <div v-if="previewMedia" class="media-preview-overlay" @click.self="closeMediaPreview">
                <div class="media-preview-dialog">
                    <div class="media-preview-head">
                        <div class="min-w-0">
                            <div class="fw-bold text-truncate" v-text="previewMedia.name"></div>
                            <div class="small text-white-50" v-text="previewMedia.label"></div>
                        </div>

                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <a :href="previewMedia.url" class="media-preview-download" target="_blank" rel="noopener">
                                Download
                            </a>
                            <button type="button" class="media-preview-close" @click="closeMediaPreview">
                                Close
                            </button>
                        </div>
                    </div>

                    <div class="media-preview-body">
                        <img v-if="previewMedia.kind === 'image'" :src="previewMedia.url" :alt="previewMedia.name" class="media-preview-image">
                        <iframe
                            v-else-if="previewMedia.kind === 'pdf'"
                            :src="previewMedia.url"
                            title="PDF preview"
                            class="media-preview-embed"
                        ></iframe>
                        <div v-else class="media-preview-empty">
                            Preview is not available for this file type. Use Download to open it.
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>

        <script src="{{ asset('js/moment.js') }}"></script>
        <script src="{{ asset('js/manifest.js') }}"></script>
        <script src="{{ asset('js/vendor.js') }}"></script>
        <script>
            window.messengerConfig = {!! json_encode([
                'userId' => Auth::id(),
                'csrfToken' => csrf_token(),
                'selectedConversationId' => $selectedConversationId,
                'routes' => [
                    'messenger' => route('messenger'),
                    'conversations' => url('/api/conversations'),
                    'messages' => url('/api/messages'),
                ],
                'echo' => [
                    'key' => config('broadcasting.connections.pusher.key'),
                    'cluster' => config('broadcasting.connections.pusher.options.cluster'),
                ],
            ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!};
        </script>
        <script src="{{ asset('assets/js/vendor.js') }}"></script>
        <script src="{{ asset('js/messages.js') }}"></script>
    </body>
</html>
