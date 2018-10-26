<table cellpadding="3" cellspacing="0" id="prowizje-table-<?php echo $ValuationID; ?>" style="width: 100%; margin: 0; border: 0;">
    <tr>
        <td colspan="2" id="pro-add-<?php echo $ValuationID; ?>" style="border: 0; padding: 6px;<?php echo (count($Kwoty) > 0 ? "border-bottom: 1px solid #000;" : ""); ?>">
            <a href="javascript:AddValuationValue(<?php echo $ValuationID; ?>)"><img src="images/add.gif" alt="Dodaj kwotę" /> Dodaj kwotę</a>
        </td>
    </tr>
<?php
    foreach($Kwoty as $KwotaID => $KwotaDane){
        include(SCIEZKA_SZABLONOW."prowizje-show-valuation-values-tr.tpl.php");
    }
?>
</table>