<?php
    $Form = new FormularzSimple();
    $Form->FormStart("EditInfo_$ID");
        ?>
        <br /><b>INFO:</b><br />
        <?php
        $Form->PoleTextarea("Save[user_main_info]", $User['user_main_info']);
        ?>
        <br /><a href="javascript:EditInfoSave(<?php echo $ID; ?>)"><img src="/images/disk.gif" alt="Zapisz" /> Zapisz</a>
        <?php
    $Form->FormEnd();
 ?>