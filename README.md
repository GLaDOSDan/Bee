# Bee

Bee is a torrent tracker announce generator created for use by torrent tracker administrators to debug their tracker.

## Requirements

- PHP
- PHP-CLI
- libcurl


## Usage

Bee has a fairly easy to understand command line interface, invoke it like so:

On Linux/Mac:  
`bin/bee /path/to/torrent_file.torrent`

On Windows:  
`php bin/bee /path/to/torrent_file.torrent`

Use `help` for a list of commands.

## License

Please see the LICENSE file for more details.

## Useful Resources

https://wiki.theory.org/BitTorrentSpecification#Tracker_HTTP.2FHTTPS_Protocol

## Credits

Thanks to Adrien Gibrat for the Torrent Bencoding class, which can be found at https://github.com/adriengibrat/torrent-rw
