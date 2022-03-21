<?php

use PHPUnit\Framework\TestCase;
use ProyectoTAU\CommandBus\CommandBus;

class CommandBusTest extends TestCase {

    /**
    * @link https://github.com/sebastianbergmann/phpunit-documentation/issues/171#issuecomment-67239415
    */
    function test_can_make_CommandBus_class(){
        try {
          $cmdbus = new CommandBus();
        } catch (\InvalidArgumentException $notExpected) {
          $this->fail();
        }

        $this->assertTrue(TRUE);
    }

    function test_can_invoke_bind_method(){
        $cmdbus = new CommandBus();

        try {
          $cmdbus->bind(null, null);
        } catch (\InvalidArgumentException $notExpected) {
          $this->fail();
        }

        $this->assertTrue(TRUE);
    }

    function test_can_bind_command_to_handler(){
        $cmdbus = new CommandBus();
        $cmdbus->bind('MyCommand', new StubClass_handler());

        $this->assertTrue(TRUE);
    }

    function test_can_dispatch_command_handler(){
        $cmdbus = new CommandBus();
        $cmdbus->bind('MyCommand', new StubClass_handler());

        try {
          $cmdbus->dispatch('MyCommand');
        } catch (\InvalidArgumentException $notExpected) {
          $this->fail();
        }

        $this->assertTrue(TRUE);
    }

    function test_handler_can_be_invoked(){
        $handler = new StubClass_handler();

        $cmdbus = new CommandBus();
        $cmdbus->bind('MyCommand', $handler);
        $cmdbus->dispatch('MyCommand');

        $this->assertTrue($handler->invoked);
    }

    function test_handler_receives_command(){
        $handler = new StubClass_handler();

        $cmdbus = new CommandBus();
        $cmdbus->bind('MyCommand', $handler);
        $cmdbus->dispatch('MyCommand');

        $this->assertSame('MyCommand', $handler->cmd);
    }

    function test_handler_can_receive_an_object_command(){
        $cmd = new StubClass_object_command();
        $handler = new StubClass_handler_for_object_command();

        $cmdbus = new CommandBus();
        $cmdbus->bind($cmd, $handler);
        $cmdbus->dispatch($cmd, [
            true, 1, []
        ]);

        $this->assertSame($cmd->param1, $handler->param1);
        $this->assertSame($cmd->param2, $handler->param2);
        $this->assertSame($cmd->param2, $handler->param3);
    }

    function test_CommandBus_can_receive_a_method_inflection(){
        $handle = new StubClass_execute();

        $cmdbus = new CommandBus(null, new StubClass_execute_inflector);
        $cmdbus->bind('MyCommand', $handle);
        $cmdbus->dispatch('MyCommand');

        $this->assertTrue($handle->invoked);
    }
}

class StubClass_execute_inflector {

    function getHandlerMethod(){
        return 'execute';
    }
}

class StubClass_execute {
    public $invoked = false;
    public $cmd = null;

    function execute($cmd){
        $this->invoked = true;
        $this->cmd = $cmd;
    }
}

class StubClass_handler {
    public $invoked = false;
    public $cmd = null;

    function handler($cmd){
        $this->invoked = true;
        $this->cmd = $cmd;
    }
}

class StubClass_handler_for_object_command {
    public $param1;
    public $param2;
    public $param3;

    function handler($cmd){
        $this->param1 = $cmd->param1;
        $this->param2 = $cmd->param2;
        $this->param3 = $cmd->param3;
    }
}

class StubClass_object_command {
    public $param1;
    public $param2;
    public $param3;

    function __constructor($param1, $param2, $param3) {
        $this->param1 = $param1;
        $this->param2 = $param2;
        $this->param3 = $param3;
    }
}
