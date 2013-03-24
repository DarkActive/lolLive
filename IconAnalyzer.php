<?php

ini_set('display_errors', 'On');
error_reporting(E_ALL);

require_once('pHasher.php');
set_time_limit(60);

class IconAnalyzer
{
    public $origImg;
    public $origHeight;
    public $origWidth;
    private $phasher;
    private $loldatadb;
    const PHASH_PRECISION = '85';
    
    function __construct($filename, $cfg = "MLG")
    {
        require_once('configs/' . strtoupper($cfg) . '.php');
        
        
        $this->origImg = $filename;
        list($this->origWidth, $this->origHeight) = getimagesize($filename);
        $this->phasher = PHasher::Instance();
        $this->loldatadb = new mysqli('irelia.worldesports.net', 'wesa_loldata', 'YxhcGvUrda2CcwLJ', 'loldata');
        
        if ($this->loldatadb->connect_errno > 0)
            die('Unable to connect to database');
    }
    
    #region Crop Functions
    
    public function cropAll()
    {
        $this->emptyDirectory('crop/');
        $this->setupCropDir();
        for ($i = 0; $i < 5; $i++)
        {
            $this->cropChampIcon("blue", $i);
            $this->cropChampIcon("red", $i);
            $this->cropSSpellIcon("blue", $i);
            $this->cropSSpellIcon("red", $i);
            $this->cropItems("blue", $i);
            $this->cropItems("red", $i);
            $this->cropCreepScore("blue", $i);
            $this->cropCreepScore("red", $i);
            $this->cropScore("blue", $i);
            $this->cropScore("red", $i);
            $this->cropTeamIcon("blue");
            $this->cropTeamIcon("red");
            $this->cropTime();
            $this->cropGold("blue");
            $this->cropGold("red");
        }
    }   
    
    public function cropCheck()
    {
        $x_crop = 0;
        $y_crop = 0;
        
        if ($this->origWidth == 1920 && $this->origHeight == 1080)
        {
            $x_crop = 663;
            $y_crop = 855;
            
            $canvas = imagecreatetruecolor(600, 60);
            $master = imagecreatefromjpeg($this->origImg);
            
            imagecopy($canvas, $master, 0, 0, $x_crop, $y_crop, $this->origWidth, $this->origHeight);
            imagejpeg($canvas, 'crop/team/check.jpg', 100);
            
            if ($this->phasher->Compare('crop/team/check.jpg', 'icons/team/check.jpg') < 90)
                return false;
        }
        else
        {
            return false;   
        }
        
        return true;
    }
    
    public function cropTeamIcon($team)
    {
        $x_crop = 0;
        $y_crop = 0;
        
        if ($this->origWidth == 1920 && $this->origHeight == 1080)
        {
            if (strtolower($team) == "blue")
            {
                $x_crop = Res1080P::X_BTEAM_CORD;
                $y_crop = Res1080P::Y_BTEAM_CORD;
            }
            else if (strtolower($team) == "red")
            {
                $x_crop = Res1080P::X_RTEAM_CORD;
                $y_crop = Res1080P::Y_RTEAM_CORD;
            }
            else
                return false;
            
            $canvas = imagecreatetruecolor(Res1080P::W_TEAM, Res1080P::H_TEAM);
            $master = imagecreatefromjpeg($this->origImg);
            
            imagecopy($canvas, $master, 0, 0, $x_crop, $y_crop, $this->origWidth, $this->origHeight);
            imagejpeg($canvas, 'crop/team/' . $team . '.jpg', 100);
           
            if (!ctype_alpha(($teamname = $this->teamToText($team))) || in_array(strtoupper($teamname), GenConfig::$ValidTeams) == false)
            {
                if (strtolower($team) == "blue")
                {
                    $x_crop = Res1080P::X_BTEAM2_CORD;
                    $y_crop = Res1080P::Y_BTEAM2_CORD;
                }
                else if (strtolower($team) == "red")
                {
                    $x_crop = Res1080P::X_RTEAM2_CORD;
                    $y_crop = Res1080P::Y_RTEAM2_CORD;
                }
                
                $canvas = imagecreatetruecolor(Res1080P::W_TEAM, Res1080P::H_TEAM);
                $master = imagecreatefromjpeg($this->origImg);
                
                imagecopy($canvas, $master, 0, 0, $x_crop, $y_crop, $this->origWidth, $this->origHeight);
                imagejpeg($canvas, 'crop/team/' . $team . '.jpg', 100);
            }
        }
        else
        {
            return false;   
        }
        
        return true;
    }
    
    public function cropTime()
    {
        $x_crop = 0;
        $y_crop = 0;
        
        if ($this->origWidth == 1920 && $this->origHeight == 1080)
        {
            $x_crop = Res1080P::X_TIME_CORD;
            $y_crop = Res1080P::Y_TIME_CORD;
            
            $canvas = imagecreatetruecolor(Res1080P::W_TIME, Res1080P::H_TIME);
            $master = imagecreatefromjpeg($this->origImg);
            
            imagecopy($canvas, $master, 0, 0, $x_crop, $y_crop, $this->origWidth, $this->origHeight);
            imagejpeg($canvas, 'crop/team/time.jpg', 100);
        }
        else
        {
            return false;   
        }
        
        return true;
    }
    
