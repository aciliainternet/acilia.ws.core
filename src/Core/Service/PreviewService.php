<?php

namespace WS\Core\Service;

use WS\Core\Library\Preview\Encryption;
use WS\Core\Library\Preview\PreviewInterface;

class PreviewService
{
    private array $supportedEntities = [];
    private bool $isPreview = false;

    public function __construct(private array $config)
    {
    }

    public function registerService(PreviewInterface $service): void
    {
        $this->supportedEntities[$service->getPreviewClassName()] = $service;
    }

    public function isSupported(string $className): bool
    {
        return isset($this->supportedEntities[$className]);
    }

    public function isEnabled(): bool
    {
        return $this->config['enabled'];
    }

    public function getPath(): string
    {
        return $this->config['path'];
    }

    public function getLocales(): array
    {
        return $this->config['locales'];
    }

    public function hash(string $type, array $options = []): string
    {
        $expire = (string) (time() + $this->config['ttl']);
        $data = [
            'type' => $type,
            'options' => $options,
            'expire' => $expire,
        ];

        $data = \strval(json_encode($data));
        $data = \strval(gzdeflate($data));
        $data = Encryption::encrypt($data, Encryption::SECRET, Encryption::ALGORITHM);
        $data = rawurlencode($data);

        return $data;
    }

    public function unHash(string $hash): array
    {
        $data = rawurldecode($hash);
        $data = Encryption::decrypt($data, Encryption::SECRET, Encryption::ALGORITHM);
        $data = \strval(gzinflate($data));
        $data = (array)json_decode($data, true);

        $this->checkPreview($data);

        return $data;
    }

    public function isPreview(): bool
    {
        return $this->isPreview;
    }

    private function checkPreview(array $data): void
    {
        if (!isset($data['expire'])) {
            return;
        }

        // check preview availability time
        $now = time();
        if ($now < $data['expire'] && ($now - $this->config['ttl'] < $data['expire'])) {
            $this->isPreview = true;
        }
    }
}
