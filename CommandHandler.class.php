<?php

class CommandHandler {

	public $TorrentAnnounce;

	public function __construct(){
		$this->TorrentAnnounce = new TorrentAnnounce;
	}

	public function input($input){
		$input = trim($input, "\n");
		$input = explode(' ', $input);

		$command = array_shift($input);
		$args = $input;

		if (method_exists($this, $command)){
			return $this->$command($args);
		}

	}


	public function help($args){

		
		
	}


}

