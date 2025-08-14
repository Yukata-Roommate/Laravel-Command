<?php

namespace YukataRm\Laravel\Command;

use Illuminate\Console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Validator as ValidatorFacade;
use Symfony\Component\Console\Exception\InvalidArgumentException;

use YukataRm\Time\Interfaces\TimerInterface;
use YukataRm\Time\Proxies\Time;

use YukataRm\Laravel\Log\Facades\Log;
use YukataRm\Laravel\Log\Logger;
use YukataRm\Log\Enums\LogFormatEnum;

use Illuminate\Support\Facades\Artisan;

/**
 * Base Command
 *
 * @package YukataRm\Laravel\Command
 */
abstract class BaseCommand extends Command
{
    /*----------------------------------------*
     * Constructor
     *----------------------------------------*/

    /**
     * constructor
     */
    public function __construct()
    {
        if (!isset($this->signature)) $this->signature = $this->signature();

        if (!isset($this->description)) $this->description = $this->description();

        parent::__construct();
    }

    /**
     * get signature
     *
     * @return string
     */
    protected function signature(): string
    {
        return "";
    }

    /**
     * get description
     *
     * @return string
     */
    protected function description(): string
    {
        return "";
    }

    /*----------------------------------------*
     * Override
     *----------------------------------------*/

