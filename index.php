<?php

require_once('IconAnalyzer.php');

const DIR_FRAMES = 'frames/';

if (isset($_GET['fr']))
{
    $frameFile = $_GET['fr']; 
    
    if (isset($_GET['cfg']))
        $cfg = $_GET['cfg'];
    else
        $cfg = "LCS";
}
else
{
    echo "Error: No frame source file set";
    exit();
}

$loldatadb = new mysqli('irelia.worldesports.net', 'wesa', 'passabola3911', 'livelcs');

if ($loldatadb->connect_errno > 0)
    die('Unable to connect to database');

$matchImg = new IconAnalyzer(DIR_FRAMES . $frameFile . '.jpg', $cfg);

$time = microtime();
$time = explode(' ', $time);
$time = $time[1] + $time[0];
$start = $time;

if ($matchImg->cropCheck() != false)
{
    $query = 'UPDATE livegame SET ' . 
            'live="' . 0 . '" ' .
            'WHERE id="1"';
    $loldatadb->query($query);
    echo "Invalid screenshot!!!";  
    exit();
}

$matchImg->cropAll();
$data = $matchImg->analyzeAll();

$mtime = $data['match']['matchinfo']['time'];
copy(DIR_FRAMES . $frameFile . '.jpg', DIR_FRAMES . 'match/' . $mtime . '.jpg');

$bTeam = $data['match']['matchinfo']['blueteam'];
$rTeam = $data['match']['matchinfo']['redteam'];

$bTeamName = getTeamName($bTeam);
$rTeamName = getTeamName($rTeam);
$bKills = getTeamKills("blue", $data);
$rKills = getTeamKills("red", $data);

$query = 'UPDATE livegame SET ' . 
            'live="' . 1 . '", ' .
            'blueTeam="' . $bTeam . '", ' . 
            'blueTeamName="' . $bTeamName . '", ' . 
            'redTeam="' . $rTeam . '", ' . 
            'redTeamName="' . $rTeamName . '", ' . 
            'blueKills="' . $bKills . '", ' . 
            'blueGold="' . $data['match']['matchinfo']['bluegold'] . '", ' . 
            'redKills="' . $rKills . '", ' . 
            'redGold="' . $data['match']['matchinfo']['redgold'] . '", ' . 
            'time="' . $data['match']['matchinfo']['time'] . '" ' . 
            'WHERE id="1"';
$loldatadb->query($query);

updatePlayer("blue", 0, $data, $loldatadb);
updatePlayer("blue", 1, $data, $loldatadb);
updatePlayer("blue", 2, $data, $loldatadb);
updatePlayer("blue", 3, $data, $loldatadb);
updatePlayer("blue", 4, $data, $loldatadb);
updatePlayer("red", 0, $data, $loldatadb);
updatePlayer("red", 1, $data, $loldatadb);
updatePlayer("red", 2, $data, $loldatadb);
updatePlayer("red", 3, $data, $loldatadb);
updatePlayer("red", 4, $data, $loldatadb);

$time = microtime();
$time = explode(' ', $time);
$time = $time[1] + $time[0];
$finish = $time;
$total_time = round(($finish - $start), 4);
echo 'Match analyzed in '.$total_time.' seconds.';

function getTeamName($bteam)
{
    if ($bteam == "CRS")
        $bTeamName = "Curse Gaming";
    elseif ($bteam == "TSM")
        $bTeamName = "TSM Snapdragon";
    elseif ($bteam == "MRN")
        $bTeamName = "Team MRN";
    elseif ($bteam == "DIG")
        $bTeamName = "Team Dignitas";
    elseif ($bteam == "CLG")
        $bTeamName = "Counter Logic Gaming";
    elseif ($bteam == "COL")
        $bTeamName = "compLexity";
    elseif ($bteam == "GGU")
        $bTeamName = "Good Game University";
    elseif ($bteam == "VCN")
        $bTeamName = "Vulcun Command";
    else
        $bTeamName = "Unknown";
    
    return $bTeamName;
}

function getTeamKills($bteam, $data)
{
    $kills = 0;
    for ($i = 0; $i < 5; $i++)
    {
        $kills += $data['match']['playerstats'][$bteam][$i]['score']['kills'];
    }
    
    return $kills;
}

function updatePlayer($team, $id, $data, $db)
{
    $score = $data['match']['playerstats'][$team][$id]['score']['kills'] .'/'. $data['match']['playerstats'][$team][$id]['score']['deaths'] .'/'. $data['match']['playerstats'][$team][$id]['score']['assists'];
    
    $query = 'UPDATE player SET ' . 
         'champ="' . $data['match']['playerstats'][$team][$id]['champ'] . '", ' . 
         'spellD="' . $data['match']['playerstats'][$team][$id]['sspell']['D'] . '", ' . 
         'spellF="' . $data['match']['playerstats'][$team][$id]['sspell']['F'] . '", ' . 
         'score="' . $score . '", ' . 
         'cs="' . $data['match']['playerstats'][$team][$id]['cscore'] . '", ' . 
         'item0="' . $data['match']['playerstats'][$team][$id]['items'][0] . '", ' . 
         'item1="' . $data['match']['playerstats'][$team][$id]['items'][1] . '", ' . 
         'item2="' . $data['match']['playerstats'][$team][$id]['items'][2] . '", ' . 
         'item3="' . $data['match']['playerstats'][$team][$id]['items'][3] . '", ' . 
         'item4="' . $data['match']['playerstats'][$team][$id]['items'][4] . '", ' . 
         'item5="' . $data['match']['playerstats'][$team][$id]['items'][5] . '" ' . 
         'WHERE team="' . $team . '" AND playerid="' . $id . '"';
    $db->query($query);
}

?>