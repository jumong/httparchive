<?php
/*
Copyright 2010 Google Inc.

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

     http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.
*/

require_once("../utils.inc");
require_once("batch_lib.inc");
require_once("bootstrap.inc");

$bHtml = array_key_exists("REMOTE_ADDR", $_SERVER);

cprint(reportSummary());

echo ($bHtml ? "<pre>" : "" );

$labelFromRun = statusLabel();
$curPasses = crawlPasses($labelFromRun, $gArchive, $locations[0]);
echo "completed passes: $curPasses\n\n";

echo "running tasks:\n";
$device = curDevice();
foreach ( $gaTasks as $task ) {
	// lock file for this specific task.
	$lockfile = lockFilename($device, $task);
	$fp = fopen($lockfile, "w+");
	if ( ! flock($fp, LOCK_EX | LOCK_NB) ) {
		// this task is still running
		echo "    $task\n";
	}
}
echo ($bHtml ? "</pre>\n" : "\n");

echo ($bHtml ? "<pre>" : "") . "recent batches:\n" . `grep "DONE:" batch.log | tac | head -4` . ($bHtml ? "</pre>\n" : "\n");

echo ($bHtml ? "<p><a href='http://httparchive.webpagetest.org/getLocations.php'>view agents</a></p>\n" : "" );
if ( $bHtml ) {
	echo "<script>\nsetTimeout('document.location=\"batch_report.php\"', 1000*60*10);\n</script>\n";
}
?>
