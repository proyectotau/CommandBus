<?php

namespace ProyectoTAU\CommandBus\Locator;

class HandlerLocator
{
    public function getHandler($handler)
    {
        return new $handler;
    }
}
