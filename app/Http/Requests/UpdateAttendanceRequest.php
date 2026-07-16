<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAttendanceRequest extends FormRequest
{
    /**
     * 認可: このリクエストを実行できるかチェック
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('attendance'));
    }

    /**
     * バリデーションルール
     */
    public function rules(): array
    {
        return [
            'type'       => 'required|in:clock_in,clock_out',
            'created_at' => 'required|date',
        ];
    }

    /**
     * 日本語エラーメッセージ (おまけ)
     */
    public function messages(): array
    {
        return [
            'type.required'       => '種別を選択してください',
            'type.in'             => '種別は出勤か退勤のどちらかを選んでください',
            'created_at.required' => '日時を入力してください',
            'created_at.date'     => '日時の形式が正しくありません',
        ];
    }
}