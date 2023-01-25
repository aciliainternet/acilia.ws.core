<?php

namespace WS\Core\Library\DataExport;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag()]
interface DataExportProviderInterface
{
    public static function getFormat(): string;

    public function export(DataExport $data): string;

    public function headers(): array;
}