    public function cropGold($team)
    {
        $x_crop = 0;
        $y_crop = 0;
        
        if ($this->origWidth == 1920 && $this->origHeight == 1080)
        {
            if (strtolower($team) == "blue")
            {
                $x_crop = Res1080P::X_BGOLD_CORD;
                $y_crop = Res1080P::Y_BGOLD_CORD;
            }
            else if (strtolower($team) == "red")
            {
                $x_crop = Res1080P::X_RGOLD_CORD;
                $y_crop = Res1080P::Y_RGOLD_CORD;
            }
            else
                return false;
            
            $canvas = imagecreatetruecolor(Res1080P::W_GOLD, Res1080P::H_GOLD);
            $master = imagecreatefromjpeg($this->origImg);
            
            imagecopy($canvas, $master, 0, 0, $x_crop, $y_crop, $this->origWidth, $this->origHeight);
            imagefilter($canvas, IMG_FILTER_GRAYSCALE);
            imagejpeg($canvas, 'crop/team/' . $team . '-gold.jpg', 100);
            
            exec('convert crop/team/' . $team . '-gold.jpg -unsharp 10x3.2+2.5+0.001 crop/team/' . $team . '-gold.jpg');
            exec('convert crop/team/' . $team . '-gold.jpg -resize 150x43 crop/team/' . $team . '-gold.jpg');
            exec('convert crop/team/' . $team . '-gold.jpg -unsharp 15x3.9+4.0+0.001 crop/team/' . $team . '-gold.jpg');
            exec('convert crop/team/' . $team . '-gold.jpg -unsharp 200x14.1+1.0+0.001 crop/team/' . $team . '-gold.jpg');
        }
        else
        {
            return false;   
        }
        
        return true;
    }
    
    public function cropChampIcon($team, $index)
    {
        $x_crop = 0;
        $y_crop = 0;
        
        if ($this->origWidth == 1920 && $this->origHeight == 1080)
        {
            if (strtolower($team) == "blue")
            {
                $x_crop = Res1080P::X_BCHAMP_CORD;
                $y_crop = Res1080P::Y_BCHAMP_CORD + ($index * Res1080P::Y_CHAMP_OFFSET);
            }
            else if (strtolower($team) == "red")
            {
                $x_crop = Res1080P::X_RCHAMP_CORD;
                $y_crop = Res1080P::Y_RCHAMP_CORD + ($index * Res1080P::Y_CHAMP_OFFSET);
            }
            else
                return false;
            
            $canvas = imagecreatetruecolor(Res1080P::W_CHAMP, Res1080P::H_CHAMP);
            $master = imagecreatefromjpeg($this->origImg);
            
            imagecopy($canvas, $master, 0, 0, $x_crop, $y_crop, $this->origWidth, $this->origHeight);
            imagejpeg($canvas, 'crop/champ/' . $team . $index . '.jpg', 75);
        }
        else
        {
            return false;   
        }
        
        return true;
    }
    
    public function cropSSpellIcon($team, $index)
    {
        $xD_crop = 0;
        $yD_crop = 0;
        $xF_crop = 0;
        $yF_crop = 0;
        
        if ($this->origWidth == 1920 && $this->origHeight == 1080)
        {
            if (strtolower($team) == "blue")
            {
                $xD_crop = Res1080P::X_BSSPELL_CORD; 
                $yD_crop = Res1080P::Y_BSSPELL_CORD + ($index * Res1080P::Y_SSPELL_OFFSET);
                
                $xF_crop = Res1080P::X_BSSPELL_CORD;
                $yF_crop = (Res1080P::Y_BSSPELL_CORD + ($index * Res1080P::Y_SSPELL_OFFSET)) + Res1080P::Y_SSPELL_OFFSS;
            }
            else if (strtolower($team) == "red")
            {
                $xD_crop = Res1080P::X_RSSPELL_CORD;
                $yD_crop = Res1080P::Y_RSSPELL_CORD + ($index * Res1080P::Y_SSPELL_OFFSET);
                
                $xF_crop = Res1080P::X_RSSPELL_CORD;
                $yF_crop = Res1080P::Y_RSSPELL_CORD + ($index * Res1080P::Y_SSPELL_OFFSET) + Res1080P::Y_SSPELL_OFFSS;
            }
            else
                return false;
            
            $canvasD = imagecreatetruecolor(Res1080P::W_SSPELL, Res1080P::H_SSPELL);
            $canvasF = imagecreatetruecolor(Res1080P::W_SSPELL, Res1080P::H_SSPELL);
            $master = imagecreatefromjpeg($this->origImg);
            
            imagecopy($canvasD, $master, 0, 0, $xD_crop, $yD_crop, $this->origWidth, $this->origHeight);
            imagejpeg($canvasD, 'crop/sspell/' . $team . $index . 'D.jpg', 80);
            
            imagecopy($canvasF, $master, 0, 0, $xF_crop, $yF_crop, $this->origWidth, $this->origHeight);
            imagejpeg($canvasF, 'crop/sspell/' . $team . $index . 'F.jpg', 80);
        }
        else
        {
            return false;   
        }
        
        return true;
    }
    
