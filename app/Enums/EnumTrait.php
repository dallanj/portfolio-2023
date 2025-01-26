<?php

namespace App\Enums;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use ReflectionEnum;
use ReflectionException;
use Exception;

trait EnumTrait
{
    /**
     * Get all enum cases
     *
     * @return Collection
     */
    public static function all(): Collection
    {
        return collect(self::cases());
    }

    /**
     * Get all enum cases with more details
     *
     * @return Collection
     */
    public static function data(): Collection
    {
        return self::all()->map(function ($item) {
            // Return the extra data if details method exists
            if (self::reflect()->hasMethod('details')) {
                return $item->details();
            }
        })->filter();
    }

    /**
     * Create a new ReflectionEnum instance
     *
     * @param string $name
     *
     * @return object
     */
    public static function reflect(): ReflectionEnum
    {
        return new ReflectionEnum(self::class);
    }

    /**
     * Callable function to add a suffix to the enum strings if applicable
     *
     * @return callable
     */
    public static function suffixable(): callable
    {
        return fn ($value) => self::reflect()->hasMethod('suffix') ? self::suffix($value) : $value;
    }

    /**
     * Get the enum value from the name instead of using from(name)
     *
     * @param string $name
     *
     * @return object
     */
    public static function fromName(string $name): object
    {
        try {
            return self::reflect()->getCase($name)->getValue();
        } catch (ReflectionException $e) {
            throw new Exception(sprintf('There is not case with the name "%s" from %s', $name, self::class));
        }
    }

    /**
     * Get the enum value from the name instead of using tryFrom(name)
     *
     * @param string $name
     *
     * @return object|null
     */
    public static function tryFromName(string $name): object|null
    {
        try {
            return self::reflect()->getCase($name)->getValue();
        } catch (ReflectionException $e) {
            return null;
        }
    }

    /**
     * Get a specific enum label
     *
     * @param Self $enum The enum
     *
     * @return String
     */
    public static function getLabel(self $enum): string
    {
        $suffix = self::suffixable();

        return Str::title($suffix($enum->value));
    }

    /**
     * Get all enums in dropdown format
     *
     * @return Collection
     */
    public static function dropdown(): Collection
    {
        $suffix = self::suffixable();

        return self::all()->map(fn ($item) => [
            'label' => Str::title($suffix($item->value)),
            'value' => lcfirst($item->name)
        ]);
    }

    /**
     * Get a specific enum in dropdown format
     *
     * @param string $string The enum value or name
     *
     * @return Array
     */
    public static function dropdownFormat(string $string): array
    {
        $enum = self::tryFrom($string) ?? self::tryFromName($string);
        $suffix = self::suffixable();

        return [
            'label' => Str::title($suffix($enum->value)),
            'value' => $enum->name
        ];
    }
}