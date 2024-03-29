<?php
namespace PlaygroundStats\Controller\Admin;

use PlaygroundStats\Entity\Card;
use Laminas\Form\Element;
use Laminas\Form\Form;
use Laminas\InputFilter\Factory as InputFactory;
use Laminas\Session\Container;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\JsonModel;
use Laminas\View\Model\ViewModel;
use Laminas\ServiceManager\ServiceLocatorInterface;

class StatisticsController extends AbstractActionController
{
    /**
     * @var gameService
     */
    protected $gameService;

    /**
     * @var pageService
     */
    protected $pageService;

    /**
     * @var gameService
     */
    protected $rewardService;

    /**
     * @var achievementService
     */
    protected $achievementService;

    /**
     * @var userService
     */
    protected $userService;

    /**
     * @var achievementListenerService
     */
    protected $achievementListenerService;

    /**
     * @var applicationService
     */
    protected $applicationService;

    protected $myExportedResult;

    protected $prizeCategoriesExport;

    /**
     *
     * @var ServiceManager
     */
    protected $serviceLocator;

    public function __construct(ServiceLocatorInterface $locator)
    {
        $this->serviceLocator = $locator;
    }

    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    public function gamesAction()
    {
        $stats = [];
        $ap = $this->getApplicationService();
        $sg = $this->getGameService();
        $su = $this->getUserService();

        $games = $sg->getQueryGamesOrderBy()->getResult();

        foreach($games as $game) {
            $gameId         = $game->getId();
            $participants   = $ap->getNumberOfPlayers($gameId);
            $entries        = $ap->getNumberOfEntries($gameId);
            $optinUser      = $ap->findOptin('optin', $gameId);
            $optinPartner   = $ap->findOptin('optinPartner', $gameId);

            $query = $sg->getEntriesQuery($game)->getQuery();
            $allEntries = $sg->getGameEntries(null, $query->getResult(), $game);

            $locations = [];
            foreach($allEntries as $entry) {
                $geoloc = explode(",", $entry['geoloc']);
                if(count($geoloc) === 2){
                    $locations[] = [
                        "lat" => floatval($geoloc[0]),
                        "lng" => floatval($geoloc[1]),
                        "label" => 'S',
                        "draggable" => false,
                        "title" => (isset($entry['email'])) ? $entry['email'] : $entry['id'],
                    ];
                }
            }

            $partArray = $ap->getParticipationsByDayByGame($game);

            $labels = array();
            $series = array();
            foreach ($partArray as $v) {
                $labels[] = substr($v['date'], 8, 2) . '/' .substr($v['date'], 5, 2);
                $series[] = intval($v['qty']);
            }

            $participationsSerie = [
                "labels" => $labels,
                "data" => $series,
                "min" => (count($series)  > 0) ? min($series) : 0,
                "max" => (count($series)  > 0) ? max($series) + 1 : 0,
            ];

            $stats[] = [
                'game'                  => $game,
                'players'               => $participants,
                'entries'               => $entries,
                'optinUser'             => $optinUser,
                'optinPartner'          => $optinPartner,
                'participationsSerie'   => $participationsSerie,
                'locations'             => $locations,
            ];
        }

        return new ViewModel(
            [
                'stats' => $stats,
            ]
        );
    }

