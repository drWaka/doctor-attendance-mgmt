<?php

// function solve(string $s): string {
//     $strArrNorm = str_split($s);
//     $strArrNoSpace = str_split(str_replace(' ', '', $s));

//     $counter = array(
//         "normal" => 0,
//         "reverse" => (count($strArrNoSpace) - 1)
//     );
//     $reversedStr = '';
//     while ($counter['normal'] < count($strArrNorm)) {
//         if ($strArrNorm[$counter['normal']] == ' ') {
//         $reversedStr .= ' ';
//         } else {
//         $reversedStr .= $strArrNoSpace[$counter['reverse']];
//         $counter['reverse']--;
//         }
//         $counter['normal']++;
        
//     }

//     return $reversedStr;
// }
// echo solve('Yancy Suarez');

$multPersist = -1;
function persistence(int $num): int {
  $GLOBALS['multPersist']++;
  
  $strVal = strval($num);
  if (strlen($strVal) == 1) {
    return $GLOBALS['multPersist'];
  }
  
  $splitVal = str_split($strVal);
  $product = 1;
  foreach($splitVal as $value) {
    $product *= $value;
  }
  return persistence($product);
}

echo persistence(4);