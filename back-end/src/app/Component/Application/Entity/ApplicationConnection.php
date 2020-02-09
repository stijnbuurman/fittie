<?php

namespace Fittie\Component\Application\Entity;

use Fittie\Component\ApplicationAuth\Credentials;
use Fittie\Component\User\Entity\UserID;

class ApplicationConnection
{
    private ApplicationConnectionID $id;
    private UserID $userID;
    private ApplicationInterface $application;
    private string $accountID;
    private string $accountName;
    private Credentials $credentials;

    public function __construct(ApplicationConnectionID $id, UserID $userID, ApplicationInterface $application, string $accountID, string $accountName, Credentials $credentials)
    {
        $this->id = $id;
        $this->userID = $userID;
        $this->application = $application;
        $this->accountID = $accountID;
        $this->accountName = $accountName;
        $this->credentials = $credentials;
    }

    public function getID(): ApplicationConnectionID
    {
        return $this->id;
    }

    public function getUserID(): UserID
    {
        return $this->userID;
    }

    public function getApplication(): ApplicationInterface
    {
        return $this->application;
    }

    public function getAccountID(): string
    {
        return $this->accountID;
    }

    public function getAccountName(): string
    {
        return $this->accountName;
    }

    public function getCredentials(): Credentials
    {
        return $this->credentials;
    }

    public static function createFrom(ApplicationConnection $applicationConnection, Credentials $newCredentials): ApplicationConnection {
        return new ApplicationConnection(
            $applicationConnection->getID(),
            $applicationConnection->getUserID(),
            $applicationConnection->getApplication(),
            $applicationConnection->getAccountID(),
            $applicationConnection->getAccountName(),
            $newCredentials,
        );
    }

    public function toArray()
    {
        return [
            'id' => $this->getID(),
            'user_id' => $this->getUserID(),
            'application' => $this->getApplication()->toArray(),
            'account_id' => $this->getAccountID(),
            'account_name' => $this->getAccountName(),
        ];
    }
}