    public function indexOLDAction()
    {
        $ap = $this->getApplicationService();
        $disposition = '';
        $user = $this->lmcUserAuthentication()->getIdentity();
        $dashboard = $ap->getDashboardMapper()->findOneBy(array('user' => $user));
        if($dashboard){
            $disposition = $dashboard->getDisposition();
        }

        list($startDate, $endDate, $form, $data) = $this->getStartEndDateValues();

        list(
                $members, $male, $female,
                $validatedMembers, $maleMembers, $femaleMembers,
                $active, $maleActive, $femaleActive,
                $optin, $maleOptin, $femaleOptin,
                $optinPartners, $maleOptinPartners, $femaleOptinPartners,
                $unregistered, $maleUnregistered, $femaleUnregistered,
                $suspended, $maleSuspended, $femaleSuspended
        ) = $ap->getListUserCountByRangeDate(array(
                'members', 'validatedMembers', 'activeMembers', 'optin', 'optinPartners', 'unregistered', 'suspended'
        ), $startDate, $endDate);

        $participations         = $ap->getParticipationsByRangeDate($startDate, $endDate);
        $now        = new \DateTime("now");
        $interval   = 'P1W';
        $beginning  = new \DateTime("now");
        $beginning->sub(new \DateInterval($interval));
        $sDate = $beginning->format('d/m/Y');
        $eDate   = $now->format('d/m/Y');

        $participationsLastWeek     = $ap->getParticipationsByRangeDate($sDate, $eDate);

        $partArray = $ap->getParticipationsByDayByRangeDate($startDate, $endDate);

        $labels = array();
        $series = array();
        foreach ($partArray as $v) {
            $labels[] = substr($v['date'], 8, 2);
            $series[] = intval($v['qty']);
        }
        $participationsSerie = array('labels' => $labels, 'series' => array($series));

        $subscribers = $ap->getSubscribersByRangeDate($startDate, $endDate);
        $subscribersLastWeek    = $ap->getSubscribersByRangeDate($sDate, $eDate);

        $totalGames             = $ap->getGamesByRangeDate($startDate, $endDate, false);
        $activeGames            = $this->getGameService()->getActiveGames(false);
        $activeArticles         = $ap->getArticlesByRangeDate($startDate, $endDate);

        return new ViewModel(
            array(
                'disposition'       => $disposition,

                'members'           => $members,
                'validatedMembers'  => $validatedMembers,
                'maleMembers'       => $maleMembers,
                'femaleMembers'     => $femaleMembers,
                'active'            => $active,
                'maleActive'        => $maleActive,
                'femaleActive'      => $femaleActive,
                'optin'             => $optin,
                'maleOptin'         => $maleOptin,
                'femaleOptin'       => $femaleOptin,
                'optinPartners'     => $optinPartners,
                'maleOptinPartners' => $maleOptinPartners,
                'femaleOptinPartners' => $femaleOptinPartners,

                'unregistered'      => $unregistered,
                'maleUnregistered'  => $maleUnregistered,
                'femaleUnregistered'=> $femaleUnregistered,
                'suspended'         => $suspended,

                'participationsJSON'=> json_encode($participationsSerie),
                'participations'    => $participations,
                'participationsLastWeek' => $participationsLastWeek,
                'totalGames'        => $totalGames,
                'activeGames'       => $activeGames,
                'activeArticles'    => $activeArticles,

                'subscribers'       => $subscribers,
                'subscribersLastWeek' => $subscribersLastWeek,

                'form'              => $form,
                'data'              => $data,
            )
        );
    }

    public function updateDashboardAction()
    {
        $ap = $this->getApplicationService();
        $user = $this->lmcUserAuthentication()->getIdentity();
        if ($this->getRequest()->isPost()) {
            $ap->createOrUpdateDashboard($user, $this->params()->fromPost('disposition'));
        }

        $model = new JsonModel(array('success' => true));

        return $model->setTerminal(true);
    }

    public function badgeAction()
    {
        $ap = $this->getApplicationService();
        list($startDate, $endDate, $form, $data) = $this->getStartEndDateValues();
        $badges = array(
                'total' => array(0, 1, 2, 3),
                'goldfather' => array(0, 1, 2, 3),
                'brain' => array(0, 1, 2, 3),
                'ambassador' => array(0, 1, 2, 3),
                'anniversary' => array(0, 1, 2, 3),
                'player' => array(0, 1, 2, 3),
        );

        $players = array();

        $badgeResults = $ap->getListBadgeCountByRangeDate($badges, $startDate, $endDate);
        $badgeResult = current($badgeResults);

        foreach ($badges as $badge => $levels) {
            foreach ($levels as $level) {
                $players[] = array(
                        'badge' => $badge,
                        'level' => $level,
                        'user' => $badgeResult
                );
                $badgeResult = next($badgeResults);
            }
        }

        $model = new ViewModel(array('players' => $players));
        $model->setTerminal(true);
        return $model;
    }

    protected function getStartEndDateValues()
    {
        $data               = '';
        $startDate          = '';
        $endDate            = '';

        $request            = $this->getRequest();
        $form               = new Form();
        $form->setAttribute('method', 'post');

        $today    = new \DateTime("now");
        $beginning      = new \DateTime("now");
        $interval = 'P365D';
        $beginning->sub(new \DateInterval($interval));

        $startDate = $beginning->format('d/m/Y');
        $endDate   = $today->format('d/m/Y');

        if ($request->isPost()) {
            $data = $request->getPost()->toArray();
            $form->setData($data);
            if ($form->isValid()) {
                if (isset($data['statsDateStart'])) {
                    $startDate =  $data['statsDateStart'];
                }
                if (isset($data['statsDateEnd'])) {
                    $endDate = $data['statsDateEnd'];
                }
            }
        }
        return array($startDate,$endDate,$form,$data);
    }


