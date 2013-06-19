<?php

class TorrentAnnounce {


	private $announce;
	private $info_hash;

	public function set_announce_url($announce_url){
		$this->announce = $announce_url;
	}

	public function set_infohash($infohash){
		$this->info_hash = pack('H*', $infohash);
	}

}