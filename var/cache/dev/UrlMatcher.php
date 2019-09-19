<?php

/**
 * This file has been auto-generated
 * by the Symfony Routing Component.
 */

return [
    false, // $matchHost
    [ // $staticRoutes
        '/api/partenaire' => [
            [['_route' => 'partenaire', '_controller' => 'App\\Controller\\PartenaireController::index'], null, null, null, false, false, null],
            [['_route' => 'partenaires', '_controller' => 'App\\Controller\\TransactionController::part'], null, ['GET' => 0], null, false, false, null],
        ],
        '/api/contrat' => [[['_route' => 'contrat', '_controller' => 'App\\Controller\\PartenaireController::contrat'], null, ['GET' => 0], null, false, false, null]],
        '/api/liste' => [[['_route' => 'liste', '_controller' => 'App\\Controller\\PartenaireController::show'], null, ['GET' => 0], null, false, false, null]],
        '/api/history' => [[['_route' => 'histor', '_controller' => 'App\\Controller\\PartenaireController::historique'], null, ['GET' => 0], null, false, false, null]],
        '/api/addP' => [[['_route' => 'add', '_controller' => 'App\\Controller\\PartenaireController::ajoutP'], null, ['POST' => 0], null, false, false, null]],
        '/api/depot' => [[['_route' => 'upda', '_controller' => 'App\\Controller\\PartenaireController::depot'], null, ['POST' => 0], null, false, false, null]],
        '/api/findCompte' => [[['_route' => 'findCompte', '_controller' => 'App\\Controller\\PartenaireController::findCompte'], null, ['POST' => 0], null, false, false, null]],
        '/api/findPar' => [[['_route' => 'findPartenaire', '_controller' => 'App\\Controller\\PartenaireController::findPartenaire'], null, ['POST' => 0], null, false, false, null]],
        '/api/addCompte' => [[['_route' => 'ajoutcompte', '_controller' => 'App\\Controller\\PartenaireController::ajouCompte'], null, ['POST' => 0], null, false, false, null]],
        '/api/findallCompte' => [[['_route' => 'findallCompte', '_controller' => 'App\\Controller\\PartenaireController::findallCompte'], null, ['GET' => 0], null, false, false, null]],
        '/api/listeSysteme' => [[['_route' => 'systeme', '_controller' => 'App\\Controller\\SecurityController::systeme'], null, ['GET' => 0], null, false, false, null]],
        '/api/listeUpart' => [[['_route' => 'userPart', '_controller' => 'App\\Controller\\SecurityController::userPart'], null, ['GET' => 0], null, false, false, null]],
        '/api/info' => [[['_route' => 'info', '_controller' => 'App\\Controller\\SecurityController::infoU'], null, ['GET' => 0], null, false, false, null]],
        '/api/countt' => [[['_route' => 'cnt', '_controller' => 'App\\Controller\\SecurityController::countT'], null, ['GET' => 0], null, false, false, null]],
        '/api/profile' => [[['_route' => 'list', '_controller' => 'App\\Controller\\SecurityController::index'], null, ['GET' => 0], null, false, false, null]],
        '/api/compte' => [[['_route' => 'compte', '_controller' => 'App\\Controller\\SecurityController::compt'], null, ['GET' => 0], null, false, false, null]],
        '/api/register' => [[['_route' => 'register', '_controller' => 'App\\Controller\\SecurityController::register'], null, ['POST' => 0], null, false, false, null]],
        '/api/login' => [[['_route' => 'login', '_controller' => 'App\\Controller\\SecurityController::login'], null, ['POST' => 0], null, false, false, null]],
        '/api/envoi' => [[['_route' => 'envoi', '_controller' => 'App\\Controller\\TransactionController::envoi'], null, null, null, false, false, null]],
        '/api/frais' => [[['_route' => 'fr', '_controller' => 'App\\Controller\\TransactionController::tarif'], null, ['POST' => 0], null, false, false, null]],
        '/api/verif' => [
            [['_route' => 'verifier', '_controller' => 'App\\Controller\\TransactionController::verif'], null, ['POST' => 0], null, false, false, null],
            [['_route' => 'verif', '_controller' => 'App\\Controller\\TransactionController::verifcode'], null, ['POST' => 0], null, false, false, null],
        ],
        '/api/retrait' => [[['_route' => 'retrait', '_controller' => 'App\\Controller\\TransactionController::retrait'], null, null, null, false, false, null]],
        '/api/use' => [[['_route' => 'user', '_controller' => 'App\\Controller\\TransactionController::user'], null, ['GET' => 0], null, false, false, null]],
        '/api/detailEnvoi' => [[['_route' => 'detailEnv', '_controller' => 'App\\Controller\\TransactionController::detailEnvi'], null, ['POST' => 0], null, false, false, null]],
        '/api/detailRetrait' => [[['_route' => 'detailRetrai', '_controller' => 'App\\Controller\\TransactionController::detailRetra'], null, ['POST' => 0], null, false, false, null]],
        '/api/detailEnvoiP' => [[['_route' => 'detailEnvP', '_controller' => 'App\\Controller\\TransactionController::detailEnviP'], null, ['POST' => 0], null, false, false, null]],
        '/api/detailRetraitP' => [[['_route' => 'detailRetraiP', '_controller' => 'App\\Controller\\TransactionController::detailRetraP'], null, ['POST' => 0], null, false, false, null]],
        '/api/login_check' => [[['_route' => 'api_login_check'], null, null, null, false, false, null]],
    ],
    [ // $regexpList
        0 => '{^(?'
                .'|/api/(?'
                    .'|bloquer/([^/]++)(*:31)'
                    .'|u(?'
                        .'|pdate(?'
                            .'|P/([^/]++)(*:60)'
                            .'|User/([^/]++)(*:80)'
                        .')'
                        .'|serid/([^/]++)(*:102)'
                    .')'
                    .'|etat/([^/]++)(*:124)'
                    .'|affecter/([^/]++)(*:149)'
                .')'
            .')/?$}sDu',
    ],
    [ // $dynamicRoutes
        31 => [[['_route' => 'par', '_controller' => 'App\\Controller\\PartenaireController::update'], ['id'], ['POST' => 0], null, false, true, null]],
        60 => [[['_route' => 'updatep', '_controller' => 'App\\Controller\\PartenaireController::modifP'], ['id'], ['PUT' => 0], null, false, true, null]],
        80 => [[['_route' => 'updateu', '_controller' => 'App\\Controller\\SecurityController::modifU'], ['id'], ['PUT' => 0], null, false, true, null]],
        102 => [[['_route' => 'getu', '_controller' => 'App\\Controller\\PartenaireController::users'], ['id'], ['GET' => 0], null, false, true, null]],
        124 => [[['_route' => 'u', '_controller' => 'App\\Controller\\SecurityController::update'], ['id'], ['PUT' => 0], null, false, true, null]],
        149 => [
            [['_route' => 'aff', '_controller' => 'App\\Controller\\SecurityController::affecter'], ['id'], ['POST' => 0], null, false, true, null],
            [null, null, null, null, false, false, 0],
        ],
    ],
    null, // $checkCondition
];