    public function shareAction()
    {
        $ap = $this->getApplicationService();
        list($startDate, $endDate, $form, $data) = $this->getStartEndDateValues();
        list(
                $sponsoredMembers,
                $shares,
                $profilShares,
                $fbInvit,
                $twInvit,
                $glInvit,
                $mailInvit
        ) = $ap->getListShareCountByRangeDate(array(
                'sponsoredMembers', 'shares', 'profilShares', 'fbInvit', 'twInvit', 'glInvit', 'mailInvit'
        ), $startDate, $endDate);
        $model = new ViewModel(
            array(
                        'sponsoredMembers'  => $sponsoredMembers,
                        'shares'            => $shares,
                        'profilShares'      => $profilShares,
                        'fbInvit'           => $fbInvit,
                        'twInvit'           => $twInvit,
                        'glInvit'           => $glInvit,
                        'mailInvit'         => $mailInvit
                )
        );
        $model->setTerminal(true);
        return $model;
    }

    /*public function indexAction()
     {
    $request 		= $this->getRequest();
    $su 			= $this->getUserService();
    $sg 			= $this->getGameService();
    $sr 			= $this->getRewardService();

    $allUser 		= count($su->findAll());
    $activeUser 	= count($su->findByState(1));
    $inactiveUser 	= $allUser - $activeUser;

    $maleUser 		= count($su->findByTitle('M'));
    $femaleUser 	= count($su->findByTitle('Me'));

    $optinUser 		= count($su->findByOptin(1));
    //$optinUserPartner 	= count($su->findByOptin(1, true));

    $activeGame 	= count($sg->getActiveGames());

    $allGames 		= count($sg->findAll());
    $allEntries 	= count($sg->findAllEntry());
    $userPerGames 	= $allEntries/$allGames;

    $activePage 	= count($this->getPageService()->getActivePages());

    $shareAction 	= count($sr->findBy(array('actionId' => array(13,14,15,16))));
    $sharePerGames 	= $allGames/$shareAction;

    $userPerBadges = array();
    $badges 	= $this->getRewardAchievementListenerService()->getBadges();

    foreach ($badges as $keyBadge => $badge) {
    foreach ($badge['levels'] as $keyLevel => $level) {
    $userPerBadges[] = array(
            'badge' => $keyBadge,
            'level' => $level['label'],
            'user'  => count($this->getAchievementService()->findBy(array('type' => 'badge', 'category'=> $keyBadge, 'level' => $keyLevel))),
    );

    }
    }

    $form = new Form();
    $form->setAttribute('method', 'post');

    if ($request->isPost()) {
    $data = $request->getPost()->toArray();
    }

    return new ViewModel(array(
        'activeUser' 	=> $activeUser,
        'inactiveUser' => $inactiveUser,
        'maleUser' 	=> $maleUser,
        'femaleUser' 	=> $femaleUser,
        'optinUser' 	=> $optinUser,
        //'optinUserPartner' => $optinUserPartner,

        'activeGame' 	=> $activeGame,
        'activePage' 	=> $activePage,

        'userPerGames'	=> $userPerGames,
        'sharePerGames'=> $sharePerGames,

        'userPerBadges'=> $userPerBadges,

        'form' 		=> $form,
    )
    );
    }*/

