<?php

namespace YukataRm\Laravel\Command\Commands;

use YukataRm\Laravel\Command\BaseCommand;

use YukataRm\Cli\Supports\Tree;

/**
 * Tree Command
 *
 * @package YukataRm\Laravel\Command\Commands
 */
class TreeCommand extends BaseCommand
{
    /**
     * command signature
     *
     * @var string
     */
    protected $signature = "tree {path?} {--max-depth=} {--show-hiddens} {--gitignores} {--show-file-size} {--show-file-modified} {--exclude-patterns=}";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Display directory tree structure";

    /*----------------------------------------*
     * Parameter
     *----------------------------------------*/

    /**
     * target path
     *
     * @var string
     */
    protected string $path;

    /**
     * max depth limit
     *
     * @var int|null
     */
    protected int|null $maxDepth;

    /**
     * whether show hidden files
     *
     * @var bool
     */
    protected bool $showHiddens;

    /**
     * whether consider .gitignore files pattern
     *
     * @var bool
     */
    protected bool $considerGitignores;

    /**
     * whether show file size
     *
     * @var bool
     */
    protected bool $showFileSize;

    /**
     * whether show file modified time
     *
     * @var bool
     */
    protected bool $showFileModifiedTime;

    /**
     * exclude patterns
     *
     * @var array
     */
    protected array $excludePatterns;

    /**
     * set parameter
     *
     * @return void
     */
    protected function setParameter(): void
    {
        $this->path = $this->argument("path") ? base_path($this->argument("path")) : base_path();

        $this->maxDepth             = $this->option("max-depth") ? (int)$this->option("max-depth") : null;
        $this->showHiddens          = $this->option("show-hiddens");
        $this->considerGitignores   = $this->option("gitignores");
        $this->showFileSize         = $this->option("show-file-size");
        $this->showFileModifiedTime = $this->option("show-file-modified");
        $this->excludePatterns      = $this->option("exclude-patterns") ? explode(",", $this->option("exclude-patterns")) : [];
    }

    /*----------------------------------------*
     * Process
     *----------------------------------------*/

    /**
     * run command process
     *
     * @return array<mixed>
     */
    protected function process(): array
    {
        $tree = new Tree();

        $tree->maxDepth($this->maxDepth);
        $tree->showHiddens($this->showHiddens);
        $tree->considerGitignores($this->considerGitignores);
        $tree->setExcludePatterns($this->excludePatterns);
        $tree->showFileSize($this->showFileSize);
        $tree->showFileModifiedTime($this->showFileModifiedTime);

        $tree->write($this->path);

        return [];
    }
}
