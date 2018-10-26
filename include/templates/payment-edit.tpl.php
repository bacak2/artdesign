<?php
    $Form = new FormularzSimple();
    $Form->FormStart("PaymentEdit_{$User}_$Row");
        ?>
        <table cellpadding="3" cellspacing="0" style="border: 0; border-collapse: collapse;">
            <tr><th>Termin płatności (YYYY-MM-DD):</th><td><?php $Form->PoleInputText("Save[payment_termin]", $Dane['payment_termin']); ?></td></tr>
            <tr><th>Kwota płatności:</th><td><?php $Form->PoleInputText("Save[payment_kwota]", $Dane['payment_kwota']); ?></td></tr>
            <tr><th>Nazwa etapu:</th><td><?php $Form->PoleInputText("Save[payment_name]", $Dane['payment_name']); ?></td></tr>
            <tr><th>INFO:</th><td><?php $Form->PoleTextarea("Save[payment_info]", $Dane['payment_info']); ?></td></tr>
        </table>
        <br /><a href="javascript:SavePayment(<?php echo $User; ?>,<?php echo $Row; ?>,<?php echo $ID; ?>)"><img src="/images/disk.gif" alt="Zapisz" /> Zapisz</a>
        <?php
    $Form->FormEnd(); 
 ?>