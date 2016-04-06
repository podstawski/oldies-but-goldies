<?php
/**
 * @author <marcin.kurczewski@gammanet.pl> Marcin Kurczewski
 */

class GN_Model_MailTemplate extends Zend_Db_Table
{
	protected $_name = 'mail_templates';
	protected $_tableName = 'mail_templates';

	public function init(array $additionalColumns = array())
	{
		try
		{
			$this->info();
		}
		catch (Zend_Db_Table_Exception $e)
		{
			$columns = array();
			$columns['id'] = 'SERIAL PRIMARY KEY';
			$columns['subject'] = 'VARCHAR(255) NOT NULL';
			$columns['body'] = 'TEXT';
			$columns['from_mail'] = 'VARCHAR(255) NOT NULL';
			$columns['from_name'] = 'VARCHAR(255) NOT NULL';
			$columns['is_plain_text'] = 'BOOLEAN';
			foreach ($additionalColumns as $k => $v)
			{
				$columns[$k] = $v;
			}

			$sql = 'CREATE TABLE ' . $this->_tableName . '(';
			foreach ($columns as $k => $v)
			{
				$sql .= '"' . $k . '" ' . $v;
				$sql .= ', ';
			}
			$sql = substr($sql, 0, -2);
			$sql .= ');';
			if (method_exists($this, 'postInit'))
			{
				call_user_func(array($this, 'preCreate'));
			}
			$adapter = $this->getAdapter();
			$adapter->query($sql);
			if (method_exists($this, 'postInit'))
			{
				call_user_func(array($this, 'postCreate'));
			}
		}
	}

	public function preCreate()
	{
		//dodawanie sekwencji itp.
	}

	public function postCreate()
	{
		//dodawanie kluczy obcych np.
	}

	private static function replaceKeywords($subject, $keywords)
	{
		foreach ($keywords as $from => $to)
		{
			$subject = str_replace('{' . $from . '}', $to, $subject);
		}
		return $subject;
	}


	public static function send($id, array $recipients, array $attachments = array(), array $keywords = array())
	{
		$config = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getOption('smtp');
		$server = $config['host'];
		if (empty($server))
		{
			throw new Exception('No SMTP server was found in configuration');
		}

		$transport = new Zend_Mail_Transport_Smtp($server);
		Zend_Mail::setDefaultTransport($transport);

		$className = get_class();
		$model = new $className();
		$template = $model->find($id)->current();
		if ($template === null)
		{
			throw new Exception(sprintf('Mail template with id=%d was not found in table "%s"', $id, $model->_tableName));
		}

		$body = $template->body;
		$subject = $template->subject;

		$body = GN_Model_MailTemplate::replaceKeywords($body, $keywords);
		$subject = GN_Model_MailTemplate::replaceKeywords($subject, $keywords);

		//incijacja maila
		$mail = new Zend_Mail();
		$mail->setSubject($subject);
		if ($template->is_plain_text)
		{
			$mail->setBodyText($body);
		}
		else
		{
			$mail->setBodyHtml($body);
		}

		//nadawca
		$mail->setFrom($template->from_mail, $template->from_name);

		//odbiorca
		foreach ($recipients as $recipient)
		{
			if (is_array($recipient))
			{
				list($address, $name) = $recipient;
				$mail->addTo($address, $name);
			}
			else
			{
				$address = $recipient;
				$mail->addTo($address, $address);
			}
		}

		//załączniki
		if (!empty($attachments))
		{
			foreach ($attachments as $attachment)
			{
				if ((!empty($attachment['path'])) and (!empty($attachment['contents'])))
				{
					throw new Exception('Attachment cannot have both contents and path specified');
				}
				//utwórz poprzez załadowanie z pliku
				if (!empty($attachment['path']))
				{
					$path = $attachment['path'];
					if (!file_exists($path))
					{
						throw new Exception(sprintf('Attachment "%s" does not exist', $path));
					}
					if (!is_readable($path))
					{
						throw new Exception(sprintf('Attachment "%s" cannot be read', $path));
					}
					$contents = file_get_contents($path);
					$at = $mail->createAttachment($contents);
					$at->type = mime_content_type($path);
					$at->filename = basename($path);
				}
				//utwórz poprzez wpisanie zawartości
				elseif (!empty($attachment['contents']))
				{
					$contents = file_get_contents($path);
					$at = $mail->createAttachment($contents);
				}
				else
				{
					throw new Exception('Attachment has no contents nor path specified');
				}

				//załaduj dodatkowe dane
				if (!empty($attachment['mime']))
				{
					$at->type = $attachment['mime'];
				}
				if (!empty($attachment['filename']))
				{
					$at->filename = $attachment['filename'];
				}
				if (!empty($attachment['encoding']))
				{
					$at->encoding = $attachment['encoding'];
				}
				if (!empty($attachment['disposition']))
				{
					$at->disposition = $attachment['disposition'];
				}
			}
		}

		$mail->send();
	}
}
