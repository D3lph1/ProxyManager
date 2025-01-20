<?php
declare(strict_types=1);

namespace ProxyManager\ProxyGenerator\AccessInterceptor\MethodGenerator;

use Closure;
use Laminas\Code\Generator\Exception\InvalidArgumentException;
use Laminas\Code\Generator\ParameterGenerator;
use Laminas\Code\Generator\PropertyGenerator;
use ProxyManager\Generator\MethodGenerator;

class SetMethodExceptionInterceptor extends MethodGenerator
{
    /**
     * Constructor
     *
     * @throws InvalidArgumentException
     */
    public function __construct(PropertyGenerator $exceptionInterceptor)
    {
        parent::__construct('setMethodExceptionInterceptor');

        $interceptor = new ParameterGenerator('exceptionInterceptor');

        $interceptor->setType(Closure::class);
        $interceptor->setDefaultValue(null);
        $this->setParameter(new ParameterGenerator('methodName', 'string'));
        $this->setParameter($interceptor);
        $this->setReturnType('void');
        $this->setBody('$this->' . $exceptionInterceptor->getName() . '[$methodName] = $exceptionInterceptor;');
    }
}
