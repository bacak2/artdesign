<div style="width: 700px; margin: 40px auto; display: block;">
    Wgraj nowy plik do katalogu <b>" <?php echo $this->SciezkaAlias; ?> "</b><br /><br />
    <?php
        $Form = new FormularzSimple();
        $Form->FormStart();
            $Form->PoleFile("NewFile", "");
            echo "<br /><br />\n";
            $Form->PoleSubmitImage("Wgraj", "WgrajPlik", "/images/upload-big.gif", "margin-right: 30px;");
            ?>
                <a href="<?php echo $this->LinkPowrotu; ?>"><img src="/images/anuluj-big.gif" alt="anuluj" /></a>
            <?php
        $Form->FormEnd();
        $Tresc = "<b>UWAGA! Nazwy przesyłanych plików:<br /></b>";
        $Tresc .= "- mogą zawierać małe litery, cyfry oraz podkreślenia<br />";
        $Tresc .= "- nie mogą zawierać spacji oraz polskich znaków<br />";
        echo "<br /><br /><div class='komunikat_ostrzezenie' style='text-align: left;'>$Tresc</div>"; 
    ?>
</div>