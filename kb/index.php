<?php
/*********************************************************************
    index.php

    Knowledgebase Index.

    Peter Rotich <peter@osticket.com>
    Copyright (c)  2006-2013 osTicket
    http://www.osticket.com

    Released under the GNU General Public License WITHOUT ANY WARRANTY.
    See LICENSE.TXT for details.

    vim: expandtab sw=4 ts=4 sts=4:
**********************************************************************/
require('kb.inc.php');
require_once(INCLUDE_DIR.'class.category.php');
$inc='knowledgebase.inc.php';
require(CLIENTINC_DIR.'header.inc.php');

include ('umwelt.php');

?>



<!-- Eingefügt von Hahnc; 170918 -->


<h2>Suchfunktion</h2>
<div class="search-form">
    <form method="get" action="faq.php">
    <input type="hidden" name="a" value="search"/>
    <input type="text" name="q" class="search" placeholder="<?php echo __('Search our knowledge base'); ?>"/>
    <button type="submit" class="green button"><?php echo __('Search'); ?></button>
    </form>
</div><br><br>
<?php
require(CLIENTINC_DIR.$inc);
require(CLIENTINC_DIR.'footer.inc.php');
?>
