<table cellpadding="5" cellspacing="0" id="platnosci-table">
    <tr>
        <th>Klient</th>
        <?php
            for($i=1; $i<=8; $i++){
                ?>
                <th>Płatność <?php echo $i ?></th>
                <?php
            }
        ?>
         <th>INFO</th>
    </tr>
    <?php
        $Form = new FormularzSimple();
        foreach($Userzy as $UserID => $User){
            $IlePlatnosci = count($Platnosci[$UserID]);
            $Suma = 0;
            $Zaplacono = 0;
            ?>
            <tr>
                <td id="client_<?php echo $UserID; ?>">
                    <?php
                        echo "<b>".$User['user_name']."</b><br />";
                        echo $User['user_login']."<br />";
                        echo $User['user_email']."<br />";
                        include(SCIEZKA_SZABLONOW."platnosci-client-td.tpl.php");
                    ?>
                </td>
                <?php
                for($i=1; $i<=8; $i++){
                    $Class = (isset($Platnosci[$UserID][$i]) ? ($Platnosci[$UserID][$i]['payment_oplacona'] == 1 ? "platnosci-oplacona" : (date("Y-m-d") > $Platnosci[$UserID][$i]['payment_termin'] ? "platnosci-po-terminie" : "")) : "platnosci-no-active");
                    ?>
                    <td id="platnosci_<?php echo $UserID."_".$i; ?>" class="<?php echo $Class; ?>">
                        <?php
                            if(isset($Platnosci[$UserID][$i])){
                                $Dane = $Platnosci[$UserID][$i];
                                $Suma += $Dane['payment_kwota'];
                                if($Dane['payment_oplacona'] == 1){
                                    $Zaplacono += $Dane['payment_kwota'];
                                }
                                include(SCIEZKA_SZABLONOW."platnosci-payment-td.tpl.php");
                            }else if($i == ($IlePlatnosci+1)){
                                include(SCIEZKA_SZABLONOW."platnosci-clear-row.tpl.php");
                            }
                        ?>
                            &nbsp;
                    </td>
                    <?php
                }
                ?>
                <td id="info_<?php echo $UserID; ?>">
                    <?php
                        include(SCIEZKA_SZABLONOW."platnosci-info-td.tpl.php");
                    ?>
                </td>
           </tr>
           <?php  
        }
    ?>
</table>