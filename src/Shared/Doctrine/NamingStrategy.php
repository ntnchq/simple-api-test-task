<?php

declare(strict_types=1);

namespace App\Shared\Doctrine;

use Doctrine\Inflector\Inflector;
use Doctrine\Inflector\InflectorFactory;
use Doctrine\ORM\Mapping\NamingStrategy as NamingStrategyInterface;

final class NamingStrategy implements NamingStrategyInterface
{
    private const REFERENCE_COLUMN_NAME = 'id';
    private const SEPARATOR = '_';

    private readonly Inflector $inflector;

    public function __construct(
        private readonly ?string $customPrefix = null,
        ?Inflector $customInflector = null,
    ) {
        $this->inflector = $customInflector ?? InflectorFactory::create()->build();
    }

    #[\Override]
    public function classToTableName(string $className): string
    {
        $this->validateClassName($className);

        if (str_contains($className, '\\')) {
            $className = substr($className, strrpos($className, '\\') + 1);
        }

        $tableName = $this->inflector->pluralize($this->classToSnakeCase($className));

        return $this->customPrefix ? $this->customPrefix.self::SEPARATOR.$tableName : $tableName;
    }

    #[\Override]
    public function propertyToColumnName(string $propertyName, ?string $className = null): string
    {
        return $this->classToSnakeCase($propertyName);
    }

    #[\Override]
    public function embeddedFieldToColumnName(
        string $propertyName,
        string $embeddedColumnName,
        ?string $className = null,
        ?string $embeddedClassName = null
    ): string {
        return $this->classToSnakeCase($propertyName).
            self::SEPARATOR.
            $this->classToSnakeCase($embeddedColumnName);
    }

    #[\Override]
    public function referenceColumnName(): string
    {
        return self::REFERENCE_COLUMN_NAME;
    }

    #[\Override]
    public function joinColumnName(string $propertyName, ?string $className = null): string
    {
        return $this->classToSnakeCase($propertyName).self::SEPARATOR.$this->referenceColumnName();
    }

    #[\Override]
    public function joinTableName(
        string $sourceEntity,
        string $targetEntity,
        string $propertyName
    ): string {
        return $this->classToTableName($sourceEntity).
            self::SEPARATOR.
            $this->inflector->pluralize($this->classToSnakeCase($propertyName));
    }

    #[\Override]
    public function joinKeyColumnName(string $entityName, ?string $referencedColumnName = null): string
    {
        $shortClassName = $this->getShortClassName($entityName);

        return $this->classToSnakeCase($shortClassName).
            self::SEPARATOR.
            ($referencedColumnName ?? $this->referenceColumnName());
    }

    /**
     * @throws \InvalidArgumentException If class name is empty
     */
    private function getShortClassName(string $className): string
    {
        $this->validateClassName($className);

        $pos = strrpos($className, '\\');

        return $pos === false ? $className : substr($className, $pos + 1);
    }

    /**
     * @throws \RuntimeException If regex operation fails
     */
    private function classToSnakeCase(string $input): string
    {
        static $cache = [];

        if (isset($cache[$input])) {
            return $cache[$input];
        }

        $result = @preg_replace('/[A-Z]/', '_\\0', lcfirst($input));

        if ($result === null && preg_last_error() !== \PREG_NO_ERROR) {
            throw new \RuntimeException('Error processing string: '.preg_last_error_msg());
        }

        return $cache[$input] = strtolower($result ?? $input);
    }

    /**
     * @throws \InvalidArgumentException If class name is empty
     */
    private function validateClassName(string $className): void
    {
        if (empty($className)) {
            throw new \InvalidArgumentException('Class name cannot be empty');
        }
    }
}
