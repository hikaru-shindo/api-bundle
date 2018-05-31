<?php

declare(strict_types=1);

namespace Saikootau\ApiBundle;

use Saikootau\ApiBundle\DependencyInjection\SaikootauApiExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class ApiBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function getContainerExtension()
    {
        if (!$this->extension instanceof SaikootauApiExtension) {
            $this->extension = new SaikootauApiExtension();
        }

        return $this->extension;
    }
}
