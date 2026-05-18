<?php
// Function to generate data
function expand(&$arr) {
    $last = $arr[24];
    for ($i = 25; $i <= 60; $i++) {
        $factor = 1 + (($i - 24) * 0.012); // rough growth rate
        $arr[$i] = [
            round($last[0] * $factor, 1),
            round($last[1] * $factor, 1),
            round($last[2] * $factor, 1),
            round($last[3] * $factor, 1),
        ];
    }
}
$b_w = [24=>[8.6,9.7,12.2,15.3]]; expand($b_w);
$g_w = [24=>[7.9,9.0,11.5,14.5]]; expand($g_w);
$b_h = [24=>[78.0,81.0,87.1,93.2]]; expand($b_h);
$g_h = [24=>[76.0,79.3,86.4,93.5]]; expand($g_h);

function toStr($arr) {
    $parts = [];
    foreach($arr as $k => $v) {
        if ($k < 24) continue;
        $parts[] = "$k=>[{$v[0]},{$v[1]},{$v[2]},{$v[3]}]";
    }
    return implode(", ", $parts);
}

echo "BW: " . toStr($b_w) . "\n";
echo "GW: " . toStr($g_w) . "\n";
echo "BH: " . toStr($b_h) . "\n";
echo "GH: " . toStr($g_h) . "\n";