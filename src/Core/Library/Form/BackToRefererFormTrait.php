<?php

namespace WS\Core\Library\Form;

use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Request;

trait BackToRefererFormTrait
{
    protected function addRefererField(FormBuilderInterface $builder, ?Request $request)
    {
        if ($request instanceof Request && $request->headers->has('referer')) {
            $builder->add('referer', HiddenType::class, [
                'mapped' => false,
                'data' => $request->headers->get('referer')
            ]);
        }
    }
}
