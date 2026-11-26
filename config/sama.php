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

    /*
    |--------------------------------------------------------------------------
    | Projets concernés (formulaire commentaires & plaintes)
    |--------------------------------------------------------------------------
    |
    | Liste des projets proposés dans le menu déroulant « Projet concerné »
    | du formulaire public. Chaque intitulé doit être unique ; l'entrée
    | « Autre » est ajoutée automatiquement pour permettre la saisie libre.
    |
    */

    'projets' => [
        'PACASEN',
        'PAIJEF',
        'PDZP',
        'PNDL',
        'PTCS/Enabel',
        'SEEN SUUF',
    ],

    /*
    |--------------------------------------------------------------------------
    | Communes de la région de Kaolack (formulaire commentaires & plaintes)
    |--------------------------------------------------------------------------
    |
    | Liste statique des communes / localités de la région de Kaolack
    | (départements de Guinguinéo, Kaolack et Nioro du Rip), proposées dans
    | le menu déroulant « Commune » du formulaire public.
    |
    */

    'communes' => [
        // Guinguinéo
        'Fass',
        'Guinguinéo',
        'Mboss',
        'Dara Mboss',
        'Gagnick',
        'Khelcom Birame',
        'Mbadakhoune',
        'Ndiago',
        'Ngathie Naoude',
        'Nguélou',
        'Ourour',
        'Panal Ouolof',
        // Kaolack
        'Gandiaye',
        'Kahone',
        'Kaolack',
        'Ndoffane',
        'Sibassor',
        'Dya',
        'Keur Baka',
        'Keur Socé',
        'Latmingué',
        'Ndiaffate',
        'Niébel',
        'Ndiédieng',
        'Thiare',
        'Thiomby',
        // Nioro du Rip
        'Keur Madiabel',
        'Nioro du Rip',
        'Dabaly',
        'Darou Salam',
        'Gainte Kaye',
        'K. Mandongo',
        'Kayemor',
        'Keur Maba Diakhou',
        'Médina-Sabakh',
        'Ndrame Escale',
        'Ngayène',
        'Paoskoto',
        'Porokhane',
        'Taïba Niassène',
        'Wack Ngouna',
    ],

];
