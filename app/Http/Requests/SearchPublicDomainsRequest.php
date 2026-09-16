<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SearchPublicDomainsRequest extends FormRequest
{
    protected $redirectRoute = 'domains.index';

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $search = $this->input('q');
        if (! is_string($search)) {
            return;
        }

        $search = mb_strtolower(trim($search));
        if (preg_match('~^https?://~i', $search)) {
            $host = parse_url($search, PHP_URL_HOST);
            $search = is_string($host) ? $host : $search;
        }

        $this->merge(['q' => rtrim($search, '/.')]);
    }

    public function rules(): array
    {
        return [
            'q' => ['nullable', 'string', 'max:255'],
            'page' => ['nullable', 'integer', 'min:1', 'max:100000'],
        ];
    }

    public function messages(): array
    {
        return [
            'q.string' => 'กรุณาพิมพ์ชื่อโดเมนที่ต้องการค้นหา',
            'q.max' => 'ชื่อโดเมนที่ค้นหาต้องไม่เกิน 255 ตัวอักษร',
            'page.*' => 'หมายเลขหน้าไม่ถูกต้อง กรุณาค้นหาใหม่',
        ];
    }
}
