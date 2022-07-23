<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SendReviewRequest extends FormRequest
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
        // $this->route('order') 可以获得当前路由对应的订单对象，
        // Rule::exists() 判断用户提交的 ID 是否属于此订单。
        return [
            'reviews' => ['required','array'],
            'reviews.*.id' => [
                'required',
                Rule::exists('order_items', 'id')->where('order_id', $this->route('order')->id),
            ],
            'reviews.*.rating' => ['required','integer'],
            'reviews.*.review' => ['required'],
        ];
    }

    /**
     * 返回 提示信息
     *
     */
    public function attributes()
    {
        return [
            'reviews.*.rating' => '评分',
            'reviews.*.review' => '评价',
        ];
    }
}
