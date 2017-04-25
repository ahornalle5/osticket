<form id="merge" action="tickets.php?id=<?php echo $ticket->getId(); ?>#mergeTicket" name="merge" method="post" enctype="multipart/form-data" class="hidden">
    <?php csrf_token(); ?>
    <input type="hidden" name="ticket_id" value="<?=$id?>">
    <input type="hidden" name="a" value="mergeticket">
    <table width="100%" border="0" cellspacing="0" cellpadding="3">
        <tr>
            <td width="120" style="vertical-align:top">&nbsp;</td>
            <td><br />&nbsp;
                <label><strong><?php echo __('Select a ticket which this one should be appended'); ?></strong><span class='error'>&nbsp;*</span></label><br />&nbsp;<br />
                <select id="keepticket" name="keepticket" onFocus="merge.manMerge.value='';">
                    <option value="">—  <?php echo __('select another Ticket from this user'); ?> — </option>
                    <?php
                        /*$sql = 'SELECT a.ticket_id, concat(a.number,": ",b.subject) as label FROM `'.TABLE_PREFIX.'ticket` as a  inner join  '.TABLE_PREFIX.'ticket__cdata '.
                        'as b on a.ticket_id = b.ticket_id inner join '.TABLE_PREFIX.'user_email as c on (a.user_id = c.user_id) '.
                        'order by `created` desc ';*/
                        $sqlSec ='SELECT ab.address '
                            .'FROM '.TABLE_PREFIX.'ticket AS aa '
                            .'LEFT JOIN '.TABLE_PREFIX.'user_email AS ab ON ( aa.user_id = ab.user_id ) '
                            .'WHERE aa.ticket_id ='.$ticket->getId().'';

                        $sql = 'SELECT a.ticket_id, concat(a.number,": ",b.subject) as label '
                            .'FROM `'.TABLE_PREFIX.'ticket` as a '
                            .'LEFT JOIN '.TABLE_PREFIX.'ticket__cdata as b on a.ticket_id = b.ticket_id '
                            .'LEFT JOIN '.TABLE_PREFIX.'user_email as c on (a.user_id = c.user_id) '
                            .'WHERE c.address=('.$sqlSec.') and a.status_id = 1  and a.ticket_id <> '.$ticket->getId().' ORDER BY `created` DESC ';
                        $lookuptickets = db_query($sql);
                        while (list($ticket_id,$label) = db_fetch_row($lookuptickets)){
                    ?>
                            <option value="<?php echo $ticket_id ?>"><?php echo $label?></option>
                      <?php }?>
                </select>
                &nbsp;<?php echo __('or type in Ticket'); ?> #
                <input type="text" name="manMerge" value="" onKeyUp="if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,'')" onFocus="merge.keepticket.value='';" />
                <br />&nbsp;
                <br />&nbsp;
            </td>
        </tr>
    </table>
    <p style="padding-left:165px;">
    <input class="btn_sm" type='submit' value="<?php echo __('Merge Tickets') ;?>" />
    <input class="btn_sm" type='reset' value="<?php echo __('Reset');?>" />
    <!--<input class="btn_sm" type='button' value="<?php echo __('Cancel');?>" onClick="history.go(-1)" />-->
    </p>
</form>
