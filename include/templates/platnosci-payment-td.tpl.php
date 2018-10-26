<?php
    echo $Dane['payment_termin']."<br />";
    echo $Dane['payment_kwota']." zł<br />";
    echo $Dane['payment_name']."<br />";
    echo nl2br($Dane['payment_info'])."<br />";
?><br />
<nobr><a href="javascript:NewTermin(<?php echo $UserID; ?>,<?php echo $i; ?>,<?php echo $Dane['payment_id']; ?>)"><img src="/images/add.gif" alt="Edytuj" /> Nowy termin</a></nobr>
<br /><br />
<?php
    $Form->PoleCheckbox("Oplacona[$UserID][$i]", 1, $Dane['payment_oplacona'], "opłacona", "id='oplac_{$UserID}_$i' onclick='OplacPayment($UserID,$i,{$Dane['payment_id']})'");
?>