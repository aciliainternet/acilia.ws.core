<?php

namespace WS\Core\Library\DataExport\Provider;

use WS\Core\Library\DataExport\DataExport;
use WS\Core\Library\DataExport\DataExportProviderInterface;

class CsvExportProvider implements DataExportProviderInterface
{
    public const EXPORT_FORMAT = 'csv';

    public static function getFormat(): string
    {
        return self::EXPORT_FORMAT;
    }

    public function export(DataExport $data): string
    {
        $pointer = fopen('php://temp', 'r+');
        if (false === $pointer) {
            return '';
        }

        // set CSV header row
        fputcsv($pointer, $data->getHeaders());

        // set CSV data rows
        foreach ($data->getData() as $row) {
            fputcsv($pointer, $row);
        }

        rewind($pointer);
        $data = stream_get_contents($pointer);
        fclose($pointer);

        return (false === $data) ? '' : $data;
    }

    public function headers(): array
    {
        return [
            ['name' => 'Content-Type', 'value' => 'text/csv']
        ];
    }
}
