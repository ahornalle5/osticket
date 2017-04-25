  			<form id="merge" action="tickets.php?id=<?php echo $ticket->getId(); ?>#mergeTicket" name="merge" method="post" enctype="multipart/form-data" class="hidden">
                         <?php csrf_token(); ?>
                        <input type="hidden" name="ticket_id" value="<?=$id?>">
                        <input type="hidden" name="a" value="mergeticket">
                           <select id="keepticket" name="keepticket">


                        <?php
                         $sql = 'SELECT a.ticket_id, concat(a.number," | ",SUBSTRING(b.subject,1,125)) as label FROM `ost_ticket` as a  inner join ost_ticket__cdata '.
                               'as b on (a.ticket_id = b.ticket_id) inner join ost_user_email as c on (a.user_id = c.user_id) '.
                               'where a.status_id in ( 1, 6, 7 )  and a.ticket_id <> '.$ticket->getId().' order by `created` desc ';
                               
                         /*      
                        $sql = 'SELECT ticket_id, concat(ticketid,\": \",subject) AS label FROM ost_ticket WHERE email='.
                                  '(SELECT email FROM ost_ticket WHERE ticket_id = '.$ticket->getId().') AND status '.
                                  '= \"open\" AND ticket_id <> '.$ticket->getId().' ORDER BY `created` DESC '; */

                        $lookuptickets = db_query($sql);
                  while (list($ticket_id,$label) = db_fetch_row($lookuptickets)){
                                ?>

                                    <option value="<?=$ticket_id?>"><?=$label?></option>
                                <?php
                                }?>



                    </select>
                            <div  style="margin-left: 50px; margin-top: 5px; margin-bottom: 10px;border: 0px;" align="left">
                                <input class="button" type='submit' value='Merge Tickets'/>
                                <input class="button" type='reset' value='Reset' />
                                <!-- <input class="button" type='button' value='Cancel' onClick="history.go(-1)" /> -->
                            </div>
                        
                    </form>
                    
