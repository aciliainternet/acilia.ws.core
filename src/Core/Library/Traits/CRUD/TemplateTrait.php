<?php

namespace WS\Core\Library\Traits\CRUD;

trait TemplateTrait
{
    protected function useCRUDTemplate(string $template): bool
    {
        $crudTemplates = [
            'index.html.twig',
            'show.html.twig',
            'ajax-show.html.twig'
        ];

        if (in_array($template, $crudTemplates)) {
            return true;
        }

        return false;
    }

    protected function getTemplate(string $template): string
    {
        $routePrefix = '';
        $controllerClass = get_class($this);
        $classPath = explode('\\', $controllerClass);

        if ($classPath[0] === 'WS') {
            $controllerName = strtolower(str_replace('Controller', '', $classPath[4]));
            $routePrefix = sprintf(
                '@%s%s/%s/%s',
                $classPath[0],
                $classPath[1],
                strtolower($classPath[3]),
                $controllerName
            );
        } elseif ($classPath[0] === 'App') {
            $controllerName = strtolower(str_replace('Controller', '', $classPath[3]));
            $routePrefix = sprintf(
                'cms/%s',
                $controllerName
            );
        }

        $templateFile = sprintf('%s/%s', $routePrefix, $template);
        if ($this->useCRUDTemplate($template)) {
            /** @var \Twig\Environment */
            $twig = $this->container->get('twig');
            if (!$twig->getLoader()->exists($templateFile)) {
                $templateFile = sprintf('@WSCore/cms/crud/%s', $template);
            }
        }

        return $templateFile;
    }
}
