<?php
header("Content-Type: text/html; charset=UTF-8");

$title = ($ost && ($title=$ost->getPageTitle()))
    ? $title : ('osTicket :: '.__('Staff Control Panel'));

if (!isset($_SERVER['HTTP_X_PJAX'])) { ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html<?php
if (($lang = Internationalization::getCurrentLanguage())
        && ($info = Internationalization::getLanguageInfo($lang))
        && (@$info['direction'] == 'rtl'))
    echo ' dir="rtl" class="rtl"';
if ($lang) {
    echo ' lang="' . Internationalization::rfc1766($lang) . '"';
}
?>>
<head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta http-equiv="cache-control" content="no-cache" />
    <meta http-equiv="pragma" content="no-cache" />
    <meta http-equiv="x-pjax-version" content="<?php echo GIT_VERSION; ?>">
    <title><?php echo (($_SERVER['HTTP_HOST'] == 'ticketsystem' || $_SERVER['HTTP_HOST'] == 'hilfe') ? '' : 'TEST ').Format::htmlchars($title); ?></title>
    <!--[if IE]>
    <style type="text/css">
        .tip_shadow { display:block !important; }
    </style>
    <![endif]-->
    <script type="text/javascript" src="<?php echo ROOT_PATH; ?>js/jquery-1.11.2.min.js?901e5ea"></script>
    <link rel="stylesheet" href="<?php echo ROOT_PATH ?>css/thread.css?901e5ea" media="all"/>
    <link rel="stylesheet" href="<?php echo ROOT_PATH ?>scp/css/scp.css?901e5ea" media="all"/>
    <link rel="stylesheet" href="<?php echo ROOT_PATH; ?>css/redactor.css?901e5ea" media="screen"/>
    <link rel="stylesheet" href="<?php echo ROOT_PATH ?>scp/css/typeahead.css?901e5ea" media="screen"/>
    <link type="text/css" href="<?php echo ROOT_PATH; ?>css/ui-lightness/jquery-ui-1.10.3.custom.min.css?901e5ea"
         rel="stylesheet" media="screen" />
     <link type="text/css" rel="stylesheet" href="<?php echo ROOT_PATH; ?>css/font-awesome.min.css?901e5ea"/>
    <!--[if IE 7]>
    <link rel="stylesheet" href="<?php echo ROOT_PATH; ?>css/font-awesome-ie7.min.css?901e5ea"/>
    <![endif]-->
    <link type="text/css" rel="stylesheet" href="<?php echo ROOT_PATH ?>scp/css/dropdown.css?901e5ea"/>
    <link type="text/css" rel="stylesheet" href="<?php echo ROOT_PATH; ?>css/loadingbar.css?901e5ea"/>
    <link type="text/css" rel="stylesheet" href="<?php echo ROOT_PATH; ?>css/flags.css?901e5ea"/>
    <link type="text/css" rel="stylesheet" href="<?php echo ROOT_PATH; ?>css/select2.min.css?901e5ea"/>
    <link type="text/css" rel="stylesheet" href="<?php echo ROOT_PATH; ?>css/rtl.css?901e5ea"/>
    <link type="text/css" rel="stylesheet" href="<?php echo ROOT_PATH ?>scp/css/translatable.css?901e5ea"/>

    <?php
    if($ost && ($headers=$ost->getExtraHeaders())) {
        echo "\n\t".implode("\n\t", $headers)."\n";
    }
    ?>
</head>
<body>
<div id="container" style="width:<?php echo $cfg->getHelpdeskWidth(); ?> !important;">
    <?php
    if($ost->getError())
        echo sprintf('<div id="error_bar">%s</div>', $ost->getError());
    elseif($ost->getWarning())
        echo sprintf('<div id="warning_bar">%s</div>', $ost->getWarning());
    elseif($ost->getNotice())
        echo sprintf('<div id="notice_bar">%s</div>', $ost->getNotice());
    ?>
    <div id="header">
        <p id="info" class="pull-right no-pjax"><?php echo sprintf(__('Welcome, %s.'), '<strong>'.$thisstaff->getFirstName().'</strong>'); ?>
           <?php
            if($thisstaff->isAdmin() && !defined('ADMINPAGE')) { ?>
            | <a href="<?php echo ROOT_PATH ?>scp/admin.php" class="no-pjax"><?php echo __('Admin Panel'); ?></a>
            <?php }else{ ?>
            | <a href="<?php echo ROOT_PATH ?>scp/index.php" class="no-pjax"><?php echo __('Agent Panel'); ?></a>
            <?php } ?>
            | <a href="<?php echo ROOT_PATH ?>scp/profile.php"><?php echo __('Profile'); ?></a>
            | <a href="<?php echo ROOT_PATH ?>scp/logout.php?auth=<?php echo $ost->getLinkToken(); ?>" class="no-pjax"><?php echo __('Log Out'); ?></a>
        </p>
        <a href="<?php echo ROOT_PATH ?>scp/index.php" class="no-pjax" id="logo">
            <span class="valign-helper"></span>
            <img src="<?php echo ROOT_PATH ?>scp/logo.php?<?php echo strtotime($cfg->lastModified('staff_logo_id')); ?>" alt="osTicket &mdash; <?php echo __('Customer Support System'); ?>"/>
        </a>
    </div>
    <div id="pjax-container" class="<?php if ($_POST) echo 'no-pjax'; ?>">
<?php } else {
    header('X-PJAX-Version: ' . GIT_VERSION);
    if ($pjax = $ost->getExtraPjax()) { ?>
    <script type="text/javascript">
    <?php foreach (array_filter($pjax) as $s) echo $s.";"; ?>
    </script>
    <?php }
    foreach ($ost->getExtraHeaders() as $h) {
        if (strpos($h, '<script ') !== false)
            echo $h;
    } ?>
    <title><?php echo ($_SERVER['HTTP_HOST'] == 'ticketsystem' ? '' : 'TEST ').($ost && ($title=$ost->getPageTitle()))?$title:'osTicket :: '.__('Staff Control Panel'); ?></title><?php
} # endif X_PJAX ?>
    <ul id="nav">
