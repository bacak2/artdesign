<!-- Formularz logowania { -->
<table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
       <td align="center" valign="middle">
            <table width="872" border="0" cellspacing="0" cellpadding="0" align="center" style="margin-top: 52px;">
                    <tr>
                            <td colspan="2" style="width: 542px; text-align: left; vertical-align: middle; font-size: 18px;">
                                <?php echo ($this->Domena == "100design.eu" ? "Projekt PLUS Internetowe interaktywne<br />zarządzanie projektami architektonicznymi" : "Internetowa interaktywna platforma projektu<br />www.100design.pl"); ?>
                            </td>
                            <td style="width: 330px; vertical-align: top; text-align: right; "><img src="images/logo-artdesign-2.png" alt="" border="0" style="float: right;" align="right"></td>
                    </tr>
                    <tr>
                            <td colspan="3" style="background-repeat: no-repeat; background-image: url('/images/log-background<?php echo $this->Domena == "100design.eu" ? "-eu" : ""; ?>.jpg'); height: 558px;">
                                <table border="0" cellspacing="0" style="width: 100%;">
                                    <tr style="height: 558px;">
                                        <td style="width: 328px;">&nbsp;</td>
                                        <td id="loguj_sie">
                                            <div id="logInForm">
                                                <form action="./" method="post">
                                                <input type="hidden" name="logowanie" value="1">
                                                <table border="0" cellspacing="0" cellpadding="2" style="width: 220px; margin: 0 auto;">
                                                        <tr>
                                                                <td align="right" valign="middle" style="width: 75px;"><p class="logowanie_new">użytkownik</p></td>
                                                                <td colspan="2"><input type="text" name="pp_login" class="login-input"></td>
                                                        </tr>
                                                        <tr>
                                                                <td colspan="3" style="height: 1px;"></td>
                                                        </tr>
                                                        <tr>
                                                                <td align="right" valign="middle" style="width: 75px;"><p class="logowanie_new">hasło</p></td>
                                                                <td colspan="2"><input type="password" name="pp_haslo" class="login-input"></td>
                                                        </tr>
                                                        <tr>
                                                                <td align="right" valign="middle" style="width: 75px;">&nbsp;</td>
                                                                <td colspan="2" align="left" valign="middle"><input type="submit" class="login-submit<?php echo $this->Domena == "100design.eu" ? "-eu" : ""; ?>" value="zaloguj się"></td>
                                                        </tr>
                                                </table>
                                                </form>
                                            </div>
                                        </td>
                                        <td style="width: 314px; vertical-align: top;">
                                            <div style="width: 314px; height: 100%; position: relative; top: 0; left: 0;">
                                                <a href="http://artdesign.pl/" target="_blank" class="log-links" id="a-artdesign">ARTDESIGN.PL</a>
                                                <a href="http://projekty-wnetrza.com/" target="_blank" class="log-links" id="a-blog-artdesign">BLOG ARTDESIGN</a>
                                                <a href="http://www.100wnetrza.pl/" target="_blank" class="log-links" id="a-wnetrza">100WNETRZA.PL</a>
                                                <a href="http://luksusowe-wnetrza.com/" target="_blank" class="log-links" id="a-facebook">LUKSUSOWE WNĘTRZA</a>
                                                <a href="http://plus.google.com/101582081884797704461?prsrc=3" target="_blank" class="log-links-mini" id="i-gplus"><img src="/images/google-icon.png" alt="Google+" /></a>
                                                <a href="http://www.facebook.com/ARTDESIGNarchitekturawnetrz" target="_blank"  class="log-links" id="i-facebook"><img src="/images/facebook-icon.png" alt="Facebook" /></a>
                                                <a href="http://www.pinterest.com/artdesignbp/" target="_blank" class="log-links-mini" id="i-pinterest"><img src="/images/pinterest-icon.png" alt="Pinterest" /></a>
                                                <a href="https://twitter.com/artdesignbp" target="_blank" class="log-links-mini" id="i-twitter"><img src="/images/twitter-icon.png" alt="Twitter" /></a>
                                                <a href="https://www.youtube.com/channel/UC33mV7m6SVEHbsUmNCjHQ1A" target="_blank" class="log-links-mini" id="i-youtube"><img src="/images/youtube-icon.png" alt="Youtube" /></a>
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                    </tr>
                    <tr>
                            <td colspan="3" style="padding-top: 26px; padding-bottom: 26px;">
                                <table border="0" cellpadding="0" cellspacing="0">
                                    <tr style="height: 63px;">
                                        <td class="logos logos-brd-right"><img src="/images/program-regionalny.jpg" alt="" /></td>
                                        <td class="logos logos-brd-right"><img src="/images/program-regionalny-text.jpg" alt="" /></td>
                                        <td class="logos logos-brd-right"><img src="/images/malopolska.jpg" alt="" /></td>
                                        <td class="logos"><img src="/images/unia-europejska.jpg" alt="" /></td>
                                    </tr>
                                </table>
                            </td>
                    </tr>
            </table>
        </td>
    </tr>
</table>