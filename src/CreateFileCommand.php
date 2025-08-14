<?php

namespace YukataRm\Laravel\Command;

use YukataRm\Laravel\Command\BaseCommand;

use Illuminate\Support\Facades\File;

/**
 * Create File Command
 *
 * @package YukataRm\Laravel\Command
 */
abstract class CreateFileCommand extends BaseCommand
{
    /**
     * run command process
     *
     * @return array<mixed>
     */
    protected function process(): array
    {
        $this->setFileDirectory();
        $this->setFileName();
        $this->setFilePath();

        if (!$this->forceCreate() && $this->fileExists($this->filePath)) throw $this->alreadyExistsError();

        $this->makeDirectory($this->fileDirectory, $this->directoryMode);

        $result = $this->createFile(
            $this->filePath,
            $this->fileContent(),
        );

        if (!$result) throw $this->createdFailedError();

        return $this->createdSuccessfully();
    }

    /**
     * get file content
     *
     * @return string
     */
    abstract protected function fileContent(): string;

    /*----------------------------------------*
     * Output
     *----------------------------------------*/

    /**
     * error result
     *
     * @param string $message
     * @return \Throwable
     */
    protected function errorResult(string $message): \Throwable
    {
        return new \RuntimeException("{$message} path: {$this->filePath}");
    }

    /**
     * already exists error
     *
     * @return \Throwable
     */
    protected function alreadyExistsError(): \Throwable
    {
        return $this->errorResult($this->alreadyExistsErrorMessage());
    }

    /**
     * get already exists error message
     *
     * @return string
     */
    protected function alreadyExistsErrorMessage(): string
    {
        return sprintf("[%s] already exists.", $this->filePath);
    }

    /**
     * file created failed error
     *
     * @return \Throwable
     */
    protected function createdFailedError(): \Throwable
    {
        return $this->errorResult($this->createdFailedErrorMessage());
    }

    /**
     * get file created failed error message
     *
     * @return string
     */
    protected function createdFailedErrorMessage(): string
    {
        return sprintf("[%s] created failed.", $this->filePath);
    }

    /**
     * file created successfully
     *
     * @return array<string, mixed>
     */
    protected function createdSuccessfully(): array
    {
        $this->outputInfo($this->createdSuccessfullyMessage());

        return ["path" => $this->filePath];
    }

    /**
     * get file created successfully message
     *
     * @return string
     */
    protected function createdSuccessfullyMessage(): string
    {
        return sprintf("[%s] created successfully.", $this->filePath);
    }

    /*----------------------------------------*
     * Property
     *----------------------------------------*/

    /**
     * make file force
     *
     * @var bool
     */
    protected bool $forceCreate = false;

    /**
     * file directory
     *
     * @var string
     */
    protected string $fileDirectory;

    /**
     * file name
     *
     * @var string
     */
    protected string $fileName;

    /**
     * file path
     *
     * @var string
     */
    protected string $filePath;

    /**
     * directory mode
     *
     * @var int
     */
    protected int $directoryMode = 0755;

    /**
     * whether make file force
     *
     * @return bool
     */
    protected function forceCreate(): bool
    {
        return $this->forceCreate;
    }

    /**
     * set file directory
     *
     * @return void
     */
    protected function setFileDirectory(): void
    {
        $this->fileDirectory = $this->fileDirectory();
    }

    /**
     * get file directory
     *
     * @return string
     */
    abstract protected function fileDirectory(): string;

    /**
     * set file name
     *
     * @return void
     */
    protected function setFileName(): void
    {
        $this->fileName = $this->fileName();
    }

    /**
     * get file name
     *
     * @return string
     */
    abstract protected function fileName(): string;

    /**
     * set file path
     *
     * @return void
     */
    protected function setFilePath(): void
    {
        $directory = $this->fileDirectory;

        if ($directory[strlen($directory) - 1] === "/") $directory = substr($directory, 0, -1);

        $this->filePath = "{$directory}/{$this->fileName}";
    }

    /*----------------------------------------*
     * Method
     *----------------------------------------*/

    /**
     * whether file already exists
     *
     * @param string $path
     * @return bool
     */
    protected function fileExists(string $path): bool
    {
        return File::exists($path);
    }

    /**
     * make directory
     *
     * @param string $directory
     * @param int $mode
     * @return bool
     */
    protected function makeDirectory(string $directory, int $mode = 0755): bool
    {
        if ($this->fileExists($directory)) return true;

        return File::makeDirectory($directory, $mode, true);
    }

    /**
     * create file
     *
     * @param string $path
     * @param string $content
     * @return bool
     */
    protected function createFile(string $path, string $content): bool
    {
        return File::put($path, $content) !== false;
    }

    /**
     * get content
     *
     * @param string $path
     * @return string
     */
    protected function getContent(string $path): string
    {
        return File::get($path);
    }
}
