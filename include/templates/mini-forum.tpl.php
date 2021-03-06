<table cellpadding="5" cellspacing="0" style="width: 100%; border-collapse: collapse;<?php echo $this->BigMode ? "border-top: 1px solid #000;" : ""; ?>">
    <tr>
        <td colspan="2" style="width: 170px; border-bottom: 1px solid #888;background-color: #DFDFDF;">
            <span style="font-size: 15px;"><b>TEMAT:</b> <?php echo $Title; ?></span>
        </td>
        <td style="border-bottom: 1px solid #888; width: 260px; background-color: #DFDFDF; vertical-align: top; text-align: right;" id="szkic-cont">
            <?php
                include(SCIEZKA_SZABLONOW."buttons/szkic-buttons.tpl.php");
            ?>
        </td>
        <td style="border-bottom: 1px solid #888; width: 135px; background-color: #DFDFDF; vertical-align: top; text-align: right;">
            <?php
                    if($this->BigMode){
            ?>
                        <a href="javascript:window.close()" style="margin-right: 10px;"><img src="/images/small-window_one.gif" alt="Zamknij okno"></a>
                <?php
                    }else{
                        ?>
                        <a href="javascript:OpenBigForum(<?php echo $Project; ?>, <?php echo $this->OpenThread; ?>)" style="margin-right: 10px;"><img src="/images/big_window.gif" alt="Duże okno"></a>
                        <?php
                    }
                ?>
        </td>
        <td style="border-bottom: 1px solid #888; width: 165px; background-color: #DFDFDF; vertical-align: top; text-align: right;">
            <?php
                if($this->Dostep != 4){
            ?>
                <a href="javascript:NewComment(0,<?php echo $Project; ?>,0,true)" style="margin-right: 10px;"><img src="/images/answer-big.gif" alt="Odpowiedz"></a>
                <?php
                }
                ?>
        </td>
        <td style="border-bottom: 1px solid #888;width: 125px; background-color: #DFDFDF; vertical-align: top; text-align: right;">
            <?php
                if($this->Dostep == 1 || $this->Dostep == 2){
             ?>
            <a href="javascript:ChangeSubject()"><img src="/images/change-subject-big.gif" alt="Zmień temat" /></a>
            <?php
                }
             ?>
        </td>
    </tr>
    <tr>
        <td id="forum-div" colspan="6" style="border-bottom: 1px solid #888;background-color: #DFDFDF;<?php echo ($OpenTextarea ? "" : " display: none;") ?>">
            <?php
                if($this->Dostep == 1 || $this->Dostep == 2){
             ?>
                <div id="change-subject-div" style="display: none; margin: 15px;">
                    <?php
                        $Form = new FormularzSimple();
                        $Form->FormStart("ChangeSubForm");
                            echo "<b>Nowy temat:</b> ";
                            $Form->PoleInputText("NewSubject", "");
                            echo "<br /><br />\n";
                            $Form->PoleSubmitImage("ZapiszSubject", "Zapisz", "/images/zapisz-big.gif");
                        $Form->FormEnd();
                    ?>
                </div>
             <?php
                }
             ?>
            <div id="new-wpis-div" style="margin: 15px;<?php echo ($OpenTextarea ? "" : " display: none;") ?>">
                <?php
                    $Form = new FormularzSimple();
                    $Form->FormStart("NewWpisForm");
                        echo "<a name='#newform'></a><b>Treść wpisu:</b><br />";
                        $Form->PoleTextarea("NewWpis", stripslashes($_SESSION['nowy_wpis_tresc'][$Project]), "style='width: 100%; height: 120px;' id='NewWpis' onchange='SaveWpis($Project);'");
                        $Form->PoleHidden("Answer", 0, "id='answer'");
                        echo "<br />";
                        include (SCIEZKA_SZABLONOW.'stopka-forum.tpl.php');
                        echo "<br />\n";
                        $Form->PoleSubmitImage("ZapiszSubject", "Zapisz", "/images/zapisz-big.gif");
                        echo "&nbsp;&nbsp;&nbsp;<a href='javascript:CancelNewWpis($Project)'><img src='/images/anuluj-big.gif' alt='anuluj' /></a>";
                    $Form->FormEnd();
                ?>
            </div>
        </td>
    </tr>
    <tr>
        <td colspan="6" style="padding: 0; border-bottom: 0;">
            <div style="<?php echo ($this->BigMode === false ? "height: 200px; overflow-y: scroll; " : ""); ?>display: block;">
            <table cellpadding="5" cellspacing="0" style="width: 100%; border: 0; border-collapse: collapse;">
        <?php
            if($this->Dostep == 4){
               ?>
                <tr>
                    <td colspan="3" style="border-bottom: 1px solid #888; background-color: #F8F8F8; font-size: 13px; font-weight: bold; text-align: center;">
                        <div style="text-align: right;">
                                <a href="javascript:NewComment(0,<?php echo $Project; ?>,0,true)" style="margin-right: 10px;"><img src="/images/napisz-big.gif" alt="Napisz"></a>
                        </div>
                    </td>
                </tr>
                <?php
            }
            if($Wpisy){
                $Kolor = "#FFFFFF";
                foreach($Wpisy as $Wpis){
                    $Kolor = ($Kolor == "#FFFFFF" ? "#F8F8F8" : "#FFFFFF");
                    ?>
                        <tr>
                            <td style="width: 200px; border-right: 1px solid #888; border-bottom: 1px solid #888; vertical-align: top; background-color: <?php echo $Kolor; ?>;"><b><?php echo $Users[$Wpis['user_id']] ?></b><br /><small><?php echo $Wpis['add_date']; ?></small></td>
                            <td colspan="2" style="border-bottom: 1px solid #888; background-color: <?php echo $Kolor; ?>;">
                                <?php
                                    echo stripslashes(nl2br($Wpis['wpis_content']));
                                ?>
                                <br /><br />
                                <div style="text-align: right;">
                                    <?php
                                    if($this->Dostep == 1 || $this->Dostep == 2){
                                    ?>

                                        <a href="<?php echo ($Akcja == "show-forum-reload" ? str_replace("include/classes/ajax/show-forum.php?", "?modul=projekty&", $_SERVER['REQUEST_URI']) : $_SERVER['REQUEST_URI']); ?>&akcja=kasowanie_wpisu&wpis=<?php echo $Wpis['wpis_id'] ?>" style="margin-right: 10px;"><img src="/images/delete-comment-big.gif" alt="Skasuj wpis"></a>
                                    <?php
                                    }
                                    
                                        ?>
                                </div>
                            </td>
                        </tr>
                    <?php
                }
        ?>
        <?php
            }else{
                ?>
                <tr>
                    <td colspan="6" style="font-size: 13px; font-weight: bold; text-align: center;">
                        <b>Brak wpisów</b>
                        <br /><br />
                        <div style="text-align: right;">
                                <a href="javascript:NewComment(0,<?php echo $Project; ?>,0,true)" style="margin-right: 10px;"><img src="/images/napisz-big.gif" alt="Napisz"></a>
                        </div>
                    </td>
                </tr>
                <?php
            }
        ?>
            </table>
            </div>
        </td>
    </tr>
</table>