<div class="valuation-left">&nbsp;<?php echo $ProDane['valuation_company']; ?></div>
<div class="valuation-right">
    <?php FormularzSimple::PoleCheckbox("valuation_checked", 1, $ProDane['valuation_checked'], "", "onclick='CheckValuation(this, $ValuationID);' style='margin-right: 5px;'"); ?>
    <a href='javascript:DeleteValuation(<?php echo $UserID; ?>, <?php echo $ValuationID; ?>)'><img src="images/delete-wycena.gif" alt="Usuń wycenę" /></a>
</div>
<div style="clear: both;"></div>
<div class="valuation-bottom"><a href="javascript:EditValuation(<?php echo $ValuationID; ?>)"><img src="images/pencil.gif" alt="Edytuj" /> Edytuj</a></div>