    public function cropItems($team, $index)
    {
        $x0_crop = 0;
        $y0_crop = 0;
        $x1_crop = 0;
        $y1_crop = 0;
        $x2_crop = 0;
        $y2_crop = 0;
        $x3_crop = 0;
        $y3_crop = 0;
        $x4_crop = 0;
        $y4_crop = 0;
        $x5_crop = 0;
        $y5_crop = 0;
        
        if ($this->origWidth == 1920 && $this->origHeight == 1080)
        {
            if (strtolower($team) == "blue")
            {
                $x0_crop = Res1080P::X_BITEM_CORD - Res1080P::X_ITEM_OFF0;
                $y0_crop = Res1080P::Y_BITEM_CORD + ($index * Res1080P::Y_ITEM_OFFSET);
                
                $x1_crop = Res1080P::X_BITEM_CORD - Res1080P::X_ITEM_OFF1;
                $y1_crop = Res1080P::Y_BITEM_CORD + ($index * Res1080P::Y_ITEM_OFFSET);
                
                $x2_crop = Res1080P::X_BITEM_CORD - Res1080P::X_ITEM_OFF2;
                $y2_crop = Res1080P::Y_BITEM_CORD + ($index * Res1080P::Y_ITEM_OFFSET);
                
                $x3_crop = Res1080P::X_BITEM_CORD - Res1080P::X_ITEM_OFF3;
                $y3_crop = Res1080P::Y_BITEM_CORD + ($index * Res1080P::Y_ITEM_OFFSET);
                
                $x4_crop = Res1080P::X_BITEM_CORD - Res1080P::X_ITEM_OFF4;
                $y4_crop = Res1080P::Y_BITEM_CORD + ($index * Res1080P::Y_ITEM_OFFSET);
                
                $x5_crop = Res1080P::X_BITEM_CORD - Res1080P::X_ITEM_OFF5;
                $y5_crop = Res1080P::Y_BITEM_CORD + ($index * Res1080P::Y_ITEM_OFFSET);
            }
            else if (strtolower($team) == "red")
            {
                $x0_crop = Res1080P::X_RITEM_CORD + Res1080P::X_ITEM_OFF0;
                $y0_crop = Res1080P::Y_RITEM_CORD + ($index * Res1080P::Y_ITEM_OFFSET);
                
                $x1_crop = Res1080P::X_RITEM_CORD + Res1080P::X_ITEM_OFF1;
                $y1_crop = Res1080P::Y_RITEM_CORD + ($index * Res1080P::Y_ITEM_OFFSET);
                
                $x2_crop = Res1080P::X_RITEM_CORD + Res1080P::X_ITEM_OFF2;
                $y2_crop = Res1080P::Y_RITEM_CORD + ($index * Res1080P::Y_ITEM_OFFSET);
                
                $x3_crop = Res1080P::X_RITEM_CORD + Res1080P::X_ITEM_OFF3;
                $y3_crop = Res1080P::Y_RITEM_CORD + ($index * Res1080P::Y_ITEM_OFFSET);
                
                $x4_crop = Res1080P::X_RITEM_CORD + Res1080P::X_ITEM_OFF4;
                $y4_crop = Res1080P::Y_RITEM_CORD + ($index * Res1080P::Y_ITEM_OFFSET);
                
                $x5_crop = Res1080P::X_RITEM_CORD + Res1080P::X_ITEM_OFF5;
                $y5_crop = Res1080P::Y_RITEM_CORD + ($index * Res1080P::Y_ITEM_OFFSET);
            }
            else
                return false;
            
            $canvas0 = imagecreatetruecolor(Res1080P::W_ITEM, Res1080P::H_ITEM);
            $canvas1 = imagecreatetruecolor(Res1080P::W_ITEM, Res1080P::H_ITEM);
            $canvas2 = imagecreatetruecolor(Res1080P::W_ITEM, Res1080P::H_ITEM);
            $canvas3 = imagecreatetruecolor(Res1080P::W_ITEM, Res1080P::H_ITEM);
            $canvas4 = imagecreatetruecolor(Res1080P::W_ITEM, Res1080P::H_ITEM);
            $canvas5 = imagecreatetruecolor(Res1080P::W_ITEM, Res1080P::H_ITEM);
            $master = imagecreatefromjpeg($this->origImg);
            
            imagecopy($canvas0, $master, 0, 0, $x0_crop, $y0_crop, $this->origWidth, $this->origHeight);
            imagejpeg($canvas0, 'crop/item/' . $team . $index . '-0.jpg', 100);
            
            imagecopy($canvas1, $master, 0, 0, $x1_crop, $y1_crop, $this->origWidth, $this->origHeight);
            imagejpeg($canvas1, 'crop/item/' . $team . $index . '-1.jpg', 100);
            
            imagecopy($canvas2, $master, 0, 0, $x2_crop, $y2_crop, $this->origWidth, $this->origHeight);
            imagejpeg($canvas2, 'crop/item/' . $team . $index . '-2.jpg', 100);
            
            imagecopy($canvas3, $master, 0, 0, $x3_crop, $y3_crop, $this->origWidth, $this->origHeight);
            imagejpeg($canvas3, 'crop/item/' . $team . $index . '-3.jpg', 100);
            
            imagecopy($canvas4, $master, 0, 0, $x4_crop, $y4_crop, $this->origWidth, $this->origHeight);
            imagejpeg($canvas4, 'crop/item/' . $team . $index . '-4.jpg', 100);
            
            imagecopy($canvas5, $master, 0, 0, $x5_crop, $y5_crop, $this->origWidth, $this->origHeight);
            imagejpeg($canvas5, 'crop/item/' . $team . $index . '-5.jpg', 100);
        }
        else
        {
            return false;   
        }
        
        return true;
    }
    
    public function cropCreepScore($team, $index)
    {
        $x_crop = 0;
        $y_crop = 0;
        
        if ($this->origWidth == 1920 && $this->origHeight == 1080)
        {
            if (strtolower($team) == "blue")
            {
                $x_crop = Res1080P::X_BCSCORE_CORD;
                $y_crop = Res1080P::Y_BCSCORE_CORD + ($index * Res1080P::Y_CSCORE_OFFSET);
            }
            else if (strtolower($team) == "red")
            {
                $x_crop = Res1080P::X_RCSCORE_CORD;
                $y_crop = Res1080P::Y_RCSCORE_CORD + ($index * Res1080P::Y_CSCORE_OFFSET);
            }
            else
                return false;
            
            $canvas = imagecreatetruecolor(Res1080P::W_CSCORE, Res1080P::H_CSCORE);
            $master = imagecreatefromjpeg($this->origImg);
            
            imagecopy($canvas, $master, 0, 0, $x_crop, $y_crop, $this->origWidth, $this->origHeight);
            $resize = imagecreatetruecolor(Res1080P::W_CSCORE * 3, Res1080P::H_CSCORE * 3);
            imagecopyresampled($resize, $canvas, 0, 0, 0, 0, Res1080P::W_CSCORE * 3, Res1080P::H_CSCORE * 3, Res1080P::W_CSCORE, Res1080P::H_CSCORE);
            imagejpeg($resize, 'crop/cscore/' . $team . $index . '.jpg', 100);    
        }
        else
        {
            return false;   
        }
        
        return true;
    }
    
    public function cropScore($team, $index)
    {
        $x_crop = 0;
        $y_crop = 0;
        
        if ($this->origWidth == 1920 && $this->origHeight == 1080)
        {
            if (strtolower($team) == "blue")
            {
                $x_crop = Res1080P::X_BSCORE_CORD;
                $y_crop = Res1080P::Y_BSCORE_CORD + ($index * Res1080P::Y_SCORE_OFFSET);
            }
            else if (strtolower($team) == "red")
            {
                $x_crop = Res1080P::X_RSCORE_CORD;
                $y_crop = Res1080P::Y_RSCORE_CORD + ($index * Res1080P::Y_SCORE_OFFSET);
            }
            else
                return false;
            
            $canvas = imagecreatetruecolor(Res1080P::W_SCORE, Res1080P::H_SCORE);
            $master = imagecreatefromjpeg($this->origImg);
            
            imagecopy($canvas, $master, 0, 0, $x_crop, $y_crop, $this->origWidth, $this->origHeight);
            imagejpeg($canvas, 'crop/score/' . $team . $index . '.jpg', 100);
        }
        else
        {
            return false;   
        }
        
        return true;
    }
    
