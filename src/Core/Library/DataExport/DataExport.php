<?php

namespace WS\Core\Library\DataExport;

class DataExport
{
    protected array $headers;
    protected array $data;

    public function __construct(array $headers, array $data)
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
