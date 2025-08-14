<?php

namespace YukataRm\Laravel\Command;

use YukataRm\Laravel\Command\BaseCommand;

use YukataRm\File\Proxies\Operator as OperatorProxy;
use YukataRm\File\Operator;

/**
 * Publish Stubs Command
 *
 * @package YukataRm\Laravel\Command
 */
abstract class PublishStubsCommand extends BaseCommand
{
    /*----------------------------------------*
     * Process
     *----------------------------------------*/

    /**
     * assets name
     *
     * @var string
     */
    protected string $assetsName;

    /**
     * stubs directory path
     *
     * @var string
     */
    protected string $stubsDirectory;

    /**
     * run command process
     *
     * @return array<mixed>
     */
    protected function process(): array
    {
        $this->outputInfo("Publishing [{$this->assetsName}] assets.");

        $count = 0;

        foreach ($this->getStubsIterator() as $stub) {
            $this->publishStub($stub);

            $count = $count + 1;
        }

        return ["assets" => $this->assetsName, "count" => $count];
    }

    /**
     * publish stub
     *
     * @param \SplFileInfo $stub
     * @return void
     */
    protected function publishStub(\SplFileInfo $stub): void
    {
        $operator = OperatorProxy::makeFrom($stub->getPathname());

        $to      = $this->getToPath($operator);
        $message = sprintf(
            "Copy file [%s]",
            str_replace(base_path() . "/", "", $to)
        );

        $this->task($message, function () use ($operator, $to) {
            $operator->copy($to);
        });
    }

    /**
     * get stubs directory path
     *
     * @return string
     */
    protected function getStubsDirectory(): string
    {
        return realpath($this->stubsDirectory);
    }

    /**
     * get to path
     *
     * @param \YukataRm\File\Operator $operator
     * @return string
     */
    protected function getToPath(Operator $operator): string
    {
        return preg_replace(
            "/\.stub$/",
            "",
            str_replace(
                $this->getStubsDirectory(),
                base_path(),
                $operator->realpath()
            )
        );
    }

    /**
     * get stubs iterator
     *
     * @return \RegexIterator
     */
    protected function getStubsIterator(): \RegexIterator
    {
        return new \RegexIterator(
            new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($this->getStubsDirectory()),
                \RecursiveIteratorIterator::LEAVES_ONLY
            ),
            "/\.stub$/"
        );
    }
}
