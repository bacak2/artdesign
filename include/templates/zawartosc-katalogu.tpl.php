<table border='0' cellpadding='0' cellspacing='0'>
    <?php
        $IleDir = count($files);
        $j = 1;
        $DirLevel = $this->ActualLevel;
        $Fll = explode("/", $_GET['fll']);
        //$this->VAR_DUMP($files);
        foreach($files as $file){
            
    ?>
    <tr>
        <td class="dir<?php echo ($j == $IleDir ? "-no-bck" : ""); ?>">
            <?php
                if(is_dir($Sciezka."/$file")){
                    $Pict = ($this->CheckOpenDir($this->DirContent['level'], $Fll, $file) || isset($CheckPath[$file]) ? ($j == $IleDir ? "tplm.gif" : "tphm.gif") : ($j == $IleDir ? "tplp.gif" : "tphp.gif"));
                    ?>
                    <a href='?modul=<?php echo $this->Parametr; ?>&project=<?php echo $Project; ?>&dir=<?php echo $Katalog.($this->CheckOpenDir($this->DirContent['level'], $Fll, $file) || isset($CheckPath[$file]) ? "&fll=$FllPath&clfll=".trim($FllPath."/$file", "/") : "&fll=".trim($FllPath."/$file", "/")); ?>'><img src="images/<?php echo $Pict; ?>" alt="" class='button-image' /></a>
                    <?php
                }else{
                    echo ($j == $IleDir ? "<img src='images/tpl.gif' alt='$file' />" : "<img src='images/tph.gif' alt='' />");
                }
            ?>
        </td>
        <td class="dir-name" style="white-space: nowrap;">
            <?php
                if(is_dir($Sciezka."/$file")){
                
             ?>
            <a href='?modul=<?php echo $this->Parametr; ?>&project=<?php echo $Project; ?>&dir=<?php echo $Katalog."&fll=".trim($FllPath."/$file", "/"); ?>'><img src='images/<?php echo ($this->CheckOpenDir($this->DirContent['level'], $Fll, $file) || isset($CheckPath[$file]) ? "open" : "close"); ?>-folder.gif' alt='' class='button-image' /> <?php echo $file; ?></a>
            <?php
                if($this->CheckOpenDir($this->DirContent['level'], $Fll, $file) || isset($CheckPath[$file])){
                    if($this->CheckOpenDir($this->DirContent['level'], $Fll, $file)){
                        $this->DirContent['home'] = array('link' => "?modul=$this->Parametr&project=$Project&dir=$Katalog".(count($Fll) > 1 ? "&fll=".$this->GetFllDir($Fll)."&clfll={$_GET['fll']}" : ""), 'name' => $file, 'level' => $DirLevel);
                        $this->DirContent['level']++;
                        $this->ActualLevel++;
                        $this->DirContent['elements'] = array();
                        $this->SciezkaAlias .= "/$file";
                        $this->RealPath = $Sciezka."/$file";
                    }else if($this->DirContent['level'] == $DirLevel && $Katalog == $this->OpenedDir && $_GET['fll'] == trim($FllPath, "/")){
                        $this->DirContent['elements'][] = array('link' => "?modul=$this->Parametr&project=$Project&dir=$Katalog&fll=".trim($FllPath."/$file", "/"), 'name' => $file, 'is_dir' => true, 'level' => $DirLevel);
                        $this->ActualLevel++;
                    }
                    $this->ShowDirContent($Sciezka."/$file", $Project, $Katalog, $CheckPath[$file], $DirLevel, trim($FllPath."/$file", "/"));
                }else if($this->DirContent['level'] == $DirLevel && $Katalog == $this->OpenedDir && $_GET['fll'] == trim($FllPath, "/")){
//                    $this->VAR_DUMP($FllPath);
//                    $this->VAR_DUMP($this->DirContent['level']);
//                    $this->VAR_DUMP($DirLevel);
//                    $this->VAR_DUMP($Katalog);
//                    $this->VAR_DUMP($this->OpenedDir);
                    $this->DirContent['elements'][] = array('link' => "?modul=$this->Parametr&project=$Project&dir=$Katalog&fll=".trim($FllPath."/$file", "/"), 'name' => $file, 'is_dir' => true, 'level' => $DirLevel);
                    //$this->VAR_DUMP(3);
                }
            }else{
                $Target = $this->OpenInNewWindow($file);
                if($this->DirContent['level'] == $DirLevel && $Katalog == $this->OpenedDir && $_GET['fll'] == $FllPath){
                    $this->DirContent['elements'][] = array('link' => $this->GetFileLink($Sciezka, $FllPath, $file, $Katalog, true), 'name' => $file, 'is_dir' => false, 'level' => $this->ActualLevel, 'target' => $Target);
                }
                ?>
                <a href='<?php echo $this->GetFileLink($Sciezka, $FllPath, $file, $Katalog); ?>'<?php echo $Target; ?>><img src='images/<?php echo $this->GetIcon($file) ?>' alt='' class='button-image' /> <?php echo $file; ?></a>
                <?php
            }
            ?>
        </td>
    </tr>
    <?php
            $j++;
        }
    ?>
</table>