    #endregion
    
    #region Tesseract Functions
    
    public function gold2Text($team)
    {
        if ($team == "blue" || $team == "red")
        {
            $img = 'crop/team/' . $team . '-gold.jpg';
            
            unlink('out.txt');
            exec('tesseract ' . $img . ' out -l eng -psm 7');
            
            $buffer = "-1";
            $handle = @fopen('out.txt', 'r');
            if ($handle)
            {
                $buffer = fgets($handle, 4096);
                $buffer = str_replace("\n", "", $buffer);
                fclose($handle);
            }
            
            $buffer = str_replace(";,", ".7", $buffer);
                
            $buffer = str_replace(",", "", $buffer);
            $buffer = str_replace("'", "", $buffer);
            $buffer = str_replace('"', "", $buffer);
            $buffer = str_replace("`", "", $buffer);
            $buffer = str_replace("‘", "", $buffer);
            $buffer = str_replace("’", "", $buffer);
            $buffer = str_replace("g", "8", $buffer);
            $buffer = str_replace("Z", "2", $buffer);
            $buffer = str_replace("S", "5", $buffer);
            $buffer = str_replace("I", "1", $buffer);
            $buffer = str_replace("l", "1", $buffer);
            $buffer = str_replace(";", ".", $buffer);
            $buffer = str_replace(":", "", $buffer);
            $buffer = str_replace("-", "", $buffer);
            $buffer = str_replace("_", "", $buffer);
            $buffer = str_replace("t", "", $buffer);
            $buffer = str_replace("T", "", $buffer);
            $buffer = str_replace("1(", "k", $buffer);
            $buffer = str_replace("1<", "k", $buffer);
            $buffer = str_replace("k.", "k", $buffer);
            $buffer = str_replace("“", "k", $buffer);
            $buffer = str_replace("~", "", $buffer);
            $buffer = str_replace("..", ".", $buffer);
            $buffer = str_replace(" ", "", $buffer);
            
            if (($pos1 = substr($buffer, 0, 1)) == "7" && ($pos2 = substr($buffer, 1, 1)) == ".")
            {
                $temp1 = substr($buffer, 0, 2);
                $temp2 = substr($buffer, 2, strlen($buffer) - 2);
                $temp1 = str_replace("7.", "2", $temp1);
                $buffer = $temp1 . $temp2;
            }
            
            if (($pos1 = substr($buffer, 0, 1)) == ".")
            {
                $temp1 = substr($buffer, 0, 1);
                $temp2 = substr($buffer, 1, strlen($buffer) - 1);
                $temp1 = str_replace(".", "", $temp1);
                $buffer = $temp1 . $temp2;
            }
            
            $temp1 = substr($buffer, 0, 2);
            $temp2 = substr($buffer, 2, strlen($buffer) - 2);
            if (strstr($temp1, ".") != false && strstr($temp2, ".") != false)
            {
                $temp1 = str_replace(".", "", $temp1);
                $buffer = $temp1 . $temp2;
            }
            
            // Too many decimal points
            if (strlen(strstr($buffer, '.')) > 3)
            {
                $buffer = str_replace(".13", ".6", $buffer);
            }
            
            if (strstr($buffer, '.') == false)
            {
                $end = substr($buffer, strlen($buffer) - 2, 2);
                $start = substr($buffer, 0, strlen($buffer) - 2);
                $buffer = $start . '.' . $end;
            }
            
            return $buffer;
        }
        
        return "UNKNOWN";
    }
    
    private function teamToText($team)
    {
        if ($team == "blue" || $team == "red")
        {
            $img = 'crop/team/' . $team . '.jpg';
            unlink('out.txt');
            exec('tesseract ' . $img . ' out -l eng -psm 7');
            
            $buffer = "-1";
            $handle = @fopen('out.txt', 'r');
            if ($handle)
            {
                $buffer = fgets($handle, 4096);
                $buffer = str_replace("\n", "", $buffer);
                fclose($handle);
            }
            
            // Incorrect Read Fixes
            $buffer = str_replace("5", "S", $buffer);
            $buffer = str_replace("|", "I", $buffer);
            $buffer = strtoupper($buffer);
            
            return $buffer;
        }
        
        return "Unknown";
    }
    
    private function timeToText()
    {
        $img = 'crop/team/time.jpg';
        $this->applyPMDUnsharp($img);
        exec('convert ' . $img . ' -resize 120x54 ' . $img);
        unlink('out.txt');
        exec('tesseract ' . $img . ' out -l eng -psm 7');
        
        $buffer = "-1";
        $handle = @fopen('out.txt', 'r');
        if ($handle)
        {
            $buffer = fgets($handle, 4096);
            $buffer = str_replace("\n", "", $buffer);
            fclose($handle);
        }
        
        // If not found, or found bogus characters, try PMDUnsharp
        $numtest = str_replace(":", "", $buffer);
        if ($buffer == "" && !ctype_digit($numtest))
        {
            $this->applyPMDUnsharp($img);
            unlink('out.txt');
            exec('tesseract ' . $img . ' out -l eng -psm 7');
            $handle = @fopen('out.txt', 'r');
            if ($handle)
            {
                $buffer = fgets($handle, 4096);
                $buffer = str_replace("\n", "", $buffer);
                fclose($handle);
            }
        }
        
        echo "ORIG Time: " . $buffer;
        
        // weird case where 1!) == 00
        $buffer = str_replace("1!)", "00", $buffer);
        
        // Unsure
        $buffer = str_replace("-1", "4", $buffer);
        
        $buffer = str_replace(",-", ":", $buffer);
        $buffer = str_replace("-", "", $buffer);
        $buffer = str_replace("I", "1", $buffer);
        $buffer = str_replace("|", "1", $buffer);
        $buffer = str_replace("S", "5", $buffer);
        $buffer = str_replace("Z", "2", $buffer);
        $buffer = str_replace("H", "4", $buffer);
        $buffer = str_replace("Q", "0", $buffer);
        $buffer = str_replace("O", "0", $buffer);
        $buffer = str_replace("?", "7", $buffer);
        $buffer = str_replace("!", "1", $buffer);
        $buffer = str_replace("$", "5", $buffer);
        $buffer = str_replace(".", "", $buffer);
        $buffer = str_replace(":9", ":0", $buffer);
        $buffer = str_replace("l", "1", $buffer);
        $buffer = str_replace(" ", "", $buffer);
        
        if (strstr($buffer, ":") == false)
        {
            $mins = substr($buffer, 0, strlen($buffer) - 2);
            $secs = substr($buffer, strlen($buffer) - 2, 2);
            $buffer = $mins . ":" . $secs;
        }
        
        return $buffer;
    }
    
