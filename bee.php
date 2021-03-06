<?php

/*

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.

Created by GLaDOSDan <gladosdan@gmail.com> - June 2013

http://github.com/GLaDOSDan/Bee

*/

define('PEER_ID', '-BEE_DEBUG-');
$GLOBALS['ANNOUNCE_URL_OVERRIDE'] = ''; //Leave blank if you want to use the announce url from the .torrent file

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
echo "	Torrent name: " . $Torrent->name() . "\n";
echo "	Announce: " . $Torrent->announce() . "\n";
echo "	Infohash: " . strtoupper($Torrent->infohash()) . "\n";


// Initialize command handler

$Command = new CommandHandler;
$Command->announce_url = $Torrent->announce();
$Command->infohash = $Torrent->infohash();
$Command->size = $Torrent->size();

$Command->loadTorrentAnnounce();

echo "> ";

while (FALSE !== ($input = fgets(STDIN))) {

	echo "\n" . $Command->input($input) . "\n";
	
	echo "> ";
}