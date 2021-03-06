<?php

// This file has been auto-generated by the Symfony Routing Component.

return [
    'partenaire' => [[], ['_controller' => 'App\\Controller\\PartenaireController::index'], [], [['text', '/api/partenaire']], [], []],
    'contrat' => [[], ['_controller' => 'App\\Controller\\PartenaireController::contrat'], [], [['text', '/api/contrat']], [], []],
    'liste' => [[], ['_controller' => 'App\\Controller\\PartenaireController::show'], [], [['text', '/api/liste']], [], []],
    'histor' => [[], ['_controller' => 'App\\Controller\\PartenaireController::historique'], [], [['text', '/api/history']], [], []],
    'add' => [[], ['_controller' => 'App\\Controller\\PartenaireController::ajoutP'], [], [['text', '/api/addP']], [], []],
    'par' => [['id'], ['_controller' => 'App\\Controller\\PartenaireController::update'], [], [['variable', '/', '[^/]++', 'id', true], ['text', '/api/bloquer']], [], []],
    'upda' => [[], ['_controller' => 'App\\Controller\\PartenaireController::depot'], [], [['text', '/api/depot']], [], []],
    'findCompte' => [[], ['_controller' => 'App\\Controller\\PartenaireController::findCompte'], [], [['text', '/api/findCompte']], [], []],
    'findPartenaire' => [[], ['_controller' => 'App\\Controller\\PartenaireController::findPartenaire'], [], [['text', '/api/findPar']], [], []],
    'ajoutcompte' => [[], ['_controller' => 'App\\Controller\\PartenaireController::ajouCompte'], [], [['text', '/api/addCompte']], [], []],
    'updatep' => [['id'], ['_controller' => 'App\\Controller\\PartenaireController::modifP'], [], [['variable', '/', '[^/]++', 'id', true], ['text', '/api/updateP']], [], []],
    'getu' => [['id'], ['_controller' => 'App\\Controller\\PartenaireController::users'], [], [['variable', '/', '[^/]++', 'id', true], ['text', '/api/userid']], [], []],
    'findallCompte' => [[], ['_controller' => 'App\\Controller\\PartenaireController::findallCompte'], [], [['text', '/api/findallCompte']], [], []],
    'systeme' => [[], ['_controller' => 'App\\Controller\\SecurityController::systeme'], [], [['text', '/api/listeSysteme']], [], []],
    'userPart' => [[], ['_controller' => 'App\\Controller\\SecurityController::userPart'], [], [['text', '/api/listeUpart']], [], []],
    'info' => [[], ['_controller' => 'App\\Controller\\SecurityController::infoU'], [], [['text', '/api/info']], [], []],
    'cnt' => [[], ['_controller' => 'App\\Controller\\SecurityController::countT'], [], [['text', '/api/countt']], [], []],
    'list' => [[], ['_controller' => 'App\\Controller\\SecurityController::index'], [], [['text', '/api/profile']], [], []],
    'compte' => [[], ['_controller' => 'App\\Controller\\SecurityController::compt'], [], [['text', '/api/compte']], [], []],
    'register' => [[], ['_controller' => 'App\\Controller\\SecurityController::register'], [], [['text', '/api/register']], [], []],
    'login' => [[], ['_controller' => 'App\\Controller\\SecurityController::login'], [], [['text', '/api/login']], [], []],
    'u' => [['id'], ['_controller' => 'App\\Controller\\SecurityController::update'], [], [['variable', '/', '[^/]++', 'id', true], ['text', '/api/etat']], [], []],
    'aff' => [['id'], ['_controller' => 'App\\Controller\\SecurityController::affecter'], [], [['variable', '/', '[^/]++', 'id', true], ['text', '/api/affecter']], [], []],
    'updateu' => [['id'], ['_controller' => 'App\\Controller\\SecurityController::modifU'], [], [['variable', '/', '[^/]++', 'id', true], ['text', '/api/updateUser']], [], []],
    'envoi' => [[], ['_controller' => 'App\\Controller\\TransactionController::envoi'], [], [['text', '/api/envoi']], [], []],
    'fr' => [[], ['_controller' => 'App\\Controller\\TransactionController::tarif'], [], [['text', '/api/frais']], [], []],
    'verifier' => [[], ['_controller' => 'App\\Controller\\TransactionController::verif'], [], [['text', '/api/verif']], [], []],
    'retrait' => [[], ['_controller' => 'App\\Controller\\TransactionController::retrait'], [], [['text', '/api/retrait']], [], []],
    'partenaires' => [[], ['_controller' => 'App\\Controller\\TransactionController::part'], [], [['text', '/api/partenaire']], [], []],
    'user' => [[], ['_controller' => 'App\\Controller\\TransactionController::user'], [], [['text', '/api/use']], [], []],
    'verif' => [[], ['_controller' => 'App\\Controller\\TransactionController::verifcode'], [], [['text', '/api/verif']], [], []],
    'detailEnv' => [[], ['_controller' => 'App\\Controller\\TransactionController::detailEnvi'], [], [['text', '/api/detailEnvoi']], [], []],
    'detailRetrai' => [[], ['_controller' => 'App\\Controller\\TransactionController::detailRetra'], [], [['text', '/api/detailRetrait']], [], []],
    'detailEnvP' => [[], ['_controller' => 'App\\Controller\\TransactionController::detailEnviP'], [], [['text', '/api/detailEnvoiP']], [], []],
    'detailRetraiP' => [[], ['_controller' => 'App\\Controller\\TransactionController::detailRetraP'], [], [['text', '/api/detailRetraitP']], [], []],
    'api_login_check' => [[], [], [], [['text', '/api/login_check']], [], []],
];
