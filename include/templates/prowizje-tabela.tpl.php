<script type="text/javascript" src="js/prowizje.js"></script>
<table cellpadding="5" cellspacing="0" id="platnosci-table">
    <tr>
        <th class="prowizje-th-klient">KLIENT</th>
        <th class="prowizje-th">WYCENA</th>
        <th class="prowizje-th">PROWIZJA</th>
        <th class="prowizje-th">WYPLACONO</th>
    </tr>
    <?php
        $Form = new FormularzSimple();
        foreach($Userzy as $UserID => $User){
            $IleProwizji = count($Prowizje[$UserID]);
            ?>
            <tr class="prowizje_client_<?php echo $UserID; ?>">
                <td id="client_<?php echo $UserID; ?>"<?php echo ($IleProwizji > 0 ? " rowspan='".($IleProwizji+1)."'" : ""); ?>>
                    <?php
                        echo "<b>".$User['user_name']."</b><br />";
                        echo $User['user_login']."<br />";
                        echo $User['user_email']."<br />";
                    ?>
                </td>
                <td colspan="3" id="add_valuation_<?php echo $UserID; ?>"><a href="javascript:AddValuation(<?php echo $UserID; ?>)"><img src="images/add.gif" alt="Dodaj" /> Dodaj</a></td>
            </tr>
            <?php
            foreach($Prowizje[$UserID] as $ValuationID => $ProDane){
                $Kwoty = $KwotyProwizji[$ValuationID];
                include(SCIEZKA_SZABLONOW."prowizje-tr.tpl.php");
            }
        }
    ?>
</table>