<?php

namespace GLaDOSDan\Bee\Torrent;

class Announce
{
    public $objects;
    private $announce;
    private $info_hash;
    private $size;
    private $request_params;

    public function set_announce_url($announce_url)
    {
        $this->announce = $announce_url;
    }

    public function set_infohash($info_hash)
    {
        $this->info_hash = $info_hash;
    }

    public function set_size($size)
    {
        $this->size = $size;
    }

    public function commit()
    {
        $this->set('info_hash', pack('H*', $this->info_hash));

        if (!isset($this->objects['left'])) {
            $this->set('left', $this->size - @$this->objects['downloaded']);
        }

        $this->set('port', '1337');
        $this->set('peer_id', PEER_ID);
        $this->set('compact', '1');


        // All is good to send the request to the tracker

        $request = $this->prepare_request();

        $return = "Request: \n\n";
        $return .= implode("\n", $this->request_params);
        $return .= "\n\nDecoded response from tracker: \n\n";

        $ch = curl_init($request);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, PEER_ID);
        $response = curl_exec($ch);
        curl_close($ch);


        $TorrentBencoder = new Bencoder;

        ob_start();
        var_dump($TorrentBencoder->decode($response));
        $return .= ob_get_contents();
        ob_end_clean();

        unset($TorrentBencoder);

        return $return;
    }

    public function set($key, $value)
    {
        $this->objects[$key] = $value;
    }

    private function prepare_request()
    {
        $this->request_params = array();

        foreach ($this->objects as $key => $value) {
            $this->request_params[] = $key . '=' . urlencode($value);
        }

        return $this->announce . '?' . implode('&', $this->request_params);
    }

}