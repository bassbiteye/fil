<?php

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

// This file has been auto-generated by the Symfony Dependency Injection Component for internal use.
// Returns the private 'vich_uploader.metadata_reader' shared service.

include_once $this->targetDirs[3].'/vendor/vich/uploader-bundle/Metadata/MetadataReader.php';

return $this->privates['vich_uploader.metadata_reader'] = new \Vich\UploaderBundle\Metadata\MetadataReader(($this->privates['vich_uploader.metadata_factory'] ?? $this->load('getVichUploader_MetadataFactoryService.php')));