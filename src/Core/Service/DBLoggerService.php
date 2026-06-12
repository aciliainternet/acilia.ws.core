<?php

namespace WS\Core\Service;

use Doctrine\ORM\EntityManagerInterface;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Level;
use Monolog\LogRecord;

class DBLoggerService extends AbstractProcessingHandler
{
    public function __construct(
        protected EntityManagerInterface $em,
        int | string | Level $level = Level::Debug,
        bool $bubble = true
    ) {
        parent::__construct($level, $bubble);
    }

    #[\Override]
    protected function write(LogRecord $log): void
    {
        $conn = $this->em->getConnection();
        $record = $log->toArray();
        $sql = 'INSERT INTO ws_log (log_id, log_channel, log_message, log_level, log_datetime) VALUES (NULL, ?, ?, ?, ?)';
        $stmt = $conn->prepare($sql);

        $stmt->bindValue(1, $record['channel']);
        $stmt->bindValue(2, $record['message']);
        $stmt->bindValue(3, $record['level_name']);
        $stmt->bindValue(4, $record['datetime']->format('Y-m-d H:i:s'));

        $stmt->executeQuery();
    }
}
