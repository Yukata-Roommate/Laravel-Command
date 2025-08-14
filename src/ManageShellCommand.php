<?php

namespace YukataRm\Laravel\Command;

use YukataRm\Laravel\Command\BaseCommand;

use Illuminate\Support\Facades\Process;

/**
 * Manage Shell Command
 *
 * @package YukataRm\Laravel\Command
 */
abstract class ManageShellCommand extends BaseCommand
{
    /*----------------------------------------*
     * Shell
     *----------------------------------------*/

    /**
     * run shell command
     *
     * @param string $message
     * @param string $command
     * @return void
     */
    protected function runShellCommand(string $message, string $command): void
    {
        $this->task($message, function () use ($command) {
            return Process::run($command)->throw();
        });
    }

    /**
     * run shell command with timeout
     *
     * @param string $message
     * @param string $command
     * @param int $timeout
     * @param int|null $idleTimeout
     * @return void
     */
    protected function runShellCommandWithTimeout(string $message, string $command, int $timeout = 60, int|null $idleTimeout = null): void
    {
        $this->task($message, function () use ($command, $timeout, $idleTimeout) {
            $process = Process::timeout($timeout);

            if (!is_null($idleTimeout)) $process->idleTimeout($idleTimeout);

            return $process->run($command)->throw();
        });
    }

    /**
     * run shell command without timeout
     *
     * @param string $message
     * @param string $command
     * @return void
     */
    protected function runShellCommandWithoutTimeout(string $message, string $command): void
    {
        $this->task($message, function () use ($command) {
            return Process::forever()->run($command)->throw();
        });
    }

    /*----------------------------------------*
     * Artisan
     *----------------------------------------*/

    /**
     * run artisan command
     *
     * @param string $message
     * @param string $command
     * @return void
     */
    protected function runArtisanCommand(string $message, string $command): void
    {
        $this->runShellCommandWithoutTimeout(
            sprintf(
                "Artisan %s",
                $message
            ),
            sprintf(
                "php artisan %s",
                $command
            )
        );
    }

    /**
     * run artisan migrate
     *
     * @param bool $force
     * @return void
     */
    protected function artisanMigrate(bool $force = false): void
    {
        $this->runArtisanCommand(
            "migrate",
            sprintf(
                "migrate %s",
                $force ? "--force" : ""
            )
        );
    }

    /**
     * run artisan optimize
     *
     * @return void
     */
    protected function artisanOptimize(): void
    {
        $this->runArtisanCommand(
            "optimize",
            "optimize"
        );
    }

    /**
     * run artisan optimize clear
     *
     * @return void
     */
    protected function artisanOptimizeClear(): void
    {
        $this->runArtisanCommand(
            "optimize clear",
            "optimize:clear"
        );
    }

    /**
     * run artisan key generate
     *
     * @return void
     */
    protected function artisanKeyGenerate(): void
    {
        $this->runArtisanCommand(
            "key:generate",
            "key:generate"
        );
    }

    /**
     * run artisan storage link
     *
     * @return void
     */
    protected function artisanStorageLink(): void
    {
        $this->runArtisanCommand(
            "storage:link",
            "storage:link"
        );
    }

    /*----------------------------------------*
     * Composer
     *----------------------------------------*/

    /**
     * run composer command
     *
     * @param string $message
     * @param string $command
     * @return void
     */
    protected function runComposerCommand(string $message, string $command): void
    {
        $this->runShellCommandWithoutTimeout(
            sprintf(
                "Composer %s",
                $message
            ),
            sprintf(
                "composer %s",
                $command
            )
        );
    }

    /**
     * run composer install
     *
     * @param array<string> $options
     * @return void
     */
    protected function composerInstall(array $options = []): void
    {
        $this->runComposerCommand(
            "install",
            sprintf(
                "install %s",
                implode(" ", $options)
            )
        );
    }

    /**
     * run composer update
     *
     * @param array<string> $options
     * @return void
     */
    protected function composerUpdate(array $options = []): void
    {
        $this->runComposerCommand(
            "update",
            sprintf(
                "update %s",
                implode(" ", $options)
            )
        );
    }

    /**
     * run composer require
     *
     * @param string $package
     * @param bool $isDev
     * @return void
     */
    protected function composerRequire(string $package, bool $isDev = false): void
    {
        $this->runComposerCommand(
            "require [{$package}]",
            sprintf(
                "require %s %s",
                $isDev ? "--dev" : "",
                $package
            )
        );
    }

    /**
     * run composer remove
     *
     * @param string $package
     * @return void
     */
    protected function composerRemove(string $package): void
    {
        $this->runComposerCommand(
            "remove [{$package}]",
            sprintf(
                "remove %s",
                $package
            )
        );
    }

