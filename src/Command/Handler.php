<?php

namespace GLaDOSDan\Bee\Command;

use GLaDOSDan\Bee\Torrent;

class Handler
{
    /** @var Torrent\Announce */
    public $TorrentAnnounce;
    public $announce_url;
    public $infohash;
    public $size;

    public function input($input)
    {
        $input = trim($input, "\n");
        $input = explode(' ', $input);

        $command = array_shift($input);
        $args = $input;

        if (method_exists($this, $command)) {
            return $this->$command($args);
        } else {
            return 'Command not found: ' . $command;
        }

    }

    public function help($args)
    {

        $return[] = 'help - This help command';
        $return[] = 'download - Set the amount downloaded, accepts `all` as an alias for the size of the torrent';
        $return[] = 'upload - Set the amount uploaded';
        $return[] = 'set - Sets a custom flag (mixed $key, mixed $val)';
        $return[] = 'get - View the current announce before it is committed';
        $return[] = 'commit - Sends the announce to the tracker and resets all variables ready for next announce';

        return implode($return, "\n");
    }

    public function download($args)
    {
        $amount = $args[0];

        if ($amount == 'all') {
            $this->TorrentAnnounce->set('downloaded', $this->size);
            $this->TorrentAnnounce->set('event', 'completed');
            return $this->get();
        }

        if (!isset($args[1])) {

            if (strlen(str_replace(range(0, 9), '', substr($args[0], -3))) == strlen(substr($args[0], -3))) {
                // Last three characters are non-numeric, so they're probably a multiplier
                $bytes = bytes_multiplier(substr($args[0], 0, -3), substr($args[0], -3));
            } elseif (!is_numeric(substr($args[0], -2))) {
                // Last two characters are non-numeric, so they're probably a multiplier
                $bytes = bytes_multiplier(substr($args[0], 0, -2), substr($args[0], -2));
            } else {
                // Value specified in bytes already
                $bytes = $amount;
            }

        } else {
            // Value specified with a multiplier
            $bytes = bytes_multiplier($amount, $args[1]);
        }

        if ($bytes == 'ERR_CONVERT') {
            return 'Unable to parse input value (invalid multiplier)';
        }

        if ($bytes == $this->size) {
            $this->TorrentAnnounce->set('event', 'completed');
        }

        if ($bytes > $this->size) {
            return 'Error: Download amount is greater than torrent size, use `download all` to set download amount to torrent size';
        }

        $this->TorrentAnnounce->set('downloaded', $bytes);
        return $this->get();
    }

    public function get($args = null)
    {
        // Returns current arguments to be committed

        $list = array();

        foreach ($this->TorrentAnnounce->objects as $k => $v) {
            if ($k == 'info_hash') {
                $v = urlencode($v);
            }
            $list[] = $k . ' => ' . $v;
        }

        return implode("\n", $list);

    }

    public function upload($args)
    {

        $amount = $args[0];

        if (!isset($args[1])) {

            if (strlen(str_replace(range(0, 9), '', substr($args[0], -3))) == strlen(substr($args[0], -3))) {
                // Last three characters are non-numeric, so they're probably a multiplier
                $bytes = bytes_multiplier(substr($args[0], 0, -3), substr($args[0], -3));
            } elseif (!is_numeric(substr($args[0], -2))) {
                // Last two characters are non-numeric, so they're probably a multiplier
                $bytes = bytes_multiplier(substr($args[0], 0, -2), substr($args[0], -2));
            } else {
                // Value specified in bytes already
                $bytes = $amount;
            }

        } else {
            // Value specified with a multiplier
            $bytes = bytes_multiplier($amount, $args[1]);
        }

        if ($bytes == 'ERR_CONVERT') {
            return 'Unable to parse input value (invalid multiplier)';
        }

        $this->TorrentAnnounce->set('uploaded', $bytes);
        return $this->get();
    }


    public function set($args)
    {
        // Sets custom arguments

        $key = array_shift($args);
        $value = implode(' ', $args);


        $this->TorrentAnnounce->set($key, $value);

        return $this->get();

    }

    public function commit($args = null)
    {

        $return = $this->TorrentAnnounce->commit();
        $this->loadTorrentAnnounce();

        return $return;

    }

    public function loadTorrentAnnounce()
    {
        unset($this->TorrentAnnounce);
        $this->TorrentAnnounce = new Torrent\Announce;
        $this->TorrentAnnounce->set_announce_url($this->announce_url);
        $this->TorrentAnnounce->set_infohash($this->infohash);
        $this->TorrentAnnounce->set_size($this->size);

        $this->TorrentAnnounce->set('info_hash', pack('H*', $this->infohash));
        $this->TorrentAnnounce->set('port', '1337');
        $this->TorrentAnnounce->set('peer_id', PEER_ID);
        $this->TorrentAnnounce->set('compact', '1');
    }


}


function bytes_multiplier($amount, $multiplier)
{

    switch (strtoupper($multiplier)) {

        case 'KB':
        case 'KIB':
            $return = $amount * 1024;
            $mult = 'KiB';
            break;

        case 'MB':
        case 'MIB':
            $return = $amount * 1024 * 1024;
            $mult = 'MiB';
            break;

        case 'GB':
        case 'GIB':
            $return = $amount * 1024 * 1024 * 1024;
            $mult = 'GiB';
            break;

        case 'TB':
        case 'TIB':
            $return = $amount * 1024 * 1024 * 1024 * 1024;
            $mult = 'TiB';
            break;


        case 'PB':
        case 'PIB':
            $return = $amount * 1024 * 1024 * 1024 * 1024 * 1024;
            $mult = 'PiB';
            break;

        default:
            return 'ERR_CONVERT';
            break;

    }

    if (!isset($return)) {
        return 'ERR_CONVERT';
    }


    echo '[Bytes Converter] Assumed ' . $amount . ' ' . $mult . "\n";

    return $return;
}

