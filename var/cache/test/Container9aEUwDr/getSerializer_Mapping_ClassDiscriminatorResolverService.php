<?php

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

// This file has been auto-generated by the Symfony Dependency Injection Component for internal use.
// Returns the private 'serializer.mapping.class_discriminator_resolver' shared service.

include_once $this->targetDirs[3].'/vendor/symfony/serializer/Mapping/ClassDiscriminatorResolverInterface.php';
include_once $this->targetDirs[3].'/vendor/symfony/serializer/Mapping/ClassDiscriminatorFromClassMetadata.php';

return $this->privates['serializer.mapping.class_discriminator_resolver'] = new \Symfony\Component\Serializer\Mapping\ClassDiscriminatorFromClassMetadata(($this->privates['serializer.mapping.class_metadata_factory'] ?? $this->load('getSerializer_Mapping_ClassMetadataFactoryService.php')));