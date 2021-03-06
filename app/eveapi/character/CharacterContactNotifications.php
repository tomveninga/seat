<?php

namespace Seat\EveApi\Character;

use Seat\EveApi\BaseApi;
use Pheal\Pheal;

class ContactNotifications extends BaseApi {

	public static function Update($keyID, $vCode)
	{

		// Start and validate they key pair
		BaseApi::bootstrap();
		BaseApi::validateKeyPair($keyID, $vCode);

		// Set key scopes and check if the call is banned
		$scope = 'Char';
		$api = 'ContactNotifications';

		if (BaseApi::isBannedCall($api, $scope, $keyID))
			return;

		// Get the characters for this key
		$characters = BaseApi::findKeyCharacters($keyID);

		// Check if this key has any characters associated with it
		if (!$characters)
			return;

		// Lock the call so that we are the only instance of this running now()
		// If it is already locked, just return without doing anything
		if (!BaseApi::isLockedCall($api, $scope, $keyID))
			$lockhash = BaseApi::lockCall($api, $scope, $keyID);
		else
			return;

		// Next, start our loop over the characters and upate the database
		foreach ($characters as $characterID) {

			// Prepare the Pheal instance
			$pheal = new Pheal($keyID, $vCode);

			// Do the actual API call. pheal-ng actually handles some internal
			// caching too.
			try {
				
				$contact_notifications = $pheal
					->charScope
					->ContactNotifications(array('characterID' => $characterID));

			} catch (\Pheal\Exceptions\APIException $e) {

				// If we cant get account status information, prevent us from calling
				// this API again
				BaseApi::banCall($api, $scope, $keyID, 0, $e->getCode() . ': ' . $e->getMessage());
			    return;

			} catch (\Pheal\Exceptions\PhealException $e) {

				throw $e;
			}

			// Check if the data in the database is still considered up to date.
			// checkDbCache will return true if this is the case
			if (!BaseApi::checkDbCache($scope, $api, $contact_notifications->cached_until, $characterID)) {

				// Process all of the received notifications
				foreach ($contact_notifications->contactNotifications as $notification) {

					$notification_data = \EveCharacterContactNotifications::where('characterID', '=', $characterID)
						->where('notificationID', '=', $notification->notificationID)
						->first();

					if (!$notification_data)
						$new_data = new \EveCharacterContactNotifications;
					else
						$new_data = $notification_data;

					$new_data->characterID = $characterID;
					$new_data->notificationID = $notification->notificationID;
					$new_data->senderID = $notification->senderID;
					$new_data->senderName = $notification->senderName;
					$new_data->sentDate = $notification->sentDate;
					$new_data->messageData = $notification->messageData;
					$new_data->save();
				}

				// Update the cached_until time in the database for this api call
				BaseApi::setDbCache($scope, $api, $contact_notifications->cached_until, $characterID);
			}
		}

		// Unlock the call
		BaseApi::unlockCall($lockhash);

		return $contact_notifications;
	}
}
