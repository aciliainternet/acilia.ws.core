<?php

namespace WS\Core\Library\Navigation;

interface NavigationProviderInterface
{
    public function resolveNavigationPath(string $path): ?ResolvedPath;
}