    private function imageToText($img)
    {
        unlink('out.txt');
        exec('tesseract ' . $img . ' out -l lolnum -psm 7');
        
        $buffer = "-1";
        $handle = @fopen('out.txt', 'r');
        if ($handle)
        {
            $buffer = fgets($handle, 4096);
            $buffer = str_replace("\n", "", $buffer);
            fclose($handle);
        }
        
        // Try a sharpen if not found, maybe do multiple sharpen steps in future
        if ($buffer == "")
        {
            $this->applyPMDUnsharp($img);
            unlink('out.txt');
            exec('tesseract ' . $img . ' out -l lolnum -psm 7');
            $handle = @fopen('out.txt', 'r');
            if ($handle)
            {
                $buffer = fgets($handle, 4096);
                $buffer = str_replace("\n", "", $buffer);
                fclose($handle);
            }
        }
        
        $buffer = str_replace("O", "0", $buffer);
        
        return $buffer;
    }
    
    private function imageToTextScore($img)
    {
        $this->applyPMDUnsharp($img);
        exec('convert ' . $img . ' -resize 270x48 ' . $img);
        unlink('out.txt');
        exec('tesseract ' . $img . ' out -l lolslash -psm 7');
        
        $buffer = "-1";
        $handle = @fopen('out.txt', 'r');
        if ($handle)
        {
            $buffer = fgets($handle, 4096);
            $buffer = str_replace("\n", "", $buffer);
            fclose($handle);
        }
        
        // Try a sharpen if not found, maybe do multiple sharpen steps in future
        if ($buffer == "")
        {
            $this->applyPMDUnsharp($img);
            unlink('out.txt');
            exec('tesseract ' . $img . ' out -l lolslash -psm 7');
            $handle = @fopen('out.txt', 'r');
            if ($handle)
            {
                $buffer = fgets($handle, 4096);
                $buffer = str_replace("\n", "", $buffer);
                fclose($handle);
            }
        }
        
        return $buffer;
    }
    
    #endregion
    
    #region Analyze Images
    
    public function analyzeAll()
    {
        $data = array(
            'match' => array(
                'matchinfo' => array(
                    'blueteam' => $this->teamToText("blue"),
                    'redteam' => $this->teamToText("red"),
                    'time' => $this->timeToText(),
                    'bluegold' => $this->gold2Text("blue"),
                    'redgold' => $this->gold2Text("red")
                ),
                'playerstats' => array(
                    'blue' => array (
                        '0' => array(
                            'champ' => $this->getPlayerChamps("blue", 0),
                            'sspell' => $this->getPlayerSSpells("blue", 0),
                            'items' => $this->getPlayerItems("blue", 0),
                            'score' => $this->getPlayerScore("blue", 0),
                            'cscore' => $this->getPlayerCScore("blue", 0)
                        ),
                        '1' => array(
                            'champ' => $this->getPlayerChamps("blue", 1),
                            'sspell' => $this->getPlayerSSpells("blue", 1),
                            'items' => $this->getPlayerItems("blue", 1),
                            'score' => $this->getPlayerScore("blue", 1),
                            'cscore' => $this->getPlayerCScore("blue", 1)
                        ),
                        '2' => array(
                            'champ' => $this->getPlayerChamps("blue", 2),
                            'sspell' => $this->getPlayerSSpells("blue", 2),
                            'items' => $this->getPlayerItems("blue", 2),
                            'score' => $this->getPlayerScore("blue", 2),
                            'cscore' => $this->getPlayerCScore("blue", 2)
                        ),
                        '3' => array(
                            'champ' => $this->getPlayerChamps("blue", 3),
                            'sspell' => $this->getPlayerSSpells("blue", 3),
                            'items' => $this->getPlayerItems("blue", 3),
                            'score' => $this->getPlayerScore("blue", 3),
                            'cscore' => $this->getPlayerCScore("blue", 3)
                        ),
                        '4' => array(
                            'champ' => $this->getPlayerChamps("blue", 4),
                            'sspell' => $this->getPlayerSSpells("blue", 4),
                            'items' => $this->getPlayerItems("blue", 4),
                            'score' => $this->getPlayerScore("blue", 4),
                            'cscore' => $this->getPlayerCScore("blue", 4)
                        )
                    ),
                    'red' => array(
                        '0' => array(
                            'champ' => $this->getPlayerChamps("red", 0),
                            'sspell' => $this->getPlayerSSpells("red", 0),
                            'items' => $this->getPlayerItems("red", 0),
                            'score' => $this->getPlayerScore("red", 0),
                            'cscore' => $this->getPlayerCScore("red", 0)
                        ),
                        '1' => array(
                            'champ' => $this->getPlayerChamps("red", 1),
                            'sspell' => $this->getPlayerSSpells("red", 1),
                            'items' => $this->getPlayerItems("red", 1),
                            'score' => $this->getPlayerScore("red", 1),
                            'cscore' => $this->getPlayerCScore("red", 1)
                        ),
                        '2' => array(
                            'champ' => $this->getPlayerChamps("red", 2),
                            'sspell' => $this->getPlayerSSpells("red", 2),
                            'items' => $this->getPlayerItems("red", 2),
                            'score' => $this->getPlayerScore("red", 2),
                            'cscore' => $this->getPlayerCScore("red", 2)
                        ),
                        '3' => array(
                            'champ' => $this->getPlayerChamps("red", 3),
                            'sspell' => $this->getPlayerSSpells("red", 3),
                            'items' => $this->getPlayerItems("red", 3),
                            'score' => $this->getPlayerScore("red", 3),
                            'cscore' => $this->getPlayerCScore("red", 3)
                        ),
                        '4' => array(
                            'champ' => $this->getPlayerChamps("red", 4),
                            'sspell' => $this->getPlayerSSpells("red", 4),
                            'items' => $this->getPlayerItems("red", 4),
                            'score' => $this->getPlayerScore("red", 4),
                            'cscore' => $this->getPlayerCScore("red", 4)
                        )
                    )
                 )
            )
        );
        
        $this->printr($data);
        
        return $data;
    }
    
