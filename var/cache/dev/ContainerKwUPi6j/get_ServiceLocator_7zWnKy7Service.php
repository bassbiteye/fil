<?php

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

// This file has been auto-generated by the Symfony Dependency Injection Component for internal use.
// Returns the private '.service_locator.7zWnKy7' shared service.

return $this->privates['.service_locator.7zWnKy7'] = new \Symfony\Component\DependencyInjection\Argument\ServiceLocator($this->getService, [
    'entityManager' => ['services', 'doctrine.orm.default_entity_manager', 'getDoctrine_Orm_DefaultEntityManagerService.php', true],
    'serializer' => ['services', 'serializer', 'getSerializerService.php', true],
    'validator' => ['services', 'validator', 'getValidatorService.php', true],
], [
    'entityManager' => '?',
    'serializer' => '?',
    'validator' => '?',
]);