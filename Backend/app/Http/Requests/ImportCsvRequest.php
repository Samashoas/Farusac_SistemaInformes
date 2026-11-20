<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class ImportCsvRequest extends FormRequest
{
    /**
     * Determina si el usuario autenticado está autorizado a realizar esta solicitud.
     */
    public function authorize(): bool
    {
        /** @var User|null $user */
        $user = $this->user();
        return $user !== null && $user->isAdministrador();
    }

    /**
     * Reglas de validación que se aplican a la solicitud.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'archivo_csv' => 'required|mimes:csv,txt|max:5120',
        ];
    }

    /**
     * Mensajes de error personalizados.
     */
    public function messages(): array
    {
        return [
            'archivo_csv.required' => 'Debe adjuntar un archivo CSV para realizar la importación.',
            'archivo_csv.mimes'    => 'El archivo seleccionado debe tener formato CSV o de texto (.csv, .txt).',
            'archivo_csv.max'      => 'El archivo no debe exceder el tamaño máximo de 5MB.',
        ];
    }
}

