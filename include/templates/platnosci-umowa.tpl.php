<b>Umowa:</b><br />
<?php
    if($User['user_umowa'] != "" && file_exists(SCIEZKA_OGOLNA.$User['user_umowa'])){
        $Projekty = new Projekty($this->Baza, $this->Uzytkownik, $this->Parametr, $this->Sciezka);
        $Umowa = pathinfo($User['user_umowa']);
        ?>
            <a href="<?php echo $User['user_umowa']; ?>"><img src="images/<?php echo $Projekty->GetIcon($Umowa['basename']); ?>" alt="Umowa"> <?php echo $Umowa['filename']; ?></a>
            &nbsp;&nbsp;&nbsp;<a href="javascript:RemoveUmowa(<?php echo $User['user_id']; ?>)"><img src="/images/bin_empty.gif" alt="Usuń umowę" /></a>
        <?php
    }else{
        ?>
        <a href="javascript:AddUmowa(<?php echo $User['user_id']; ?>)"><img src="/images/add.gif" alt="Dodaj umowę" /> Wprowadź umowę</a>
        <?php
    }
 ?>