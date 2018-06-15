<?php
/**
 * @author Johannes Braun <j.braun@agentur-halma.de>
 * @package halma
 * @version 2016-12-05
 */
namespace Contentomat\Paperboy;

use \Exception;
use \Contentomat\Debug;
use \Contentomat\Paperboy\SubscriptionHandler;
use \Contentomat\Logger;

class Import {
	

	protected $newsletterIDs = array(88);


	protected $SubscriptionHandler;


	public function __construct() {
		$this->SubscriptionHandler = new SubscriptionHandler();
	}


	/**
	 * Import CSV data from file
	 *
	 * @param array $data		Subscriber data as passed to SubscriptionHandler
	 *							Must at least contain an index 'email' and may
	 *							contain any further db fields
	 * @return void			
	 * @throws Exception
	 */
	public function import_subscriber($params) {

		$params['activateSubscription'] = true;

		$r = $this->SubscriptionHandler->subscribeNewsletter($params);
		if (!$r) {
			Logger::warn(sprintf('Failed to import <%s>: %s', $params['email'], $this->SubscriptionHandler->getErrorNr()));
			throw new Exception('Subscription failed: subscribeNewsletter');
		}

		$subscriber = $this->SubscriptionHandler->getSubscriberDataFromEmail($params['email']);
		if (empty($subscriber)) {
			Logger::warn(sprintf('Failed to import <%s>: Internal Error', $params['email']));
			throw new Exception('Subscription failed:getSubscriberDataFromEmail ');
		}

		$r = $this->SubscriptionHandler->activateSubscriber(array(
			'hash' => $subscriber['action_hash'],
			'subscriberID' => $subscriber['id']
		));
		if (!$r) {
			Logger::warn(sprintf('Failed to import <%s>: Activation failed', $params['email']));
			throw new Exception('Subscription failed: activateSubscriber');
		}
		Logger::notice(sprintf('Imported <%s>', $params['email']));
	}
}
