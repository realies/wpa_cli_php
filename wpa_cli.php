<?php
/**
 * PHP based wpa_cli interface for the WPA Supplicant
 *
 * @package    Wpa_cli_php
 * @version    Release: 0.1
 * @author     Deyan Dimitrov <deyan.dimitroff@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 * @link       http://github.com/realies/wpa_cli_php
 */

class Wpa_cli {

	public $iface = "wlan0"; // wireless interface for public network connection
	private $show_hidden_networks = true; // whether to populate the scan_results with networks that are broadcasted without ssid
	private $sort_by_signal_level = true; // pretty self explanatory

	function ifup() {
		$stdout = shell_exec("ifup {$this->iface}");
		if ($stdout === "ifup: interface {$this->iface} already configured") // maybe use preg_match for matching
		{
			return false;
		} else {
			return true;
		}
	}

	function ifdown() {
		$stdout = shell_exec("ifdown {$this->iface}");
		if ($stdout === "ifdown: interface {$this->iface} not configured") // maybe use preg_match for matching
		{
			return false;
		} else {
			return true;
		}
	}

	function wpa_shell($command) {
		return trim(shell_exec("wpa_cli -i{$this->iface} {$command}"));
	}

	function status() {
		$stdout = $this->wpa_shell("status");

		$rows = explode(PHP_EOL, $stdout);
		if (!strpos($rows[0], "=")) {
			return false;
		}
		// Unexpected stdout from wpa_cli

		foreach ($rows as $key => $value) {
			$row = explode("=", $value);
			$rows_assoc[$row[0]] = $row[1];
		}

		return $rows_assoc;
	}

	function scan() {
		$stdout = $this->wpa_shell("scan");

		if ($stdout === "OK") {
			return true;
		} else {
			return false;
		}
	}

	function scan_results() {
		$stdout = $this->wpa_shell("scan_results");

		$rows = explode(PHP_EOL, $stdout);
		if (!strpos($rows[0], "\t")) {
			unset($rows[0]);
		}

		$networks = array();
		foreach ($rows as $key => $value) {
			$row = explode("\t", $value);
			if (!isset($row[4]) and !$this->show_hidden_networks) {
				continue;
			}

			$row_assoc['bssid'] = $row[0];
			$row_assoc['frequency'] = $row[1];
			$row_assoc['signal level'] = $row[2];
			$row_assoc['flags'] = $row[3];
			if (!$row_assoc['ssid'] = @$row[4]) {
				$row_assoc['ssid'] = "Hidden network";
			}

			$networks[] = $row_assoc;
		}

		if ($this->sort_by_signal_level) {
			function sort_by_signal_level($a, $b) {
				return ($b['signal level'] - $a['signal level']);
			}
			usort($networks, 'sort_by_signal_level');
		}

		return $networks;
	}

	function list_networks() {
		$stdout = $this->wpa_shell("list_networks");

		$rows = explode(PHP_EOL, $stdout);
		if (!strpos($rows[0], "\t")) {
			unset($rows[0]);
		}

		$networks = array();
		foreach ($rows as $key => $value) {
			$row = explode("\t", $value);

			$row_assoc['network id'] = $row[0];
			$row_assoc['ssid'] = $row[1];
			$row_assoc['bssid'] = $row[2];
			if (isset($row[3])) {
				$row_assoc['flags'] = $row[3];
			} else {
				unset($row_assoc['flags']);
			}

			$networks[] = $row_assoc;
		}

		return $networks;
	}

	function add_network() {
		$stdout = $this->wpa_shell("add_network");

		if (is_numeric($stdout)) {
			return $stdout;
		} else {
			return false;
		}
	}

	function set_network($id, $variable, $value) {
		$stdout = $this->wpa_shell("set_network {$id} {$variable} {$value}");

		if ($stdout === "OK") {
			return true;
		} else {
			return false;
		}
	}

	function select_network($id) {
		$stdout = $this->wpa_shell("select_network {$id}");

		if ($stdout === "OK") {
			//	if ($this->ifdown()) {
			//		sleep(1);
			//		$this->ifup();
			//	}
			return true;
		} else {
			return false;
		}
	}

	function save_config() {
		$stdout = $this->wpa_shell("save_config");
		if ($stdout === "OK") {
			return true;
		} else {
			return false;
		}
	}

	// works with both ssid or network id
	function forget_network($id) {
		if (!is_numeric($id)) {
			$id = array_search($id, $this->list_networks());
			if (!is_numeric($id)) {
				return false;
			}
		}

		$stdout = $this->wpa_shell("remove_network {$id}");
		if ($stdout === "OK") {
			$stdout = $this->wpa_shell("save_config");
			if ($stdout === "OK") {
				$stdout = $this->wpa_shell("reconfigure");
				if ($stdout === "OK") {
					return true;
				} else {
					return false;
				}
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
}