<?php include STAFFINC_DIR . "templates/navigation.tmpl.php"; ?>
    </ul>
    <ul id="sub_nav">
<?php include STAFFINC_DIR . "templates/sub-navigation.tmpl.php"; ?>
    </ul>
    <div id="content">
<!-- department selector Anfang -->
<!-- deparmtent selector bei der Suche und der erweiterten Suche ausblenden  -->
<?php 
if(isset($inc) && $inc == 'tickets.inc.php' && $_REQUEST['a']!='search' && !isset($_REQUEST['advsid'])) { //  && $_REQUEST['a']!='search' 
	$DSitem = array();
	foreach($ds['depts'] as $DSid => $DSname) {
		if($DSid == 0) {
			$DSwhere = 'WHERE (';
			$DSwhere .= ' ticket.dept_id IN ('.($ds['deptIDs']?implode(',', db_input($ds['deptIDs'])):0).')';
	    	$DSwhere .=' OR (ticket.staff_id='.db_input($thisstaff->getId()).' AND status.state="open") '; // zugewiesene Tickets zählen
		
			if(($teams=$thisstaff->getTeams()) && count(array_filter($teams)))
	    		$DSwhere .=' OR (ticket.team_id IN ('.implode(',', db_input(array_filter($teams))).') AND status.state="open") ';// MA-Team Tickets zählen

			$DSwhere .= ' )';
		}
		elseif($DSid > 0)
			$DSwhere = 'WHERE ticket.dept_id = '.$DSid;

		$_REQUEST['status'] = (isset($_REQUEST['status']))?$_REQUEST['status']:'open';
		$DSstatus = (in_array($_REQUEST['status'], array('closed')))?'closed':'open';
		if($_REQUEST['status'] == 'closed') { // closed
			$DSwhere .= '';
		}elseif($_REQUEST['status'] == 'assigned') { //assigned (My tickets)
		    $DSwhere.=' AND ticket.staff_id='.db_input($thisstaff->getId());
		}elseif($_REQUEST['status'] == 'overdue') { //overdue
		    $DSwhere.=' AND ticket.isoverdue=1 ';
		}elseif($_REQUEST['status'] == 'answered') { //answered
		    $DSwhere.=' AND ticket.isanswered=1 ';
		}elseif($_REQUEST['status'] == 'open') { //open
		    if(!$cfg->showAnsweredTickets()) //Showing answered tickets on open queue??
		        $DSwhere.=' AND ticket.isanswered=0 ';
		    if(!($cfg->showAssignedTickets() || $thisstaff->showAssignedTickets())) { // Showing assigned tickets on open queue?
		        $DSwhere.=' AND ticket.staff_id=0 '; //XXX: NOT factoring in team assignments - only staff assignments.
		    }
		}

		$DSsql =  'SELECT count( ticket.ticket_id ) AS tickets '
	                .'FROM ' . TICKET_TABLE . ' ticket '
	                .'INNER JOIN '.TICKET_STATUS_TABLE. ' status
	                    ON (ticket.status_id=status.id AND status.state=\''.$DSstatus.'\') '
	                .$DSwhere;
		$DSres = db_query($DSsql);
		list($DSitemCount) = db_fetch_row($DSres);
		
		if($DSid == $ds['deptId']) {
			$classDScurrent = ' DSitemCurrent';
			$currentDept = $DSname;
		} else {
			$classDScurrent = '';
		}
		$DSitem[] = '<div class="DSitem'.$classDScurrent.'" onclick="window.location=\'tickets.php?dsId='.$DSid.'&status='.strtolower($_REQUEST['status']).'\'">'.$DSname
					.'&nbsp;('.$DSitemCount.')'
					.'</div>';
	}
?>
	<div id="deptSelector">
		<div class="noteCurrentDept">
			<?php echo __('Current department').':&nbsp;<strong>&raquo;&nbsp;'.$currentDept.'&nbsp;&laquo;</strong> &nbsp;-&nbsp; ( '.__('klick here to change').' )'; ?>
	    </div>
		<table>
	    	<tr>
            	<td class="DSlabel"><?php echo __('Department selection').':'; ?></td>
	    		<td><?php foreach($DSitem AS $item) {echo $item;} ?></td>
            </tr>
    	</table>
	</div>
<?php
} ?>
<script type="text/javascript">
	$(document).ready(function(){
		var dsDiv = $("#deptSelector");
		var dsTable = $("#deptSelector table");
		var timeToSlide = 300;
		dsTable.hide();
		$(".noteCurrentDept").click(function() {
			dsTable.show(timeToSlide);
		});
		$("#nav, #sub_nav").click(function() {
			dsTable.hide(timeToSlide);
		});
		dsDiv.hover(function() {
						<!-- dsTable.show(timeToSlide); -->
					}, function() {
						dsTable.hide(timeToSlide);
					}
		);
	});
</script>
<!-- department selector Ende -->

        <?php if($errors['err']) { ?>
            <div id="msg_error"><?php echo $errors['err']; ?></div>
        <?php }elseif($msg) { ?>
            <div id="msg_notice"><?php echo $msg; ?></div>
        <?php }elseif($warn) { ?>
            <div id="msg_warning"><?php echo $warn; ?></div>
        <?php }
        foreach (Messages::getMessages() as $M) { ?>
            <div class="<?php echo strtolower($M->getLevel()); ?>-banner"><?php
                echo (string) $M; ?></div>
<?php   } ?>
