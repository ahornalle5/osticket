<?php
    //merge this ticket to another ticket
	global $thisstaff;

        // merge thread-entrys
        $sql= 'UPDATE '.THREAD_ENTRY_TABLE.' SET thread_id='.db_input($kTicket->getThreadId()).
			   ' WHERE thread_id='.db_input($this->getThreadId());
		db_query($sql);
		
		// delete thread-events state=created or username=SYSTEM 
        $sql= 'DELETE FROM '.THREAD_EVENT_TABLE.' WHERE state=\'created\' OR username=\'SYSTEM\' ';
		db_query($sql);
		
		// merge thread-events
        $sql= 'UPDATE '.THREAD_EVENT_TABLE.' SET thread_id='.db_input($kTicket->getThreadId()).
			   ' WHERE thread_id='.db_input($this->getThreadId());
		db_query($sql);
		
		// merge attachments not nessesary because attachments assigned to thread-entries
		
		// merge tasks
        $sql= 'UPDATE '.TASK_TABLE.' SET object_id='.db_input($kTicket->getId()).
			   ' WHERE object_id='.db_input($this->getId().' AND object_type = \'T\' ');
		db_query($sql);
		
		// update lastresponse
		if(strtotime($kTicket->getLastResponseDate()) < strtotime($this->getLastResponseDate())) {
        	$sql= 'UPDATE '.THREAD_TABLE.' SET lastresponse = '.db_input($this->getLastResponseDate()).
				   ' WHERE id='.db_input($kTicket->getThreadId());
			db_query($sql);
		}
		
		// update lastmessage
		if(strtotime($kTicket->getLastMessageDate()) < strtotime($this->getLastMessageDate())) {
        	$sql= 'UPDATE '.THREAD_TABLE.' SET lastmessage = '.db_input($this->getLastMessageDate()).
				   ' WHERE id='.db_input($kTicket->getThreadId());
			db_query($sql);
		}
		
		// mark kTicket (un-)answered?
		if($kTicket->isanswered == 0 && (strtotime($kTicket->getLastMessageDate()) < strtotime($this->getLastResponseDate()))) {
			$kTicket->isanswered = 1;
			$kTicket->save();
		}
		elseif($kTicket->isanswered == 1 && (strtotime($kTicket->getLastResponseDate()) < strtotime($this->getLastMessageDate()))) {
			$kTicket->isanswered = 0;
			$kTicket->save();
		}
	
		// create message ticket merged
		$kTicketLink = '<a href="tickets.php?id='.$kTicket->getId().'" title="'.__('Ticket').' #'.$kTicket->getNumber().'">'.$kTicket->getNumber().'</a>';
		$mTicketVars['staff_id'] = $thisstaff->getId();
		$mTicketVars['poster'] = $thisstaff->getName();
		$mTicketVars['note'] = sprintf(__('Merged with ticket #%s'), $kTicketLink);
		$this->postNote($mTicketVars, $origin='', $alerts=false);
		#$this->save();
		
		// close ticket
		$this->closed = $this->lastupdate = SqlFunction::NOW();
        $this->duedate = null;
        $this->staff = $thisstaff;
        $this->clearOverdue(false);
		$this->isanswered = 0;
		$status = TicketStatus::lookup(3); // closed
		$this->status = $status;
        $ecb = function($t) use ($status) {
            $t->logEvent('closed', array('status' => array($status->getId(), $status->getName())));
            $t->deleteDrafts();
        };

		$this->save();
		
	$return = TRUE;
?>
