<!doctype html>
<html>
 <head>
  <title>wpa_cli_php demo</title>
 </head>
 <body>
 <?php
include "wpa_cli.php";
$wpa_cli = new wpa_cli();

while (true) {
	if ($wpa_cli->scan() === true) {
		show_status($wpa_cli);
		show_results($wpa_cli);
		break;
	} else {
		sleep(0.5);
	}
}

function show_status($wpa_cli) {
	$status = $wpa_cli->status();

	echo <<< EOF
 <h2>status()</h2>
 <table>
  <thead>
   <tr>
    <th>key</th>
    <th>value</th>
   </tr>
  </thead>
  <tbody>
EOF;
	foreach ($status as $key => $value) {
		echo <<< EOF
<tr>
<td>{$key}</td>
<td>{$value}</td>
</tr>

EOF;
	}
	echo <<< EOF
  </tbody>
 </table><br><br>

EOF;
}

function show_results($wpa_cli) {
	echo <<< EOF
 <h2>scan_results()</h2>
 <table>
  <thead>
   <tr>
    <th>bssid</th>
    <th>frequency</th>
    <th>signal level</th>
    <th>flags</th>
    <th>ssid</th>
   </tr>
  </thead>
  <tbody>

EOF;
	foreach ($wpa_cli->scan_results() as $key => $value) {
		echo <<< EOF
   <tr>
    <td class="bssid">{$value['bssid']}</td>
    <td class="frequency">{$value['frequency']}</td>
    <td class="signal level">{$value['signal level']}</td>
    <td class="flags">{$value['flags']}</td>
    <td class="ssid">{$value['ssid']}</td>
   </tr>

EOF;
	}
	echo <<< EOF
  </tbody>
  </table>

EOF;
}
?>
 </body>
</html>