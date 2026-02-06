<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class ProductFilterRequest extends FormRequest
{

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'q' => ['nullable', 'string', 'max:255'],
            'price_from' => ['nullable', 'numeric', 'min:0'],
            'price_to' => ['nullable', 'numeric', 'min:0'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'in_stock' => ['nullable', 'boolean'],
            'rating_from' => ['nullable', 'numeric', 'min:0', 'max:5'],
            'sort' => ['nullable', 'string', Rule::in(['price_asc', 'price_desc', 'rating_desc', 'newest'])],
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }

    /**
     * @return array|string[]
     */
    public function messages(): array
    {
        return [
            'q.string' => __('validation.q.string'),
            'q.max' => __('validation.q.max'),
            'price_from.numeric' => __('validation.price_from.numeric'),
            'price_from.min' => __('validation.price_from.min'),
            'price_to.numeric' => __('validation.price_to.numeric'),
            'price_to.min' => __('validation.price_to.min'),
            'category_id.integer' => __('validation.category_id.integer'),
            'category_id.exists' => __('validation.category_id.exists'),
            'in_stock.boolean' => __('validation.in_stock.boolean'),
            'rating_from.numeric' => __('validation.rating_from.numeric'),
            'rating_from.min' => __('validation.rating_from.min'),
            'rating_from.max' => __('validation.rating_from.max'),
            'sort.string' => __('validation.sort.string'),
            'sort.in' => __('validation.sort.in'),
            'page.integer' => __('validation.page.integer'),
            'page.min' => __('validation.page.min'),
            'per_page.integer' => __('validation.per_page.integer'),
            'per_page.min' => __('validation.per_page.min'),
            'per_page.max' => __('validation.per_page.max'),
        ];
    }

    /**
     * @return array|string[]
     */
    public function attributes(): array
    {
        return [
            'q' => __('attributes.q'),
            'price_from' => __('attributes.price_from'),
            'price_to' => __('attributes.price_to'),
            'category_id' => __('attributes.category_id'),
            'in_stock' => __('attributes.in_stock'),
            'rating_from' => __('attributes.rating_from'),
            'sort' => __('attributes.sort'),
            'page' => __('attributes.page'),
            'per_page' => __('attributes.per_page'),
        ];
    }

    /**
     * @return void
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('in_stock')) {
            $this->merge([
                'in_stock' => filter_var($this->in_stock, FILTER_VALIDATE_BOOLEAN),
            ]);
        }

    }

    /**
     * @param Validator $validator
     * @return mixed
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'message' => __('validation.failed'),
                'errors' => $validator->errors(),
            ], 422)
        );
    }
}
