<?php

// This file has been auto-generated by the Symfony Dependency Injection Component for internal use.

if (\class_exists(\ContainerHL9gAcy\srcApp_KernelDevDebugContainer::class, false)) {
    // no-op
} elseif (!include __DIR__.'/ContainerHL9gAcy/srcApp_KernelDevDebugContainer.php') {
    touch(__DIR__.'/ContainerHL9gAcy.legacy');

    return;
}

if (!\class_exists(srcApp_KernelDevDebugContainer::class, false)) {
    \class_alias(\ContainerHL9gAcy\srcApp_KernelDevDebugContainer::class, srcApp_KernelDevDebugContainer::class, false);
}

return new \ContainerHL9gAcy\srcApp_KernelDevDebugContainer([
    'container.build_hash' => 'HL9gAcy',
    'container.build_id' => '10f6e5ba',
    'container.build_time' => 1568710857,
], __DIR__.\DIRECTORY_SEPARATOR.'ContainerHL9gAcy');