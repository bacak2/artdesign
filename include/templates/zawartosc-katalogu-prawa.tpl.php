<?php
    $height = 454;
    $width = 650;
    if($this->DirContent['level'] > 1){ 
        ?>
        <div style="width:650px;margin:auto">
            <div style="font-weight: bold; float: left; display: block; padding-right: 12px; border-right: 1px solid #000; margin-bottom: 5px;"><a href="?modul=<?php echo $this->Parametr; ?>&project=<?php echo $this->OpenedProject.$this->AddDirToLink().$this->AddCatalogToLink(); ?>"><img src="images/lista-icon.jpg" alt="LISTA" /> LISTA</a></div>
            <div style="font-weight: bold; padding-left: 12px; float: left; display: block; margin-bottom: 5px;"><a href="?modul=<?php echo $this->Parametr; ?>&project=<?php echo $this->OpenedProject.$this->AddDirToLink().$this->AddCatalogToLink(); ?>&view=miniatury"><img src="images/miniatury-icon.jpg" alt="MINIATURKI" /> MINIATURKI</a></div>
        </div>
        <div style="clear: both;"></div>
        <?php
        $height = 469;
    }
    if($_GET['view'] == "miniatury"){
        $width = 750; 
    }
?>
<div style="height: <?php echo $height; ?>px; overflow-y: auto; width: <?php echo $width; ?>px; margin-left: 30px;  display: block; margin:auto">
<table border='0' cellpadding='4' cellspacing='0' style="width: <?php echo ($width-17); ?>px;">
    <tr>
        <td style="width: 30px; border-bottom: 1px solid #000;"><a href="<?php echo $this->DirContent['home']['link']; ?>">... <img src="/images/open-folder.gif" alt="" /></a></td>
        <th style="border-bottom: 1px solid #000; vertical-align: middle;"><a href="<?php echo $this->DirContent['home']['link']; ?>"><?php echo $this->DirContent['home']['name']; ?></a></th>
        <td class="ikona" style="width: 18px; border-bottom: 1px solid #000;">
            <?php
                if($this->DirContent['level'] > 0){
            ?>
            <a href="/download.php?project=<?php echo $this->OpenedProject.$this->AddDirToLink().$this->AddCatalogToLink(); ?>"><img src="/images/disk.gif" alt="download" /></a>
            <?php
                }else{
                    echo "&nbsp;";
                }
            ?>
        </td>
        <td style="width: 18px; border-bottom: 1px solid #000;">
            <?php
                if(($this->DirContent['level'] > 2 || ($this->DirContent['level'] == 2 && $this->Dostep < 3)) && $this->SprawdzKasowanie($this->OpenedDir)){ 
            ?>
            <a href="?modul=projekty&akcja=kasowanie_folderu&project=<?php echo $this->OpenedProject.($this->OpenedDir ? "&dir=$this->OpenedDir" : "").$this->AddCatalogToLink(); ?>"><img src="/images/bin_empty.gif" alt="usuń" /></a>
            <?php
                }else if($this->Dostep == 1 && $this->DirContent['level'] == 1){
                    ?><a href="?modul=projekty&akcja=archiwizuj&project=<?php echo $this->OpenedProject.$this->AddCatalogToLink(); ?>"><img src="/images/<?php echo ($this->ArchiwumStatus == 0 ? "archive.gif" : "przywroc.gif"); ?>" alt="<?php echo ($this->ArchiwumStatus == 0 ? "archiwizuj" : "przywróć"); ?>" /></a><?php
                }else{
                    echo "&nbsp;";
                }
            ?>
        </td>
    </tr>
    <?php
        $Kolor = "#FFFFFF";
        //$this->VAR_DUMP($this->DirContent['elements']);
        foreach($this->DirContent['elements'] as $File){
            $Kolor = ($Kolor == "#FFFFFF" ? "#F0F0F0" : "#FFFFFF");
            if($File['is_dir']){
                $Img = "close-folder.gif";
                $Link = str_replace("?modul=projekty&", "download.php?", $File['link']);
                $LinkUsun = $File['link']."&akcja=kasowanie_folderu";
                $is_image = false;
            }else{
                $Img = $this->GetIcon($File['name']);
                $Link = "/download.php?project=$this->OpenedProject&dir=$this->OpenedDir".$this->AddCatalogToLink()."&sfll={$File['name']}";
                $LinkUsun = "?modul=projekty&akcja=kasowanie_pliku&project=$this->OpenedProject&dir=$this->OpenedDir".$this->AddCatalogToLink()."&sfll={$File['name']}".$this->AddViewToLink();
                $is_image = Usefull::isImage($File['name']);
            }
            ?>
                <tr>
                    <?php
                        if($_GET['view'] == "miniatury" && $is_image){
                          ?>
                          <td style="background-color: <?php echo $Kolor; ?>; white-space: nowrap;" colspan="2">
                              <a href="<?php echo $File['link'].$this->AddViewToLink(); ?>"<?php echo $File['target']; ?> style="float: left; display: block;"><img src="/show-pict.php?project=<?php echo $this->OpenedProject."&dir=$this->OpenedDir".$this->AddCatalogToLink(); ?>&sfll=<?php echo $File['name']; ?>&width=150" alt="" style="vertical-align: top; margin-right: 7px;" /></a>
                              <a href="<?php echo $File['link'].$this->AddViewToLink(); ?>"<?php echo $File['target']; ?> style="float: left; max-width: 515px; display: block; overflow-x: hidden;"><?php echo $File['name']; ?></a>
                          </td>
                          <?php
                        }else{
                    ?>
                    <td style="background-color: <?php echo $Kolor; ?>;">
                        <a href="<?php echo $File['link']; ?>"<?php echo $File['target']; ?>><img src="/images/<?php echo $Img; ?>" alt="" /></a>
                    </td>
                    <td style="background-color: <?php echo $Kolor; ?>;"><a href="<?php echo $File['link']; ?>"<?php echo $File['target']; ?> style="float: left; max-width: 540px;  display: block; overflow-x: hidden;"><?php echo $File['name']; ?></a></td>
                    <?php
                        }
                    ?>
                    <td style="width: 18px; background-color: <?php echo $Kolor; ?>; vertical-align: top;"><a href="<?php echo $Link; ?>"><img src="/images/disk.gif" alt="download" /></a></td>
                    <td style="width: 18px; background-color: <?php echo $Kolor; ?>; vertical-align: top;">
                      <?php
                            #if(($this->DirContent['level'] > 1 || ($this->DirContent['level'] == 1 && !in_array($File['name'], array('Inspiracje_inwestora', 'Wyceny', 'Pliki_wykonawcy'))))  && $this->SprawdzKasowanie($this->OpenedDir)){
                            if($this->DirContent['level'] > 0  && $this->SprawdzKasowanie($this->OpenedDir)){

                        ?>
                        <a href="<?php echo $LinkUsun; ?>"><img src="/images/bin_empty.gif" alt="usuń" /></a> 
                        <?php
                            }else if($this->Dostep == 1 && $this->DirContent['level'] == 0){
                                ?><a href="<?php echo $File['link']."&akcja=archiwizuj"; ?>"><img src="/images/<?php echo ($this->ArchiwumStatus == 0 ? "archive.gif" : "przywroc.gif"); ?>" alt="<?php echo ($this->ArchiwumStatus == 0 ? "archiwizuj" : "przywróć"); ?>" /></a><?php
                            }else{
                                echo "&nbsp;";
                            }
                            ?>
                    </td>
                </tr>
            <?php
        }
    ?>
</table>
</div>