<?php
/*********************************************************************
    class.timesheet.php

    timesheet functions

    Jens Eberle <jens@isohelpdesk.de>
    Copyright (c)  2006-2015 osTicket.com.de
    http://www.osticket.com.de

    Released under the GNU General Public License WITHOUT ANY WARRANTY.
    See LICENSE.TXT for details.

    vim: expandtab sw=4 ts=4 sts=4:
**********************************************************************/

class TS {
	
	var $version;
	var $latestVersion = 'ts1.10-1';
	
	function __construct() {
		global $cfg, $errors, $msg;
		$this->version = $cfg->getTimesheetVersion();
		
		if(!$this->version) {// prüfen, ob timesheet installiert ist...
			$install = self::install();
			if($install !== TRUE)
				$errors['err'] = $install;
			else
				$msg .= sprintf(__('Timesheet module installed successfully. Current Version is: %s'), $this->version);
		}
		elseif($this->version != $this->latestVersion) {// prüfen, ob die installierte Version aktuell ist...
			$update = self::update();
			if($update !== TRUE)
				$errors['err'] = $update;
			else
				$msg .= sprintf(__('Timesheet module updated successfully. Current Version is: %s'), $this->version);
		}
	}
	
	function getVersion() {
		return $this->version;
	}

	function updateVersion() {
			$sql = 'UPDATE '.CONFIG_TABLE
	            .' SET `value`='.db_input($this->version)
	            .' WHERE `key`="timesheet_version"';
	        if(db_query($sql))
            	return true;
			else
				return false;
	}
	
	function install() {
		$sql1 = "
			CREATE TABLE IF NOT EXISTS `".TABLE_PREFIX."timesheet` (
			`id` int(11) unsigned NOT NULL auto_increment,
			`thread_id` int(11) unsigned NOT NULL,
			`object_id` int(11) unsigned NOT NULL,
			`object_type` char(1) NOT NULL,
			`thread_entry_id` int(11) unsigned NOT NULL,
			`staff_id` int(11) unsigned NOT NULL default '0',
			`processingTime` int(11) unsigned NOT NULL default '0',
			`processingTime_type_id` int(11) unsigned NOT NULL default '1',
			`settled` enum('1', '0') NOT NULL DEFAULT '0',
			`created` datetime NOT NULL,
			`updated` datetime NOT NULL,
			PRIMARY KEY  (`id`),
			KEY `thread_entry_id` (`thread_entry_id`)
			) DEFAULT CHARSET=utf8;
		";
		if(db_query($sql1)) {
			$sql2 = 'INSERT INTO '.CONFIG_TABLE
	            .' SET `namespace`="core"'
	            .', `key`="timesheet_version"'
	            .', value='.db_input($this->latestVersion);
	        if(db_query($sql2) && $this->version = $this->latestVersion)
            	return TRUE;
			else
				return sprintf(__('Timesheet module installed successfully - but config table entry couldn\'t updated. Data with key="timesheetInstalled" should be "%s"'), $this->latestVersion);
		}
		else
			return __('Can\'t install timesheet module').' - '.$this->latestVersion;
	}
	
