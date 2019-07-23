<?php

namespace ProyectoTAU\CommandBus;

class CommandBus {
	private $bus = [];
	
	public function bind($cmd, $classhandler){
		$this->bus[$this->getClassnameIfCmdIsAnObject($cmd)] = $classhandler;
	}
	
	public function dispatch($cmd){
		$this->bus[$this->getClassnameIfCmdIsAnObject($cmd)]->handler($cmd);
	}
	
	private function getClassnameIfCmdIsAnObject($cmd){
		if ( is_object($cmd) ) {
				$cmd = get_class($cmd);
		}
		return $cmd;
	}
}