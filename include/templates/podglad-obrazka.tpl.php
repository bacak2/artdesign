<div style="width: 850px; margin: 25px auto; display: block; text-align: center;">
    <a href="/show-pict.php?project=<?php echo $_GET['project']; ?>&dir=<?php echo $_GET['dir'].$this->AddCatalogToLink()."&sfll={$_GET['sfll']}"; ?>" target="_blank"><img src="/show-pict.php?project=<?php echo $_GET['project']; ?>&dir=<?php echo $_GET['dir'].$this->AddCatalogToLink()."&sfll={$_GET['sfll']}"; ?>&width=850&height=400" alt="" style="border: 1px solid #E8E8E8;" /></a>
    <br /><br /><br /><br />
    <?php
        $KeysImage = array_keys($this->ImagesInDir);
        $KeysImageInv = array_flip($KeysImage);
        $Index = $KeysImageInv[$_GET['sfll']];
        if($Index > 0){
            $PrevImg = $Index - 1;
            ?>
            <a href="<?php echo $this->ImagesInDir[$KeysImage[$PrevImg]].$this->AddViewToLink(); ?>"><img src="/images/prev-big.gif" alt="Poprzednie" /></a>&nbsp;&nbsp;&nbsp;
            <?php
        }
    ?>
    <a href="/?modul=projekty&project=<?php echo $_GET['project']; ?>&dir=<?php echo $_GET['dir'].$this->AddCatalogToLink().$this->AddViewToLink(); ?>"><img src="/images/back-big.gif" alt="Powrót" /></a>
    <?php
        $IndexNxt = $KeysImageInv[$_GET['sfll']];
        if($IndexNxt < (count($this->ImagesInDir) - 1)){
            $Next = $IndexNxt + 1;
            ?>
            &nbsp;&nbsp;&nbsp;<a href="<?php echo $this->ImagesInDir[$KeysImage[$Next]].$this->AddViewToLink(); ?>"><img src="/images/next-big.gif" alt="Następne" /></a>
            <?php
        }
    ?>
    <p style="font-weight: bold; font-size: 15px; text-align: center;"><?php echo $_GET['sfll']; ?></p><br />
</div>