    /*
        var thisWeek = query({
        'ids': ids,
        'dimensions': 'ga:date,ga:nthDay',
        'metrics': 'ga:sessions',
        'start-date': moment(now).subtract(1, 'day').day(0).format('YYYY-MM-DD'),
        'end-date': moment(now).format('YYYY-MM-DD')
        });

        var lastWeek = query({
        'ids': ids,
        'dimensions': 'ga:date,ga:nthDay',
        'metrics': 'ga:sessions',
        'start-date': moment(now).subtract(1, 'day').day(0).subtract(1, 'week')
            .format('YYYY-MM-DD'),
        'end-date': moment(now).subtract(1, 'day').day(6).subtract(1, 'week')
            .format('YYYY-MM-DD')
        });
    */
    public function indexAction()
    {
        $ap = $this->getApplicationService();
        $sg = $this->getGameService();
        $su = $this->getUserService();
        $stats = [];
        $cards = $ap->getCardMapper()->findAll();

        $wholeSerie = $ap->getGaReport('date', 'sessions', null, null, '30daysAgo', 'today');
        $sessionSerie1 = $ap->getGaReport('date', 'sessions', null, null, '7daysAgo', 'today');
        $sessionSerie2 = $ap->getGaReport('date', 'sessions', null, null, '14daysAgo', '7daysAgo');
        $countriesSerie = $ap->getGaReport('country', 'pageviews', 5, 'desc');
        $browserSerie = $ap->getGaReport('browser', 'pageviews', null, 'desc');
        $sourceSerie = $ap->getGaReport('source', 'pageviews', null, 'desc');
        $topPagesSerie = $ap->getGaReport('pageTitle', 'pageviews', 10, 'desc');

        $sessionSerie = $sessionSerie1;
        $sessionSerie['data2'] = $sessionSerie2['data'];

        $games = $sg->getQueryGamesOrderBy()->getResult();
        $partArray = $ap->getParticipationsByDayByGame();

        $labels = [];
        $series = [];
        foreach ($partArray as $v) {
            $labels[] = substr($v['date'], 8, 2) . '/' .substr($v['date'], 5, 2);
            $series[] = intval($v['qty']);
        }

        $participationsSerie = [
            "labels" => $labels,
            "data" => $series,
            "min" => (count($series)  > 0) ? min($series) : 0,
            "max" => (count($series)  > 0) ? max($series) + 1 : 0,
        ];

        $numberOfEntries = $ap->getNumberOfEntries();
        $numberOfPlayers = $ap->getNumberOfPlayers();

        $labelsGames = [];
        $seriesGames = [];
        $seriesBgcolor = [];

        foreach ($games as $game) {
            $gameId         = $game->getId();
            $classType      = $game->getClassType();
            $bgColor        = 'rgb(54, 162, 235)';
            $participants   = $ap->getNumberOfPlayers($gameId);
            $entries        = $ap->getNumberOfEntries($gameId);
            $optinUser      = $ap->findOptin('optin', $gameId);
            $optinPartner   = $ap->findOptin('optinPartner', $gameId);
            $partArray      = $ap->getParticipationsByDayByGame($game);

            if ($classType == 'quiz') {
                $bgColor = 'rgb(75, 192, 192)';
            } elseif ($classType == 'postvote') {
                $bgColor = 'rgb(255, 159, 64)';
            } elseif ($classType == 'instantwin') {
                $bgColor = 'rgb(255, 99, 132)';
            } elseif ($classType == 'lottery') {
                $bgColor = 'rgb(255, 205, 86)';
            }

            $labelsGames[] = $game->getTitle();
            $seriesGames[] = $entries;
            $seriesBgcolor[] = $bgColor;

            $labels = array();
            $series = array();
            foreach ($partArray as $v) {
                $labels[] = substr($v['date'], 8, 2) . '/' .substr($v['date'], 5, 2);
                $series[] = intval($v['qty']);
            }

            $partsSerie = [
                "labels" => $labels,
                "data" => $series,
                "min" => (count($series)  > 0) ? min($series) : 0,
                "max" => (count($series)  > 0) ? max($series) + 1 : 0,
            ];

            $stats[] = [
                'game'                  => $game,
                'players'               => $participants,
                'entries'               => $entries,
                'optinUser'             => $optinUser,
                'optinPartner'          => $optinPartner,
                'participationsSerie'   => $partsSerie,
            ];
        }

        $participationsGames = [
            "labels" => $labelsGames,
            "data" => $seriesGames,
            "bgcolor" => $seriesBgcolor,
            "min" => (count($seriesGames)  > 0) ? min($seriesGames) : 0,
            "max" => (count($seriesGames)  > 0) ? max($seriesGames) + 1 : 0,
        ];

        // initialize default stats
        $gameId         = '';
        $participants   = '';
        $entries        = '';
        $optinUser      = '';
        $optinPartner   = '';
        $newUsers       = '';

        $request        = $this->getRequest();
        $form           = new Form();
        $form->setAttribute('method', 'post');

        if ($request->isPost()) {
            $data = $request->getPost()->toArray();
            $form->setData($data);
            if ($form->isValid()) {
                // Change stats with gameId
                $gameId         = $data['gameId'];
                $participants   = $ap->getNumberOfPlayers($gameId);
                $entries        = $ap->getNumberOfEntries($gameId);
                $optinUser      = $ap->findOptin('optin', $gameId);
                $optinPartner   = $ap->findOptin('optinPartner', $gameId);
            }
        }

        return new ViewModel(
            [
                'form'                  => $form,
                'gameId'                => $gameId,
                'players'               => $participants,
                'entries'               => $entries,
                'optinUser'             => $optinUser,
                'optinPartner'          => $optinPartner,
                'games'                 => $games,
                'participationsSerie'   => $participationsSerie,
                'numberOfEntries'       => $numberOfEntries,
                'numberOfPlayers'       => $numberOfPlayers,
                'participationsGames'   => $participationsGames,
                'stats'                 => $stats,
                'sessionSerie'          => $sessionSerie,
                'countriesSerie'        => $countriesSerie,
                'browserSerie'          => $browserSerie,
                'sourceSerie'           => $sourceSerie,
                'topPagesSerie'         => $topPagesSerie,
                'wholeSerie'            => $wholeSerie,
                'cards'                 => $cards,
            ]
        );
    }

