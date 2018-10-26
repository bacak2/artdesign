<div id="client_info_<?php echo $UserID; ?>">
    <b>Dodatkowe informacje:</b><br /><?php echo nl2br($User['user_add_info']); ?><br /><br />
    <a href="javascript:EditClient(<?php echo $User['user_id']; ?>)"><img src="/images/pencil.gif" alt="Edytuj" /> Edytuj dane</a>
</div>
<br /><br />
<div id="client_umowa_<?php echo $UserID; ?>">
    <?php
       include(SCIEZKA_SZABLONOW."platnosci-umowa.tpl.php");
    ?>
</div>