<?php

namespace WS\Core\Service;

use Symfony\Component\DependencyInjection\Attribute\TaggedLocator;
use Symfony\Component\DependencyInjection\ServiceLocator;
use WS\Core\Library\DataExport\DataExport;
use WS\Core\Library\DataExport\DataExportProviderInterface;

class DataExportService
{
    public function __construct(
        #[TaggedLocator(DataExportProviderInterface::class, defaultIndexMethod: 'getFormat')]
        private ServiceLocator $exporters,
    ) {
    }

    public function export(DataExport $data, string $format): string
    {
        if (!$this->exporters->has($format)) {
            throw new \Exception(sprintf('Export format "%s" is not allowed', $format));
        }

        return $this->exporters->get($format)->export($data);
    }

    public function headers(string $format): array
    {
        if (!$this->exporters->has($format)) {
            throw new \Exception(sprintf('Export format "%s" is not allowed', $format));
        }

        return $this->exporters->get($format)->headers();
    }
}
