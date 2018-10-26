<table border="0" cellpadding="3" cellspacing="0" class="formularz-no-border">
    <tr>
        <?php
            foreach($Options as $Option){
                echo "<td style='width: 125px;text-align: left;'>".(count($Option) ? "<a href='{$Option['LINK']}'><img src='/images/{$Option['IMG']}' alt='{$Option['LABEL']}' /></a>" : "&nbsp;")."</td>\n";
            }
        ?>
        <td>&nbsp;</td>
    </tr>
</table>
<br /><br />
