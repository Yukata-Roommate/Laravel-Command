<?php

namespace YukataRm\Laravel\Command;

use Illuminate\Console\Command;

use YukataRm\Laravel\Logger\Facade\Logger as LoggerFacade;
use YukataRm\Laravel\Logger\Logger;
use YukataRm\Logger\Enum\LogFormatEnum;

/**
 * Base Command
 * 
 * @package YukataRm\Laravel\Command
 */
abstract class BaseCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $message = $this->process();

            $this->logging(true, $message);
        } catch (\Throwable $exception) {
            $this->logging(false, $exception->getMessage());

            throw $exception;
        }
    }

    /**
     * run command process
     * 
     * @return string|array<string, mixed>
     */
    abstract protected function process(): string|array;

    /*----------------------------------------*
     * Logging
     *----------------------------------------*/

    /**
     * logging
     * 
     * @param bool $result
     * @param string|array<string, mixed> $message
     * @return void
     */
    protected function logging(bool $result, string|array $message): void
    {
        if (!$this->isLoggingEnable()) return;

        if (is_string($message)) $message = ["message" => $message];

        $logger = $this->logger();

        $contents = array_merge($this->loggingContents(), $message, ["result" => $result]);

        $logger->add($contents);

        $logger->logging();
    }

    /**
     * whether to enable logging
     * 
     * @return bool
     */
    protected function isLoggingEnable(): bool
    {
        return $this->configLoggingEnable();
    }

    /**
     * get logger instance
     * 
     * @return \YukataRm\Laravel\Logger\Logger
     */
    protected function logger(): Logger
    {
        $logger = LoggerFacade::info();

        $logger->setDirectory($this->configLoggingDirectory());

        $logger->setLogFormat(LogFormatEnum::MESSAGE);

        return $logger;
    }

    /**
     * get logging contents
     * 
     * @return array<string, mixed>
     */
    protected function loggingContents(): array
    {
        return [
            "datetime"  => date("Y-m-d H:i:s"),
            "class"     => get_class($this),
            "signature" => $this->signature,
        ];
    }

    /*----------------------------------------*
     * Config
     *----------------------------------------*/

    /**
     * get config or default
     * 
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    protected function config(string $key, mixed $default): mixed
    {
        return config("yukata-roommate.command.{$key}", $default);
    }

    /**
     * get config logging enable
     * 
     * @return bool
     */
    protected function configLoggingEnable(): bool
    {
        return $this->config("logging.enable", false);
    }

    /**
     * get config logging directory
     * 
     * @return string
     */
    protected function configLoggingDirectory(): string
    {
        return $this->config("logging.directory", "command");
    }
}
