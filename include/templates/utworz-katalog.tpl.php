<div style="width: 700px; margin: 40px auto; display: block;">
    Utwórz nowy podkatalog w katalogu <b>" <?php echo $this->SciezkaAlias; ?> "</b><br /><br />
    <?php
        $Form = new FormularzSimple();
        $Form->FormStart();
            $Form->PoleInputText("NewFolder", "");
            echo "<br /><br />\n";
            $Form->PoleSubmitImage("Wgraj", "WgrajPlik", "/images/new-folder-big.gif", "margin-right: 30px;");
            ?>
                <a href="<?php echo $this->LinkPowrotu; ?>"><img src="/images/anuluj-big.gif" alt="anuluj" /></a>
            <?php
        $Form->FormEnd();
        $Tresc = "<b>UWAGA! Nazwy tworzonych katalogu:<br /></b>";
        $Tresc .= "- mogą zawierać małe litery, cyfry oraz podkreślenia<br />";
        $Tresc .= "- nie mogą zawierać spacji oraz polskich znaków<br />";
        $Tresc .= "- muszą zawierać conajmniej 3 znaki<br />";
        echo "<br /><br /><div class='komunikat_ostrzezenie' style='text-align: left;'>$Tresc</div>"; 
    ?>
</div>