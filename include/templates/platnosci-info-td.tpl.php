<nobr><b>SUMA PŁATNOŚCI:</b> <?php echo $Suma; ?> zł</nobr><br />
<nobr><b>DO ZAPŁATY POZOSTAŁO:</b> <?php echo $Suma - $Zaplacono; ?> zł</nobr><br />
<b>INFO:</b><br /><?php echo $User['user_main_info']; ?><br /> 
<a href="javascript:EditInfo(<?php echo $User['user_id']; ?>)"><img src="/images/pencil.gif" alt="Edytuj" /> Edytuj info</a>