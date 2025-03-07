<?php

namespace App\PiniaStation\Factories;

use App\PiniaStation\Facades\PiniaLoader;
use Illuminate\Database\Eloquent\Model;
use Exception;
use ReflectionClass;
use ReflectionMethod;
use FilesystemIterator;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Inertia\Inertia;
use Illuminate\Support\Str;
use Illuminate\Container\Container;

class PiniaLoaderFactory
{
    /** @var array $state */
    protected $state = [];
    /** @var array $lazyState */
    protected $lazyState = [];
    /** @var array $lazyModules */
    protected $lazyModules = [];
    /** @var array $modules */
    protected $modules = [];

    /** @var array $loaders - pinia loader files */
    protected $loaders = [];

    public function __construct(protected Container $container)
    {
    }

    /** 
     * This function "load" can be called in many ways, but the goal is to call the function using the arguments (args)
     * - This will return a result for each key, typically an array or collection
     * - All result(s) must be packed into an array for pinia state management, ordered by their store and their keys and values
     * - The packed data is then returned to the front end to load into pinia
     * 
     * PiniaLoader::load('store', [
     *       'all'       => [],
     *       'active'    => [...$args] // Turn args into array if its a string be traversal
     * ]);
     
        * PiniaLoader::load('store', 'active', $arg); // Turn args into array if its a string be traversal
        * 
        * Utilize Laravel 11 Helpers to pack every loaded store and their key and value(s)
        * - share using intertia or as a response for sensitive data on every request
    */
    public function initilize($mappings)
    {
        $this->loaders = array_merge(
            $this->loaders,
            $mappings
                ->mapWithKeys(function ($class) {
                    // Get the loader namespace and class name 
                    $loader = $class->name;
                    // Get the loaders methods
                    $methods = $class->getMethods();
                    // Get the loader class instance
                    $instance = new $loader;
                    // Get store module name ex: 'options'
                    $module = Str::lower(Str::replace('Loader', '', $class->getShortName()));
                    $this->container->singleton($loader, fn ($instance) => $instance);
                    return [
                        "$module" => [
                            'class'   => $loader,
                            'methods' => $methods,
                        ]
                    ];
                })
                ->toArray()
        );
    }

    /**
     * Loads state management for the given module.
     *
     * @param string $module
     * @param string|array $functions
     * @param array $args
     * @param bool $lazy
     *
     * @return void
     */
    public function load(string $module, $functions = 'all', $args = [], bool $lazy = false): void
    {
        // Ensure the module exists in the loaders array
        if (!array_key_exists($module, $this->loaders)) {
            throw new Exception("Loader for module {$module} not found.");
        }

        $this->container->make($this->loaders[$module]['class']);

        // Normalize the functions to an array
        if (is_string($functions)) {
            $functions = [$functions => $args];
        }
        
        collect($functions)
            ->mapWithKeys(function ($value, $key) {
                return is_int($key) ? [$value => null] : [$key => $value];
            })
            ->each(function($args, $method) use ($module, $lazy) {
                // Wrap arguments in array if null or not
                $wrappedArgs = Arr::wrap($args);
                // Validate arguments
                $validatedArgs = $this->validateArguments($module, $method, $wrappedArgs);
                // New instance of a class
                $instance = $this->loaders[$module]['class'];

                PiniaLoader::module(
                    $module, 
                    $lazy
                        ? [$method => fn () => (new $instance)->{$method}(...array_values($validatedArgs))]
                        : [$method => (new $instance)->{$method}(...array_values($validatedArgs))]
                );
            });
    }

    /**
     * Invokes a method on the given instance and stores the result.
     *
     * @param string $module
     * @param array|object $state
     *
     * @return $this
     */
    public function module(string $module, $state)
    {
        if (!is_string($module) || empty($module)) {
            throw new Exception('$module must be a string.');
        }

        [$isLazy, $newState] = $this->verifyState($state);

        array_push(
            $this->{$isLazy ? 'lazyModules' : 'modules'},
            [$module => $newState]
        );

        return $this;
    }