    private function printr ( $object , $name = '' ) {

        //print ( '\'' . $name . '\' : ' ) ;

        if ( is_array ( $object ) ) {
            print ( '<pre>' )  ;
            print_r ( $object ) ; 
            print ( '</pre>' ) ;
        } else {
            var_dump ( $object ) ;
        }

    }
    
    public function getAllItems()
    {
        $time = microtime();
        $time = explode(' ', $time);
        $time = $time[1] + $time[0];
        $start = $time;
        
        for ($i = 0; $i < 5; $i++)
        {
            $items['blue'][$i] =  $this->getPlayerItems("blue", $i);
            $items['red'][$i] =  $this->getPlayerItems("red", $i); 
        }
        
        $time = microtime();
        $time = explode(' ', $time);
        $time = $time[1] + $time[0];
        $finish = $time;
        $total_time = round(($finish - $start), 4);
        echo 'Page generated in '.$total_time.' seconds.';
        
        $this->printr($items);
    }
    
    public function getPlayerItems($team, $index)
    {
        $similar_items = array(3101, 3115, 2003, 2004, 1027, 1028, 2037, 2039, 2043);
        
        $cropdir = 'crop/item';
        $icondir = 'icons/item'; 
        $items = array();
        $img0 = $cropdir . '/' . strtolower($team) . $index . '-0.jpg';
        $img1 = $cropdir . '/' . strtolower($team) . $index . '-1.jpg';
        $img2 = $cropdir . '/' . strtolower($team) . $index . '-2.jpg';
        $img3 = $cropdir . '/' . strtolower($team) . $index . '-3.jpg';
        $img4 = $cropdir . '/' . strtolower($team) . $index . '-4.jpg';
        $img5 = $cropdir . '/' . strtolower($team) . $index . '-5.jpg';
        $max0 = 0; $max1 = 0; $max2 = 0; $max3 = 0; $max4 = 0; $max5 = 0;
        $match0 = "NONE"; $match1 = "NONE"; $match2 = "NONE"; $match3 = "NONE"; $match4 = "NONE"; $match5 = "NONE";
        
        if ($handle = opendir($icondir))
        {
            while (false !== ($entry = readdir($handle)))
            {
                if ($entry != '.' && $entry != '..')
                {
                    $pct = $this->phasher->Compare($img0, $icondir . '/' . $entry);
                    if ($pct > self::PHASH_PRECISION && $max0 < $pct)
                    {
                        $max0 = $pct;
                        $match0 = $entry;
                    }
                    
                    $pct = $this->phasher->Compare($img1, $icondir . '/' . $entry);
                    if ($pct > self::PHASH_PRECISION && $max1 < $pct)
                    {
                        $max1 = $pct;
                        $match1 = $entry;
                    }
                    
                    $pct = $this->phasher->Compare($img2, $icondir . '/' . $entry);
                    if ($pct > self::PHASH_PRECISION && $max2 < $pct)
                    {
                        $max2 = $pct;
                        $match2 = $entry;
                    }
                    
                    $pct = $this->phasher->Compare($img3, $icondir . '/' . $entry);
                    if ($pct > self::PHASH_PRECISION && $max3 < $pct)
                    {
                        $max3 = $pct;
                        $match3 = $entry;
                    }
                    
                    $pct = $this->phasher->Compare($img4, $icondir . '/' . $entry);
                    if ($pct > self::PHASH_PRECISION && $max4 < $pct)
                    {
                        $max4 = $pct;
                        $match4 = $entry;
                    }
                    
                    $pct = $this->phasher->Compare($img5, $icondir . '/' . $entry);
                    if ($pct > self::PHASH_PRECISION && $max5 < $pct)
                    {
                        $max5 = $pct;
                        $match5 = $entry;
                    }
                }
            }   
            if ($max0 != 0)
            {
                if (($itemid = substr($match0, 0, 4)) != "ENCH")
                    $items[0] = $itemid;
                else
                    $items[0] = substr($match0, 5, 4);
            }
            else
                $items[0] = 0;
            
            if ($max1 != 0)
            {
                if (($itemid = substr($match1, 0, 4)) != "ENCH")
                    $items[1] = $itemid;
                else
                    $items[1] = substr($match1, 5, 4);
            }
            else
                $items[1] = 0;
            
            if ($max2 != 0)
            {
                if (($itemid = substr($match2, 0, 4)) != "ENCH")
                    $items[2] = $itemid;
                else
                    $items[2] = substr($match2, 5, 4);
            }
            else
                $items[2] = 0;
            
            if ($max3 != 0)
            {
                if (($itemid = substr($match3, 0, 4)) != "ENCH")
                    $items[3] = $itemid;
                else
                    $items[3] = substr($match3, 5, 4);
            }
            else
                $items[3] = 0;
            
            if ($max4 != 0)
            {
                if (($itemid = substr($match4, 0, 4)) != "ENCH")
                    $items[4] = $itemid;
                else
                    $items[4] = substr($match4, 5, 4);
            }
            else
                $items[4] = 0;
            
            if ($max5 != 0)
            {
                if (($itemid = substr($match5, 0, 4)) != "ENCH")
                    $items[5] = $itemid;
                else
                    $items[5] = substr($match5, 5, 4);
            }
            else
                $items[5] = 0;
         
            closedir($handle);
        }
        
        for ($i = 0; $i < count($items); $i++)
        {
            if (in_array($items[$i], $similar_items))
            {
                $items[$i] = $this->findColorItem($items[$i], ${'img' . $i});
            }
            elseif ($items[$i] == "EMPT")
            {
                $items[$i] = 0;
            }
        }
        
        return $items;
    }
    
