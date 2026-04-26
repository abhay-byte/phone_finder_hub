<?php

namespace App\Models;

use Illuminate\Contracts\Routing\UrlRoutable;
use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;

/**
 * Base model for Firestore-backed entities.
 * Mimics Eloquent property access and accessor behavior.
 */
abstract class FirestoreModel implements Arrayable, JsonSerializable, UrlRoutable
{
    protected array $attributes = [];

    protected array $casts = [];

    public function __construct(array $attributes = [])
    {
        $this->attributes = $attributes;
    }

    /**
     * Magic getter supporting accessors and relationship methods.
     */
    public function __get(string $name): mixed
    {
        // Check for accessor: getFooAttribute
        $studly = str_replace(' ', '', ucwords(str_replace('_', ' ', $name)));
        $accessor = "get{$studly}Attribute";
        if (method_exists($this, $accessor)) {
            return $this->$accessor();
        }

        // Check for relationship method
        if (method_exists($this, $name)) {
            $result = $this->$name();

            return $result;
        }

        $value = $this->attributes[$name] ?? null;

        // Apply casts
        if (isset($this->casts[$name])) {
            $value = $this->castAttribute($this->casts[$name], $value);
        }

        // Auto-cast arrays
        if (is_array($value)) {
            // If associative array with primitive values, cast to object for -> access
            if ($this->isAssociativeArray($value) && $this->isScalarArray($value)) {
                return (object) $value;
            }
        }

        return $value;
    }

    public function __set(string $name, mixed $value): void
    {
        $this->attributes[$name] = $value;
    }

    public function __isset(string $name): bool
    {
        return isset($this->attributes[$name]) || method_exists($this, $name) || method_exists($this, 'get'.str_replace(' ', '', ucwords(str_replace('_', ' ', $name))).'Attribute');
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function setAttributes(array $attributes): void
    {
        $this->attributes = $attributes;
    }

    public function getAttribute(string $key): mixed
    {
        return $this->__get($key);
    }

    public function setAttribute(string $key, mixed $value): void
    {
        $this->__set($key, $value);
    }

    public function getAttributeValue(string $key): mixed
    {
        return $this->__get($key);
    }

    public function toArray(): array
    {
        return $this->attributes;
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public function getRouteKey(): string
    {
        return $this->attributes['id'] ?? '';
    }

    public function getRouteKeyName(): string
    {
        return 'id';
    }

    public function resolveRouteBinding($value, $field = null): ?static
    {
        return null;
    }

    public function resolveChildRouteBinding($childType, $value, $field): ?static
    {
        return null;
    }

    public function offsetExists(mixed $offset): bool
    {
        return isset($this->attributes[$offset]);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->__get($offset);
    }

    /**
     * Cast attribute value based on cast type.
     */
    protected function castAttribute(string $type, mixed $value): mixed
    {
        if ($value === null) {
            return null;
        }

        return match ($type) {
            'int', 'integer' => (int) $value,
            'float', 'double', 'decimal:2', 'decimal:1' => (float) $value,
            'bool', 'boolean' => (bool) $value,
            'array', 'json', 'object', 'collection' => is_string($value) ? json_decode($value, true) : (array) $value,
            'date', 'datetime' => is_string($value) ? \Carbon\Carbon::parse($value) : $value,
            'hashed' => $value,
            default => $value,
        };
    }

    protected function isAssociativeArray(array $arr): bool
    {
        if (empty($arr)) {
            return false;
        }

        return array_keys($arr) !== range(0, count($arr) - 1);
    }

    protected function isScalarArray(array $arr): bool
    {
        foreach ($arr as $v) {
            if (is_array($v) || is_object($v)) {
                return false;
            }
        }

        return true;
    }
}
