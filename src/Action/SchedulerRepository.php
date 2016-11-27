<?php
namespace App\Action;

use Illuminate\Database\Capsule\Manager;

/**
 * Class SchedulerRepository
 * @package App\Action
 */
class SchedulerRepository
{
    /* @var Manager */
    private $db;

    /**
     * SchedulerRepository constructor.
     * @param Manager $db
     */
    public function __construct(Manager $db)
    {
        $this->db = $db;

    }

    /**
     * @param $elem
     * @return null|object
     */
    private function getZero($elem)
    {
        return isset($elem[0]) ? (object)$elem[0] : null;
    }

    //User table functions
    /**
     * @return \Illuminate\Support\Collection
     */
    public function getAllUsers()
    {
        return $this->db->table('users')
            ->get();
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function getUsers()
    {
        return $this->db->table('users')
            ->where('enabled', true)
            ->get();
    }

    /**
     * @param $hash
     * @return null|object
     */
    public function getUserByPW($hash)
    {
        if (empty($hash)) {
            return null;
        }

        $user = $this->db->table('users')
            ->where('hash', 'like', $hash)
            ->get();

        return $this->getZero($user);

    }

    /**
     * @param $name
     * @return null|object
     */
    public function getUserByName($name)
    {
        if (empty($name)) {
            return null;
        }

        $user = $this->db->table('users')
            ->where('name', 'like', $name)
            ->get();

        return $this->getZero($user);

    }

    /**
     * @param $user
     * @return null
     */
    public function setUser($user)
    {
        if (empty($user)) {
            return null;
        }

        $u = $this->getUserByName($user['name']);
        if (empty($u)) {
            $newUser = array(
                'name' => $user['name'],
                'enabled' => $user['enabled'],
                'hash' => $user['hash'],
            );

            $this->db->table('users')
                ->insert([$newUser]);

        } else {
            $hash = $user['hash'];

            $this->db->table('users')
                ->where('id', $u->id)
                ->update([
                    'hash' => $hash,
                ]);
        }

        return null;
    }

    //Events table functions
    /**
     * @return \Illuminate\Support\Collection
     */
    public function getCurrentEvents()
    {
        return $this->db->table('events')
            ->where('view', true)
            ->orderBy('start_date', 'asc')
            ->get();
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function getEnabledEvents()
    {
        return $this->db->table('events')
            ->where('enabled', true)
            ->orderBy('start_date', 'asc')
            ->get();
    }

    /**
     * @param $projectKey
     * @return null|object
     */
    public function getEvent($projectKey)
    {
        if (empty($projectKey)) {
            return null;
        }

        $event = $this->db->table('events')
            ->where('projectKey', '=', $projectKey)
            ->get();

        return $this->getZero($event);
    }

    /**
     * @param $label
     * @return null|object
     */
    public function getEventByLabel($label)
    {
        $event = $this->db->table('events')
            ->where('label', '=', $label)
            ->get();

        return $this->getZero($event);
    }

    /**
     * @param $projectKey
     * @return mixed
     */
    public function getEventLabel($projectKey)
    {
        $event = $this->getEvent($projectKey);

        return $event->label;
    }

    /**
     * @param $projectKey
     * @return null
     */
    public function getLocked($projectKey)
    {
        $status = $this->db->table('events')
            ->where('projectKey', '=', $projectKey)
            ->get();

        $status = $this->getZero($status);
        if (!is_null($status)) {
            return $status->locked;
        } else {
            return null;
        }
    }

    /**
     * @param $projectKey
     * @return null
     */
    public function numberOfReferees($projectKey)
    {
        $numRefs = $this->db->table('events')
            ->select('num_refs')
            ->where('projectKey', '=', $projectKey)
            ->get();
        $numRefs = $this->getZero($numRefs);

        if (!is_null($numRefs)) {
            return $numRefs->num_refs;
        } else {
            return null;
        }
    }

    /**
     * @param $key
     */
    public function lockProject($key)
    {
        $this->db->table('events')
            ->where('projectKey', $key)
            ->update(['locked' => true]);
    }

    /**
     * @param $key
     */
    public function unlockProject($key)
    {
        $this->db->table('events')
            ->where('projectKey', $key)
            ->update(['locked' => false]);
    }

    //Games table functions
    /**
     * @param string $projectKey
     * @param string $group
     * @param bool $medalRound
     * @return \Illuminate\Support\Collection
     */
    public function getGames($projectKey = '%', $group = '%', $medalRound = false)
    {
        $group .= '%';
        $medalRound = $medalRound ? '%' : false;

        $games = $this->db->table('games')
            ->where([
                ['projectKey', '=', $projectKey],
                ['division', 'like', $group],
                ['medalRound', 'like', $medalRound],
            ])
            ->orWhere([
                ['projectKey', 'like', $projectKey],
                ['division', 'like', $group],
                ['date', '<=', date('Y-m-d')]

            ])
            ->get();

        return $games;
    }

    /**
     * @param string $projectKey
     * @param string $group
     * @param bool $medalRound
     * @return \Illuminate\Support\Collection
     */
    public function getUnassignedGames($projectKey = '%', $group = '%', $medalRound = false)
    {
        $group .= '%';
        $medalRound = $medalRound ? '%' : false;

        return $this->db->table('games')
            ->where([
                ['projectKey', 'like', $projectKey],
                ['division', 'like', $group],
                ['medalRound', 'like', $medalRound],
                ['assignor', 'like', '']
            ])
            ->get();
    }

    /**
     * @param $projectKey
     * @param $rep
     * @param bool $medalRound
     * @return \Illuminate\Support\Collection
     */
    public function getGamesByRep($projectKey, $rep, $medalRound = false)
    {
        return $this->db->table('games')
            ->where([
                ['projectKey', '=', $projectKey],
                ['medalRound', 'like', $medalRound],
                ['assignor', '=', $rep],
            ])
            ->get();

    }

    /**
     * @param $projectKey
     * @return array
     */
    public function getGroups($projectKey)
    {
        $groups = $this->db->table('games')
            ->where('projectKey', $projectKey)
            ->select('division')
            ->distinct()
            ->get();

        $result = [];
        foreach ($groups as $group) {
            $group = substr($group->division, 0, 3);
            if (!in_array($group, $result)) {
                $result[] = $group;
            }
        }
        asort($result);

        return $result;
    }

    /**
     * @param $projectKey
     * @param $rep
     */
    public function clearAssignor($projectKey, $rep)
    {
        $this->db->table('games')
            ->where([
                ['assignor', $rep],
                ['projectKey', '=', $projectKey]
            ])
            ->update(['assignor' => '']);
    }

    /**
     * @param $data
     * @return null
     */
    public function updateAssignor($data)
    {
        if (empty($data)) {
            return null;
        }

        foreach ($data as $id => $rep) {
            if ($id != 'Submit') {
                $rep = $rep == 'None' ? null : $rep;
                $this->db->table('games')
                    ->where('id', $id)
                    ->update(['assignor' => $rep]);
            }
        }

        return null;
    }

    /**
     * @param $data
     * @return null
     */
    public function updateAssignments($data)
    {
        if (empty($data)) {
            return null;
        }

        $data['r4th'] = isset($data['r4th']) ? $data['r4th'] : null;
        foreach ($data as $id => $value) {
            if ($value == 'Update Assignments') {
                $this->db->table('games')
                    ->where('id', $id)
                    ->update([
                        'cr' => trim($data['cr']),
                        'ar1' => trim($data['ar1']),
                        'ar2' => trim($data['ar2']),
                        'r4th' => trim($data['r4th'])
                    ]);
            }
        }

        return null;
    }

    /**
     * @param $id
     * @return null
     */
    public function gameIdToGameNumber($id)
    {
        $gameNo = $this->db->table('games')
            ->select('game_number')
            ->where('id', '=', $id)
            ->get();
        $gameNo = $this->getZero($gameNo);

        if (!is_null($gameNo)) {
            return $gameNo->game_number;
        } else {
            return null;
        }
    }

    /**
     * @return array|null
     */
    public function getGamesHeader()
    {
        $games = $this->getGames();
        $gameLabels = (array)$this->getZero($games);

        if (!empty($gameLabels)) {

            return array_keys($gameLabels);
        }

        return null;
    }

    /**
     * @return int
     */
    public function getNextGameId()
    {
        $id = 0;

        $games = $this->db->table('games')
            ->get();

        foreach ($games as $game) {
            $id = $game->id;
        };

        $id++;

        return $id;
    }

    /**
     * @param $data
     * @return array|null
     */
    public function modifyGames($data)
    {
        if (is_null($data)) {
            return null;
        }

        $hdr = array_values($data['hdr']);
        $games = $data['data'];

        $changes = array('adds' => 0, 'updates' => 0, 'errors' => []);

        //convert float to int
        $newGames = [];
        foreach ($games as $game) {
            foreach ($game as $key => &$value) {
                if (is_float($value)) {
                    $value = (int)$value;
                }
            }
            $newGames[] = $game;
        }
        $games = $newGames;

        if (!empty($games)) {
            foreach ($games as $game) {
                $nextData = [];
                $game = array_values($game);
                foreach ($game as $key => $field) {
                    $nextData[$hdr[$key]] = $game[$key];
                }

                //ensure empty fields default to correct type
                foreach ($nextData as $key => $value) {
                    if (is_null($value)) {
                        switch ($key) {
                            case 'date':
                                $value = date('Y-m-d');
                                break;
                            case 'time':
                                $value = "00:00";
                                break;
                            case 'medalRound':
                                $value = 0;
                                break;
                            default:
                                $value = '';
                        }
                    }
                    $typedData[$key] = $value;
                }

                if (!empty($typedData['projectKey'])) {

                    $isGame = $this->getGameByKeyAndNumber($typedData['projectKey'], $typedData['game_number']);

                    if (empty($isGame)) {
                        $result = $this->insertGame($typedData);
                    } else {
                        $result = $this->updateGame($typedData);
                    }

                    $changes['adds'] += $result['adds'];
                    $changes['updates'] += $result['updates'];
                } else {
                    $changes['errors'][] = 'Missing projectKey, unable to add game';
                }
            }
        }

        return $changes;

    }

    /**
     * @param $projectKey
     * @param $game_number
     * @return null|object
     */
    public function getGameByKeyAndNumber($projectKey, $game_number)
    {
        $game = $this->db->table('games')
            ->where([
                ['projectKey', '=', $projectKey],
                ['game_number', '=', $game_number]
            ])
            ->get();

        return $this->getZero($game);
    }

    /**
     * @param $id
     * @return null|object
     */
    private function getGame($id)
    {
        $game = $this->db->table('games')
            ->where('id', '=', $id)
            ->get();

        return $this->getZero($game);
    }

    /**
     * @param $data
     * @return array|null
     */
    private function updateGame($data)
    {
        if (empty($data)) {
            return null;
        }

        $changes = array('adds' => 0, 'updates' => 0);

        $key = $data['projectKey'];
        $num = $data['game_number'];

        $game = $this->getGameByKeyAndNumber($key, $num);

        //doing update by projectKey & game number; including id caused integrity error
        unset($game->id);
        unset($data['id']);

        $data['time'] = date('H:i:s', strtotime($data['time']));

        $dif = array_diff((array)$game, $data);

        if (!empty($dif)) {
            $this->db->table('games')
                ->where([
                    ['projectKey', $key],
                    ['game_number', $num]
                ])
                ->update($data);

            $changes['updates']++;
        }

        return $changes;
    }

    /**
     * @param $data
     * @return array|null
     */
    private function insertGame($data)
    {
        if (empty($data)) {
            return null;
        }

        $changes = array('adds' => 0, 'updates' => 0);

        $id = isset($data['id']) ? $data['id'] : null;

        $game = $this->getGame($id);

        if (is_null($game)) {
            $this->db->table('games')
                ->insert($data);
            $changes['adds']++;
        } else {
            $changes = $this->updateGame($data);
        }

        return $changes;
    }

    /**
     * @param $projectKey
     * @return \Illuminate\Support\Collection
     */
    public function getGameCounts($projectKey)
    {
        return $this->db->table('games')
            ->selectRaw('assignor, date, division, COUNT(division) as game_count')
            ->where('projectKey', 'like', $projectKey)
            ->groupBy(['assignor', 'division'])
            ->get();
    }

    /**
     * @param $projectKey
     * @return \Illuminate\Support\Collection
     */
    public function getDatesDivisions($projectKey)
    {
        return $this->db->table('games')
            ->selectRaw('DISTINCT assignor, date, division')
            ->where('projectKey', 'like', $projectKey)
            ->orderBy('assignor', 'asc')
            ->orderBy('date', 'asc')
            ->orderBy('division', 'asc')
            ->get();
    }

    static function firstLastSort($a, $b)
    {
        if ($a == $b) {
            return 0;
        }

        $A = explode(' ', $a);
        $B = explode(' ', $b);

        $lastA = isset($A[1]) ? $A[1] : '';
        $lastB = isset($B[1]) ? $B[1] : '';

        return ($lastA < $lastB) ? -1 : 1;
    }

    public function assignmentsByReferee($projectKey)
    {
        $cr = $this->db->table('games')
            ->selectRaw('DISTINCT cr AS Referee')
            ->where('projectKey', 'like', $projectKey)
            ->where('cr', '<>', '');

        $ar1 = $this->db->table('games')
            ->selectRaw('DISTINCT ar1 AS Referee')
            ->where('projectKey', 'like', $projectKey)
            ->where('ar1', '<>', '');

        $ar2 = $this->db->table('games')
            ->selectRaw('DISTINCT ar2 AS Referee')
            ->where('projectKey', 'like', $projectKey)
            ->where('ar2', '<>', '');

        $r4th = $this->db->table('games')
            ->selectRaw('DISTINCT r4th AS Referee')
            ->where('projectKey', 'like', $projectKey)
            ->where('r4th', '<>', '');

        $refs = $cr
            ->union($ar1)
            ->union($ar2)
            ->union($r4th);

        $result = $refs
            ->get();

        foreach ($result as $ref){
            $refList[] = $ref->Referee;
        }

        usort($refList, array($this, "firstLastSort"));

        return $refList;
    }

    //Limits table functions
    /**
     * @param $projectKey
     * @return \Illuminate\Support\Collection
     */
    public function getLimits($projectKey)
    {
        return $this->db->table('limits')
            ->where('projectKey', '=', $projectKey)
            ->get();
    }

    //Log writer
    /**
     * @param $projectKey
     * @param $msg
     * @return null
     */
    public function logInfo($projectKey, $msg)
    {
        $data = [
            'timestamp' => date('Y-m-d H:i:s'),
            'projectKey' => $projectKey,
            'note' => $msg
        ];

        $this->db->table('log')
            ->insert($data);

        return null;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function getAccessLog()
    {
        return $this->db->table('log')
            ->get();
    }

    public function showVariables()
    {
        var_dump($this->db->getConnection());die();
    }
}
