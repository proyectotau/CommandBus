# CommandBus
Minimalist implementation of CommandBus pattern for test purpose only

# According to (it remembers remotelly to)
https://tactician.thephpleague.com/

# Install

Insert official repository in your `composer.json`

```json
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/proyectotau/command-bus.git"
        }
    ],
```

Require it by composer

```sh
   composer -vvv require proyectotau/command-bus:"1.*-dev@dev"
```

# Usage

Create a `CommandHandler` class

```php
class CommandHandler {
	
	function handler($cmd){
		// run your task here
		// you can get public var from cmd if it is an object
	}
}
```
Make a `CommandBus` class

```php
	$cmdbus = new CommandBus();
```

And `bind` command to that command handler

```php
	$cmdbus->bind('MyCommand', $handler);
```

Command can be an object with parameters

```php
	$cmdobj = new CommandObject(true, 1, []);
	$cmdbus->bind($cmdobj, $handler);
```

Finally, `dispatch` command

```php
	$cmdbus->dispatch('MyCommand');
```

or

```php
	$cmdbus->dispatch($cmdobj);
```

As a result, handler method will be invoke receiving command as an argument. If it is an object, you could get constructor's params. Let command be an object like this:

```php
class CommandObject {
	public $param1;
	public $param2;
	public $param3;
	
	function __constructor($param1, $param2, $param3) {
		$this->param1 = $param1;
		$this->param2 = $param2;
		$this->param3 = $param3;
	}
}
```

You can pick up them

```php
function handler($cmd){
		$this->param1 = $cmd->param1;
		$this->param2 = $cmd->param2;
		$this->param3 = $cmd->param3;
	}
```

# Tests

You can run tests like this

```sh
vendor/bin/phpunit --color --testdox tests/CommandBusTest.php
```
