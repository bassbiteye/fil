<?php

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

// This file has been auto-generated by the Symfony Dependency Injection Component for internal use.
// Returns the private '.service_locator.jUXcOmJ' shared service.

return $this->privates['.service_locator.jUXcOmJ'] = new \Symfony\Component\DependencyInjection\Argument\ServiceLocator($this->getService, [
    'serializer' => ['services', 'serializer', 'getSerializerService.php', true],
    'userRepository' => ['privates', 'App\\Repository\\PartenaireRepository', 'getPartenaireRepositoryService.php', true],
], [
    'serializer' => '?',
    'userRepository' => 'App\\Repository\\PartenaireRepository',
]);
