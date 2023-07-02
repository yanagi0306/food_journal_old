<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateParentExpenseCategoryRequest extends FormRequest
{
    /**
     * リクエストの認証ルールを決定します。
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // ここではすべてのユーザーがこのリクエストを実行できるようにします。
        // 必要に応じて認証ロジックを追加してください。
        return true;
    }

    /**
     * リクエストに適用するバリデーションルールを取得します。
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'cat_name' => ['required', 'max:10'],
        ];
    }
}