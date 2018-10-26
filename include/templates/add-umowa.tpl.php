<?php
    $Form = new FormularzSimple();
    $Form->FormStart("AddUmowa_$ID");
        ?>
        <?php
        $Form->PoleFile("user_umowa", null);
        $Form->PoleHidden("Save[user_id]", $ID);
        ?>
        <br /><a href="javascript:AddUmowaSave(<?php echo $ID; ?>)"><img src="/images/disk.gif" alt="Zapisz" /> Zapisz</a>
        &nbsp;&nbsp;<a href="javascript:AddUmowaCancel(<?php echo $ID; ?>)"><img src="/images/cancel.gif" alt="Anuluj" /> Anuluj</a>
        <?php
    $Form->FormEnd();
 ?>