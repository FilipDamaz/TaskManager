<?php

namespace App;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    public function getCacheDir(): string
    {
        $cacheDir = $_SERVER['KERNEL_CACHE_DIR'] ?? $_ENV['KERNEL_CACHE_DIR'] ?? null;
        if (is_string($cacheDir) && '' !== $cacheDir) {
            return $cacheDir;
        }

        return parent::getCacheDir();
    }
}
