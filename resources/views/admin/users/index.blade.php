@extends('layouts.app')

@section('title', 'ユーザー管理')

@section('content')

@if (session('success'))
    <div id="flash" class="flash-success" style="background: #d4edda; color: #155724; padding: 12px; margin-bottom: 16px; border-radius: 6px; text-align: center; transition: opacity 0.5s;">
        ✓ {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div style="background: #f8d7da; color: #721c24; padding: 12px; margin-bottom: 16px; border-radius: 6px; text-align: center;">
        ⚠ {{ session('error') }}
    </div>
@endif

<div class="card">
    <h1 class="card-title">ユーザー管理</h1>

@php
    $userCount = \App\Models\User::count();
    $isPro = auth()->user()->role === 'admin' || auth()->user()->subscribed('default');
    $freeLimit = config('billing.free_user_limit');
@endphp

<div style="background: #eef2ff; border: 1px solid #c7d2fe; border-radius: 6px; padding: 12px 16px; margin-top: 12px; font-size: 13px;">
    <i class="bi bi-people-fill"></i>
    登録済み:<strong>{{ $userCount }}人</strong> /
    @if ($isPro)
        <span style="color: #059669; font-weight: bold;">無制限(Pro / 管理者)</span>
    @else
        <span style="color: #dc2626;">上限: {{ $freeLimit }}人(Freeプラン)</span>
        @if ($userCount >= $freeLimit)
            <span style="margin-left: 12px; color: #dc2626; font-weight: bold;">⚠上限に達しています</span>
        @endif
    @endif
</div>

{{-- CSVインポートフォーム --}}
<div style="background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 6px; padding: 16px; margin-top: 16px;">
    <div style="font-weight: bold; margin-bottom: 8px;">
        <i class="bi bi-upload"></i>CSVでユーザー一括登録
    </div>
    <div style="font-size: 12px; color: #666; margin-bottom: 12px;">
        フォーマット: <code>name,email,password,role</code><br>
        例: <code>田中太郎,tanaka@example.com,password123,user</code>
    </div>
    <form action="{{ route('admin.users.import') }}" method="POST" enctype="multipart/form-data" style="display: flex; gap: 8px; align-items: center;">
        @csrf
        <input type="file" name="csv" accept=".csv" required
              style="padding: 6px; border: 1px solid #ddd; border-radius: 4px; font-size: 13px;">
        <button type="submit"
                style="padding: 6px 16px; background: #10b981; color: white; border: none; border-radius: 4px; font-size: 13px; cursor: pointer;">
            インポート
        </button>
    </form>
    @if (session('import_errors'))
        <div style="margin-top: 10px; color: #991b1b; font-size: 12px">
            スキップされた行: 
            <ul style="margin: 4px 0 0 20px;">
                @foreach (session('import_errors') as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif
</div>


    <table style="width: 100%; margin-top: 16px; border-collapse: collapse;">
        <thead>
            <tr style="border-bottom: 2px solid #ddd;">
                <th style="padding: 10px; text-align: left; font-size: 13px;">ID</th>
                <th style="padding: 10px; text-align: left; font-size: 13px;">名前</th>
                <th style="padding: 10px; text-align: left; font-size: 13px;">メール</th>
                <th style="padding: 10px; text-align: left; font-size: 13px;">役割</th>
                <th style="padding: 10px; text-align: right; font-size: 13px;">操作</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($users as $user)
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 10px;">{{ $user->id }}</td>
                    <td style="padding: 10px;">{{ $user->name }}</td>
                    <td style="padding: 10px; color: #666; font-size: 13px;">{{ $user->email }}</td>
                    <td style="padding: 10px;">
                        <form action="{{ route('admin.users.update-role', $user) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('PUT')
                            <select name="role" onchange="this.form.submit()" style="padding: 4px 8px; font-size: 13px;">
                                <option value="user"  @selected($user->role === 'user' || $user->role === null)>一般</option>
                                <option value="admin" @selected($user->role === 'admin')>管理者</option>
                            </select>
                        </form>
                    </td>
                    <td style="padding: 10px; text-align: right;">
                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST"
                              onsubmit="return confirm('本当に {{ $user->name }} を削除しますか?');" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" style="padding: 4px 12px; background: #e74c3c; color: white; border: none; border-radius: 4px; font-size: 12px; cursor: pointer;">
                                削除
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<script>
    const flash = document.getElementById('flash');
    if (flash) {
        setTimeout(() => flash.style.opacity = '0', 1000);
        setTimeout(() => flash.remove(), 1500);
    }
</script>

@endsection