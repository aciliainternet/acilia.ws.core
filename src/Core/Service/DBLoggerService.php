<?php

namespace WS\Core\Service;

use Doctrine\ORM\EntityManagerInterface;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;

class DBLoggerService extends AbstractProcessingHandler
{
    protected EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em, string $level = Logger::DEBUG, bool $bubble = true)
    {
        $this->em = $em;
        parent::__construct($level, $bubble);
    }

    protected function write(array $record): void
    {
        $connection = $this->em->getConnection();
        if ($connection) {
            $sql = 'INSERT INTO ws_log (log_id, log_channel, log_message, log_level, log_datetime) VALUES (NULL, ?, ?, ?, ?)';
            $stmt = $connection->prepare($sql);

            $stmt->bindValue(1, $record['channel']);
            $stmt->bindValue(2, $record['message']);
            $stmt->bindValue(3, $record['level_name']);
            $stmt->bindValue(4, $record['datetime']->format('Y-m-d H:i:s'));

            $stmt->execute();
        }
    }
}
