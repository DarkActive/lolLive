<?php

require_once('pHasher.php');

$img = 'cuts/singleDigit2.png';
$img2 = 'cuts/singleDigit3.png';

$phasher = PHasher::Instance();

$arr1=$phasher->HashImage($img, 0, 0, 16);
$arr2=$phasher->HashImage($img2, 0, 0, 16);

for($i=0; $i < count($arr1); $i++)
{
    echo $arr1[$i];   
}
echo "<br>";
for($i=0; $i < count($arr2); $i++)
{
    echo $arr2[$i];   
}

echo $phasher->Compare($img, $img2) . "% alike";

/*
$start = microtime(true);
if ($handle = opendir('icons/item'))
{
    $max = 0;
    $match = "NONE";
    while (false !== ($entry = readdir($handle)))
    {
        if ($entry != "." && $entry != "..")
        {
            $pct = $phasher->Compare($img, 'icons/item/' . $entry);
            if ($max < $pct && $pct > 75)
            {
                $max = $pct;
                $match = $entry;
            }
            
            if ($max >= 95)
                break;
        }
    }
    
    closedir($handle);
}

echo "Clossest match is " . $match . " at " . $max . "% (" . ((microtime(true) - $start)/1000) . " mili-sec)";
 */

?>