    public function verifyState($state)
    {
        if (is_array($state)) {
            $state = collect($state)->toArray();
        } elseif (method_exists($state, 'toArray')) {
            $state = $state->toArray();
        } elseif (!is_callable($state)) {
            throw new \Exception('$state must be an array or a Collection.');
        }

        foreach ($state as $key => $value) {
            if (method_exists((object) $value, 'toArray')) {
                $state[$key] = $value->toArray();
            }
        }

        $isLazy = false;

        if (is_callable($state) || $this->array_some(array_values($state), fn ($el) => is_callable($el))) {
            $isLazy = true;
        }

        return [$isLazy, $state];
    }

    /**
     * Checks if some items in the array pass the predicate.
     */
    protected function array_some($arr, callable $callback)
    {
        foreach ($arr as $ele) {
            if (call_user_func($callback, $ele)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Loads the state into Pinia store using Inertia.
     *
     * @param string $store
     * @param string $key
     * @param mixed $value
     *
     * @return void
     */
    protected static function loadState(string $store, string $key, $value): void
    {
        Inertia::share("pinia.{$store}.{$key}", $value);
    }

    /**
     * Get the correct method within the module.
     *
     * @param string $module
     * @param mixed $method
     *
     * @return array
     */
    protected function getLoaderMethod(string $module, $method)
    {
        return Arr::first($this->loaders[$module]['methods'], fn ($x) => $x->name === $method);
    }

    /**
     * Validates and returns the arguments passed to the loader function method
     *
     * @param array $args
     * @param string $function
     * 
     * @return array
     */
    private function validateArguments(string $module, string $method, ?array $args): array
    {
        // Gather the required parameters from the method
        $reflectionMethod = $this->getLoaderMethod($module, $method);

        // Get the parameters for the function method
        $parameters = $reflectionMethod->getParameters();
        
        // Validate the provided arguments
        $validated = [];
        foreach ($parameters as $index => $parameter) {
            $param_name = $parameter->getName();
            $param_type = $parameter->getType();
            if (Arr::isAssoc($args)) {
                $validated[] = $args;
            } elseif (array_key_exists($index, $args)) {
                // If argument is provided, validate the type
                $arg = $args[$index];
                if (!$param_type->allowsNull()) {
                    $param_type_name = $param_type->getName();
                    if ($param_type->isBuiltin()) {
                        if (gettype($arg) !== $param_type_name) {
                            throw new Exception("Invalid argument type for parameter {$param_name} in function {$method}. Expected {$param_type_name}, got " . gettype($arg) . ".");
                        }
                    } else {
                        if (!($arg instanceof $param_type_name)) {
                            throw new Exception("Invalid argument type for parameter {$param_name} in method {$method}. Expected instance of {$param_type_name}, got " . get_class($arg) . ".");
                        }
                    }
                }
                $validated[] = $arg;
            } elseif ($parameter->isDefaultValueAvailable()) {
                // If argument is not provided, use the default value
                $validated[] = $parameter->getDefaultValue();
            } elseif ($parameter->allowsNull()) {
                // If argument is not provided but nullable
                $validated[] = null;
            } else {
                // If argument is not provided and there's no default value, throw an exception
                throw new Exception("Missing required argument for parameter {$param_name} in method {$method}.");
            }
        }
        return $validated;
    }

    /**
     * Converts the results to a JSON string.
     *
     * @param int $options JSON encoding options.
     * 
     * @return string JSON encoded results.
     */
    public function toJson($options = 0): string
    {
        return json_encode($this->toArray(), $options);
    }

    /**
     * Returns a JSON response with the results and additional data.
     *
     * @param array $additional_data Additional data to include in the response.
     * 
     * @return \Illuminate\Http\JsonResponse JSON response with results and additional data.
     */
    public function toApiResponse(array $additional_data = []): JsonResponse 
    {
        return response()->json([
            '$pinia' => $this->toArray(),
            ...$additional_data
        ]);
    }

    /**
     * Returns the currently saved data as an array.
     * @return array
     */
    public function toArray()
    {
        $store = [];

        if (!empty($this->state) || !empty($this->lazyState)) {
            $store['state'] = [];
        }

        if (!empty($this->modules) || !empty($this->lazyModules)) {
            $store['modules'] = [];
        }

        if (!empty($this->state)) {
            $store['state'] = $this->reduceData($this->state, $store['state'], function ($acc, $cur) {
                return $this->array_merge_phase($acc, $this->generateState($cur));
            });
        }

        if (!empty($this->lazyState)) {
            $store['state'] = $this->reduceData($this->lazyState, $store['state'], function ($acc, $cur) {
                return $this->array_merge_phase($acc, $this->generateLazyState($cur));
            });
        }

        if (!empty($this->modules)) {
            foreach ($this->modules as $module) {
                $store['modules'] = $this->array_merge_phase($store['modules'], $this->generateNamespacedModules($module));
            }
        }

        if (!empty($this->lazyModules)) {
            foreach ($this->lazyModules as $module) {
                $store['modules'] = $this->array_merge_phase($store['modules'], $this->generateLazyNamespacedModules($module));
            }
        }

        return $store;
    }

    /**
     * Recursively merge two or more assoc arrays
     *
     * @param array $merged base merged array
     * @param array[] $rest the rest of the assoc arrays to be merged in
     *
     * @return array
     */
    public function array_merge_phase(array $merged, ...$rest)
    {
        // Check the base case
        if (empty($rest)) {
            return $merged;
        }

        foreach($rest as $array) {
            if (!is_array($array)) {
                $array = [ $array ];
            }

            foreach ($array as $key => $value) {
                if (is_array($value) && isset($merged[$key]) && is_array($merged[$key])) {
                    $merged[$key] = $this->array_merge_phase($merged[$key], $value);
                } else {
                    $merged[$key] = $value;
                }
            }
        }

        return $merged;
    }

    /**
     * Reduce data from array.
     */
    public function reduceData($a, $b, $reduce)
    {
        foreach ($a as $state) {
            $b = $reduce($b, $state);
        }

        return $b;
    }

    /**
     * Generates State.
     */
    protected function generateState($state)
    {
        return $state;
    }

    /**
     * Generates Lazy State.
     */
    protected function generateLazyState($state)
    {
        $state = is_callable($state) ? $state() : $state;

        foreach ($state as $key => $value) {
            if (is_callable($value)) {
                $state[$key] = $value();
            }
        }

        return $state;
    }

    /**
     * Generates Modules.
     */
    protected function generateNamespacedModules($module)
    {
        foreach ($module as $namespace => $state) {
            if (!Str::contains($namespace, '/')) { // simple module namespace
                return [$namespace => ['state' => $state]];
            } else { // complex nested modules namespace
                $namespaces = array_reverse(collect(explode('/', $namespace))->toArray());
                // final state is starting value
                $arr = $state;
                // build array in reverse
                foreach ($namespaces as $idx => $key) {
                    $type = $idx === 0 ? 'state' : 'modules';
                    $arr = [$key => [$type => $arr]];
                }

                return $arr;
            }
        }
    }

    /**
     * Generates Lazy Modules.
     */
    protected function generateLazyNamespacedModules($module)
    {
        foreach ($module as $namespace => $state) {
            if (!Str::contains($namespace, '/')) { // simple module namespace
                if (is_callable($state)) {
                    $state = $state();
                }

                foreach ($state as $key => $value) {
                    if (is_callable($value)) {
                        $state[$key] = $value();
                    }
                }

                return [$namespace => ['state' => $state]];
            } else { // complex nested modules namespace
                $namespaces = array_reverse(collect(explode('/', $namespace))->toArray());

                // final state is starting value
                $arr = $state;
                // build array in reverse
                foreach ($namespaces as $idx => $key) {
                    if (is_callable($arr)) {
                        $arr = $arr();
                    }

                    foreach ($arr as $arrKey => $arrValue) {
                        if (is_callable($arrValue)) {
                            $arr[$arrKey] = $arrValue();
                        }
                    }

                    $type = $idx === 0 ? 'state' : 'modules';
                    $arr = [$key => [$type => $arr]];
                }

                return $arr;
            }
        }
    }
}
