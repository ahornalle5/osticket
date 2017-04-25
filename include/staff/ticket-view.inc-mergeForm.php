    <form id="merge" class="hidden tab_content spellcheck exclusive"
        data-lock-object-id="ticket/<?php echo $ticket->getId(); ?>"
        data-lock-id="<?php echo $mylock ? $mylock->getId() : ''; ?>"
        action="tickets.php?id=<?php
        echo $ticket->getId(); ?>#mergeTicket" name="merge" method="post" enctype="multipart/form-data">
        <?php csrf_token(); ?>
        <input type="hidden" name="id" value="<?php echo $ticket->getId(); ?>">
        <input type="hidden" name="msgId" value="<?php echo $msgId; ?>">
        <input type="hidden" name="a" value="mergeticket">
        <input type="hidden" name="lockCode" value="<?php echo $mylock ? $mylock->getCode() : ''; ?>">
        <table style="width:100%" border="0" cellspacing="0" cellpadding="3">
            <?php
            if ($errors['reply']) {?>
            <tr><td width="120">&nbsp;</td><td class="error"><?php echo $errors['reply']; ?>&nbsp;</td></tr>
            <?php
            }?>
           <tbody id="body_thrd">
            <tr>
                <td width="120">
                    &nbsp;
                </td>
                <td>&nbsp;<br />&nbsp;<br />
                    <?php echo __('Select another ticket from this user to merge this tickets.').'<br>'; ?>
                    <?php echo __('All entries will be added to selected ticket.').'<br>'; ?>
                    <?php echo __('The following conditions must be fulfilled:'); ?>
                    <ul>
                        <li><?php echo __('You can edit both tickets'); ?></li>
                        <li><?php echo __('Both tickets are open'); ?></li>
                        <li><?php echo __('Both tickets are not locked by another agent'); ?></li>
                    </ul>
                    <br />&nbsp;
                </td>
            </tr>
            <tr>
                <td width="120" style="vertical-align:top">
                    <label><strong><?php echo __('ticket'); ?>:</strong></label>
                </td>
                <td>
                    <select id="keepTicket" name="keepTicket" onFocus="merge.manKeepTicket.value='';">
                        <option value="">&mdash;  <?php echo __('select another Ticket from this user'); ?> &mdash; </option>
                        <?php
						#$user->getId();
						if($user) {
						    $userTickets = TicketModel::objects();
						    $filter = $userTickets->copy()
						        ->values_flat('ticket_id')
						        ->filter(array('user_id' => $user->getId(), 'status__state' => 'open'));
						    $userTickets->filter(array('ticket_id__in' => $filter));
							$userTickets->values('ticket_id', 'number', 'cdata__subject');
							TicketForm::ensureDynamicDataView();
							// Fetch the results

							foreach($userTickets as $T){
								if($T['ticket_id'] != $ticket->getId())
                                   echo '<option value="'.$T['ticket_id'].'">#'.$T['number'].': '.$T['cdata__subject'].'</option>';
                            }
						} ?>
                    </select><span class='error'>&nbsp;*</span>&nbsp;
                    <?php echo __('or select by ticket number'); ?> 
                    #<input type="text" name="manKeepTicket" id="manKeepTicket" value="" 
                            onKeyUp="if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,'')" 
                            onFocus="merge.keepTicket.value='';" 
                    />
                    <br />&nbsp;<br />&nbsp;
                </td>
            </tr>
            </tbody>
        </table>
        <p  style="text-align:center;">
            <input class="save pending" type="submit" value="<?php echo __('Merge Tickets') ;?>">
            <input class="" type="reset" value="<?php echo __('Reset');?>">
        </p>
    </form>