    /**
     * Executes the current command.
     *
     * @return int
     */
    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!$this->validate) return parent::execute($input, $output);

        $this->setValidator();

        $this->runValidation();

        return parent::execute($input, $output);
    }

    /**
     * Get the value of a command argument.
     *
     * @param  string|null  $key
     * @return array|string|bool|null
     */
    #[\Override]
    public function argument($key = null)
    {
        return $this->validate ? $this->validated($key) : parent::argument($key);
    }

    /**
     * Get the value of a command option.
     *
     * @param  string|null  $key
     * @return string|array|bool|null
     */
    #[\Override]
    public function option($key = null)
    {
        return $this->validate ? $this->validated($key) : parent::option($key);
    }

    /*----------------------------------------*
     * Required
     *----------------------------------------*/

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $this->timerStart();

            $this->setParameter();

            $result = $this->process();

            $this->timerStop();

            $this->logging(true, $result);
        } catch (\Throwable $exception) {
            $this->outputError($exception->getMessage());

            $this->timerStop();

            $this->logging(false, ["exception" => $exception->getMessage()]);

            throw $exception;
        }
    }

    /**
     * set parameter
     *
     * @return void
     */
    abstract protected function setParameter(): void;

    /**
     * run command process
     *
     * @return array<mixed>
     */
    abstract protected function process(): array;

    /*----------------------------------------*
     * Timer
     *----------------------------------------*/

    /**
     * Timer instance
     *
     * @var \YukataRm\Time\Interfaces\TimerInterface
     */
    protected TimerInterface $timer;

    /**
     * timer start
     *
     * @return void
     */
    protected function timerStart(): void
    {
        $this->timer = Time::start();
    }

    /**
     * timer stop
     *
     * @return void
     */
    protected function timerStop(): void
    {
        $this->timer->stop();
    }

    /*----------------------------------------*
     * Output
     *----------------------------------------*/

    /**
     * output result
     *
     * @param string $type
     * @param string $message
     * @return void
     */
    protected function outputResult(string $type, string $message): void
    {
        match ($type) {
            "info"    => $this->components->info($message),
            "success" => $this->components->success($message),
            "error"   => $this->components->error($message),
            "warn"    => $this->components->warn($message),

            default   => $this->components->line($type, $message),
        };
    }

    /**
     * output info
     *
     * @param string $message
     * @return void
     */
    protected function outputInfo(string $message): void
    {
        $this->outputResult("info", $message);
    }

    /**
     * output success
     *
     * @param string $message
     * @return void
     */
    protected function outputSuccess(string $message): void
    {
        $this->outputResult("success", $message);
    }

    /**
     * output error
     *
     * @param string $message
     * @return void
     */
    protected function outputError(string $message): void
    {
        $this->outputResult("error", $message);
    }

    /**
     * output warn
     *
     * @param string $message
     * @return void
     */
    protected function outputWarn(string $message): void
    {
        $this->outputResult("warn", $message);
    }

    /*----------------------------------------*
     * Task
     *----------------------------------------*/

    /**
     * task
     *
     * @param string $message
     * @param callable $callback
     * @return void
     */
    protected function task(string $message, callable $callback): void
    {
        $this->components->task($message, $callback);
    }

    /*----------------------------------------*
     * Validation
     *----------------------------------------*/

    /**
     * whether to enable validation
     *
     * @var bool
     */
    protected bool $validate = false;

    /**
     * Validator instance
     *
     * @var \Illuminate\Contracts\Validation\Validator
     */
    protected Validator $validator;

    /**
     * validation rules
     *
     * @var array<string, mixed>
     */
    protected array $rules;

    /**
     * validation messages
     *
     * @var array<string, mixed>
     */
    protected array $messages;

    /**
     * validation attributes
     *
     * @var array<string, mixed>
     */
    protected array $attributes;

    /**
     * set Validator instance
     *
     * @return void
     */
    protected function setValidator(): void
    {
        $this->validator = ValidatorFacade::make(
            $this->validationData(),
            $this->rules(),
            $this->messages(),
            $this->attributes()
        );
    }

    /**
     * run validation
     *
     * @return void
     */
    protected function runValidation(): void
    {
        if ($this->validator->fails()) throw new InvalidArgumentException(
            implode(PHP_EOL, $this->validationErrors())
        );
    }

    /**
     * get validation data
     *
     * @return array<string, mixed>
     */
    protected function validationData(): array
    {
        return array_merge($this->arguments(), $this->options());
    }

    /**
     * get validation rules
     *
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        return isset($this->rules) ? $this->rules : [];
    }

    /**
     * get validation messages
     *
     * @return array<string, mixed>
     */
    protected function messages(): array
    {
        return isset($this->messages) ? $this->messages : [];
    }

    /**
     * get validation attributes
     *
     * @return array<string, mixed>
     */
    protected function attributes(): array
    {
        return isset($this->attributes) ? $this->attributes : [];
    }

    /**
     * get validated
     *
     * @param string|null $key
     * @return mixed
     */
    protected function validated(string|null $key = null)
    {
        $validated = $this->validator->validated();

        return is_null($key) ? $validated : $validated[$key];
    }

    /**
     * get validation error messages
     *
     * @return array<string, mixed>
     */
    protected function validationErrors(): array
    {
        return $this->validator->errors()->all();
    }

    /*----------------------------------------*
     * Logging
     *----------------------------------------*/

    /**
     * logging
     *
     * @param bool $result
     * @param array<mixed> $message
     * @return void
     */
    protected function logging(bool $result, array $message): void
    {
        if (!$this->isLoggingEnable()) return;

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
        return true;
    }

    /**
     * get logger instance
     *
     * @return \YukataRm\Laravel\Log\Logger
     */
    protected function logger(): Logger
    {
        $logger = Log::make()->info();

        $logger->setBaseDirectory($this->logBaseDirectory());

        $logger->setDirectory($this->logDirectory());

        $logger->setFileNameFormat($this->logFileNameFormat());

        $logger->setFileExtension($this->logFileExtension());

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
        $default = [
            "datetime"          => date("Y-m-d H:i:s"),
            "class"             => get_class($this),
            "signature"         => $this->signature,
            "memory_peak_usage" => memory_get_peak_usage(),
            "execution_time"    => $this->timer->elapsedMilliseconds(),
        ];

        if ($this->validate) $default["validated"] = $this->validated();

        return $default;
    }

    /**
     * get base directory
     *
     * @return string
     */
    protected function logBaseDirectory(): string
    {
        return storage_path("logs");
    }

    /**
     * get directory
     *
     * @return string
     */
    protected function logDirectory(): string
    {
        return "command";
    }

    /**
     * get file name format
     *
     * @return string
     */
    protected function logFileNameFormat(): string
    {
        return "Y-m-d";
    }

    /**
     * get file extension
     *
     * @return string
     */
    protected function logFileExtension(): string
    {
        return "log";
    }

    /*----------------------------------------*
     * Call Static
     *----------------------------------------*/

    /**
     * call static
     *
     * @param array<mixed> $parameters
     * @return int
     */
    public static function callStatic(array $parameters = []): int
    {
        return Artisan::call(static::class, $parameters);
    }
}
