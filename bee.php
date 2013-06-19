<?php

echo "\n";
echo "                 \\     /\n";
echo "             \\    o ^ o    /\n";
echo "               \\ (     ) /\n";
echo "    ____________(%%%%%%%)____________\n";
echo "   (     /   /  )%%%%%%%(  \\   \\     )\n";
echo "   (___/___/__/           \\__\\___\\___)\n";
echo "      (     /  /(%%%%%%%)\\  \\     )\n";
echo "       (__/___/ (%%%%%%%) \\___\\__)\n";
echo "               /(       )\\\n";
echo "             /   (%%%%%)   \\\n";
echo "                  (%%%)\n";

echo "\n\nWelcome to Bee v0.1\n\n";


require_once('TorrentBencoder.class.php');
require_once('TorrentHandler.class.php');
require_once('TorrentAnnounce.class.php');
require_once('CommandHandler.class.php');

$path = $argv[1];

if (!file_exists($path)){
	die("Unable to load torrent file - file does not exist\n");
}

$Torrent = new TorrentHandler($path);

if (!$Torrent->is_torrent()){
	die("Unable to load torrent - is not torrent file\n");
}

echo "Loaded torrent:\n";
echo "	Filename: " . pathinfo($path, PATHINFO_BASENAME) . "\n";
echo "	Announce: " . $Torrent->announce() . "\n";
echo "	Infohash: " . strtoupper($Torrent->infohash()) . "\n";


// Initialize command handler

$Command = new CommandHandler;
$Command->TorrentAnnounce->set_announce_url($Torrent->announce());
$Command->TorrentAnnounce->set_infohash($Torrent->infohash());

echo "> ";

while (FALSE !== ($input = fgets(STDIN))) {

	echo "\n" . $Command->input($input) . "\n\n";
	
	echo "> ";
}