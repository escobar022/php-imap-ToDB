<?php namespace PhpImap;

/**
 * @see https://github.com/barbushin/php-imap
 * @author Barbushin Sergey http://linkedin.com/in/barbushin
 */
class IncomingMail {

	public $id;
	public $newid;
	public $date;
	public $subject;

	public $fromName;
	public $fromAddress;

	public $to = array();
	public $toString;
	public $cc = array();
	public $replyTo = array();

	public $textPlain;
	public $textHtml;
	/** @var IncomingMailAttachment[] */
	protected $attachments = array();

	public function addAttachment(IncomingMailAttachment $attachment) {
		$this->attachments[$attachment->id] = $attachment;
	}

	/**
	 * @return IncomingMailAttachment[]
	 */
	public function getAttachments() {
		return $this->attachments;
	}

	/**
	 * Get array of internal HTML links placeholders
	 * @return array attachmentId => link placeholder
	 */
	public function getInternalLinksPlaceholders() {
		return preg_match_all( '/=["\'](cid:([\w\.%*@-]+))["\']/i', $this->textHtml, $matches ) ? array_combine( $matches[2], $matches[1] ) : array();
	}

	public function replaceInternalLinks($baseUri) {
		$baseUri = rtrim($baseUri, '\\/') . '/';
		$fetchedHtml = $this->textHtml;
		foreach($this->getInternalLinksPlaceholders() as $attachmentId => $placeholder) {
			$fetchedHtml = str_replace($placeholder, $baseUri . basename($this->attachments[$attachmentId]->filePath), $fetchedHtml);
		}
		return $fetchedHtml;
	}

	/**
	 * Get array of internal HTML links placeholders and add info to Database
	 * @return array attachmentId => link placeholder
	 */

	function db_add_message( $baseUri ) {

		$baseUri     = rtrim( $baseUri, '\\/' ) . '/';
		$fetchedHtml = $this->textHtml;

		foreach ( $this->getInternalLinksPlaceholders() as $attachmentId => $placeholder ) {
			if ( isset( $this->attachments[ $attachmentId ] ) ) {
				$fetchedHtml = str_replace( $placeholder, $baseUri . basename( $this->attachments[ $attachmentId ]->filePath ), $fetchedHtml );
			}
		}

		$execute     = mysql_query( "INSERT INTO emailtodb_email (IDEmail, EmailFrom, EmailFromP, EmailTo, DateE, DateDb, Subject, Message, Message_html, MsgSize) VALUES
						('" . $this->id . "',
						'" . $this->fromAddress . "',
						'" . addslashes( strip_tags( $this->fromName ) ) . "',
						'" . addslashes( strip_tags( $this->toString ) ) . "',
						'" . $this->date . "',
						'" . date( "Y-m-d H:i:s" ) . "',
						'" . addslashes( $this->subject ) . "',
						'" . addslashes( '' ) . "',
						'" . addslashes( $fetchedHtml ) . "',
						'')" );
		$execute     = mysql_query( "select LAST_INSERT_ID() as UID" );
		$row         = mysql_fetch_array( $execute );
		$this->newid = $row["UID"];


		$attachments = $this->getAttachments();


		foreach ( $attachments as $attachment ) {
			$this->db_add_attach( $this->newid, $attachment->name, $attachment->filePath, $attachment->disposition );
		}

		return $fetchedHtml;
	}

	function db_add_attach( $newid, $file_orig, $filedir,$attachmenttype ) {

		mysql_query( "INSERT INTO emailtodb_attach (IDEmail, FileNameOrg, Filedir, AttachType) VALUES ('" . $newid . "','" . addslashes( $file_orig ) . "','" . addslashes( $filedir ) ."','" . addslashes( $attachmenttype ) . "')" );
	}

}

class IncomingMailAttachment {

	public $id;
	public $name;
	public $filePath;
	public $disposition;
}
