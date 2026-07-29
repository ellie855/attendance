<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', '掲示板')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=BIZ+UDPGothic:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.css">
    <style>
        body { font-family: 'BIZ UDPGothic', 'Space Grotesk', sans-serif; max-width: none; margin: 40px 40px 40px 280px; padding: 0 20px; color: #1a1a1a; }
        h1 { font-family: 'Space Grotesk', 'BIZ UDPGothic', sans-serif; font-weight: 700; border-bottom: 2px solid #333; padding-bottom: 8px; letter-spacing: -0.5px; }
        h2 { font-family: 'Space Grotesk', 'BIZ UDPGothic', sans-serif; font-weight: 600; letter-spacing: -0.3px; }
        a { color: #06c; }
        .post { border: 1px solid #ddd; border-radius: 6px; padding: 16px; margin-bottom: 12px; }
        .post h3 { margin: 0 0 8px 0; }
        .post .meta { color: #888; font-size: 12px; }
        .post .body { white-space: pre-wrap; margin: 8px 0; }
        form.inline { display: inline; }
        input[type=text], textarea { width: 100%; padding: 8px; box-sizing: border-box; font-size: 14px; }
        textarea { height: 120px; }
        label { display: block; margin: 12px 0 4px; font-weight: bold; }
        button { padding: 8px 16px; cursor: pointer; }
        .btn-danger { background: #e74c3c; color: white; border: none; border-radius: 4px; }
        .error { color: #c00; font-size: 13px; }
        nav[role="navigation"] { margin-top: 24px; display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; }
        nav[role="navigation"] .hidden { display: flex !important; }
        nav[role="navigation"] .sm\:hidden { display: none !important; }
        nav[role="navigation"] p { margin: 0; color: #666; font-size: 13px; }
        nav[role="navigation"] span[aria-current="page"] span,
        nav[role="navigation"] a, nav[role="navigation"] span {
            display: inline-block; padding: 6px 12px; margin: 0 2px;
            border: 1px solid #ddd; border-radius: 4px;
            color: #06c; text-decoration: none; background: #fff; font-size: 14px;
        }
        nav[role="navigation"] a:hover { background: #f0f7ff; }
        nav[role="navigation"] span[aria-current="page"] span { background: #06c; color: white; border-color: #06c; }
        nav[role="navigation"] span[aria-disabled="true"] span { color: #ccc; background: #f8f8f8; cursor: default; }
        nav[role="navigation"] svg { width: 14px; height: 14px; vertical-align: middle; }
        nav[role="navigation"] p span { padding: 0; border: 0; background: none; color: inherit; display: inline; margin: 0; }
        .card { display: flex; flex-direction: column; gap: 20px; padding: 24px; border: 1px solid rgba(0,0,0,0.06); border-radius: 12px; background: rgba(255,255,255,0.55); margin-bottom: 24px; box-shadow:  0 4px 16px rgba(0,0,0,0.04); }
        .card-title { margin: 0; font-size: 20px; }
        .card-main { display: flex; gap: 20px; }
        .card-main .left { flex: 2; }
        .card-main .right { flex: 1; display: flex; flex-direction: column; gap: 12px; }
        .card .actions { margin: 0; }
        .clock-time { font-size: 150px; font-weight: bold; font-family: monospace; letter-spacing: 2px; }
        .clock-sec { font-size: 25%; opacity: 0.6; font-weight: normal; vertical-align: baseline; margin-left: 4px; }
        .clock-date { font-size: 18px; color: #666; margin-top: 8px; }
        .info-box { display: block; padding: 12px 16px; border: 1px solid rgba(0,0,0,0.06); border-radius: 8px; background: white; text-align: center; text-decoration: none; color: #333; font-size: 14px; box-shadow: 0 1px 3px rgba(0,0,0,0.04); transition: all 0.15s; }
        a.info-box:hover { background: #f0f7ff; color: #06c; box-shadow: 0 4px 12px rgba(0,0,0,0.08); transform: translateY(-1px); }
        .status-on { background: #d4edda; color: #155724; border-color: #c3e6cb; font-weight: bold; }
        .status-off { background: #f8d7da; color: #721c24; border-color: #f5c6cb; font-weight: bold; }
        .status-none { background: #e2e3e5; color: #6c757d; border-color: #d6d8db; }
        .user-box { font-weight: bold; }
        .actions { display: flex; gap: 16px; margin: 24px 0; justify-content: center; }
        .actions button { padding: 16px 40px; font-size: 18px; font-weight: bold; border: none; border-radius: 8px; cursor: pointer; min-width: 140px; }
        .btn-in  { background: #10b981; color: white; box-shadow: 0 2px 6px rgba(16,185,129,0.25); transition: all 0.15s; }
        .btn-out { background: #ef4444; color: white; box-shadow: 0 2px 6px rgba(239,68,68,0.25); transition: all 0.15s; }
        .btn-in:hover  { background: #059669; box-shadow: 0 2px 16px rgba(16,185,129,0.35); transform: translateY(-1px); }
        .btn-out:hover { background: #dc2626; box-shadow: 0 6px 16px rgba(239,68,68,0.35); transform: translateY(-1px); }
        .btn-link { padding: 16px 40px; font-size: 18px; font-weight: bold; border-radius: 8px; text-decoration: none; min-width: 140px; text-align: center; display: inline-block; color: white; }
        .history-list { max-height: 480px; overflow-y: auto; border: 1px solid rgba(0,0,0,0.06); border-radius: 12px; background: rgba(255,255,255,0.55); box-shadow: 0 4px 16px rgba(0,0,0,0.04); }
        .history-list .log-row:last-child { border-bottom: none; }
        .log-row { display: flex; gap: 16px; padding: 12px 16px; border-bottom: 1px solid rgba(0,0,0,0.04); align-items: center; transition: background 0.15s; }
        .log-row:hover { background: rgba(0,0,0,0.02); }
        .log-time { font-family: monospace; color: #555; min-width: 180px; }
        .log-type { font-weight: bold; }
        .log-type.clock_in  { color: #10b981; }
        .log-type.clock_out { color: #ef4444; }
        .log-user { background: #f4f6fb; padding: 2px 8px; border-radius: 4px; font-size: 12px; color: #555; }
        .log-duration { background: #d1fae5; color: #047857; padding: 2px 8px; border-radius: 4px; font-size: 12px; font-weight: bold; }
        .log-elapsed { background: #fef3c7; color: #92400e; padding: 2px 8px; border-radius: 4px; font-size: 12px; font-weight: bold; font-family: monospace; }       
        .log-edit { margin-left: auto; padding: 4px 10px; background: #06c; color: white; text-decoration: none; border-radius: 4px; font-size: 12px; }
        .log-edit:hover { background: #048; }
        .confirm-box { padding: 40px 20px; text-align: center; border: 1px solid #ddd; border-radius: 12px; background: #fafafa; }
        .confirm-box h1 { border: none; font-size: 28px; }
        .confirm-time { font-size: 40px; font-family: monospace; font-weight: bold; margin: 24px 0; color: #333; }
        .confirm-actions { display: flex; gap: 16px; justify-content: center; align-items: center; flex-wrap: wrap; }
        .confirm-actions button { padding: 14px 28px; font-size: 16px; font-weight: bold; border: none; border-radius: 6px; cursor: pointer; }
        .btn-cancel { padding: 14px 28px; font-size: 16px; background: #888; color: white; text-decoration: none; border-radius: 6px; }
        .btn-cancel:hover { background: #666; }
        .sidebar { position: fixed; top: 0; left: 0; width: 240px; height: 100vh; background: #1a2533; color: #e0e0e0; display: flex; flex-direction: column; padding: 32px 20px; box-sizing: border-box; cursor: pointer; box-shadow: 2px 0 12px rgba(0,0,0,0.04); }
        .sidebar a, .sidebar button, .sidebar form { cursor: default; }
        .sidebar a:hover, .sidebar button:hover { cursor: pointer; }
        .sidebar-brand { display: flex; align-items: center; gap: 12px; font-weight: bold; font-size: 18px; margin-bottom: 40px; color: #fff; cursor: pointer; user-select: none; }
        .sidebar-brand:hover { opacity: 0.8; }
        .sidebar-logo { font-size: 26px; color: #64b5f6; }
        .sidebar-nav { display: flex; flex-direction: column; gap: 8px; flex: 1; }
        .sidebar-link { padding: 12px 16px; border-radius: 10px; color: #ccc; text-decoration: none; font-size: 15px; display: flex; align-items: center; gap: 14px; }
        .sidebar-link:hover { background: rgba(255,255,255,0.08); color: #fff; }
        .sidebar-icon { font-size: 20px; min-width: 22px; text-align: center; }
        .sidebar-footer { border-top: 1px solid rgba(255,255,255,0.1); padding-top: 20px; margin-top: 20px; }        
        .sidebar-user { display: flex; gap: 10px; align-items: center; }
        .sidebar-avatar { width: 36px; height: 36px; border-radius: 50%; background: #64b5f6; color: white; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 16px; }
        .sidebar-info { flex: 1; min-width: 0; }
        .sidebar-username { font-size: 13px; font-weight: bold; color: #e0e0e0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .sidebar-logout { background: transparent; border: none; color: #aaa; padding: 0; font-size: 11px; cursor: pointer; }
        .sidebar-logout:hover { color: #fff; }
        body.sidebar-collapsed .sidebar { width: 60px; padding: 24px 8px; }
        body.sidebar-collapsed .sidebar-label,
        body.sidebar-collapsed .sidebar-info { display: none; }
        body.sidebar-collapsed .sidebar-link { justify-content: center; padding: 10px 0; }
        body.sidebar-collapsed .sidebar-brand { justify-content: center; }
        body.sidebar-collapsed .sidebar-user { justify-content: center; }
        body.sidebar-collapsed { margin-left: 100px !important; }
        .color-picker { position: fixed; top: 80px; right: 16px; display: flex; flex-direction: column; gap: 8px; z-index: 100; }
        .color-swatch { width: 28px; height: 28px; border-radius: 50%; border: 2px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.2); cursor: pointer; padding: 0; transition: transform 0.15s; }
        .color-swatch:hover { transform: scale(1.15); }
        body.dark-mode { color: #e0e0e0; }
        body.dark-mode h1, body.dark-mode h2 { color: #e0e0e0; border-color: rgba(255,255,255,0.15); }
        body.dark-mode a { color: #64b5f6; }
        body.dark-mode .topbar { background: #050a17; }
        body.dark-mode .sidebar { background: #050a17; border-right: 1px solid rgba(255,255,255,0.05); }
        body.dark-mode .card { background: rgba(255,255,255,0.04); border-color: rgba(255,255,255,0.08); }
        body.dark-mode .card-title { color: #e0e0e0; border-color: rgba(255,255,255,0.15); }
        body.dark-mode .clock-time { color: #fff; }
        body.dark-mode .clock-date { color: #aaa; }
        body.dark-mode .info-box { background: rgba(255,255,255,0.05); color: #e0e0e0; border-color: rgba(255,255,255,0.1); }
        body.dark-mode a.info-box:hover { background: rgba(100,181,246,0.15); color: #90caf9; }
        body.dark-mode .status-on { background: rgba(46,125,50,0.25); color: #81c784; border-color: rgba(46,125,50,0.4); }
        body.dark-mode .status-off { background: rgba(198,40,40,0.25); color: #ef5350; border-color: rgba(198,40,40,0.4); }
        body.dark-mode .status-none { background: rgba(96,125,139,0.25); color: #b0bec5; border-color: rgba(96,125,139,0.4); }
        body.dark-mode .history-list { background: rgba(255,255,255,0.03); border-color: rgba(255,255,255,0.08); }
        body.dark-mode .log-row { border-bottom-color: rgba(255,255,255,0.05); }
        body.dark-mode .log-row:hover { background: rgba(255,255,255,0.04); }
        body.dark-mode .log-time { color: #999; }
        body.dark-mode .log-duration { background: rgba(46,125,50,0.3); color: #a5d6a7; }
        body.dark-mode .log-elapsed { background: rgba(255,193,7,0.2); color: #ffd54f; }
        body.dark-mode .log-user { background: rgba(255,255,255,0.08); color: #ccc; }
        .warning-box { background: #fef3c7; color: #92400e; border: 1px solid #fcd34d; border-radius: 8px; padding: 14px 18px; margin: 20px 0; font-size: 14px; display: flex; align-items: center; gap: 10px; }
        .warning-box .bi { font-size: 20px; flex-shrink: 0; }
        .dashboard-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 24px; align-items: start; }
        .dashboard-left { display: flex; flex-direction: column; gap: 24px; }
        .summary-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        .summary-card { background: rgba(255,255,255,0.55); border: 1px solid rgba(0,0,0,0.06); border-radius: 12px; padding: 18px; box-shadow: 0 4px 16px rgba(0,0,0,0.04); }
        .summary-card .label { font-size: 12px; color: #666; font-weight: 600; margin-bottom: 6px; letter-spacing: 0.5px; }
        .summary-card .value { font-family: 'Space Grotesk', sans-serif; font-size: 28px; font-weight: 700; color: #1a1a1a; letter-spacing: -1px; }
        .summary-card .unit { font-size: 14px; color: #888; font-weight: normal; margin-left: 4px; }
        .summary-card .icon { font-size: 16px; color: #06c; margin-bottom: 6px; }
        .dashboard-right h2 { margin-top: 0; }

        /* 磨いた見た目 */
        body { margin-top: 20px; margin-bottom: 20px; }
        .dashboard-grid { gap: 20px; margin-bottom: 20px; align-items: start; }
        .dashboard-left { gap: 16px; }
        .card { padding: 24px; }
        .card-title {
            margin: 0 0 16px;
            font-size: 15px;
            font-weight: 600;
            color: #444;
            border-bottom: 1px solid rgba(0,0,0,0.06);
            padding-bottom: 8px;
        }

        /* 勤怠カード: 時計と info-box を中央寄せで並列 */
        .card-main {
            display: flex;
            gap: 32px;
            align-items: center;
            justify-content: center;
            padding: 12px 0;
        }
        .card-main .left { text-align: center; flex: none; }
        .card-main .right { display: flex; flex-direction: column; gap: 8px; min-width: 200px; flex: none; }
        .clock-time { font-size: 96px; font-weight: bold; font-family: monospace; letter-spacing: 2px; line-height: 1; }
        .clock-date { font-size: 14px; color: #666; margin-bottom: 4px; }
        .info-box { padding: 10px 16px; font-size: 14px; }

        /* 打刻ボタン */
        .actions { margin: 20px 0 0; gap: 16px; justify-content: center; }
        .actions button, .btn-link {
            padding: 20px 48px;
            font-size: 20px;
            min-width: 200px;
            font-weight: bold;
            border-radius: 10px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.15);
            transition: all 0.2s;
        }
        .actions button:hover, .btn-link:hover { transform: translateY(-2px); box-shadow: 0 6px 14px rgba(0,0,0,0.2); }

        /* 作業内容カード */
        .dashboard-left > .card:nth-child(2) { padding: 16px 20px; }
        .dashboard-left > .card:nth-child(2) button { padding: 8px 20px; font-size: 13px; min-width: auto; }
        .dashboard-left > .card:nth-child(2) textarea { min-height: 60px; height: 60px; font-size: 13px; }

        /* サマリー: 色付き4カード */
        .summary-grid { grid-template-columns: repeat(5, 1fr); gap: 12px; }
        .summary-card { padding: 16px; border-left: 4px solid #10b981; transition: all 0.2s; }
        .summary-card:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
        .summary-grid .summary-card:nth-child(2) { border-left-color: #06c; }
        .summary-grid .summary-card:nth-child(3) { border-left-color: #f59e0b; }
        .summary-grid .summary-card:nth-child(4) { border-left-color: #ef4444; }
        .summary-grid .summary-card:nth-child(5) { border-left-color: #8b5cf6; }
        .summary-grid .summary-card:nth-child(5) .value { font-size: 20px; white-space: nowrap; }
        .summary-grid .summary-card:nth-child(5) .unit { font-size: 11px; }

        .summary-card .label { font-size: 11px; margin-bottom: 6px; letter-spacing: 0.5px; }
        .summary-card .value { font-size: 28px; letter-spacing: -1px; line-height: 1; }
        .summary-card .icon { font-size: 14px; margin-bottom: 2px; }

        /* 履歴: カラム揃え */
        .dashboard-right { display: flex; flex-direction: column; }
        .dashboard-right h2 { font-size: 18px; margin-bottom: 12px; }
        .history-list { flex: 1; max-height: calc(100vh - 140px); overflow-y: auto; }
        .log-row {
            display: grid;
            grid-template-columns: 130px 100px 80px 60px;
            gap: 12px;
            padding: 10px 16px;
            align-items: center;
        }
        .log-time { min-width: auto; font-size: 13px; }
        .log-type { font-size: 13px; }
        .log-duration { padding: 2px 6px; text-align: center; font-size: 11px; }
        .log-edit { padding: 4px 8px; font-size: 11px; text-align: center; }

        /* バッジ */
        .save-status {display: inline-flex;align-items: center;gap: 4px;}
        .save-status.saved {background: #e5e7eb;color: #6b7280;}
        .save-status.dirty {background: #fef3c7;color: #92400e;}
        .log-type.break_start, .log-type.break_end { color: #f59e0b; }

        /* 幅が狭い PC (1280px 以下) */
       @media (max-width: 1280px) {
           .clock-time { font-size: 100px; }
           .actions button, .btn-link { padding: 18px 36px; font-size:18px; min-width: 140px; }
           body { max-width: 100%; margin-right: 20px; }
       }

       /*　画面が狭い時、全体を小さく　（１０２４px　以下）*/
       @media (max-width: 1024px) {
           body {max-width: 100%; margin-right: 20px; }
           .clock-time { font-size: 80px; }
           .actions button, .btn-link { padding: 14px 24px; font-size: 15px; min-width: 110px; }
           .card { padding: 16px; }
           .dashboard-left > .card:first-child { padding: 20; }
           .summary-card { padding: 8px; }
           .summary-card .value { font-size: 16px; }
           .log-row { padding: 6px 10px; gap: 8px; }
           .log-time { min-width: 110px; font-size: 12px; }
       }
       </style>
</head>
<body>
    <div class="color-picker">
        <button class="color-swatch" data-bg="#fff" style="background:#fff" title="白"></button>
        <button class="color-swatch" data-bg="#f0e1ba" style="background:#fdf6e3" title="クリーム"></button>
        <button class="color-swatch" data-bg="#cbe9cd" style="background:#e8f5e9" title="ミント"></button>
        <button class="color-swatch" data-bg="#bed6e7" style="background:#e3f2fd" title="スカイ"></button>
        <button class="color-swatch" data-bg="#f5d1dd" style="background:#fce4ec" title="ピンク"></button>
        <button class="color-swatch" data-bg="#0f1729" data-dark="true" style="background:#0f1729" title="ダーク"></button>
    </div>

    <aside class="sidebar">

        <a href="/" class="sidebar-brand" style="text-decoration: none; color: inherit;">
            <i class="bi bi-stopwatch-fill sidebar-logo"></i>
            <span class="sidebar-label">勤怠アプリ</span>
        </a>

        <nav class="sidebar-nav">
            <a href="/attendances" class="sidebar-link">
                <i class="bi bi-clock-fill sidebar-icon"></i>
                <span class="sidebar-label">勤怠</span>
            </a>
            <a href="/attendance-requests" class="sidebar-link">
                <i class="bi bi-pencil-square sidebar-icon"></i>
                <span class="sidebar-label">勤怠修正</span>
            </a>
            <a href="{{ route('leave-requests.index') }}" class="sidebar-link">
                <i class="bi bi-calendar-event sidebar-icon"></i>
                <span class="sidebar-label">休暇申請</span>
            </a>
            <a href="{{ route('reports.monthly') }}" class="sidebar-link">
                <i class="bi bi-bar-chart-line-fill sidebar-icon"></i>
                <span class="sidebar-label">月次レポート</span>
            </span>
            <a href="/posts" class="sidebar-link">
                <i class="bi bi-megaphone-fill sidebar-icon"></i>
                <span class="sidebar-label">掲示板</span>
            </a>

            @auth
                <a href="{{ route('notifications.index') }}" class="sidebar-link" style="position:relative;">
                    <i class="bi bi-bell-fill sidebar-icon"></i>
                    <span class="sidebar-label">通知</span>
                    @php $unread = auth()->user()->unreadNotifications()->count(); @endphp
                    @if ($unread > 0)
                        <span style="position: absolute; top: 8px; right: 12px; background: #ef4444; color: white; font-size: 10px; padding: 2px 6px; border-radius: 10px; font-weight: bold; min-width: 18px; text-align: center;">
                            {{ $unread > 99 ? '99+' : $unread }}
                        </span>
                    @endif
                </a>
            @endauth


            @auth
                @if (auth()->user()->isAdmin())
                    <a href="{{ route('admin.users.index') }}" class="sidebar-link">
                        <i class="bi bi-people-fill sidebar-icon"></i>
                        <span class="sidebar-label">ユーザー管理</span>
                    </a>
                @endif
            @endauth

            @auth
                @if (auth()->user()->isAdmin())
                    <a href="{{ route('admin.attendance-requests.index') }}" class="sidebar-link">
                        <i class="bi bi-check2-square sidebar-icon"></i>
                        <span class="sidebar-label">承認待ち</span>
                    </a>
                @endif
            @endauth

            @auth
                @if (auth()->user()->isAdmin())
                    <a href="{{ route('admin.leave-requests.index') }}" class="sidebar-link">
                        <i class="bi bi-calendar-check sidebar-icon"></i>
                        <span class="sidebar-label">休暇承認</span>
                    </a>
                @endif
            @endauth

        </nav>

        <div class="sidebar-footer">
            @auth
                <div class="user-menu-wrap" style="position: relative;">
                    <button type="button" onclick="toggleUserMenu(event)"
                            class="sidebar-user"
                            style="cursor: pointer; background: none; border: none; padding: 0; width: 100%; text-align: left; display: flex; align-items: center; gap: 10px;">
                        <div class="sidebar-avatar">{{ mb_substr(auth()->user()->name, 0, 1) }}</div>
                        <div class="sidebar-info" style="flex: 1;">
                            <div class="sidebar-username">{{ auth()->user()->name }}</div>
                            <div style="color: #9ca3af; font-size: 10px;">クリックでメニュー ▲</div>
                        </div>
                    </button>

                    <div id="user-menu" style="display: none; position: absolute; bottom: calc(100% + 8px); left: 0; right: 0; background: #1f2937; border: 1px solid #374151; border-radius: 8px; padding: 6px; box-shadow: 0 -4px 20px rgba(0,0,0,0.4); z-index: 100;">
                        <a href="{{ route('profile.edit') }}"
                           style="display: block; padding: 10px 14px; color: #e5e7eb; text-decoration: none; border-radius: 4px; font-size: 13px;"
                           onmouseover="this.style.background='#374151'" onmouseout="this.style.background='transparent'">
                            <i class="bi bi-person-fill" style="margin-right: 8px;"></i>プロフィール
                        </a>
                        <a href="#"
                           style="display: block; padding: 10px 14px; color: #6b7280; text-decoration: none; border-radius: 4px; font-size: 13px; cursor: not-allowed;"
                           onclick="event.preventDefault();">
                            <i class="bi bi-gear-fill" style="margin-right: 8px;"></i>設定 <span style="font-size: 10px; margin-left: 4px;">(準備中)</span>
                        </a>
                        <div style="height: 1px; background: #374151; margin: 4px 0;"></div>
                        <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                            @csrf
                            <button type="submit"
                                    style="display: block; width: 100%; text-align: left; padding: 10px 14px; color: #ef4444; background: none; border: none; border-radius: 4px; font-size: 13px; cursor: pointer;"
                                    onmouseover="this.style.background='#374151'" onmouseout="this.style.background='transparent'">
                                <i class="bi bi-box-arrow-right" style="margin-right: 8px;"></i>ログアウト
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <a href="{{ route('login') }}" class="sidebar-link">
                    <i class="bi bi-box-arrow-in-right sidebar-icon"></i>
                    <span class="sidebar-label">ログイン</span>
                </a>
            @endauth
        </div>
    </aside>
    @yield('content')
    <script>
        const savedBg = localStorage.getItem('bgColor');
        const savedDark = localStorage.getItem('bgDark') === '1';
        if (savedBg) document.body.style.background = savedBg;
        if (savedDark) document.body.classList.add('dark-mode');

        document.querySelectorAll('.color-swatch').forEach(btn => {
            btn.addEventListener('click', () => {
                const c = btn.getAttribute('data-bg');
                const isDark = btn.getAttribute('data-dark') === 'true';
                document.body.style.background = c;
                document.body.classList.toggle('dark-mode', isDark);
                localStorage.setItem('bgColor', c);
                localStorage.setItem('bgDark', isDark ? '1' : '0');
            });
        });

        // サイドバー開閉
        const sidebarCollapsed = localStorage.getItem('sidebarCollapsed') === '1';
        if (sidebarCollapsed) document.body.classList.add('sidebar-collapsed');

        function toggleSidebar() {
            document.body.classList.toggle('sidebar-collapsed');
            const c = document.body.classList.contains('sidebar-collapsed');
            localStorage.setItem('sidebarCollapsed', c ? '1' : '0');
        }

        // <　ボタンとブランドクリックで開閉
        document.querySelector('.sidebar').addEventListener('click', (e) => {
            if (e.target.closest('a, button, form, .sidebar-link')) return;
            toggleSidebar();
        });

        // Ctrl + B で展開
        document.addEventListener('keydown', (e) => {
            if (e.ctrlKey && e.key.toLowerCase() === 'b') {
                e.preventDefault();
                toggleSidebar();
            }
        });

        // ユーザーメニュー開閉
        function toggleUserMenu(e) {
            e.stopPropagation();
            const menu = document.getElementById('user-menu');
            menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
        }
        document.addEventListener('click', (e) => {
            const menu = document.getElementById('user-menu');
            if (menu && !e.target.closest('.user-menu-wrap')) {
                menu.style.display = 'none';
            }
        });
    </script>
</body>
</html>