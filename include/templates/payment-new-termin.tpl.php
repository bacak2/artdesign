<?php
    $Form = new FormularzSimple();
    $Form->FormStart("PaymentEdit_{$User}_$Row");
        ?>
        <table cellpadding="3" cellspacing="0" style="border: 0; border-collapse: collapse;">
            <tr><th>Nowy termin płatności (YYYY-MM-DD):</th><td><?php $Form->PoleInputText("Save[payment_termin]"); ?></td></tr>
            <tr><th>INFO:</th><td><?php $Form->PoleTextarea("Save[payment_info]", $Dane['payment_info']); ?></td></tr>
        </table>
        <br />
        <a href="javascript:SaveNewTermin(<?php echo $User; ?>,<?php echo $Row; ?>,<?php echo $ID; ?>)"><img src="/images/disk.gif" alt="Zapisz" /> Zapisz</a>
        &nbsp;&nbsp;&nbsp;
        <a href="javascript:CancelNewTermin(<?php echo $User; ?>,<?php echo $Row; ?>,<?php echo $ID; ?>)"><img src="/images/cancel.gif" alt="Anuluj" /> Anuluj</a>
        <?php
    $Form->FormEnd(); 
 ?>