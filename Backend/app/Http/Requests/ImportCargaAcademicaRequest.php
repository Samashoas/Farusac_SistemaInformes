<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class ImportCargaAcademicaRequest extends FormRequest
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
            'archivo_csv' => 'required|file|max:10240',
            'carrera'     => 'nullable|string|max:100',
            'anio'        => 'nullable|integer',
            'semestre'    => 'nullable|string|max:50',
        ];
    }

    /**
     * Mensajes de error personalizados.
     */
    public function messages(): array
    {
        return [
            'archivo_csv.required' => 'Debe adjuntar el archivo CSV con la carga académica.',
            'archivo_csv.file'     => 'El archivo seleccionado no es válido.',
            'archivo_csv.max'      => 'El archivo no debe exceder el tamaño máximo de 10MB.',
        ];
    }
}

