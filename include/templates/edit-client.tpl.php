<?php
    $Form = new FormularzSimple();
    $Form->FormStart("EditClient_$ID");
        ?>
        <br /><b>Dodatkowe informacje (nr. tel, e-maile, adres):</b><br />
        <?php
        $Form->PoleTextarea("Save[user_add_info]", $User['user_add_info']);
        ?>
        <br /><a href="javascript:EditClientSave(<?php echo $ID; ?>)"><img src="/images/disk.gif" alt="Zapisz" /> Zapisz</a>
        <?php
    $Form->FormEnd();
 ?>