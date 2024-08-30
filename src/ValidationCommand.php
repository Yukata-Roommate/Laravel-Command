<?php

namespace YukataRm\Laravel\Command;

use YukataRm\Laravel\Command\BaseCommand;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Validator as ValidatorFacade;
use Symfony\Component\Console\Exception\InvalidArgumentException;

/**
 * Validation Command
 * 
 * @package YukataRm\Laravel\Command
 */
abstract class ValidationCommand extends BaseCommand
{
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
        $this->setValidator();

        $this->runValidation();

        return parent::execute($input, $output);
    }

    /*----------------------------------------*
     * Property
     *----------------------------------------*/

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

        if (is_null($key)) return $validated;

        if (!isset($validated[$key])) throw new InvalidArgumentException("The given key [{$key}] was not found in the validated data.");

        return $validated[$key];
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
     * Method
     *----------------------------------------*/

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
}
