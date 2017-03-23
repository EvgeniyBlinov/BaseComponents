<?php
/**
 * BaseComponents.
 * Copyright (c) 2014 Evgeniy Blinov (http://blinov.in.ua/)
 * 
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 * 
 * @link       https://github.com/EvgeniyBlinov/BaseComponents for the canonical source repository
 * @author     Evgeniy Blinov <evgeniy_blinov@mail.ru>
 * @copyright  Copyright (c) 2017 Evgeniy Blinov
 * @license    http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace BaseComponents;

class DIContainer
{
    /**
     * @var array
     **/
    protected $values = array();

    /**
     * Magic set
     *
     * @param mixed $id
     * @param mixed $value
     * @return void
     * @author Evgeniy Blinov <evgeniy_blinov@mail.ru>
     **/
    function __set($id, $value)
    {
        $this->values[$id] = $value;
    }

    /**
     * Magic get
     *
     * @param mixed $id
     * @return mixed
     * @author Evgeniy Blinov <evgeniy_blinov@mail.ru>
     **/
    function __get($id)
    {
        if (!isset($this->values[$id])) {
            throw new \InvalidArgumentException(sprintf('Value "%s" is not defined.', $id));
        }

        return is_callable($this->values[$id]) ?
            $this->values[$id]($this) :
            $this->values[$id];
    }

    /**
     * Magic call
     *
     * @param string $method
     * @param array $arguments
     * @return mixed
     * @author Evgeniy Blinov <evgeniy_blinov@mail.ru>
     **/
    function __call($method, $arguments = array())
    {
        if (method_exists($this, $method)) {
            return call_user_func_array(array($this, $method), $arguments);
        }
        if (isset($this->values[$method]) && is_callable($this->values[$method])) {
            return call_user_func_array(
                $this->values[$method],
                array_merge(array($this), $arguments)
            );
        }

        throw new \BadMethodCallException(sprintf('Method "%s" of DIContainer does not exist', $method));
    }

    /**
     * Create singleton
     *
     * @param Callable $callable
     * @return Callable
     * @author Evgeniy Blinov <evgeniy_blinov@mail.ru>
     **/
    function asShared($callable)
    {
        return function ($c) use ($callable)
        {
            static $object;

            if (is_null($object)) {
                $object = $callable($c);
            }

            return $object;
        };
    }
}
