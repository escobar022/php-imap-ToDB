<?php

require_once( '../src/PhpImap/IncomingMail.php' );
require_once( '../src/PhpImap/Mailbox.php' );


use PhpImap\Mailbox as ImapMailbox;
use PhpImap\IncomingMail;
use PhpImap\IncomingMailAttachment;


//DataBase  Connection
$cfg["db_host"] = 'db_host';
$cfg["db_user"] = 'db_user';
$cfg["db_pass"] = 'db_pass';
$cfg["db_name"] = 'db_name';

$mysql_pconnect = mysql_pconnect( $cfg["db_host"], $cfg["db_user"], $cfg["db_pass"] );

if ( ! $mysql_pconnect ) {
	echo "Connection Failed";
	exit;
}
$db = mysql_select_db( $cfg["db_name"], $mysql_pconnect );
if ( ! $db ) {
	echo "DB Select Failed";
	exit;
}

//Clear Database
/*mysql_query( "TRUNCATE TABLE `emailtodb_attach`" );
mysql_query( "TRUNCATE TABLE `emailtodb_email`" );*/


// IMAP must be enabled in Google Mail Settings
//http://lcl.apewp.org/php-imap-master/example/index.php
define( 'GMAIL_IMAP_PATH', '{imap.gmail.com:993/imap/ssl}INBOX' );
define( 'GMAIL_EMAIL', 'some@gmail.com' );
define( 'GMAIL_PASSWORD', '*********' );
define( 'ATTACHMENTS_DIR', __DIR__ . '/attachments' );

$mailbox = new ImapMailbox( GMAIL_IMAP_PATH, GMAIL_EMAIL, GMAIL_PASSWORD, ATTACHMENTS_DIR, 'utf-8' );
$mails   = array();

$baseUri = 'http://lcl.sandbox.com/php-imap-master/example/attachments/';

// Get some mail
$mailsIds = $mailbox->searchMailBox( 'ALL' );

if ( ! $mailsIds ) {
	die( 'Mailbox is empty' );
}

//get only from first email in array
//$mailId = reset($mailsIds);

//get only from latest email in array
$mailId = end( $mailsIds );


//Get Header Only, See Option in function
//$header = $mailbox->getHeader( $mailId );
//var_dump( $header );

//Get all mail parts
$mail = $mailbox->getMail( $mailId );
//var_dump( $mail );

//Display Mail with relative URI, Dependent on getMail()
var_dump($mail->replaceInternalLinks($baseUri));

//Insert Into DB with Base URI
//$db_insert = $mail->db_add_message( $baseUri );
//var_dump( $db_insert );


/*Use PHPMailer to Send email Received
 *
 * Need to add PHPMailer to root folder of project, https://github.com/PHPMailer/PHPMailer
    require_once( '../PHPMailer-master/PHPMailerAutoload.php' );
 * (OR reference it to current location, ei.Worpdress as below)
    global $phpmailer;
    if ( ! is_object( $phpmailer ) || ! is_a( $phpmailer, 'PHPMailer' ) ) {
		require_once ABSPATH . WPINC . '/class-phpmailer.php';
		require_once ABSPATH . WPINC . '/class-smtp.php';
		$phpmailer = new PHPMailer();
	}
*/

/*
//optional for POP3
$pop = POP3::popBeforeSmtp(GMAIL_IMAP_PATH, 110, 30, GMAIL_EMAIL, GMAIL_PASSWORD, 1);

//Create a new PHPMailer instance
//Passing true to the constructor enables the use of exceptions for error handling
$mail2 = new PHPMailer();
try {
	$mail2->isSMTP();
	$mail2->SMTPDebug = 0;
	$mail2->Debugoutput = 'html';
	$mail2->Host = '';
	$mail2->Port = 25;
	$mail2->SMTPAuth = false;
	$mail2->From = GMAIL_EMAIL;
	$mail2->FromName = 'Sender';
	$mail2->addAddress('receiver@example.com','Receiver');

	//Optional to add different Reply to
	//	$mail2->addReplyTo('sender@example.com', 'Sender');

	//Optional See getHeader
	//	$mail2->addCustomHeader('ParentID','420');

	$mail2->Subject = $mail->subject;
	$mail2->msgHTML($mail->replaceInternalLinks($baseUri));

	//get attachments
	foreach($mail->getAttachments() as $ATTACHMENT){
		if($ATTACHMENT->disposition != 'INLINE'){
			$mail2->addAttachment($ATTACHMENT->filePath, $ATTACHMENT->name);
		}
	}
	$mail2->send();
	echo "Message sent!";
} catch (phpmailerException $e) {
	echo $e->errorMessage(); //Pretty error messages from PHPMailer
} catch (Exception $e) {
	echo $e->getMessage(); //Boring error messages from anything else!
}
*/
