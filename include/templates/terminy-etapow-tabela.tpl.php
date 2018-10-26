<script type="text/javascript" src="js/terminy.js"></script>
<table cellpadding="5" cellspacing="0" id="platnosci-table">
    <tr>
        <td style="width: 250px; font-weight: bold;">inwestor/projekt</td>
        <td style="width: 100px; font-weight: bold;">etap</td>
        <td style="width: 200px; font-weight: bold;">termin realizacji etapu</td>
        <td style="width: 200px; font-weight: bold;">realizacja</td>
        <td style="font-weight: bold;">brak realizacji etapu</td>
    </tr>
    <?php
        foreach($Architekci as $ArchID => $ArchName){
            ?>
            <tr><th colspan="5" class="architekt"><a href="javascript:ShowDefaultTerminy('#terminy_<?php echo $ArchID; ?>', <?php echo $ArchID; ?>);" class="architekt_href"><?php echo $ArchName['user_name']; ?></a></th>
            <tr>
                <td colspan="5" id="terminy_<?php echo $ArchID; ?>" class="architekt_terminy">
                    <?php
                        $Platnosci = $this->GetArchitektProjects($ArchID, 10, "payment_zrealizowane = '0' AND payment_brak_realizacji = '0'");
                        include(SCIEZKA_SZABLONOW."terminy-architekt-tabela.tpl.php");
                    ?>
                </td>
            </tr>
            <?php
        }
    ?>
</table>