<?php

return [
    'boolean' => 'El campo :attribute debe ser verdadero o falso.',
    'confirmed' => 'La confirmación de :attribute no coincide.',
    'current_password' => 'La contraseña es incorrecta.',
    'email' => 'El campo :attribute debe ser un correo electrónico válido.',
    'exists' => 'El valor seleccionado para :attribute no es válido.',
    'image' => 'El campo :attribute debe ser una imagen.',
    'max' => [
        'file' => 'El archivo :attribute no debe superar los :max kilobytes.',
        'string' => 'El campo :attribute no debe tener más de :max caracteres.',
    ],
    'mimes' => 'El campo :attribute debe ser un archivo de tipo: :values.',
    'min' => [
        'string' => 'El campo :attribute debe tener al menos :min caracteres.',
    ],
    'required' => 'El campo :attribute es obligatorio.',
    'string' => 'El campo :attribute debe ser texto.',
    'unique' => 'Ya existe un registro con ese valor de :attribute.',
    'uploaded' => 'No se pudo subir :attribute.',
    'password' => [
        'letters' => 'La contraseña debe contener al menos una letra.',
        'mixed' => 'La contraseña debe contener mayúsculas y minúsculas.',
        'numbers' => 'La contraseña debe contener al menos un número.',
        'symbols' => 'La contraseña debe contener al menos un símbolo.',
    ],

    'attributes' => [
        'name' => 'nombre',
        'email' => 'email',
        'password' => 'contraseña',
        'role_id' => 'rol',
        'member_id' => 'matriculado',
        'image' => 'imagen',
        'registration_number' => 'N° de matrícula',
        'first_name' => 'nombre',
        'last_name' => 'apellido',
        'dni' => 'DNI',
        'address' => 'dirección',
        'phone' => 'teléfono',
        'city_id' => 'ciudad',
        'province_id' => 'provincia',
        'account_status_id' => 'estado de cuenta',
        'description' => 'descripción',
    ],
];
