<?php

namespace Atom\Kernel\Env;

use Closure;
use Dotenv\Dotenv;
use Dotenv\Repository\Adapter\PutenvAdapter;
use Dotenv\Repository\RepositoryBuilder;
use Dotenv\Repository\RepositoryInterface;
use InvalidArgumentException;
use PhpOption\Option;

class Env
{
    const PRODUCTION = "production";
    const TESTING = "testing";
    const DEV = "development";

    protected static $ALLOWED_ENV = [self::PRODUCTION, self::TESTING, self::DEV];

    /**
     * @var string
     */
    private $env;
    /**
     * @var Dotenv
     */
    protected $dotEnv;
    /**
     * @var bool
     */
    protected $putEnvEnabled = true;


    /**
     * @var string
     */
    private $path;

    private static $defaultEnv = self::DEV;

    /**
     * @var RepositoryInterface
     */
    private $repository;

    public static function default(string $env)
    {
        self::validateEnv($env);
        self::$defaultEnv = $env;
    }

    public static function add(string $env)
    {
        if (!in_array($env, self::$ALLOWED_ENV)) {
            self::$ALLOWED_ENV[] = $env;
        }
    }

    public static function create(string $path, ?string $env = null): Env
    {
        return new self($env ?? self::$defaultEnv, $path);
    }

    public function __construct(string $env, string $path)
    {
        $this->validateEnv($env);
        $this->path = $path;
        $this->env = $env;
    }

    public function enablePutEnv(): Env
    {
        $this->putEnvEnabled = true;
        return $this;
    }

    public function disablePutEnv(): Env
    {
        $this->putEnvEnabled = false;
        return $this;
    }

    /**
     * @param $expected
     * @return bool
     */
    public function is($expected): bool
    {
        return $this->env === $expected;
    }

    /**
     * @return bool
     */
    public function isProduction(): bool
    {
        return $this->is(self::PRODUCTION);
    }

    /**
     * @return bool
     */
    public function isTesting(): bool
    {
        return $this->is(self::TESTING);
    }

    /**
     * @return bool
     */
    public function isDev(): bool
    {
        return $this->is(self::DEV);
    }

    /**
     * @param string $env
     */
    public static function validateEnv(string $env)
    {
        if (!in_array($env, self::$ALLOWED_ENV)) {
            throw new InvalidArgumentException("ENV should be in [" . implode(",", self::$ALLOWED_ENV) . "]");
        }
    }

    /**
     * @return Dotenv
     */
    public function dotEnv(): Dotenv
    {
        if ($this->dotEnv === null) {
            $this->dotEnv = Dotenv::create($this->getRepository($this->putEnvEnabled), $this->path, [
                ".env." . strtolower($this->env),
                ".env",
            ]);
        }
        return $this->dotEnv;
    }

    /**
     * @param string $key
     * @param $default
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        return Option::fromValue($this->getRepository()->get($key))
            ->map(function ($value) {
                switch (strtolower($value)) {
                    case 'true':
                    case '(true)':
                        return true;
                    case 'false':
                    case '(false)':
                        return false;
                    case 'empty':
                    case '(empty)':
                        return '';
                    case 'null':
                    case '(null)':
                        return null;
                }
                if (preg_match('/\A([\'"])(.*)\1\z/', $value, $matches)) {
                    return $matches[2];
                }
                return $value;
            })
            ->getOrCall(function () use ($default) {
                return $default instanceof Closure ? $default() : $default;
            });
    }

    /**
     * @param bool $usePutEnv
     * @return RepositoryInterface
     */
    public function getRepository(bool $usePutEnv = true): RepositoryInterface
    {
        if ($this->repository == null) {
            $factory = RepositoryBuilder::createWithDefaultAdapters();
            if ($usePutEnv) {
                $factory->addAdapter(PutenvAdapter::class);
            }
            $this->repository = $factory->immutable()->make();
        }
        return $this->repository;
    }
}