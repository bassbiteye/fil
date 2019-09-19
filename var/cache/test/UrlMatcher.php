<?php

/**
 * This file has been auto-generated
 * by the Symfony Routing Component.
 */

return [
    false, // $matchHost
    [ // $staticRoutes
        '/api/partenaire' => [[['_route' => 'partenaire', '_controller' => 'App\\Controller\\PartenaireController::index'], null, null, null, false, false, null]],
        '/api/liste' => [
            [['_route' => 'liste', '_controller' => 'App\\Controller\\PartenaireController::show'], null, ['GET' => 0], null, false, false, null],
            [['_route' => 'list', '_controller' => 'App\\Controller\\SecurityController::index'], null, ['GET' => 0], null, false, false, null],
        ],
        '/api/history' => [[['_route' => 'histor', '_controller' => 'App\\Controller\\PartenaireController::historique'], null, ['GET' => 0], null, false, false, null]],
        '/api/addP' => [[['_route' => 'add', '_controller' => 'App\\Controller\\PartenaireController::ajoutP'], null, ['POST' => 0], null, false, false, null]],
        '/api/depot' => [[['_route' => 'upda', '_controller' => 'App\\Controller\\PartenaireController::depot'], null, ['POST' => 0], null, false, false, null]],
        '/api/addCompte' => [[['_route' => 'compte', '_controller' => 'App\\Controller\\PartenaireController::addCompte'], null, ['POST' => 0], null, false, false, null]],
        '/api/admin' => [[['_route' => 'security', '_controller' => 'App\\Controller\\SecurityController::acceuil'], null, null, null, false, false, null]],
        '/api/register' => [[['_route' => 'register', '_controller' => 'App\\Controller\\SecurityController::register'], null, ['POST' => 0], null, false, false, null]],
        '/api/login' => [[['_route' => 'login', '_controller' => 'App\\Controller\\SecurityController::login'], null, ['POST' => 0], null, false, false, null]],
        '/api/login_check' => [[['_route' => 'api_login_check'], null, null, null, false, false, null]],
    ],
    [ // $regexpList
        0 => '{^(?'
                .'|/api/(?'
                    .'|bloquer/([^/]++)(*:31)'
                    .'|etat/([^/]++)(*:51)'
                .')'
            .')/?$}sDu',
    ],
    [ // $dynamicRoutes
        31 => [[['_route' => 'par', '_controller' => 'App\\Controller\\PartenaireController::update'], ['id'], ['PUT' => 0], null, false, true, null]],
        51 => [
            [['_route' => 'user', '_controller' => 'App\\Controller\\SecurityController::update'], ['id'], ['PUT' => 0], null, false, true, null],
            [null, null, null, null, false, false, 0],
        ],
    ],
    null, // $checkCondition
];
