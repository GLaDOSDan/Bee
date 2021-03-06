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


	public function size(){
		return $this->TorrentBencoder->size();
	}

	public function name(){
		return $this->TorrentBencoder->name();
	}

	public function announce(){

		if (!empty($GLOBALS['ANNOUNCE_URL_OVERRIDE'])){
			return $GLOBALS['ANNOUNCE_URL_OVERRIDE'];
		}

		return $this->TorrentBencoder->announce();
	}

	public function infohash(){
		return $this->TorrentBencoder->hash_info();
	}

}