    public function exportAction()
    {
        $ap         = $this->getApplicationService();

        $data       = '';
        $records    = '';
        $results    = '';

        $request    = $this->getRequest();
        $form       = $this->getServiceLocator()->get('playgroundstats_export_form');
        $form->setAttribute('method', 'post');

        $category = $form->get('prizeCategory');

        // Form send
        if ($request->isPost()) {
            $data = $request->getPost()->toArray();
            $form->setData($data);

            $inputFilter = $form->getInputFilter();
            $inputFilter->get('prizeCategory')->setRequired(false);
            //$inputFilter->get('memberid')->setRequired(FALSE);

            if ($form->isValid()) {
                $data       = $form->getData();
                $results    = $ap->getExportRecords($data);
                if ($results instanceof \PDOStatement) {
                    $records = $results->rowCount();
                } else if(is_array($results)) {
                    $records = count($results);
                } else {
                    $records = 0;
                }
                $session = new Container('Export');
                $session->data = $data;
            } else {
                $data = '';
            }
        }

        return new ViewModel(
            array(
                'form'      => $form,
                'data'      => $data,
                'records'   => $records,
            )
        );
    }

    public function downloadexportAction()
    {
        $dataContainer  = new Container('Export');
        $ap = $this->getApplicationService();
        $data = $dataContainer->offsetGet('data');

        $this->myExportedResult = $ap->getExportRecords($data);

        if ($this->myExportedResult instanceof \PDOStatement) {
            $this->downloadexportActionResultSetSQL();
        } else {
            $this->downloadexportActionObject();
        }
    }

    protected function downloadexportActionResultSetSQL()
    {
        header('Content-Encoding: UTF-8');
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="export.csv"');
        header('Accept-Ranges: bytes');

        $ap             = $this->getApplicationService();
        $dataContainer  = new Container('Export');

        if ($this->myExportedResult && $dataContainer->offsetExists('data')) {
            $result     = $this->myExportedResult;
            $data       = $dataContainer->offsetGet('data');

            echo "\xEF\xBB\xBF"; // UTF-8 BOM
            if ($data['gameid'] != '') :
                echo "Id du jeu;";

            endif;
            echo "ID du membre;Civilité;Nom;Prénom;Pseudo;Email;Adresse;CP;Ville;Date de naissance;Tél fixe;Tél mobile;";
            echo "Optin Newsletter;Optin NL partenaires;Date d'inscription;Source d'inscription;Email validé;";
            echo "Nb partages total;Nb partages FB;Nb partages Twitter;Nb partages G+;Nb partages mail;Nb participations";
            if (isset($data['actionName'])) {
                foreach ($data['actionName'] as $action) {
                    echo ";" . $this->getServiceLocator()->get('MvcTranslator')->translate($action, 'playgroundstats');
                }
            }
            echo "\n";

            while ($row = $result->fetch()) {
                //var_dump($row);
                $userId         = $row['user_id'];
                $totalShares    = $ap->getSharesByUser('shares', $userId);
                $fbShares       = $ap->getSharesByUser('fbShares', $userId);
                $twShares       = $ap->getSharesByUser('twShares', $userId);
                $glShares       = $ap->getSharesByUser('glShares', $userId);
                $mailShares     = $ap->getSharesByUser('mailShares', $userId);

                //var_dump($row);exit;
                if ($data['gameid'] != '') {
                    echo $data['gameid'] . ";";
                }
                echo $row['user_id'] . ";" . $row['title'] . ";" . $row['lastname'] . ";" . $row['firstname'] . ";" . $row['username'] . ";" . $row['email'];
                if ($row['address2'] != '') {
                    $address = $row['address'] . ' - ' . $row['address2'];
                } else {
                    $address = $row['address'];
                }
                echo  ";" . $address . ";" . $row['postal_code'] . ";" . $row['city'] . ";" . $row['dob'];
                echo  ";" . $row['telephone'] . ";" . $row['mobile'];

                $optin = ($row['optin'] == true) ? 'oui' : 'non';
                echo  ";" . $optin;

                $optinPartner = ($row['optin_partner'] == true) ? 'oui' : 'non';
                echo  ";" . $optinPartner . ";";

                echo   $row['created_at'] . ";" . $row['registration_source'];

                $validatedEmail = ($row['state']== null) ? 'non' : 'oui';
                echo  ";" . $validatedEmail;
                echo  ";" . $totalShares . ";" . $fbShares . ";" . $twShares . ";" . $glShares . ";" . $mailShares;

                // $this->prizeCategoriesExport = $ap->getPrizeCategoriesByUser($userId, $data['prizeCategory']);
                // $prizeCategoriesArray = array();
                // while ($prizeCategories = $this->prizeCategoriesExport->fetch()) {
                //     $prizeCategoriesArray[] .= $prizeCategories['title'];
                //     unset($prizeCategories);
                // }
                // $prizeCategoriesString = implode(',', $prizeCategoriesArray);
                // echo ";" . $prizeCategoriesString;

                $nbEntries = $ap->getNumberEntriesByUser($userId, $data);
                echo ";" . $nbEntries;

                if (isset($data['actionName'])) {
                    foreach ($data['actionName'] as $action) {
                        $actionLabel = str_replace("/", "", str_replace("-", "", $action));
                        echo ";" . $row[$actionLabel];
                    }
                }

                echo  "\n";
                unset($row);
            }
        }
        exit();
    }

