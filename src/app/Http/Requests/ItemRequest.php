<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ItemRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['required'],
            'brand' => ['max:20'],
            'description' => ['required', 'max:255'],
            'image' => ['required', 'mimes:jpeg,png'],
            'content' => ['required'],
            'condition' => ['required'],
            'price' => ['required', 'integer', 'min:0'],
        ];
    }

    public function messages()
    {
        return [
            'name.required' => '商品名を入力してください',
            'brand.max' => 'ブランド名は20文字以内で入力してください',
            'description.required' => '商品の説明を入力してください',
            'description.max' => '商品の説明は255文字以内で入力してください',
            'image.required' => '商品画像を登録してください',
            'image.mines' => '「.png」または「.jpeg」形式でアップロードしてください',
            'content.required' => '商品のカテゴリーを選択してください',
            'condition.required' => '商品の状態を選択してください',
            'price.required' => '値段を入力してください',
            'price.integer' => '数値で入力してください',
            'price.min' => '０円以上で入力してください',
        ];
    }
}
