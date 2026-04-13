<?php

namespace App\Logging;

use App\Models\ApplicationLog;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Level;
use Monolog\LogRecord;

class DatabaseLogHandler extends AbstractProcessingHandler
{
    public function __construct(
        protected ?string $connection = null,
        protected string $table = 'logs',
        int|string|Level $level = Level::Debug,
        bool $bubble = true,
    ) {
        parent::__construct($level, $bubble);
    }

    protected function write(LogRecord $record): void
    {
        $entry = new ApplicationLog;

        if ($this->connection !== null && $this->connection !== '') {
            $entry->setConnection($this->connection);
        }

        $entry->setTable($this->table);

        $entry->fill([
            'channel' => $record->channel,
            'level' => $record->level->toPsrLogLevel(),
            'message' => $record->message,
            'context' => empty($record->context) ? null : $record->context,
            'extra' => empty($record->extra) ? null : $record->extra,
            'created_at' => $record->datetime,
        ])->save();
    }
}