	function update() {
		switch($this->version) {
			case 'ts-v0.1':
				// ist das Update auf v1.10 schon durchgeführt worden?
				$sql = 'SELECT * FROM '.THREAD_TABLE.' LIMIT 1';
				if(!db_query($sql))
					return __('Error: osTicket version is to old for this timesheet module. The upgrade may not have been successful finished. Update pending until osTicket version is 1.10 or newer');
				// Update von ts-v0.1 auf ts1.10-1
				$sqlNewTable = "
					CREATE TABLE IF NOT EXISTS `".TABLE_PREFIX."timesheet` (
					`id` int(11) unsigned NOT NULL auto_increment,
					`thread_id` int(11) unsigned NOT NULL,
					`object_id` int(11) unsigned NOT NULL,
					`object_type` char(1) NOT NULL,
					`thread_entry_id` int(11) unsigned NOT NULL,
					`staff_id` int(11) unsigned NOT NULL default '0',
					`processingTime` int(11) unsigned NOT NULL default '0',
					`processingTime_type_id` int(11) unsigned NOT NULL default '1',
					`settled` enum('1', '0') NOT NULL DEFAULT '0',
					`created` datetime NOT NULL,
					`updated` datetime NOT NULL,
					PRIMARY KEY  (`id`),
					KEY `thread_entry_id` (`thread_entry_id`)
					) DEFAULT CHARSET=utf8;
				";
				if(db_query($sqlNewTable)) {
					// Daten umziehen...
					$sqlOldData = '	SELECT tht.id AS thread_id, tts.id AS id, 
										tts.ticket_id AS object_id, tts.thread_Id AS thread_entry_id, tts.staff_id, 
										tts.processingTime, tts.type_id AS processingTime_type_id, 
										tts.settled, tts.created, tts.updated 
									FROM `'.TABLE_PREFIX.'ticket_timesheet` tts
									LEFT JOIN '.THREAD_TABLE.' tht ON (tht.object_id = tts.ticket_id AND tht.object_type = \'T\')';
					if(($rows=db_query($sqlOldData)) && db_num_rows($rows)) {
            			while ($row = mysqli_fetch_assoc($rows)) {
       						$sqlInsert = 'INSERT INTO '.TIMESHEET_TABLE.' SET '
										.'  thread_id='.db_input($row['thread_id'])
		     					      	.' ,object_id='.db_input($row['object_id'])
		     					      	.' ,object_type='.db_input('T')
		     					      	.' ,thread_entry_id='.db_input($row['thread_entry_id'])
		  					         	.' ,staff_id='.db_input($row['staff_id'])
		     					      	.' ,processingTime='.db_input($row['processingTime'])
		     					      	.' ,processingTime_type_id='.db_input($row['processingTime_type_id'])
		    				    	   	.' ,settled='.db_input($row['settled'])
		    				    	   	.' ,created='.db_input($row['created'])
		    					       	.' ,updated='.db_input($row['updated'])
										;
							if(db_query($sqlInsert)) {
								$sqlDelete = 'DELETE FROM `'.TABLE_PREFIX.'ticket_timesheet` WHERE id = '.db_input($row['id']);
								db_query($sqlDelete);
							}
    					}
						// Daten sind umgezogen. Alte Tabelle löschen
						if(($rows=db_query($sqlOldData)) && db_num_rows($rows) == 0) {
							$rows=db_query('DROP TABLE `'.TABLE_PREFIX.'ticket_timesheet` ');
							// Update erfolgreich - timesheet-Version aktualisieren
							db_query('UPDATE '.CONFIG_TABLE.' SET `key` = \'timesheet_version\' WHERE `key` = \'timesheetInstalled\' ');
							$this->version = 'ts1.10-1';
							self::updateVersion();
						}
						else
							return __('Error: can\'t delete old Table from version ts-v0.1 - it\' not empty. Please reload Page to retry');
					}
				}
				else
					return sprintf(__('Error: can\'t create new Table for timesheet module (%s) - Update from version %s'), $this->latestVersion, $this->version);
				
				break;
			case 'ts1.10-1':
				// nothing to do, we are on latest version
				break;
			default:
				return __('Error: no supported timesheet module version').' - '.$this->version;
		}
		// prüfen, ob aktualisierte Version die aktuellste ist
		if($this->version != $this->latestVersion)
			self::update();
		else 
			return TRUE;
	}
	

	function getPTinputFieldJS() {
		echo '
		<script language="JavaScript" type="text/javascript">
		
		function timesheetStart() {
			
			if(!$(".timer").length && 
			   !$(".ptTimerDisplay").length &&
			   !$(".ptHiddenFieldValue").length &&
			   !$(".ptStartStopButton").length &&
			   !$(".ptResetButton").length) {
				   // benötigte DOM-Struktur steht noch nicht -> erneut versuchen in 1 Sec.
				   starten = setTimeout(function() {timesheetStart();}, 1000);
			}
			else {
				// alles klar - let´s go...
				var startTime = 60; // Startwert in Sekunden - auch bei reset
				var timerIntervall = 1000; // in Millisekunden
				   
				var timer;
				var timerOn = 0;
				var count = startTime;
				var timerDisplay = $(".ptTimerDisplay");
				var inputFieldTime = $(".ptInputFieldTime");
				var hiddenFieldValue = $(".ptHiddenFieldValue");
				var startStopButton = $(".ptStartStopButton");
				var resetButton = $(".ptResetButton");
				
				function formatTimeOutput(time, format) {
					
					if(format != "hh:mm:ss")
						format = "hh:mm";
						
					hrs = Math.floor(time / 3600);
					min = Math.floor((time - (hrs * 3600)) / 60);
					sec = time - (hrs * 3600 + min * 60);
					
					if(format != "hh:mm:ss")
						min = (sec > 30) ? min + 1 : min;
					
					hrs = (hrs < 10)? "0" + hrs : hrs;
					min = (min < 10)? "0" + min : min;
					sec = (sec < 10)? "0" + sec : sec;
					
					if(format == "hh:mm:ss")
						return hrs + ":" + min + ":" + sec;
					else
						return hrs + ":" + min;
				}
		
				function timerFunction() {
					count++;
					
					timerDisplay.text(formatTimeOutput(count, "hh:mm:ss"));
					inputFieldTime.html(formatTimeOutput(count, "hh:mm"));
					hiddenFieldValue.attr("value", count);
					timer = setTimeout(timerFunction, timerIntervall);
				}
			
				function startTimer() {
					if(timerOn == 0) {
			        	timerOn = 1;
						startStopButton.attr("value", "'.__('Stop').'");
						timer = setTimeout(timerFunction, timerIntervall);
					}
				}
			
				function stopTimer() {
					if(timerOn == 1) {
						timerOn = 0;
						startStopButton.attr("value", "'.__('Start').'");
						clearTimeout(timer);
					}
				}
			
				startStopButton.click(function(e) {
					e.defaultPrevented;
					if(timerOn == 0) {
						startTimer();
					}
					else {
						stopTimer();
					}
				});
				
				inputFieldTime.click(function(e) {
					e.defaultPrevented;
					stopTimer();
				});
				
				inputFieldTime.keydown(function (e) {
  					if (e.keyCode == 13) {
    					$(this).blur();
						return false;
  					}
				});
				
				inputFieldTime.blur(function(e) {
					e.defaultPrevented;
					var timeArray = $(this).html().split(":");
					checkTimeInput(timeArray);
				});
				
				function checkTimeInput(timeArray) {
					
					if(timeArray.length == 1) {
						// ein Wert = min
						countInt = timeArray[0] * 60;
					}
					if(timeArray.length == 2) {
						// zwei Werte = hrs:min
						countInt = timeArray[0] * 3600 + timeArray[1] * 60;
					}
					if(timeArray.length == 3) {
						// drei Werte = hrs:min:sec
						countInt = timeArray[0] * 3600 + timeArray[1] * 60 + timeArray[2] * 1;
					}
					
					if(countInt % 1 == 0) 
						count = countInt;

					timerDisplay.text(formatTimeOutput(count, "hh:mm:ss"));
					hiddenFieldValue.attr("value", count);
					if(timeArray.length == 3)
						inputFieldTime.html(formatTimeOutput(count, "hh:mm:ss"));
					else
						inputFieldTime.html(formatTimeOutput(count, "hh:mm"));
				};
				
				resetButton.click(function(e) {
					e.defaultPrevented;
					stopTimer();
					timerDisplay.text(formatTimeOutput(startTime, "hh:mm:ss"));
					inputFieldTime.html(formatTimeOutput(startTime, "hh:mm"));
					hiddenFieldValue.attr("value", startTime);
					count = startTime;
				});
				
				timerDisplay.text(formatTimeOutput(startTime, "hh:mm:ss"));
				startTimer();
			}
		}
		
		timesheetStart();
		</script>
		';
	}

	private function getPTinputField() {
		return '
			<span class="pull-left" style="margin:2px 10px;">'.__('Timer').': <span class="ptTimerDisplay">00:01</span> ('.__('h/min/sec').')</span>
			<input type="button" name="ptStartStopButton" class="action-button pull-left confirm-action ptStartStopButton" value="'.__('Start').'" />
			<input type="button" name="ptResetButton" class="action-button pull-left confirm-action ptResetButton" value="'.__('Reset').'" />
			<div contenteditable="true" name="ptInputFieldTime" class="action-button pull-left ptInputFieldTime" 
				style="text-align:center; letter-spacing:1px; width:80px; background-color:#FFF; -webkit-user-select:auto; -moz-user-select:auto; 
				cursor:text;">00:01</div>&nbsp;('.__('h/min').')&nbsp;&nbsp;
			<input type="hidden" name="ptHiddenFieldValue" class="ptHiddenFieldValue" value="0" />
			<input type="hidden" name="ptInputFieldType" class="ptInputFieldType" value="1" />
			<br />&nbsp;
		
		';
	}

	function getPTinputFieldT() {
		echo '
		<tr>
			<td width="120" style="vertical-align:top"><strong>'.__('Processing Time').':</strong></td>
			<td>'.self::getPTinputField().'</td>
		</tr>';
	}

	function getPTinputFieldA() {
		echo '
		<tr>
			<td>'.self::getPTinputField().'</td>
		</tr>';
	}

	function displayTimeFieldOverview($thread_id = 0) {
		echo '
                <tr>
                    <th>'.__('Processing Time').':</th>
                    <td>'.self::formatPT(self::getPTtotal($thread_id)).' '.__('h/min').'</td>
                </tr>
		';
	}
	
	function getPTtotal($thread_id = 0) {
		$sql = 'SELECT SUM(processingTime) AS processingTime '
				.'FROM `'.TIMESHEET_TABLE.'` '
				.'WHERE thread_id = '.$thread_id.' '
				.'GROUP BY thread_id';
		
        $processingTime=0;
        if(($res=db_query($sql)) && db_num_rows($res))
            list($processingTime)=db_fetch_row($res);
			
		return $processingTime;
	}

	function getPTtotalByObjectId($object_id = 0, $object_type = 0) {
		// Ticket-ID aus der Thread-Tabelle holen....
		$sql = 'SELECT id FROM '.THREAD_TABLE.' WHERE object_id = '.db_input($object_id).' AND object_type = '.db_input($object_type);
		$res=db_query($sql);
		list($thread_id) = db_fetch_row($res);
		
		return self::getPTtotal($thread_id);
	}
	
	function getPTthreadEntry($thread_entry_id = 0) {
		$sql = 'SELECT SUM(processingTime) AS processingTime '
				.'FROM `'.TIMESHEET_TABLE.'` '
				.'WHERE thread_entry_id = '.$thread_entry_id.' '
				.'GROUP BY thread_entry_id';
		
        $processingTime=0;
        if(($res=db_query($sql)) && db_num_rows($res))
            list($processingTime)=db_fetch_row($res);
			
		return $processingTime;
	}
	
	function formatPT($time = 0) {
		$time = intval($time);
		
		$hrs = intval($time / 3600);
		$min = intval(($time - ($hrs * 3600)) / 60);
		$sec = $time - ($hrs * 3600 + $min * 60);
		// sec runden
		$min = ($sec > 30)?$min + 1 : $min;

		$hrs = ($hrs < 10)?'0'.$hrs : $hrs;
		$min = ($min < 10)?'0'.$min : $min;
		$sec = ($sec < 10)?'0'.$sec : $sec;
		//echo 'Hour '.$hrs.' Min '.$min.' Sec '.$sec;
		
		return $hrs.':'.$min;
	}

	function displayThreadEntryTime($thread_entry_id = 0, $thread_entry_type = 'M') {
		if($thread_entry_type == 'R' || $thread_entry_type == 'N') {
			echo '<span style="vertical-align:middle;" class="faded title">( '
			.TS::formatPT(TS::getPTthreadEntry($thread_entry_id))
			.' '.__('h/min').' ) &nbsp;</span>';
		}
	}
	
	function saveThreadTime($vars) {
		/* 	Soll bei Ticket durch MA wirklich die Zeit gespeichert werden, wenn es keine Antwort gibt?
			Wie soll Sie zugeordnet werden?
			
		if($vars['a'] == 'open' && $vars['response'] != '') { // Ticket durch MA, wenn es eine Antwort gibt
			if($vars['thread_entry_type'] == 'R')
				$vars['a'] = 'reply';
			else 
				$vars['thread_entry_type'] = '0';
		}
		elseif($vars['a'] == 'open' && $vars['response'] == '') { // Ticket durch MA, wenn es keine Antwort gibt
			if($vars['thread_entry_type'] == 'M') {
				$vars['a'] = 'reply';
				$vars['thread_entry_type'] = 'N';
				$vars['thread_entry_id'] += 1;
			}
			else 
				$vars['type'] = '0';
		}
		*/
		
		if(in_array($vars['thread_entry_type'], array('R','N'))) {
			if(!isset($vars['processingTime'])) $vars['processingTime'] = 0;
			if(!isset($vars['processingTime_type_id'])) $vars['processingTime_type_id'] = 0;
			
			if(intval($vars['processingTime']) > 0) {
				//Wenn $vars['thread_entry_id'] = 0 -> Abfrage des letzten Datensatzes der Thread-Tabelle via db_insert_id() fehlgeschlagen.... FALLBACK
				if($vars['thread_entry_id'] == 0) {
					$sql = 'SELECT id FROM '.THREAD_ENTRY_TABLE.' WHERE thread_id = '.db_input($vars['thread_id']).' ORDER BY id DESC LIMIT 1';
					$res=db_query($sql);
					list($vars['thread_entry_id'])=db_fetch_row($res);
				}
				// Fehlende Daten aus der Thread-Tabelle holen....
				$sql = 'SELECT object_id, object_type FROM '.THREAD_TABLE.' WHERE id = '.db_input($vars['thread_id']);
				$res=db_query($sql);
				list($vars['object_id'], $vars['object_type'])=db_fetch_row($res);
				
				$created = (isset($vars['created']))?db_input($vars['created']):'NOW()';
				$sql=' INSERT INTO '.TIMESHEET_TABLE.' SET '
		           	.'  thread_id='.db_input($vars['thread_id'])
		           	.' ,thread_entry_id='.db_input($vars['thread_entry_id'])
		           	.' ,object_id='.db_input($vars['object_id'])
		           	.' ,object_type='.db_input($vars['object_type'])
		           	.' ,staff_id='.db_input($vars['staff_id'])
		           	.' ,processingTime='.db_input($vars['processingTime'])
                    .' ,processingTime_type_id='.db_input($vars['processingTime'])
                    //.' ,settled='.db_input($vars['staffId'])
		           	.' ,created='.$created
					;
				if(!db_query($sql))
					return false;
				
				return true;
			}
		}
	}
	
	

}
define('TIMESHEET_TABLE', TABLE_PREFIX.'timesheet');

$timesheet = new TS;
?>
