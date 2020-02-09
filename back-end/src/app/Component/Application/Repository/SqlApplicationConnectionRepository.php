<?php


namespace Fittie\Component\Application\Repository;


use Fittie\Component\Application\Entity\ApplicationConnection;
use Fittie\Component\Application\Entity\ApplicationConnectionID;
use Fittie\Component\Application\Exception\ApplicationNotFound;
use Fittie\Component\Application\Mapper\ApplicationConnectionDBToEntityMapper;
use Fittie\Component\User\Entity\UserID;
use Illuminate\Support\Facades\DB;

class SqlApplicationConnectionRepository implements ApplicationConnectionRepositoryInterface
{
    private ApplicationConnectionDBToEntityMapper $mapper;

    public function __construct(ApplicationConnectionDBToEntityMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    public function save(ApplicationConnection $application)
    {
        DB::table('application_connections')->updateOrInsert([
            'id' => $application->getID(),
            'user_id' => $application->getUserID(),
        ], [
            'id' => $application->getID(),
            'user_id' => $application->getUserID(),
            'application' => $application->getApplication()->getSlug(),
            'application_account_id' => $application->getAccountID(),
            'application_account_name' => $application->getAccountName(),
            'credentials' => json_encode($application->getCredentials()->toArray()),
        ]);
    }

    public function all(UserID $userID)
    {
        $applicationConnections = DB::table('application_connections')
            ->where('user_id', $userID)
            ->get();

        return array_map([$this->mapper, 'map'], $applicationConnections->toArray());
    }

    public function get(UserID $userID, ApplicationConnectionID $applicationConnectionID)
    {
        $applicationConnection = DB::table('application_connections')
            ->where('user_id', $userID)
            ->where('id', $applicationConnectionID)
            ->first();

        if ($applicationConnection === null) {
            throw new ApplicationNotFound();
        }

        return $this->mapper->map($applicationConnection);
    }

    public function delete(UserID $userID, ApplicationConnectionID $applicationConnectionID)
    {
        return DB::table('application_connections')
            ->where('id', (string)$applicationConnectionID)
            ->where('user_id', (string)$userID)
            ->delete();
    }
}
