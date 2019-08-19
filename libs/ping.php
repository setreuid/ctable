<?php

// error_reporting(E_ALL);
// ini_set("display_errors", 1);

$content = file_get_contents('php://input');
$data = json_decode($content, true);

// Look for token
$token = (isset($data['token']) && preg_match('/^[0-9a-f]{8}$/', $data['token'])) ? $data['token'] : false;
if (!$token) $token = sprintf('%08x', crc32(microtime()));

// get current minute, build APC key
$quadrant         = ceil(date_create()->format('s') / 15); // between 1-4
$previousQuadrant = $quadrant - 1 < 1 ? 4 : $quadrant - 1;
$key              = 'pinger_'.$quadrant;
$previousKey      = 'pinger_'.$previousQuadrant;

// get tokens for the last 30 seconds
$current  = apcu_fetch($key);
$previous = apcu_fetch($previousKey);

if (!is_array($current)) $current = array();
if (!is_array($previous)) $previous = array();

// Add current token if not found
if (count($current) < 250 && !in_array($token, $current))
{
    $current[] = $token;
    apcu_store($key, $current, 31);
}

// Build return object: userCount, token
$output = array(
    'userCount' => (count($current) > 250 ? '250+' : count(array_unique(array_merge($current, $previous)))),
    'token' => $token,
);

header('Content-Type: application/json');
echo json_encode($output);

?>
