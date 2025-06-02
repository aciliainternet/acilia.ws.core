<?php

namespace WS\Core\Library\CRUD;

use Symfony\Component\Form\AbstractType;

abstract class AbstractFilterType extends AbstractType
{
    #[\Override]
    public function getBlockPrefix(): string
    {
        return 'fe';
    }
}
