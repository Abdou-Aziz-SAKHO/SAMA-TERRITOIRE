<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Logo de l'en-tête PDF
    |--------------------------------------------------------------------------
    |
    | Nom du fichier image (logo/mairie) à utiliser dans l'en-tête des
    | documents PDF générés. Le fichier doit être déposé dans
    | public/assets/img/.
    |
    | JPG/JPEG recommandé (affiché sans l'extension PHP GD). Les PNG
    | nécessitent l'extension GD activée.
    |
    | Ex. : 'logo-mairie-kaolack.jpg', 'ENTETE-PDF.jpg'…
    |
    */

    'logo_pdf' => env('SAMA_LOGO_PDF', 'SAMA_TERRIToire.jpg'),

];