    protected function downloadexportActionObject()
    {
        $ap = $this->getApplicationService();
        $contentRecord  = '';

        $dataContainer  = new Container('Export');

        if (isset($this->myExportedResult) && $dataContainer->offsetExists('data')) {
            $result     = $this->myExportedResult;
            $data       = $dataContainer->offsetGet('data');
            //var_dump($result);
            //var_dump($data);

            foreach ($result as $key => $record) {
                $userId         = $record->getId();
                $totalShares    = $ap->getSharesByUser('shares', $userId);
                $fbShares       = $ap->getSharesByUser('fbShares', $userId);
                $twShares       = $ap->getSharesByUser('twShares', $userId);
                $glShares       = $ap->getSharesByUser('glShares', $userId);
                $mailShares         = $ap->getSharesByUser('mailShares', $userId);
                if ($data['gameid'] != '') :
                    $contentRecord .= $data['gameid'] . ";";

                endif; // à déterminer
                $contentRecord .=       $record->getId()
                . ";" . $record->getTitle()
                . ";" . $record->getLastname()
                . ";" . $record->getFirstname()
                . ";" . $record->getEmail();
                if ($record->getAddress2() != '') :
                    $address = $record->getAddress() . ' - ' . $record->getAddress2(); else :
                    $address = $record->getAddress();

                endif;
                $contentRecord .= ";" . $address
                . ";" . $record->getPostalCode()
                . ";" . $record->getCity();
                if ($data['birthdate'] != 'all') :
                    $contentRecord .= ";" . $record->getDob()->format('d/m/Y');

                endif;
                $contentRecord .= ";" . $record->getTelephone()
                . ";" . $record->getMobile();
                if ($data['optin'] != 'all') {
                    if ($record->getOptin() == true) :
                        $optin = 'oui'; else :
                                        $optin = 'non';

                    endif;
                    $contentRecord .= ";" . $optin;
                }
                if ($record->getOptinPartner() == true) :
                    $optinPartner = 'oui'; else :
                    $optinPartner = 'non';

                endif;
                $contentRecord .= ";" . $optinPartner
                . ";" . $record->getChildren();
                if ($data['inscriptiondate'] != 'all') :
                    $contentRecord .= ";" . $record->getCreatedAt()->format('d/m/Y');

                endif;
                $contentRecord .= ";" . $record->getRegistrationSource();
                if ($data['hardbounce'] != 'all') {
                    if ($record->getIsHardbounce() == 1) :
                        $hardbounce = 'oui'; else :
                        $hardbounce = 'non';

                    endif;
                    $contentRecord .= ";" . $hardbounce;
                }
                if ($data['validatedemail'] != 'all') {
                    if ($record->getState()== null) :
                        $validatedEmail = 'non'; else :
                                        $validatedEmail = 'oui';

                    endif;
                    $contentRecord .= ";" . $validatedEmail;
                }
                $contentRecord .= ";$totalShares;$fbShares;$twShares;$glShares;$mailShares\n";
            }
        }

        $content        = "\xEF\xBB\xBF"; // UTF-8 BOM
        if ($data['gameid'] != '') :
            $content .= "Id du jeu;";

        endif;
        $content        .= "ID du membre;Civilité;";
        $content        .= "Nom;";
        $content        .= "Prénom;";
        $content        .= "Email;Adresse;CP;Ville;";
        if ($data['birthdate'] != 'all') :
            $content .= "Date de naissance;";

        endif;
        $content       .= "Tél fixe;Tél mobile;";
        if ($data['optin'] != 'all') :
            $content .= "Optin Newsletter;";

        endif;
        $content       .= "Optin NL partenaires;";
        if ($data['inscriptiondate'] != 'all') :
            $content .= "Date d'inscription;";
        endif;
        $content        .= "Source d'inscription;";
        if ($data['hardbounce'] != 'all') :
            $content .= "Hard Bounce;";

        endif;
        if ($data['validatedemail'] != 'all') :
            $content .= "Email validé;";

        endif;
        $content       .= "Nb partages total;Nb partages FB;Nb partages Twitter;Nb partages G+;Nb partages mail;";

        $content       .= "\n";
        $content       .= $contentRecord;

        $response = $this->getResponse();
        $headers = $response->getHeaders();
        $headers->addHeaderLine('Content-Encoding: UTF-8');
        $headers->addHeaderLine('Content-Type', 'text/csv; charset=UTF-8');
        $headers->addHeaderLine('Content-Disposition', "attachment; filename=\"export.csv\"");
        $headers->addHeaderLine('Accept-Ranges', 'bytes');
        $headers->addHeaderLine('Content-Length', strlen($content));

        $response->setContent($content);

        return $response;
    }

