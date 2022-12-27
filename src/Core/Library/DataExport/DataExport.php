<?php

namespace WS\Core\Library\DataExport;

class DataExport
{
    public function __construct(protected array $headers, protected array $data)
    {
        $this->headers = $headers;
        $this->data = $data;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getData(): array
    {
        return $this->data;
    }
}
