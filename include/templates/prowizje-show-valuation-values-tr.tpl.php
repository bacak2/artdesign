<tr id="row_pro_<?php echo $KwotaID ?>">
    <td id="pro_<?php echo $KwotaID ?>" style="width: 50%; border: 0; border-right: 1px solid #000;">
        <div class="valuation-left">&nbsp;<?php echo $KwotaDane['valuation_value']; ?></div>
        <div class="valuation-right">
            <?php FormularzSimple::PoleCheckbox("valuation_value_checked", 1, $KwotaDane['valuation_value_checked'], "", "onclick='CheckValuationValue(this, $ValuationID, $KwotaID);' style='margin-right: 5px;'"); ?>
            <a href='javascript:DeleteValuationValue(<?php echo $KwotaID; ?>)'><img src="images/delete-wycena.gif" alt="Usuń kwotę" /></a>
        </div>
    </td>
    <td id="wyplacono_<?php echo $KwotaID ?>" style="width: 50%; border: 0;">
    <?php
        include(SCIEZKA_SZABLONOW."prowizje-show-valuation-values-paid.tpl.php");
    ?>
    </td>
</tr>