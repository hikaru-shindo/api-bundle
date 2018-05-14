<?php

declare(strict_types=1);

namespace Saikootau\ApiBundle;

use Saikootau\ApiBundle\DependencyInjection\ApiBundleExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class ApiBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function getContainerExtension()
    {
        if (!$this->extension instanceof ApiBundleExtension) {
            $this->extension = new ApiBundleExtension();
        }

        return $this->extension;
    }
}
