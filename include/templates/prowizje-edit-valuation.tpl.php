<?php
    $Form = new FormularzSimple();
    $Form->FormStart("ValuationEdit_$ValuationID");
        ?>
        <table cellpadding="3" cellspacing="0" style="border: 0; border-collapse: collapse;">
            <tr><th>Firma:</th><td><?php $Form->PoleInputText("Save[valuation_company]", $ProDane['valuation_company']); ?></td></tr>
        </table>
        <br />
        <a href="javascript:SaveCompanyEdit(<?php echo $ValuationID; ?>)"><img src="/images/disk.gif" alt="Zapisz" /> Zapisz</a>
        &nbsp;&nbsp;&nbsp;
        <a href="javascript:CancelCompanyEdit(<?php echo $ValuationID; ?>)"><img src="/images/cancel.gif" alt="Anuluj" /> Anuluj</a>
        <?php
    $Form->FormEnd();
 ?>