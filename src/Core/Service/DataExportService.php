<?php

namespace WS\Core\Service;

use WS\Core\Library\DataExport\DataExport;
use WS\Core\Library\DataExport\DataExportProviderInterface;

class DataExportService
{
    protected array $dataExporters = [];

    public function addDataExporter(DataExportProviderInterface $dataExporter): void
    {
        $this->dataExporters[$dataExporter->getFormat()] = $dataExporter;
    }

    public function export(DataExport $data, string $format): string
    {
        if (!array_key_exists($format, $this->dataExporters)) {
            throw new \Exception(sprintf('Export format "%s" is not allowed', $format));
        }

        return $this->dataExporters[$format]->export($data);
    }

    public function headers(string $format): array
    {
        if (!array_key_exists($format, $this->dataExporters)) {
            throw new \Exception(sprintf('Export format "%s" is not allowed', $format));
        }

        return $this->dataExporters[$format]->headers();
    }
}
