<?php

namespace AvtoDev\DevTools\Tests\Bootstrap;

use TypeError;
use Exception;

/**
 * Class AbstractTestsBootstrapper.
 */
abstract class AbstractTestsBootstrapper
{
    /**
     * Prefix for 'magic' bootstrap methods.
     */
    const MAGIC_METHODS_PREFIX = 'boot';

    /**
     * AbstractTestsBootstrapper constructor.
     *
     * @throws Exception
     */
    public function __construct()
    {
        set_exception_handler(function ($e) {
            if ($e instanceof Exception || $e instanceof TypeError) {
                echo sprintf(
                    'Exception: "%s" (file: %s, line: %d)' . PHP_EOL,
                    $e->getMessage(),
                    $e->getFile(),
                    $e->getLine()
                );
            }

            exit(100);
        });

        // Iterate all methods names
        foreach (get_class_methods(static::class) as $method_name) {
            // Check for method name prefix
            if (static::startsWith($method_name, static::MAGIC_METHODS_PREFIX)) {
                // ...and make call
                if ($this->$method_name() !== true) {
                    throw new Exception(sprintf(
                        'Bootstrap method "%s" has non-true result. So, we cannot start tests for this reason',
                        $method_name
                    ));
                }
            }
        }

        restore_exception_handler();
    }

    /**
     * Determine if a given string starts with a given substring.
     *
     * @param string       $haystack
     * @param string|array $needles
     *
     * @return bool
     */
    public static function startsWith($haystack, $needles)
    {
        foreach ((array) $needles as $needle) {
            if ($needle !== '' && \mb_substr($haystack, 0, \mb_strlen($needle)) === (string) $needle) {
                return true;
            }
        }

        return false;
    }
}