    public function downloadAction()
    {
        $su             = $this->getUserService();
        $sg             = $this->getGameService();
        $sr             = $this->getRewardService();

        $allUser        = count($su->findAll());
        $activeUser     = count($su->findByState(1));
        $inactiveUser   = $allUser - $activeUser;

        $maleUser       = count($su->findByTitle('M'));
        $femaleUser     = count($su->findByTitle('Me'));

        $optinUser      = count($su->findByOptin(1));

        $activeGame     = count($sg->getActiveGames());

        $allGames       = count($sg->findAll());
        $allEntries     = count($sg->findAllEntry());
        $userPerGames   = $allEntries/$allGames;

        $activePage     = count($this->getPageService()->getActivePages());

        $shareAction    = count($sr->findBy(array('action' => array(13, 14, 15, 16))));
        $sharePerGames  = $allGames/$shareAction;

        $userPerBadges = array();
        $badges     = $this->getRewardAchievementListenerService()->getBadges();

        foreach ($badges as $keyBadge => $badge) {
            foreach ($badge['levels'] as $keyLevel => $level) {
                $userPerBadges[] = array(
                        'badge' => $keyBadge,
                        'level' => $level['label'],
                        'user'  => count($this->getAchievementService()->findBy(array('type' => 'badge', 'category'=> $keyBadge, 'level' => $keyLevel))),
                );
            }
        }

        $content        = "\xEF\xBB\xBF"; // UTF-8 BOM
        $content       .= "Inscrits Actifs;Inscrits Suspendus;Inscrits Homme;Inscrits Femme;Abonnement Newsletter;Jeux Actifs;Articles Actifs;Participant par jeu;Partages par jeu;";

        foreach ($userPerBadges as $badge) {
            $content .=  $badge['badge'].' '.$badge['level'].';';
        }

        $content       .= "\n";

        $content   .= $activeUser
        . ";" . $inactiveUser
        . ";" . $maleUser
        . ";" . $femaleUser
        . ";" . $optinUser
        . ";" . $activeGame
        . ";" . $activePage
        . ";" . number_format($userPerGames, 2)
        . ";" . number_format($sharePerGames, 2);

        foreach ($userPerBadges as $badge) {
            $content .=  $badge['user'].';';
        }

        $content       .= "\n";

        $response = $this->getResponse();
        $headers = $response->getHeaders();
        $headers->addHeaderLine('Content-Encoding: UTF-8');
        $headers->addHeaderLine('Content-Type', 'text/csv; charset=UTF-8');
        $headers->addHeaderLine('Content-Disposition', "attachment; filename=\"statistics.csv\"");
        $headers->addHeaderLine('Accept-Ranges', 'bytes');
        $headers->addHeaderLine('Content-Length', strlen($content));

        $response->setContent($content);

        return $response;
    }

    /**
     * ListAction : retrieve all cards
     *
     * @return array $return
     */
    public function listCardAction()
    {
        $cards = $this->getApplicationService()->getCardMapper()->findAll();

        return [
            'cards' => $cards,
            'flashMessages' => $this->flashMessenger()->getMessages(),
        ];
    }

    /**
     * CreateCard : create a card
     *
     * @return viewModel $viewModel
     */
    public function createCardAction()
    {
        $form = $this->getServiceLocator()->get('playgroundstats_card_form');

        $request = $this->getRequest();
        $card = new Card();

        if ($request->isPost()) {
            $data = array_merge(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray()
            );

            $card = $this->getApplicationService()->createCard($data, 'playgroundstats_card_form');
            if ($card) {
                $this->flashMessenger()->addMessage('The card "'.$card->getTitle().'" has been created');

                return $this->redirect()->toRoute('admin/stats/card/list');
            } else {
                $this->flashMessenger()->addMessage('The card has not been created');

                return $this->redirect()->toRoute('admin/stats/card/list');
            }
        }

        $viewModel = new ViewModel();
        $viewModel->setTemplate('playground-stats/statistics/card');

        return $viewModel->setVariables(array('form' => $form));
    }


