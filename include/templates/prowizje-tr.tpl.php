<tr class="prowizje_client_a_<?php echo $UserID; ?>" id="row_<?php echo $ValuationID; ?>">
    <td id="wycena_<?php echo $ValuationID; ?>">
        <?php
            if(isset($EditValuation) && $EditValuation){
                include(SCIEZKA_SZABLONOW."prowizje-edit-valuation.tpl.php");
            }else{
                include(SCIEZKA_SZABLONOW."prowizje-show-valuation.tpl.php");
            }
        ?>
    </td>

    <td colspan="2" id="prowizja_<?php echo $ValuationID; ?>" style="padding: 0;">
        <?php
            if($ProDane['valuation_checked'] == 1){
                include(SCIEZKA_SZABLONOW."prowizje-show-valuation-values.tpl.php");
            }
        ?>
    </td>
</tr>