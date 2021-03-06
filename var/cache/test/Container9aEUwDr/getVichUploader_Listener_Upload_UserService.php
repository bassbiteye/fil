<?php

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

// This file has been auto-generated by the Symfony Dependency Injection Component for internal use.
// Returns the private 'vich_uploader.listener.upload.User' shared service.

include_once $this->targetDirs[3].'/vendor/doctrine/event-manager/lib/Doctrine/Common/EventSubscriber.php';
include_once $this->targetDirs[3].'/vendor/vich/uploader-bundle/EventListener/Doctrine/BaseListener.php';
include_once $this->targetDirs[3].'/vendor/vich/uploader-bundle/EventListener/Doctrine/UploadListener.php';
include_once $this->targetDirs[3].'/vendor/vich/uploader-bundle/Adapter/AdapterInterface.php';
include_once $this->targetDirs[3].'/vendor/vich/uploader-bundle/Adapter/ORM/DoctrineORMAdapter.php';

return $this->privates['vich_uploader.listener.upload.User'] = new \Vich\UploaderBundle\EventListener\Doctrine\UploadListener('User', ($this->privates['vich_uploader.adapter.orm'] ?? ($this->privates['vich_uploader.adapter.orm'] = new \Vich\UploaderBundle\Adapter\ORM\DoctrineORMAdapter())), ($this->privates['vich_uploader.metadata_reader'] ?? $this->load('getVichUploader_MetadataReaderService.php')), ($this->services['vich_uploader.upload_handler'] ?? $this->load('getVichUploader_UploadHandlerService.php')));
