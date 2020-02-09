<?php


namespace Fittie\Component\Application\Repository;


use Fittie\Component\Application\Entity\ApplicationConnection;
use Fittie\Component\Application\Entity\ApplicationConnectionID;
use Fittie\Component\User\Entity\UserID;

interface ApplicationConnectionRepositoryInterface
{
    public function save(ApplicationConnection $application);

    public function all(UserID $userID);

    public function get(UserID $userID, ApplicationConnectionID $applicationConnectionID);

    public function delete(UserID $userID, ApplicationConnectionID $applicationConnectionID);

}
