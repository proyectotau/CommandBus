<?php

namespace ProyectoTAU\CommandBus;

use ProyectoTAU\CommandBus\Locator\HandlerLocator;
use ProyectoTAU\CommandBus\Inflector\HandlerInflector;

class CommandBus {
    private $bus = [];
    private $handlerLocator = null;
    private $handlerInflector = null;

    public function __construct($handlerLocator = null, $handlerInflector = null)
    {
        if ($handlerLocator == null){
            $this->handlerLocator = new HandlerLocator();
        } else {
            $this->handlerLocator = $handlerLocator;
        }

        if ($handlerInflector == null){
            $this->handlerInflector = new HandlerInflector();
        } else {
            $this->handlerInflector = $handlerInflector;
        }
    }

    public function bind($cmd, $classhandler){
        $this->bus[$this->getClassnameIfCmdIsAnObject($cmd)] = $this->getClassIfHandlerIsAString($classhandler);
    }

    public function dispatch($cmd, $input = []){
        $handler = $this->handlerInflector->getHandlerMethod();
        return $this->bus[$this->getClassnameIfCmdIsAnObject($cmd)]->$handler($this->mapInputToCommand($cmd, $input));
    }

    private function getClassnameIfCmdIsAnObject($cmd){
        if ( is_object($cmd) ) {
                $cmd = get_class($cmd);
        }
        return $cmd;
    }

    private function getClassIfHandlerIsAString($handler)
    {
        if( is_string($handler) ) {
            return $this->getHandler($handler);
        } else {
            return $handler;
        }
    }

    private function getHandler($handler)
    {
        return $this->handlerLocator->getHandler($handler);
    }

    /**
     * Map the input to the command
     *
     * @param  $command
     * @param  $input
     * @return object
     */
    protected function mapInputToCommand($command, $input)
    {
        if (is_object($command)) {
            return $command;
        }
        
        try {
            $class = new \ReflectionClass($command);
        } catch(\Exception $e)
        {
            return $command;
        }

        $dependencies = [];
        foreach ($class->getConstructor()->getParameters() as $parameter) {
            if ( $parameter->getPosition() == 0 && $parameter->getType() && $parameter->getType()->getName() === 'array') {
                if ($input !== []) {
                    $dependencies[] = $input;
                } else {
                    $dependencies[] = $this->getDefaultValueOrFail($parameter);
                }
            } else {
                $name = $parameter->getName();
                if (array_key_exists($name, $input)) {
                    $dependencies[] = $input[$name];
                } else {
                    $dependencies[] = $this->getDefaultValueOrFail($parameter);
                }
            }
        }

        return $class->newInstanceArgs($dependencies);
    }
}
