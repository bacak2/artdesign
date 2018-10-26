<table cellpadding="0" cellspacing="0" style="border: 0; width: 100%;" id="forum-menu">
    <tr>
        <?php
            foreach($this->Threads as $Idx => $Name){ 
        ?>
            <td<?php echo ($Idx > 1 ? " style='border-left: 1px solid #000;'" : "").($this->OpenThread == $Idx ? " class='picked'" : ""); ?>><a name="menu-forum" href='<?php echo $Link."&watek=$Idx"; ?>#menu-forum'><?php echo $Name; ?></a></td>
        <?php
            }
        ?>
    </tr>
</table>