<?php
ob_end_clean();
ini_set('output_buffering', 'Off');
ini_set('zlib.output_compression', 'Off');
ini_set('implicit_flush', 'On');
ob_implicit_flush(true);

header('Content-Type: text/plain');
header('Transfer-Encoding: chunked');
header('Connection: keep-alive');

//echo str_repeat(' ', 8192); // Larger buffer to force client to start processing
//flush();

header('X-Debug-Flushed: Step 1');
flush();
sleep(1);

header('X-Debug-Flushed: Step 2');
flush();
sleep(1);


// Function to send a chunk
function send_chunk($data) {
    echo dechex(strlen($data)) . "\r\n"; // Send chunk size in hex
    echo $data . "\r\n"; // Send chunk data
    flush(); // Force the output
}

for ($i = 1; $i <= 5; $i++) {
    send_chunk("Step $i of 5 completed.\n");
    sleep(1);
}

// End the chunked transfer
send_chunk("");
flush();