    /**
    * editAction : edit a card
    *
    * @return viewModel $viewModel
    */
    public function editCardAction()
    {
        $cardId = $this->getEvent()->getRouteMatch()->getParam('cardId');
        $card = $this->getApplicationService()->getCardMapper()->findById($cardId);

        $form = $this->getServiceLocator()->get('playgroundstats_card_form');

        $request = $this->getRequest();

        $form->bind($card);

        if ($request->isPost()) {
            $data = array_merge(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray()
            );

            $card = $this->getApplicationService()->editCard(
                $data,
                $card,
                'playgroundstats_card_form'
            );

            if ($card) {
                $this->flashMessenger()->addMessage('The card "'.$card->getTitle().'" has been updated');

                return $this->redirect()->toRoute('admin/stats/card/list');
            } else {
                $this->flashMessenger()->addMessage('The card has not been updated');

                return $this->redirect()->toRoute('admin/stats/card/list');
            }
        }

        $viewModel = new ViewModel();
        $viewModel->setTemplate('playground-stats/statistics/card');

        return $viewModel->setVariables(array('form' => $form));
    }

    /**
     * RemoveCardAction : remove a card
     *
     * @return redirect
     */
    public function removeCardAction()
    {
        $cardId = $this->getEvent()->getRouteMatch()->getParam('cardId');
        $card = $this->getApplicationService()->getCardMapper()->findById($cardId);
        $title = $card->getTitle();
        $this->getApplicationService()->removeCard($card);
        $this->flashMessenger()->addMessage('The leaderboard "'.$title.'"has been removed');

        return $this->redirect()->toRoute('admin/stats/card/list');
    }

    /**
     * ViewCardAction : view a card
     *
     * @return redirect
     */
    public function viewCardAction()
    {
        $viewModel = new ViewModel();
        $viewModel->setTemplate('playground-stats/statistics/view-card');
        if ($this->getRequest()->isXmlHttpRequest()) {
            $viewModel->setTerminal(true);
        }

        $cardId = $this->getEvent()->getRouteMatch()->getParam('cardId');
        $card = $this->getApplicationService()->getCardMapper()->findById($cardId);
        $sqlResult = $this->getApplicationService()->getSqlResult($card);

        return $viewModel->setVariables(array('card' => $card, 'sqlResult' => $sqlResult));
    }

    public function getAchievementService()
    {
        if (!$this->achievementService) {
            $this->achievementService = $this->getServiceLocator()->get('playgroundreward_achievement_service');
        }

        return $this->achievementService;
    }

    public function setAchievementService($achievementService)
    {
        $this->achievementService = $achievementService;

        return $this;
    }

    public function getRewardService()
    {
        if (!$this->rewardService) {
            $this->rewardService = $this->getServiceLocator()->get('playgroundreward_event_service');
        }

        return $this->rewardService;
    }

    public function setRewardService(GameService $rewardService)
    {
        $this->rewardService = $rewardService;

        return $this;
    }

    public function getRewardAchievementListenerService()
    {
        if (!$this->achievementListenerService) {
            $this->achievementListenerService = $this->getServiceLocator()->get('playgroundreward_achievement_listener');
        }

        return $this->achievementListenerService;
    }

    public function setRewardAchievementListenerService(GameService $achievementListenerService)
    {
        $this->achievementListenerService = $achievementListenerService;

        return $this;
    }

    public function getGameService()
    {
        if (!$this->gameService) {
            $this->gameService = $this->getServiceLocator()->get('playgroundgame_game_service');
        }

        return $this->gameService;
    }

    public function setGameService(GameService $gameService)
    {
        $this->gameService = $gameService;

        return $this;
    }

    public function getPageService()
    {
        if (!$this->pageService) {
            $this->pageService = $this->getServiceLocator()->get('playgroundcms_page_service');
        }

        return $this->pageService;
    }

    public function setPageService(\PlaygroundCms\Service\Page $pageService)
    {
        $this->pageService = $pageService;

        return $this;
    }

    public function getUserService()
    {
        if (!$this->userService) {
            $this->userService = $this->getServiceLocator()->get('playgrounduser_user_service');
        }

        return $this->userService;
    }

    public function setUserService($userService)
    {
        $this->userService = $userService;

        return $this;
    }

    /**
     *
     * @return \Application\Service\ApplicationService
     */
    public function getApplicationService()
    {
        if (!$this->applicationService) {
            $this->applicationService = $this->getServiceLocator()->get(\PlaygroundStats\Service\Stats::class);
        }

        return $this->applicationService;
    }

    public function setApplicationService($applicationService)
    {
        $this->applicationService = $applicationService;

        return $this;
    }
}