    public function getAllChamps()
    {
        $time = microtime();
        $time = explode(' ', $time);
        $time = $time[1] + $time[0];
        $start = $time;
        
        for ($i = 0; $i < 5; $i++)
        {
            $champs['blue'][$i] =  $this->getPlayerChamps("blue", $i);
            $champs['red'][$i] =  $this->getPlayerChamps("red", $i); 
        }
        
        $time = microtime();
        $time = explode(' ', $time);
        $time = $time[1] + $time[0];
        $finish = $time;
        $total_time = round(($finish - $start), 4);
        echo 'Champs analyzed in '.$total_time.' seconds.';
        
        $this->printr($champs);
    }
    
    public function getPlayerChamps($team, $index)
    {
        $cropdir = 'crop/champ';
        $icondir = 'icons/champ'; 
        $img = $cropdir . '/' . strtolower($team) . $index . '.jpg';
        $max = 0;
        $match = "NONE";
        
        if ($handle = opendir($icondir))
        {
            while (false !== ($entry = readdir($handle)))
            {
                if ($entry != '.' && $entry != '..')
                {
                    $pct = $this->phasher->Compare($img, $icondir . '/' . $entry);
                    if ($pct > 70 && $max < $pct)
                    {
                        $max = $pct;
                        $match = $entry;
                    }
                }
            }
            
            if ($max != 0)
                return substr($match, 0, strpos($match, '_'));
        }
        
        return "Unknown";
    }
    
    public function getAllCScores()
    {
        $time = microtime();
        $time = explode(' ', $time);
        $time = $time[1] + $time[0];
        $start = $time;
        
        for ($i = 0; $i < 5; $i++)
        {
            $cscores['blue'][$i] =  $this->getPlayerCScore("blue", $i);
            $cscores['red'][$i] =  $this->getPlayerCScore("red", $i); 
        }
        
        $time = microtime();
        $time = explode(' ', $time);
        $time = $time[1] + $time[0];
        $finish = $time;
        $total_time = round(($finish - $start), 4);
        echo 'Champs analyzed in '.$total_time.' seconds.';
        
        $this->printr($cscores);
    }
    
    public function getPlayerCScore($team, $id)
    {
        $imgFile = 'crop/cscore/' . $team . $id . '.jpg';

        return $this->imageToText($imgFile);
    }
    
    public function getAllScores()
    {
        $time = microtime();
        $time = explode(' ', $time);
        $time = $time[1] + $time[0];
        $start = $time;
        
        for ($i = 0; $i < 5; $i++)
        {
            $cscores['blue'][$i] =  $this->getPlayerCScore("blue", $i);
            $cscores['red'][$i] =  $this->getPlayerCScore("red", $i); 
        }
        
        $time = microtime();
        $time = explode(' ', $time);
        $time = $time[1] + $time[0];
        $finish = $time;
        $total_time = round(($finish - $start), 4);
        echo 'Champs analyzed in '.$total_time.' seconds.';
        
        $this->printr($cscores);
    }
    
    public function getPlayerScore($team, $id)
    {
        $imgFile = 'crop/score/' . $team . $id . '.jpg';

        $score = $this->imageToTextScore($imgFile);    
        
        $score = str_replace("O", "0", $score);
       
        // Only 1 "/"
        if (strstr($score, "/") != FALSE && strstr(strstr($score, "/"), "/") == false)
        {
            if (($fr = substr($score, 0, 1)) == "0" && substr($score, 1, 1) != "/")
            {
                $score = $fr . "/" . substr($score, 2);
            }
        }
        
        $tok = strtok($score, '/');
        $score_arr = array (
            'kills' => $tok,
            'deaths' => strtok('/'),
            'assists' => strtok('/')
        );
        
        if (strlen(($temp = $score_arr['kills'])) == 3)
        {
            
            $score_arr['kills'] = substr($temp, 0, 1);
            $score_arr['assists'] = $score_arr['deaths'];
            $score_arr['deaths'] = substr($temp, 2, 1);
        }
        elseif (strlen(($temp = $score_arr['kills'])) == 4)
        {
            $temp = str_replace("11", "/", $temp);
            $tok = strtok($temp, '/');
            $score_arr['kills'] = $tok;
            $score_arr['assists'] = $score_arr['deaths'];
            $score_arr['deaths'] = strtok('/');
        }
        elseif (strlen(($temp = $score_arr['deaths'])) == 3)
        {
            $score_arr['deaths'] = substr($temp, 0, 1);
            $score_arr['assists'] = substr($temp, 2, 1);
        }
        elseif (strlen(($temp = $score_arr['deaths'])) == 4)
        {
            // !!!!!! NOT A GOOD FIX !!!!!! TEMP single case fix (might break on other cases) - see no other way atm
            $temp = str_replace("11", "/", $temp);
            $tok = strtok($temp, '/');
            $score_arr['deaths'] = $tok;
            $score_arr['assists'] = strtok('/');
        }
        elseif (strlen(($temp = $score_arr['kills'])) == 5)
        {
            // !!!!!! NOT A GOOD FIX !!!!!! TEMP single case fix (might break on other cases) - see no other way atm
            $temp = str_replace("11", "/", $temp);
            $tok = strtok($temp, '/');
            $score_arr['kills'] = $tok;
            $score_arr['assists'] = $score_arr['deaths'];
            $score_arr['deaths'] = strtok('/');
        }
        
        // On kills and maybe also deaths & assists, "0" seems to be read as "43" (since 43 unlikely kills, just do regular replace).
        $score_arr['kills'] = str_replace("43", "0", $score_arr['kills']);
        
        // On kills and maybe also deaths & assists, "0" seems to be read as "63" (since 63 unlikely kills, just do regular replace).
        $score_arr['kills'] = str_replace("63", "0", $score_arr['kills']);
        
        // Remove random digits after 0
        if (strlen($score_arr['kills']) > 1 && substr($score_arr['kills'], 0, 1) == "0")
            $score_arr['kills'] = 0;
        
        // Remove random digits after 0
        if (strlen($score_arr['deaths']) > 1 && substr($score_arr['deaths'], 0, 1) == "0")
            $score_arr['deaths'] = 0;
        
        // Remove random digits after 0
        if (strlen($score_arr['assists']) > 1 && substr($score_arr['assists'], 0, 1) == "0")
            $score_arr['assists'] = 0;
        
        return $score_arr;
    }
    
