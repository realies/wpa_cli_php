# wpa_cli_php
PHP based wpa_cli interface for WPA Supplicant

## Usage

- configure the interface for use in the $iface variable
- create an instance of the wpa_cli php class in a compatible environment
- use some of the provided commands or execute commands to wpa_cli via the wpa_shell function

## Notes

Originally developed for use with the Raspberry Pi, but implementation should be possbile for other type of systems.
The user running the webserver should have access to the wpa_cli, ifup and ifdown binaries in order for them to be executed.
On a Debian system, this is easy with the visudo command that edits the priviliges file for the user accounds.
Adding the following line to the file would enable the access to the www-data user:

`www-data ALL=(ALL) NOPASSWD:/sbin/wpa_cli,/sbin/ifup,/sbin/ifdown`

**demo.php** contains example usage of displaying the status of the network interface and performs a scan, followed with the results

license: http://www.gnu.org/licenses/gpl-3.0.en.html GPLv3


## Commands

**bool ifup( void )** - executes the ifup to the specified network interface

**bool ifdown( void )** - executes the ifdown to the specified network interface

**string wpa_shell( string $command )** - executes commands straight to the wpa_cli binary

**array status( void )** - returns an array containing status information for the interface

**bool scan( void )** - requests a scan to the specified network interface

**array scan_results( void )** - returns an array containing the last scan results

**array list_networks( void )** - returns an array containing all known networks

**num add_network( void )** - adds a network and returns its id

**bool set_network( num $id, string $variable, string $value )** - sets parameters to a specified network

**bool select_network( num $id )** - selects a specified network and disables the others

**bool save_config( void )** - saves the configuration file

**bool forget_network( string $id )** - removes a network from the known networks, saves and reloads the config
