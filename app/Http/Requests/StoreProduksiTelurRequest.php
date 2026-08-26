<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProduksiTelurRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'kandang_id' => 'required|exists:kandang,id',
            'tanggal' => 'required|date',
            'karpet' => 'required|integer|min:0',
            'sisa' => 'nullable|integer|min:0|max:29',
            'jumlah_butir' => 'required|integer|min:1',
            'shift' => [
                'required', 'string', 'max:20',
                Rule::unique('produksi_telur')
                    ->where('kandang_id', $this->kandang_id)
                    ->where('tanggal', $this->tanggal)
                    ->ignore($this->route('produksi')),
            ],
            'foto' => 'nullable',
            'foto.*' => 'image|max:2048',
            'foto_base64' => 'nullable|array',
            'foto_base64.*' => 'string',
        ];
    }

    public function messages(): array
    {
        return [
            'kandang_id.required' => 'Kandang wajib dipilih.',
            'jumlah_butir.required' => 'Jumlah butir wajib diisi.',
            'jumlah_butir.min' => 'Jumlah butir minimal 1.',
            'shift.unique' => 'Shift ini sudah dicatat untuk kandang dan tanggal tersebut.',
        ];
    }
}
