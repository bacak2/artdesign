<?php
    if($KwotaDane['valuation_value_checked'] == 1){
?>
<div class="valuation-left">&nbsp;<?php echo $KwotaDane['valuation_value_paid']; ?></div>
<div class="valuation-right"><a href="javascript:EditValuationValuePaid(<?php echo $ValuationID; ?>, <?php echo $KwotaID; ?>)"><img src="images/pencil.gif" alt="Edytuj kwotę" /></a></div>
<?php
    if($KwotaDane['valuation_value_paid_info'] != ""){
?>
<div class="valuation-bottom">&nbsp;<?php echo nl2br(stripslashes($KwotaDane['valuation_value_paid_info'])); ?></div>
<?php
    }
    }
?>