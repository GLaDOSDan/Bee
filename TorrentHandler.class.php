<?php

class TorrentHandler {

	public $path;
	public $data;

	private $TorrentBencoder;
	
	function __construct($path){
		$this->path = $path;
		$this->data = file_get_contents($this->path);

		$this->TorrentBencoder = new TorrentBencoder($this->data);
	
	}


	public function is_torrent(){
		return $this->TorrentBencoder->is_torrent($this->data);
	}

}