<?php

// This file has been auto-generated by the Symfony Dependency Injection Component for internal use.

if (\class_exists(\ContainerIZNAFcY\srcApp_KernelTestDebugContainer::class, false)) {
    // no-op
} elseif (!include __DIR__.'/ContainerIZNAFcY/srcApp_KernelTestDebugContainer.php') {
    touch(__DIR__.'/ContainerIZNAFcY.legacy');

    return;
}

if (!\class_exists(srcApp_KernelTestDebugContainer::class, false)) {
    \class_alias(\ContainerIZNAFcY\srcApp_KernelTestDebugContainer::class, srcApp_KernelTestDebugContainer::class, false);
}

return new \ContainerIZNAFcY\srcApp_KernelTestDebugContainer([
    'container.build_hash' => 'IZNAFcY',
    'container.build_id' => '54f89e1c',
    'container.build_time' => 1565180944,
], __DIR__.\DIRECTORY_SEPARATOR.'ContainerIZNAFcY');
