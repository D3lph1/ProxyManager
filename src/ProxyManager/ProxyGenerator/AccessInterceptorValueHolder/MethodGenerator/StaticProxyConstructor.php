<?php

declare(strict_types=1);

namespace ProxyManager\ProxyGenerator\AccessInterceptorValueHolder\MethodGenerator;

use Laminas\Code\Generator\Exception\InvalidArgumentException;
use Laminas\Code\Generator\ParameterGenerator;
use Laminas\Code\Generator\PropertyGenerator;
use ProxyManager\Generator\MethodGenerator;
use ProxyManager\ProxyGenerator\Util\Properties;
use ProxyManager\ProxyGenerator\Util\UnsetPropertiesGenerator;
use ReflectionClass;

/**
 * The `staticProxyConstructor` implementation for access interceptor value holders
 */
class StaticProxyConstructor extends MethodGenerator
{
    /**
     * Constructor
     *
     * @throws InvalidArgumentException
     */
    public function __construct(
        ReflectionClass $originalClass,
        PropertyGenerator $valueHolder,
        PropertyGenerator $prefixInterceptors,
        PropertyGenerator $suffixInterceptors,
        PropertyGenerator $exceptionInterceptors,
    ) {
        parent::__construct('staticProxyConstructor', [], self::FLAG_PUBLIC | self::FLAG_STATIC);

        $prefix = new ParameterGenerator('prefixInterceptors');
        $suffix = new ParameterGenerator('suffixInterceptors');
        $exception = new ParameterGenerator('exceptionInterceptors');

        $prefix->setDefaultValue([]);
        $suffix->setDefaultValue([]);
        $prefix->setType('array');
        $suffix->setType('array');
        $exception->setType('array');

        $this->setParameter(new ParameterGenerator('wrappedObject'));
        $this->setParameter($prefix);
        $this->setParameter($suffix);
        $this->setParameter($exception);
        $this->setReturnType($originalClass->getName());

        $this->setDocBlock(
            "Constructor to setup interceptors\n\n"
            . '@param \\' . $originalClass->getName() . " \$wrappedObject\n"
            . "@param \\Closure[] \$prefixInterceptors method interceptors to be used before method logic\n"
            . "@param \\Closure[] \$suffixInterceptors method interceptors to be used before method logic\n\n"
            . "@param \\Closure[] \$exceptionInterceptors method interceptors to be used in case of exception happened during method logic execution\n\n"
            . '@return self'
        );

        $this->setBody(
            'static $reflection;' . "\n\n"
            . '$reflection = $reflection ?? new \ReflectionClass(__CLASS__);' . "\n"
            . '$instance   = $reflection->newInstanceWithoutConstructor();' . "\n\n"
            . UnsetPropertiesGenerator::generateSnippet(Properties::fromReflectionClass($originalClass), 'instance')
            . '$instance->' . $valueHolder->getName() . " = \$wrappedObject;\n"
            . '$instance->' . $prefixInterceptors->getName() . " = \$prefixInterceptors;\n"
            . '$instance->' . $suffixInterceptors->getName() . " = \$suffixInterceptors;\n"
            . '$instance->' . $exceptionInterceptors->getName() . " = \$exceptionInterceptors;\n\n"
            . 'return $instance;'
        );
    }
}
