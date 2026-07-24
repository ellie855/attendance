@extends('layouts.app')

@section('title', 'プロフィール編集')

@section('content')
<div style="display: flex; flex-direction: column; gap: 20px;">

    {{-- プロフィール情報 (名前・メール) --}}
    <div class="card">
        <h2 class="card-title">プロフィール情報</h2>
        <p style="color: #666; font-size: 13px; margin-bottom: 20px;">名前とメールアドレスを変更できます。</p>

        <form method="POST" action="{{ route('profile.update') }}">
            @csrf
            @method('PATCH')

            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 13px; color: #555; margin-bottom: 6px;">名前</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required autofocus
                    style="width: 100%; padding: 8px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px;">
                @error('name')
                    <p style="color: #e74c3c; font-size: 12px; margin-top: 4px;">{{ $message }}</p>
                @enderror
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; font-size: 13px; color: #555; margin-bottom: 6px;">メールアドレス</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                    style="width: 100%; padding: 8px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px;">
                @error('email')
                    <p style="color: #e74c3c; font-size: 12px; margin-top: 4px;">{{ $message }}</p>
                @enderror
            </div>

            <div style="display: flex; align-items: center; gap: 12px;">
                <button type="submit"
                    style="padding: 8px 20px; background: #06c; color: white; border: none; border-radius: 6px; font-size: 14px; cursor: pointer;">保存</button>

                @if (session('status') === 'profile-updated')
                    <span id="profile-saved-msg" style="color: #047857; font-size: 13px;">✓ 保存しました</span>
                    <script>setTimeout(() => document.getElementById('profile-saved-msg')?.remove(), 2000);</script>
                @endif
            </div>
        </form>
    </div>

    {{-- パスワード変更 --}}
    <div class="card">
        <h2 class="card-title">パスワード変更</h2>
        <p style="color: #666; font-size: 13px; margin-bottom: 20px;">安全のため、長くて他で使ってないパスワードを設定してください。</p>

        <form method="POST" action="{{ route('password.update') }}">
            @csrf
            @method('PUT')

            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 13px; color: #555; margin-bottom: 6px;">現在のパスワード</label>
                <input type="password" name="current_password" autocomplete="current-password"
                    style="width: 100%; padding: 8px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px;">
                @error('current_password', 'updatePassword')
                    <p style="color: #e74c3c; font-size: 12px; margin-top: 4px;">{{ $message }}</p>
                @enderror
            </div>

            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 13px; color: #555; margin-bottom: 6px;">新しいパスワード</label>
                <input type="password" name="password" autocomplete="new-password"
                    style="width: 100%; padding: 8px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px;">
                @error('password', 'updatePassword')
                    <p style="color: #e74c3c; font-size: 12px; margin-top: 4px;">{{ $message }}</p>
                @enderror
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; font-size: 13px; color: #555; margin-bottom: 6px;">新しいパスワード(確認用)</label>
                <input type="password" name="password_confirmation" autocomplete="new-password"
                    style="width: 100%; padding: 8px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px;">
            </div>

            <div style="display: flex; align-items: center; gap: 12px;">
                <button type="submit"
                    style="padding: 8px 20px; background: #06c; color: white; border: none; border-radius: 6px; font-size: 14px; cursor: pointer;">変更</button>

                @if (session('status') === 'password-updated')
                    <span id="password-saved-msg" style="color: #047857; font-size: 13px;">✓ パスワードを変更しました</span>
                    <script>setTimeout(() => document.getElementById('password-saved-msg')?.remove(), 2000);</script>
                @endif
            </div>
        </form>
    </div>

    {{-- アカウント削除 --}}
    <div class="card" style="border: 1px solid #fecaca; background: #fef2f2;">
        <h2 class="card-title" style="color: #991b1b;">⚠ アカウント削除</h2>
        <p style="color: #666; font-size: 13px; margin-bottom: 20px;">
            アカウントを削除すると、勤怠データや申請履歴が **すべて完全に削除** されます。この操作は元に戻せません。
        </p>

        <form method="POST" action="{{ route('profile.destroy') }}"
              onsubmit="return confirm('本当にアカウントを削除しますか？この操作は元に戻せません。');">
            @csrf
            @method('DELETE')

            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 13px; color: #555; margin-bottom: 6px;">確認のためパスワードを入力</label>
                <input type="password" name="password" placeholder="パスワード"
                    style="width: 300px; padding: 8px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px;">
                @error('password', 'userDeletion')
                    <p style="color: #e74c3c; font-size: 12px; margin-top: 4px;">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit"
                style="padding: 8px 20px; background: #e74c3c; color: white; border: none; border-radius: 6px; font-size: 14px; cursor: pointer;">アカウントを削除する</button>
        </form>
    </div>

</div>
@endsection
