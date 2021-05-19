<?php

namespace Modules\Application\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CommentStoreRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'transaction_id' => [
                'required',
                Rule::exists('transactions', 'id')
                    ->where(function ($query) {
                        $query->where('user_id', $this->user()->id);
                    })
            ],
            'message'        => 'required|min:5|max:200',
        ];
    }

}
