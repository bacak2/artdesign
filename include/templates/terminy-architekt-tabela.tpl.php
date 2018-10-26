<table cellpadding="5" cellspacing="0" style="border: 0; width: 100%;">
    <?php
        foreach($Platnosci as $Idx => $PDane){
            ?>
            <tr>
                <td style="width: 250px; border-left: 0;<?php echo ($Idx == 0 ? " border-top: 0;" : ""); ?>"><?php echo $Klienci[$PDane['user_id']]["user_name"]." / ".$BaseQuery->GetProjectNameByUser(null, $PDane['user_id']); ?></td>
                <td style="width: 100px; text-align: center;<?php echo ($Idx == 0 ? " border-top: 0;" : ""); ?>"><?php echo $PDane['row_id'] ?></td>
                <td style="width: 200px; text-align: center;<?php echo ($Idx == 0 ? " border-top: 0;" : ""); ?>"><?php echo $PDane['payment_termin'] ?></td>
                <td style="width: 200px;<?php echo ($Idx == 0 ? " border-top: 0;" : ""); ?>">
                    <?php
                        if($ShowCheckbox){
                            ?>
                                <div class='realizacja_div'<?php echo Usefull::ShowOpoznienieTermin($PDane['payment_termin']) ?>><?php FormularzSimple::PoleCheckbox("Realizacja[{$PDane['payment_id']}]", 1, 0, false, "onclick='RealizacjaCheck(\"#terminy_$ArchID\", $ArchID, {$PDane['payment_id']})'"); ?></div>
                        <?php
                        }
                        ?>
                </td>
                <td style="border-right: 0;<?php echo ($Idx == 0 ? " border-top: 0;" : ""); ?>">
                <?php
                    if($ShowCheckbox){
                        FormularzSimple::PoleCheckbox("BrakRealizacji[{$PDane['payment_id']}]", 1, 0, false, "onclick='BrakRealizacjiCheck(\"#terminy_$ArchID\", $ArchID, {$PDane['payment_id']})'");
                    }
                ?>
                </td>
            </tr>
            <?php
        }
    ?>
</table>
<div style="background-color: #404040; width: 100%; float: left;">
    <div style="width: 30%; float: left; height: 100%; padding: 7px 0px;<?php echo ($SelectedLink == 1 ? " background-color: #AFAFAF;" : ""); ?>"><a href="javascript:ShowAllTerminy('#terminy_<?php echo $ArchID; ?>', <?php echo $ArchID; ?>);" class="terminy_href">POKAŻ POZOSTAŁE TERMINY</a></div>
    <div style="width: 30%; float: left; height: 100%; padding: 7px 0px;<?php echo ($SelectedLink == 2 ? " background-color: #AFAFAF;" : ""); ?>"><a href="javascript:ShowZrealizowane('#terminy_<?php echo $ArchID; ?>', <?php echo $ArchID; ?>);" class="terminy_href">ARCHIWUM - ZREALIZOWANYCH ETAPÓW</a></div>
    <div style="width: 30%; float: left; height: 100%; padding: 7px 0px;<?php echo ($SelectedLink == 3 ? " background-color: #AFAFAF;" : ""); ?>"><a href="javascript:ShowBrakRealizacji('#terminy_<?php echo $ArchID; ?>', <?php echo $ArchID; ?>);" class="terminy_href">BRAK REALIZACJI - ARCHIWUM</a></div>
</div>