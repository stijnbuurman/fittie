<?php


namespace FittieApplications\Garmin\Auth;


use DateTimeZone;
use Fittie\Component\Analytics\DataSet;
use Fittie\Component\Analytics\Exception\InvalidMetricType;
use Fittie\Component\Analytics\MeasurementOverTime;
use Fittie\Component\Analytics\MeasurementsRequest;
use Fittie\Component\Analytics\MetricOverTime;
use Fittie\Component\ApplicationAuth\UserPassword\Client\UserPasswordClientInterface;
use Fittie\Component\ApplicationAuth\UserPassword\Credentials\UserPasswordCredentials;
use Carbon\Carbon;
use dawguk\GarminConnect;
use dawguk\GarminConnect\exceptions\AuthenticationException;
use Illuminate\Support\Arr;

const WELLNESS_TOTAL_STEPS = 29;
const WELLNESS_RESTING_HEART_RATE = 60;

class GarminUserPasswordClient implements UserPasswordClientInterface
{
    private GarminConnect $client;
    private UserPasswordCredentials $credentials;

    public function __construct(GarminConnect $client, UserPasswordCredentials $credentials)
    {
        $this->client = $client;
        $this->credentials = $credentials;
    }

    static public function makeClient(UserPasswordCredentials $credentials): UserPasswordClientInterface
    {
        $garminConnect = new GarminConnect([
            'username' => $credentials->getUsername(),
            'password' => $credentials->getPassword(),
        ]);

        return new GarminUserPasswordClient($garminConnect, $credentials);
    }

    public function connect(UserPasswordCredentials $credentials)
    {
        try {
            $garminConnect = new GarminConnect([
                'username' => $credentials->getUsername(),
                'password' => $credentials->getPassword(),
            ]);
        } catch(AuthenticationException $authenticationException) {
            return false;
        }

        return true;
    }

    public function getMeasurements(MeasurementsRequest $request): DataSet
    {
        switch ($request->getMetricType()->getName()) {
            case 'steps':
                return $this->getSteps($request);
                break;
            case 'restingHeartRate':
                return $this->getRestingHeartRate($request);
                break;
            default:
                throw new InvalidMetricType();
        }
    }

    public function getSteps(MeasurementsRequest $request): DataSet
    {
        if ($request->getStart()->diff($request->getEnd())->d < 1) {
            $dataset = new DataSet(new MetricOverTime('steps', 'steps', 15 * 60));
            $data = $this->client->getDailySummaryChart(Carbon::instance($request->getStart())->timezone(config('fittie.timezone'))->format('Y-m-d'));

            foreach((array)$data as $movementValue) {
                $dateStart = Carbon::createFromTimeString($movementValue['startGMT'], 'UTC');
                $dateEnd = Carbon::createFromTimeString($movementValue['endGMT'], 'UTC');
                $steps = $movementValue['steps'];

                $dataset->addMeasurement(new MeasurementOverTime($dateStart, $dateEnd, $steps));
            }

            return $dataset;
        }

        $dataset = new DataSet(new MetricOverTime('steps', 'steps', 24 * 60 * 60));
        $start = Carbon::instance($request->getStart())->timezone(config('fittie.timezone'))->format('Y-m-d');
        $end = Carbon::instance($request->getEnd())->timezone(config('fittie.timezone'))->format('Y-m-d');
        $data = $this->client->getWellnessData($start, $end, [WELLNESS_TOTAL_STEPS]);

        $rows = Arr::get($data, 'allMetrics.metricsMap.WELLNESS_TOTAL_STEPS', []);
        foreach($rows as $row) {
            $date = Carbon::createFromFormat('Y-m-d', $row['calendarDate'], new DateTimeZone(config('fittie.timezone')))->startOfDay();
            $dateEnd = clone $date;
            $dateEnd = $dateEnd->endOfDay();
            $dataset->addMeasurement(new MeasurementOverTime($date->toDate(), $dateEnd->toDate(), $row['value']));
        }

        return $dataset;
    }

    public function getRestingHeartRate(MeasurementsRequest $request): DataSet
    {

        if ($request->getStart()->diff($request->getEnd())->d < 1) {
            $dataset = new DataSet(new MetricOverTime('Resting Heart Rate', 'bpm', 60 * 2));

            $data = $this->client->getDailyHeartRate(Carbon::instance($request->getStart())->timezone(config('fittie.timezone'))->format('Y-m-d'));

            $rows = (array) Arr::get($data, 'heartRateValues', []);

            foreach($rows as $heartRateValue) {
                $dateStart = Carbon::createFromTimestamp($heartRateValue[0] / 1000);
                $dateEnd = $dateStart->clone()->addMinutes(2)->addSeconds(-1);

                $dataset->addMeasurement(new MeasurementOverTime($dateStart, $dateEnd, $heartRateValue[1]));
            }

            return $dataset;
        }

        $dataset = new DataSet(new MetricOverTime('Resting Heart Rate', 'bpm', 24 * 60 * 60));

        $start = Carbon::instance($request->getStart())->timezone(config('fittie.timezone'))->format('Y-m-d');
        $end = Carbon::instance($request->getEnd())->timezone(config('fittie.timezone'))->format('Y-m-d');
        $data = $this->client->getWellnessData($start, $end, [WELLNESS_RESTING_HEART_RATE]);


        $rows = Arr::get($data, 'allMetrics.metricsMap.WELLNESS_RESTING_HEART_RATE', []);
        foreach($rows as $row) {
            $date = Carbon::createFromFormat('Y-m-d', $row['calendarDate'], new DateTimeZone(config('fittie.timezone')))->startOfDay();
            $dateEnd = clone $date;
            $dateEnd = $dateEnd->endOfDay();
            $dataset->addMeasurement(new MeasurementOverTime($date->toDate(), $dateEnd->toDate(), $row['value']));
        }

        return $dataset;
    }
}
