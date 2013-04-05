<?php
///////////////////////////////////////////////////////
///                                                 ///
/// LCS - LOL Championship Series (Riot)            ///
/// LOLScoreAnalyzer 1.0                            ///
/// Configuration & Resolution File                 ///
///                                                 ///
/// Last Updated: March 16, 2013                    ///
///                                                 ///
///////////////////////////////////////////////////////

class Res1080P
{   
    #region Team Constants
    const W_TEAM = 110;
    const H_TEAM = 45;
    const X_BTEAM_CORD = 741;
    const Y_BTEAM_CORD = 75;
    const X_RTEAM_CORD = 1070;
    const Y_RTEAM_CORD = 75;
    const X_BTEAM2_CORD = 481;
    const Y_BTEAM2_CORD = 4;
    const X_RTEAM2_CORD = 1318;
    const Y_RTEAM2_CORD = 8;
    #endregion
    
    #region Game Time
    const W_TIME = 40;
    const H_TIME = 18;
    const X_TIME_CORD = 936;
    const Y_TIME_CORD = 76;
    #endregion
    
    #region Gold
    const W_GOLD = 70;
    const H_GOLD = 20;
    const X_BGOLD_CORD = 759;
    const Y_BGOLD_CORD = 29;
    const X_RGOLD_CORD = 1110;
    const Y_RGOLD_CORD = 29;
    #endregion
    
    #region Champion Constants
    const W_CHAMP = 50;
    const H_CHAMP = 50;
    const X_BCHAMP_CORD = 39;
    const Y_BCHAMP_CORD = 258;
    const X_RCHAMP_CORD = 1829;
    const Y_RCHAMP_CORD = 258;
    const Y_CHAMP_OFFSET = 105;
    #endregion
    
    #region Summoner Spell Constants
    const W_SSPELL = 25;
    const H_SSPELL = 25;
    const X_BSSPELL_CORD = 10;
    const Y_BSSPELL_CORD = 285;
    const X_RSSPELL_CORD = 1883;
    const Y_RSSPELL_CORD = 285;
    const Y_SSPELL_OFFSS = 27;
    const Y_SSPELL_OFFSET = 105;
    #endregion
    
    #region Item Constants
    const W_ITEM = 26;
    const H_ITEM = 26;
    const X_BITEM_CORD = 728;
    const Y_BITEM_CORD = 928;
    const X_RITEM_CORD = 1166;
    const Y_RITEM_CORD = 928;
    const X_ITEM_OFF0 = 0;
    const X_ITEM_OFF1 = 26;
    const X_ITEM_OFF2 = 53;
    const X_ITEM_OFF3 = 80;
    const X_ITEM_OFF4 = 106;
    const X_ITEM_OFF5 = 132;
    const Y_ITEM_OFFSET = 30;
    #endregion
    
    #region Creep Score Constants
    const W_CSCORE = 26;
    const H_CSCORE = 16;
    const X_BCSCORE_CORD = 889;
    const Y_BCSCORE_CORD = 931;
    const X_RCSCORE_CORD = 1005;
    const Y_RCSCORE_CORD = 931;
    const Y_CSCORE_OFFSET = 30;
    #endregion
    
    #region Player Score Constants
    const W_SCORE = 90;
    const H_SCORE = 16;
    const X_BSCORE_CORD = 775;
    const Y_BSCORE_CORD = 931;
    const X_RSCORE_CORD = 1054;
    const Y_RSCORE_CORD = 931;
    const Y_SCORE_OFFSET = 30;
    #endregion
    
}

class GenConfig
{
    static $ValidTeams = array("TSM", "VUL", "COL", "CRS", "DIG", "CLG", "MRN", "GGU");
}
?>