    public function getAllSSpells()
    {
        $time = microtime();
        $time = explode(' ', $time);
        $time = $time[1] + $time[0];
        $start = $time;
        
        for ($i = 0; $i < 5; $i++)
        {
            $sspells['blue'][$i] =  $this->getPlayerSSpells("blue", $i);
            $sspells['red'][$i] =  $this->getPlayerSSpells("red", $i); 
        }
        
        $time = microtime();
        $time = explode(' ', $time);
        $time = $time[1] + $time[0];
        $finish = $time;
        $total_time = round(($finish - $start), 4);
        echo 'Summoner Spells analyzed in '.$total_time.' seconds.';
        
        $this->printr($sspells);
    }
    
    public function getPlayerSSpells($team, $index)
    {
        $cropdir = 'crop/sspell';
        $icondir = 'icons/sspell'; 
        $imgD = $cropdir . '/' . strtolower($team) . $index . 'D.jpg';
        $imgF = $cropdir . '/' . strtolower($team) . $index . 'F.jpg';
        $maxD = 0; $maxF = 0;
        $matchD = ""; $matchF = "";
        
        $sspells['D'] = "Unknown";
        $sspells['F'] = "Unknown";
        
        if ($handle = opendir($icondir))
        {
            while (false !== ($entry = readdir($handle)))
            {
                if ($entry != '.' && $entry != '..')
                {
                    $pct = $this->phasher->Compare($imgD, $icondir . '/' . $entry);
                    if ($pct > 70 && $maxD < $pct)
                    {
                        $maxD = $pct;
                        $matchD = $entry;
                    }
                    $pct = $this->phasher->Compare($imgF, $icondir . '/' . $entry);
                    if ($pct > 70 && $maxF < $pct)
                    {
                        $maxF = $pct;
                        $matchF = $entry;
                    }
                }
            }
            
            if ($maxD != 0)
                $sspells['D'] =  substr($matchD, 0, strpos($matchD, '.'));
            
            if ($maxF != 0)
                $sspells['F'] = substr($matchF, 0, strpos($matchF, '.'));
        }
        
        return $sspells;
    }
    
    private function findColorItem($id, $imgFile)
    {
        if (exif_imagetype($imgFile) != IMAGETYPE_JPEG)
            $img = imagecreatefrompng($imgFile);
        else
            $img = imagecreatefromjpeg($imgFile);
            
        
        if ($id == 2003 || $id == 2004)
        {
            $rgb = imagecolorat($img, 17, 17);
            $colors = imagecolorsforindex($img, $rgb);
            
            if ($colors['red'] > $colors['blue'])
                return 2003;
            else
                return 2004;
        }
        else if ($id == 2037 || $id == 2039)
        {
            $rgb = imagecolorat($img, 17, 17);
            $colors = imagecolorsforindex($img, $rgb);
            
            if ($colors['red'] > $colors['blue'])
                return 2037;
            else
                return 2039;
        }
        else if ($id == 3101 || $id == 3115)
        {
            $rgb = imagecolorat($img, 11, 4);
            $colors = imagecolorsforindex($img, $rgb);
            
            if ($colors['red'] > $colors['blue'] && $colors['green'] > $colors['blue'])
                return 3115;
            else
                return 3101;
        }
        else if ($id == 1027 || $id == 1028)
        {
            $rgb = imagecolorat($img, 14, 14);
            $colors = imagecolorsforindex($img, $rgb);
            
            if ($colors['red'] > $colors['blue'])
                return 1028;
            else
                return 1027;
        }
        else if ($id == 2043 || $id == "EXPL")
        {
            $rgb = imagecolorat($img, 5, 5);
            $colors = imagecolorsforindex($img, $rgb);
            
            if (($colors['red'] + 20) > $colors['blue'])
                return 2043;
            else
                return 2043;
        }

        return -1;
    }
    
    #endregion
    
    #region Private Heleper Functions
    
    private function applyPMDUnsharp($img)
    {
        exec ('convert ' . $img . ' -resize 10000x100%% -unsharp 0x10 -resize 1x100%% -threshold 55%% ' . $img);
    }
    
    private function emptyDirectory($dirname,$self_delete=false) {
        if (is_dir($dirname))
            $dir_handle = opendir($dirname);
        if (!$dir_handle)
            return false;
        while($file = readdir($dir_handle)) {
            if ($file != "." && $file != "..") {
                if (!is_dir($dirname."/".$file))
                    @unlink($dirname."/".$file);
                else
                    $this->emptyDirectory($dirname.'/'.$file,true);    
            }
        }
        closedir($dir_handle);
        if ($self_delete){
            @rmdir($dirname);
        }   
        return true;
    }
    
    private function setupCropDir()
    {
        mkdir('crop/sspell');
        chmod('crop/sspell', 0777);
        mkdir('crop/item');
        chmod('crop/item', 0777);
        mkdir('crop/champ');
        chmod('crop/champ', 0777);
        mkdir('crop/cscore');
        chmod('crop/cscore', 0777);
        mkdir('crop/score');
        chmod('crop/score', 0777);
        mkdir('crop/team');
        chmod('crop/team', 0777);
    }
    
    #endregion
}

?>