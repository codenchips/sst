<?php

// Disable Apache buffering
if (function_exists('apache_setenv')) {
    apache_setenv('no-gzip', '1'); // Disable gzip
}


header('Cache-Control: no-cache, no-transform');
header('Content-Type: application/json');
header('Connection: keep-alive');

ob_end_clean();
ini_set('output_buffering', 'Off');
ini_set('zlib.output_compression', 'Off');
ini_set('implicit_flush', 'On');
ob_implicit_flush(true);



echo str_repeat(' ', 8192); // Larger buffer to force client to start processing
flush();

header('X-Debug-Flushed: Step 1');
flush();
sleep(1);

header('X-Debug-Flushed: Step 2');
flush();
sleep(1);


for ($i = 1; $i <= 5; $i++) {
    $json = json_encode([
            'step' => $i,
            'total' => 5,
            'message' => "Step $i of 5 completed."
        ]) . "\n";

    echo $json . "\r\n"; // Send the chunk data
    flush();

    sleep(1);
}

//echo json_encode(['complete' => true, 'message' => 'All steps completed.']) . "\n";
//flush();
