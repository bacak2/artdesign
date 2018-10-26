<div class='komunikat_ostrzezenie'>
    <?php
        $Form = new FormularzSimple();
        $Form->FormStart();
     ?>
    <br/><b>Wpisz dodatkowe hasło autoryzacyjne aby mieć dostęp do modułu <?php echo $this->Nazwa; ?>:</b><br/><br/>
    <?php
        $Form->PolePassword("access_platnosci", "");
    ?>
    <br /><br />
    <?php
        $Form->PoleSubmitImage("DeleteAccept", "Zatwierdź", "images/zatwierdz-big.gif",  "style='display: inline; vertical-align: middle;'");
        $Form->FormEnd();
    ?>
<br/><br/></div>