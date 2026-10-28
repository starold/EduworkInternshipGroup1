<?php

namespace App\Http\Requests\Admin;

use App\Enums\BookingStatus;
use App\Models\BookingItem;
use App\Models\Fine;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreFineRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isAdmin();
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('amount')) {
            $raw = $this->input('amount');
            $normalized = $this->normalizeAmount($raw);
            $this->merge(['amount' => $normalized]);
        }
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'booking_item_id' => ['required', 'integer', 'exists:booking_items,id'],
            'type' => ['required', 'string', 'in:late,damage,other'],
            'description' => ['required', 'string', 'min:3', 'max:1000'],
            'amount' => [
                'required',
                'numeric',
                'min:' . Fine::MIN_AMOUNT,
                'max:' . Fine::MAX_AMOUNT,
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'amount.min' => 'Nominal denda minimal adalah Rp10.000.',
            'amount.max' => 'Nominal denda maksimal adalah Rp100.000.000.',
            'amount.numeric' => 'Nominal denda harus berupa angka valid.',
            'amount.required' => 'Nominal denda wajib diisi.',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $itemId = $this->input('booking_item_id');
            if (! $itemId) {
                return;
            }

            $item = BookingItem::with('booking')->find($itemId);
            if (! $item || ! $item->booking) {
                $validator->errors()->add('booking_item_id', 'Item pesanan tidak ditemukan.');

                return;
            }

            if (in_array($item->booking->status, [BookingStatus::Rejected, BookingStatus::Cancelled])) {
                $validator->errors()->add('booking_item_id', 'Denda tidak dapat dibuat untuk pesanan yang ditolak atau dibatalkan.');
            }

            if (! $item->isReturned()) {
                $validator->errors()->add('booking_item_id', 'Denda manual hanya dapat dibuat setelah motor dinyatakan sudah kembali.');
            }

            if ($item->booking->status === BookingStatus::Completed) {
                $validator->errors()->add('booking_item_id', 'Denda tidak dapat dibuat untuk pesanan yang telah selesai.');
            }
        });
    }

    private function normalizeAmount(mixed $value): mixed
    {
        if (is_null($value)) {
            return null;
        }

        if (is_int($value) || is_float($value)) {
            return $value;
        }

        if (is_string($value)) {
            $cleaned = trim($value);
            $cleaned = preg_replace('/^rp\.?\s*/i', '', $cleaned);
            $cleaned = trim($cleaned);

            // Format "29.001,50" (Indonesian notation: dots for thousands, comma for decimals)
            if (preg_match('/^\d{1,3}(\.\d{3})+(,\d+)?$/', $cleaned)) {
                $cleaned = str_replace('.', '', $cleaned);
                $cleaned = str_replace(',', '.', $cleaned);
            }
            // Format "29,001.50" (English notation: commas for thousands, dot for decimals)
            elseif (preg_match('/^\d{1,3}(,\d{3})+(\.\d+)?$/', $cleaned)) {
                $cleaned = str_replace(',', '', $cleaned);
            }
            // Format "29.001" or "30.001" (Indonesian thousand dot separator)
            elseif (preg_match('/^\d{1,3}(\.\d{3})+$/', $cleaned)) {
                $cleaned = str_replace('.', '', $cleaned);
            }
            // Format "29,001" (Thousand comma separator)
            elseif (preg_match('/^\d{1,3}(,\d{3})+$/', $cleaned)) {
                $cleaned = str_replace(',', '', $cleaned);
            }
            // Decimal with comma without thousand separator: e.g. "29001,50"
            elseif (preg_match('/^\d+,\d+$/', $cleaned)) {
                $cleaned = str_replace(',', '.', $cleaned);
            }

            if (is_numeric($cleaned)) {
                $num = (float) $cleaned;

                return $num == (int) $num ? (int) $num : round($num, 2);
            }
        }

        return $value;
    }
}
