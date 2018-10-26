<script type="text/javascript" src="js/kontrola-projektow.js"></script>
<a name='newform'></a>
<p style="font-weight: bold; margin-left: 17px;">KONTROLA PROJEKTÓW - WPISY NA PANELACH DYSKUSYJNYCH</p>
<div style="display: none; margin: 17px; font-size: 17px; font-weight: bold" id="tytul-odpowiedzi"></div>
<div id="new-wpis-div" style="margin: 15px;<?php echo (intval($Project) > 0 && isset($_SESSION['nowy_wpis_tresc'][$Project]) && $_SESSION['nowy_wpis_tresc'][$Project] != "" ? "" : " display: none;") ?>">
    <?php
        $Form = new FormularzSimple();
        $Form->FormStart("NewWpisForm");
            echo "<b>Treść wpisu:</b><br />";
            $Form->PoleTextarea("NewWpis", stripslashes($_SESSION['nowy_wpis_tresc'][$Project]), "style='width: 100%; height: 120px;' id='NewWpis'");
            $Form->PoleHidden("Answer", 0, "id='answer'");
            $Form->PoleHidden("AddProject", 0, "id='add_project'");
            $Form->PoleHidden("AddThread", 0, "id='add_thread'");
            $Form->PoleHidden("id_wpis", "", "id='id_wpis'");
            echo "<br /><br />\n";
            $Form->PoleSubmitImage("ZapiszSubject", "Zapisz", "/images/zapisz-big.gif");
            echo "&nbsp;&nbsp;&nbsp;<a href='javascript:CancelNewWpis($Project)'><img src='/images/anuluj-big.gif' alt='anuluj' /></a>";
        $Form->FormEnd();
    ?>
</div>
<div id="wpisy-month-<?php echo $ThisMonth; ?>">
<?php include(SCIEZKA_SZABLONOW."kontrola-projektow-tabela.tpl.php"); ?>
</div>
<?php
    $next_data = date("Y-m-01", strtotime($ThisMonth."-01 -1 month"));
    for($data = $next_data; $data >= $FirstPostMonth; $data = $next_data){
        $Exp = explode("-", $data);
        ?>
        <a class="wpisy-month-trigger" href="javascript:ShowForumMonth('<?php echo date("Y-m", strtotime($data)); ?>')"><span><?php echo $this->MapujNazweMiesiaca($Exp[1])." ".$Exp[0]; ?></span></a>
        <div id="wpisy-month-<?php echo date("Y-m", strtotime($data)); ?>" class="wpisy-month"></div>
        <?php
        $next_data = date("Y-m-01", strtotime($data."-1 month"));
    }
?>
<script type="text/javascript">
    CKEDITOR.replace('NewWpis');
</script>