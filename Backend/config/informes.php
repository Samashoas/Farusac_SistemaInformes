<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Días de plazo para edición / eliminación de informes
    |--------------------------------------------------------------------------
    |
    | Define la cantidad de días continuos que tiene un docente o coordinador
    | para editar o eliminar un informe enviado antes de que quede bloqueado.
    |
    */
    'dias_bloqueo' => (int) env('INFORMES_DIAS_BLOQUEO', 3),
];