    /**
     * run composer dump-autoload
     *
     * @return void
     */
    protected function composerDumpAutoload(): void
    {
        $this->runComposerCommand(
            "dump-autoload",
            "dump-autoload"
        );
    }

    /*----------------------------------------*
     * Npm
     *----------------------------------------*/

    /**
     * run npm command
     *
     * @param string $message
     * @param string $command
     * @return void
     */
    protected function runNpmCommand(string $message, string $command): void
    {
        $this->runShellCommandWithoutTimeout(
            sprintf(
                "Npm %s",
                $message
            ),
            sprintf(
                "npm %s",
                $command
            )
        );
    }

    /**
     * run npm install
     *
     * @param bool $isClean
     * @return void
     */
    protected function npmInstall(bool $isClean = false): void
    {
        $this->runNpmCommand(
            $isClean ? "clean install" : "install",
            $isClean ? "clean-install" : "install"
        );
    }

    /**
     * run npm install package
     *
     * @param string $package
     * @param bool $isDev
     * @return void
     */
    protected function npmInstallPackage(string $package, bool $isDev = false): void
    {
        $this->runNpmCommand(
            "install [{$package}]",
            sprintf(
                "install %s %s",
                $isDev ? "--save-dev" : "--save",
                $package
            )
        );
    }

    /**
     * run npm uninstall
     *
     * @param string $package
     * @return void
     */
    protected function npmUninstall(string $package): void
    {
        $this->runNpmCommand(
            "uninstall [{$package}]",
            sprintf(
                "uninstall %s",
                $package
            )
        );
    }

    /**
     * run npm run
     *
     * @param string $script
     * @return void
     */
    protected function npmRun(string $script): void
    {
        $this->runNpmCommand(
            "run [{$script}]",
            sprintf(
                "run %s",
                $script
            )
        );
    }

    /**
     * run npm audit fix
     *
     * @return void
     */
    protected function npmAuditFix(): void
    {
        $this->runNpmCommand(
            "audit fix",
            "audit fix"
        );
    }

    /**
     * run npm pkg set scripts
     *
     * @param string $key
     * @param string $value
     * @return void
     */
    protected function npmPkgSetScripts(string $key, string $value): void
    {
        $this->runNpmCommand(
            "pkg set scripts",
            sprintf(
                "pkg set scripts.%s=\"%s\"",
                $key,
                $value
            )
        );
    }

    /*----------------------------------------*
     * Git
     *----------------------------------------*/

    /**
     * run git command
     *
     * @param string $message
     * @param string $command
     * @return void
     */
    protected function runGitCommand(string $message, string $command): void
    {
        $this->runShellCommandWithoutTimeout(
            sprintf(
                "Git %s",
                $message
            ),
            sprintf(
                "git %s",
                $command
            )
        );
    }

    /**
     * run git add
     *
     * @param string $file
     * @return void
     */
    protected function gitAdd(string $file): void
    {
        $this->runGitCommand(
            "add [{$file}]",
            sprintf(
                "add %s",
                $file
            )
        );
    }

    /**
     * run git commit
     *
     * @param string $message
     * @return void
     */
    protected function gitCommit(string $message): void
    {
        $this->runGitCommand(
            "commit",
            sprintf(
                "commit -m \"%s\"",
                $message
            )
        );
    }

    /**
     * run git push
     *
     * @return void
     */
    protected function gitPush(): void
    {
        $this->runGitCommand(
            "push",
            "push"
        );
    }

    /**
     * run git pull
     *
     * @param string $remote
     * @param string $branch
     * @return void
     */
    protected function gitPull(string $remote, string $branch): void
    {
        $this->runGitCommand(
            "pull [{$remote}] [{$branch}]",
            sprintf(
                "pull %s %s",
                $remote,
                $branch
            )
        );
    }

    /**
     * run git checkout
     *
     * @param string $branch
     * @return void
     */
    protected function gitCheckout(string $branch): void
    {
        $this->runGitCommand(
            "checkout [{$branch}]",
            sprintf(
                "checkout %s",
                $branch
            )
        );
    }

    /**
     * run git checkout new branch
     *
     * @param string $branch
     * @return void
     */
    protected function gitCheckoutNew(string $branch): void
    {
        $this->runGitCommand(
            "checkout new [{$branch}]",
            sprintf(
                "checkout -b %s",
                $branch
            )
        );
    }

    /**
     * run git rebase
     *
     * @param string $branch
     * @return void
     */
    protected function gitRebase(string $branch): void
    {
        $this->runGitCommand(
            "rebase [{$branch}]",
            sprintf(
                "rebase %s",
                $branch
            )
        );
    }

    /**
     * whether git branch exists
     *
     * @param string $branch
     * @return bool
     */
    protected function gitBranchExists(string $branch): bool
    {
        $result = Process::run(sprintf("git show-ref --verify --quiet refs/heads/%s", $branch));

        return $result->successful();
    }
}
