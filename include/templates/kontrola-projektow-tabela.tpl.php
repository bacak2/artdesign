<table cellpadding="5" cellspacing="0" id="kontrola-table">
    <tr>
        <th>Data wpisu</th>
        <th>Projekt, wątek, użytkownik</th>
        <th style="width: 700px;">Treść wpisu</th>
        <th>Idź do projektu</th>
        <th>Napisz/odpowiedz</th>
        <th>Usuń wpis</th>
    </tr>
    <?php
        $LastDate = false;
        $i = 0;
        foreach($Wpisy as $Wpis){
            $i++;
            $NewDate = date("Y-m-d", strtotime($Wpis['add_date']));
            if($LastDate !== false && $NewDate != $LastDate){
                ?>
                <tr>
                    <td colspan="6" class="przerywnik-kontrola">&nbsp;</td>
                </tr>
                <?php
            }
        ?>
            <tr>
                <td><?php echo date("d.m.y", strtotime($Wpis['add_date']))."<br />".Usefull::WeekDay($Wpis['add_date']); ?></td>
                <td id="name_<?php echo $Wpis['project_id']; ?>_<?php echo $Wpis['thread_id']; ?>"><?php echo "<b>".$Projects[$Wpis['project_id']]."</b> / ".$Threads[$Wpis['thread_id']]." / <b>".$Users[$Wpis['user_id']]."</b>"; ?></td>
                <td style="width: 700px;">
                    <div id="wpis_<?php echo $Wpis['wpis_id']; ?>" class="forum-zwin"><?php echo nl2br(stripslashes(strip_tags($Wpis['wpis_content'] ) ) ); ?></div>
                    <div id="wpis_rozwin_<?php echo $Wpis['wpis_id']; ?>" class="forum-rozwin-button"><a href="javascript:Rozwin(<?php echo $Wpis['wpis_id']; ?>)">[rozwiń całość]</a></div>
                </td>
                <td><a href="?modul=projekty&project=<?php echo $Wpis['project_id']; ?>&watek=<?php echo $Wpis['thread_id']; ?>#menu-forum"><img src="/images/next-small.gif" alt="Idź do projektu"></a></td>
                <td><a href="javascript:NewComment(0,<?php echo $Wpis['project_id']; ?>,<?php echo $Wpis['thread_id']; ?>,true)"><img src="/images/answer-small.gif" alt="Napisz"></a></td>
                <td>
                    <?php
                        if($_SESSION['poziom_uprawnien'] == 1){
                    ?>
                    <a href="?modul=kontrola_projektow&akcja=kasowanie_wpisu&project=<?php echo $Wpis['project_id']; ?>&wpis=<?php echo $Wpis['wpis_id'] ?>"><img src="/images/delete-comment-small.gif" alt="Skasuj wpis"></a>
                    <?php
                        }else{
                            echo "&nbsp;";
                        }
                        if($_SESSION['poziom_uprawnien'] == 1 && (strtotime($Wpis['date_wait']) - strtotime("now") > 0) && $Wpis['date_wait'] != null){
                            ?><td><div id="opcje_wpis">
                                        <!--<script type="text/javascript">Timer("<?php echo strtotime($Wpis['date_wait']) ?>");</script>-->
                                        <!--<script type="text/javascript">eval('var inter'+<?php echo $i ?>+' = setInterval(Odmierz,1000,"<?php echo (strtotime($Wpis['date_wait']) - strtotime("now")) ?>","<?php echo $i ?>")');</script>-->
                                        <script type="text/javascript">Timer("<?php echo (strtotime($Wpis['date_wait']) - strtotime("now")) ?>","<?php echo $i ?>");</script>
                                        <a class="buttonPublic" style="margin-bottom: 10px;" id="publikuj<?php echo $i ?>" href="<?php echo ($Akcja == "show-forum-reload" ? str_replace("include/classes/ajax/show-forum.php?", "?modul=projekty&", $_SERVER['REQUEST_URI']) : $_SERVER['REQUEST_URI']); ?>&akcja=publikuj_wpis&wpis=<?php echo $Wpis['wpis_id'] ?>&projekt=<?php echo $Wpis['project_id'] ?>" style="margin-right: 10px;">publikuj</a><br>
                                        <a class="buttonPublic" href="javascript:EditWpis('<?php echo htmlspecialchars($Wpis['wpis_content']); ?>','<?php echo $Wpis['wpis_id'] ?>')" style="margin-right: 10px;">edytuj</a>
                    </div></td>
                                     <?php
                        }
                    ?>
                 </td>
            </tr>
        <?php
            $LastDate = $NewDate;
        }
    ?>
</table>