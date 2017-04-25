<form action="tickets.php?id=<?php echo $ticket->getId(); ?>&a=edit" method="post" class="save"  enctype="multipart/form-data">
	 <?php 
	 	csrf_token(); 
		$info=Format::htmlchars(($errors && $_POST)?$_POST:$ticket->getUpdateInfo());
	 ?>
 	<input type="hidden" name="do" value="update">
 
 	<ul class="tabs" id="response-tabs">
        <li class="active"><?php echo __('Edit'); ?></li>   
    </ul>

 
	 <table bgcolor="#EEEEEE" width="100%" style="border: 1px solid black; padding-left: 10px;" >
	 <!-- Auskommentiert um den Wert nicht zu Ã¤ndern -->
	 <!-- <input type="hidden" name="source" value="Other"> -->
	 <!-- <input type="hidden" name="topicId" value="1"> -->
	 	
	 	<!-- ************************************************************************* -->
	 	<!-- GESAMTPROBLEM / STATUS -->
	 	<!-- ************************************************************************* -->
	 	<tr>
	        <td width="160"> Gesamtproblem: </td>
	            
	        <td style="width: 718px" cowspan="2">           
				<!-- Gesamtproblem/Betreff/Subject -->
	            <input style="width:400px" id="subject" name="subject" value="<?php $subject_field = TicketForm::getInstance()->getField('subject');
	                echo $subject_field->display($ticket->getSubject()); ?>" size="12" autocomplete=OFF>&nbsp; 
					<i>(Betreff)</i>
			</td>				
						<!-- Ãœber 5 Zeilen -->
					
			<td rowspan="6" align="center">
				<input type="submit" name="submit" value="<?php echo __('Save');?>" style="height: 60px">
	        </td>

		</tr>

		<!-- ************************************************************************* -->
	 	<!-- HELP TOPIC / HILFETHEMA -->
	 	<!-- ************************************************************************* -->
		<tr>
		    <td><?php echo __('Help Topic');?>:</td>
		    <td style="width: 718px">
		    	<select name="topicId" style="width:250px;">
		           	<option value="" selected >&mdash; <?php echo __('Select Help Topic');?> &mdash;</option>
		                <?php
		                    if($topics=Topic::getHelpTopics()) {
		                        foreach($topics as $id =>$name) {
		                            echo sprintf('<option value="%d" %s>%s</option>',
		                                    $id, ($info['topicId']==$id)?'selected="selected"':'',$name);
		                        }
		                    }
	                    ?>
		        </select>
		                <font class="error"><b>*</b>&nbsp;<?php echo $errors['topicId']; ?></font>
		     </td>	                 

	     </tr>  

	    <!-- ************************************************************************* -->
	 	<!-- TICKET SOURCE / TICKETHERKUNFT -->
	 	<!-- ************************************************************************* -->         
	           
		<tr>
			<td><?php echo __('Ticket Source');?>:</td>
			<td style="width: 718px">
				<select name="source" style="width:250px;">
	                <option value="" selected >&mdash; <?php
	                    echo __('Select Source');?> &mdash;</option>
	                    <?php
		                    $source = $info['source'] ?: 'Phone';
		                    foreach (Ticket::getSources() as $k => $v) {
		                        echo sprintf('<option value="%s" %s>%s</option>', $k, ($source == $k ) ? 'selected="selected"' : '', $v);
			                    }
	                    ?>
	            </select>
			                <font class="error"><b>*</b>&nbsp;<?php echo $errors['source']; ?></font>
			</td>         

	    </tr>           
	    
	    <!-- ************************************************************************* -->
	 	<!-- PRIORITY / PRIORITÄT -->
	 	<!-- ************************************************************************* -->

		<tr>
			<td><?php echo __('Priority');?>:</td>
			<td>
	        	<select name="priority" style="width:250px;">
	                <option value="" selected >&mdash; <?php echo __('Select Priority');?> &mdash;</option>
	                    <?php
		                    if($priority=Priority::getPriorities()) {
		                        foreach($priority as $id=>$name) {
		                        	echo sprintf('<option value="%s" %s>%s</option>',
		                            		// Welcher Eintrag soll ausgewählt werden?
		                                    $id, ($ticket->getPriority() == $name) ? 'selected="selected"' : '', $name);
		                        }
		                    }
	                    ?>
	            </select>
	        </td>         
		</tr>

		<!-- ************************************************************************* -->
	 	<!-- DUE DATE / FÄLLIGKEITSDATUM -->
	 	<!-- ************************************************************************* -->

		<input type="hidden" name="note" value="Due Date">
		<input type="hidden" name="a" value="edit">
		<input type="hidden" name="id" value="<?php echo $ticket->getId(); ?>">
				
		<tr>
	        <td width="160">
	                <?php echo __('Due Date');?>:
	        </td>
	        <td style="width: 718px">
	            <input class="dp" id="duedate" name="duedate" value="<?php echo Format::htmlchars($info['duedate']); ?>" size="12" autocomplete=OFF>
	            &nbsp;&nbsp;
	            <?php
		            $min=$hr=null;
		            if($info['time'])
		                list($hr, $min)=explode(':', $info['time']);

		            echo Misc::timeDropdown($hr, $min, 'time');
	            ?>
	            &nbsp;<font class="error">&nbsp;<?php echo $errors['duedate']; ?>&nbsp;<?php echo $errors['time']; ?></font>
	            <em><?php echo __('Time is based on your time zone');?> GMT
	                (<?php echo $cfg->getTimezone($thisstaff); ?>)</em>&nbsp;
	        </td>
	    </tr>		
		<tr cowspan="3">
	        <td> &nbsp;</td>
	       	<td style="font:small-caption; width: 718px;">Bitte IMMER ein F&aumllligkeitsdatum angeben!</td>
	    </tr>
	</table>
</form>

<!--		
<table class="form_table dynamic-forms" width="940" border="0" cellspacing="0" cellpadding="2">
    <?php
		print_r($forms);        
		 if ($forms)
            foreach ($forms as $form) {
                $form->render(true, false, array('mode'=>'edit','width'=>160,'entry'=>$form));
        } ?>
</table>
-->

<!-- ************************************************************************* -->
<!-- SUB NAVI OF TOP - BOTTOM -->
<!-- ************************************************************************* -->

<ul id="sub_nav" style="border: 1px solid black">
	<?php include STAFFINC_DIR . "templates/sub-navigation.tmpl.php"; ?>
</ul>


