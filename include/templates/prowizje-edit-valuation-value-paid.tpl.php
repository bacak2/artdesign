<?php
    $Form = new FormularzSimple();
    $Form->FormStart("ValuationValuePaid_$KwotaID");
        ?>
        <table cellpadding="3" cellspacing="0" style="border: 0; border-collapse: collapse;">
            <tr><th> Kwota: </th><td><?php $Form->PoleInputText("Save[valuation_value_paid]", $Kwota['valuation_value_paid']); ?></td></tr>
            <tr><th> Info: </th><td><?php $Form->PoleTextarea("Save[valuation_value_paid_info]", stripslashes($Kwota['valuation_value_paid_info'])); ?></td></tr>
        </table>
        <br />
        <a href="javascript:SaveValuationValuePaid(<?php echo $ValuationID; ?>, <?php echo $KwotaID; ?>)"><img src="/images/disk.gif" alt="Zapisz" /> Zapisz</a>
        &nbsp;&nbsp;&nbsp;
        <a href="javascript:CancelValuationValuePaid(<?php echo $KwotaID; ?>)"><img src="/images/cancel.gif" alt="Anuluj" /> Anuluj</a>
        <?php
    $Form->FormEnd();
 ?>