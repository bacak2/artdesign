<?php
    $Form = new FormularzSimple();
    $Form->FormStart("ValuationAddValue_$ValuationID");
        ?>
        <table cellpadding="3" cellspacing="0" style="border: 0; border-collapse: collapse;">
            <tr><th> Kwota: </th><td><?php $Form->PoleInputText("Save[valuation_value]", $Kwota['valuation_value']); ?></td></tr>
        </table>
        <br />
        <a href="javascript:SaveAddValuationValue(<?php echo $ValuationID; ?>)"><img src="/images/disk.gif" alt="Zapisz" /> Zapisz</a>
        &nbsp;&nbsp;&nbsp;
        <a href="javascript:CancelValuationValue(<?php echo $ValuationID; ?>)"><img src="/images/cancel.gif" alt="Anuluj" /> Anuluj</a>
        <?php
    $Form->FormEnd();
 ?>