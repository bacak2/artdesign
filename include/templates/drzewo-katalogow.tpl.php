<!--[if IE]>
    <style>
    .dir_tree{
        max-height: 480px;

    }
    </style>
<![endif]-->
<!--[if lte IE 8]>
     <style>
    .dir_tree{
        width: 180px;

    }
    </style>
   
<![endif]-->
<div class="dir_tree" style="height: calc(100% - 10px );  width: calc(100% - 10px );  padding-left: 10px; overflow-y: scroll;  display: block; position:absolute">
<table border="0" cellpadding="0" cellspacing="0" id="DirTree">
    <?php
//    <tr>
//        <td><img src="images/house.gif" alt="Home" /></td>
//        <th style="padding-left: 6px;"><a href="?modul=<?php #echo $this->Parametr; "> PROJEKTY </a></th>
///    </tr>
            ?>
    <?php
        $i=1;
        $IleGlownych = count($DirTree[0]);
        $this->DirContent['home'] = array('link' => "./", 'name' => "PROJEKTY");
        $this->DirContent['level'] = 0;
        $this->ActualLevel = 0;
        foreach($DirTree[0] as $DirID => $DirData){
            $Pict2 = ($this->OpenedProject > 0 && $this->OpenedProject == $DirData['project_id'] ? ($i == $IleGlownych ? "tplm.gif" : "tphm.gif") : ($i == $IleGlownych ? "tplp.gif" : "tphp.gif"));
            ?>
            <tr>
                <td class="dir<?php echo ($i == $IleGlownych ? "-no-bck" : ""); ?>"><a href='?modul=<?php echo $this->Parametr."&project={$DirData['project_id']}"; ?>'><img src="images/<?php echo $Pict2; ?>" alt="Open" class="button-image" /></a></td>
                <td>
                    <?php
                        $DirName = str_replace(array("projekty/","projekty_archiwum/"), "", $DirData['dir_real_name']);
                        $DirName = explode("-", $DirName);
                        if($this->OpenedProject > 0 && $this->OpenedProject == $DirData['project_id']){
                            $this->DirContent['home'] = array('link' => "?modul=$this->Parametr", 'name' => $DirName[0]);
                            $this->SciezkaAlias .= "/{$DirName[0]}";
                            $this->DirContent['level'] = 1;
                            $this->ActualLevel = 1;
                            $this->DirContent['elements'] = array();
                            $this->RealPath = $DirData['dir_real_name'];
                            echo "<a href='?modul=$this->Parametr&project={$DirData['project_id']}'><img src='images/open-folder.gif' alt='Close' class='button-image' /> {$DirName[0]}</a>";
                           ?>
                            <table border='0' cellpadding='0' cellspacing='0'>
                                <?php
                                    $IleDir = count($DirTree[$DirID]);
                                    $j = 1;
                                    foreach($DirTree[$DirID] as $SecDirID => $SecDirData){
                                        $Pict = ($this->OpenedDir == $SecDirID || isset($_SESSION['tree'][$this->OpenedProject][$SecDirID]) ? ($j == $IleDir ? "tplm.gif" : "tphm.gif") : ($j == $IleDir ? "tplp.gif" : "tphp.gif"));
                                ?>
                                <tr>
                                    <td class="dir<?php echo ($j == $IleDir ? "-no-bck" : ""); ?>"><a href='?modul=<?php echo $this->Parametr; ?>&project=<?php echo $DirData['project_id'].($this->OpenedDir == $SecDirID || isset($_SESSION['tree'][$this->OpenedProject][$SecDirID]) ? "&cldir=$SecDirID" : "&dir=$SecDirID"); ?>'><img src="images/<?php echo $Pict; ?>" alt="Open" class='button-image' /></a></td>
                                    <td class="dir-name">
                                        <a href='?modul=<?php echo $this->Parametr; ?>&project=<?php echo $DirData['project_id']."&dir=$SecDirID";  ?>'><img src='images/<?php echo ($this->OpenedDir == $SecDirID || isset($_SESSION['tree'][$this->OpenedProject][$SecDirID]) ? "open" : "close"); ?>-folder.gif' alt='Open' class='button-image' /> <?php echo $SecDirData['dir_type']; ?></a>
                                        <?php
                                            if($this->OpenedDir == $SecDirID || isset($_SESSION['tree'][$this->OpenedProject][$SecDirID])){
                                                if($this->OpenedDir == $SecDirID){
                                                    $this->DirContent['home'] = array('link' => "?modul=$this->Parametr&project={$DirData['project_id']}&cldir=$SecDirID", 'name' => $SecDirData['dir_type']);
                                                    $this->SciezkaAlias .= "/{$SecDirData['dir_type']}";
                                                    $this->DirContent['level'] = 2;
                                                    $this->ActualLevel = 2;
                                                    $this->DirContent['elements'] = array();
                                                    $this->RealPath = $SecDirData['dir_real_name'];
                                                }else if($this->DirContent['level'] == 1){
                                                    $this->DirContent['elements'][] = array('link' => "?modul=$this->Parametr&project={$DirData['project_id']}&dir=$SecDirID", 'name' => $SecDirData['dir_type'], 'is_dir' => true);
                                                }
                                                $this->ShowDirContent($SecDirData['dir_real_name'], $DirData['project_id'], $SecDirID, $_SESSION['tree'][$this->OpenedProject][$SecDirID]);
                                             }else if($this->DirContent['level'] == 1){
                                                $this->DirContent['elements'][] = array('link' => "?modul=$this->Parametr&project={$DirData['project_id']}&dir=$SecDirID", 'name' => $SecDirData['dir_type'], 'is_dir' => true);
                                             }
                                        ?>
                                    </td>
                                </tr>
                                <?php
                                        $j++;
                                    }
                                ?>
                            </table>
                            <?php
                        }else{
                            if($this->DirContent['level'] == 0){
                                $this->DirContent['elements'][] = array('link' => "?modul=$this->Parametr&project={$DirData['project_id']}", 'name' => $DirName[0], 'is_dir' => true);
                            }
                            echo "<a href='?modul=$this->Parametr&project={$DirData['project_id']}'><img src='images/close-folder.gif' alt='Open' class='button-image' /> {$DirName[0]}</a>";
                        }
                    ?>
                </td>
            </tr>
            <?php
            $i++;
        }
    ?>
</table>
</div>

