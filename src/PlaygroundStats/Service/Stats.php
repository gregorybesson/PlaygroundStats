<?php

namespace PlaygroundStats\Service;

use Zend\ServiceManager\ServiceManagerAwareInterface;
use Zend\ServiceManager\ServiceManager;
use ZfcBase\EventManager\EventProvider;
use Zend\Stdlib\Hydrator\ClassMethods;

use Doctrine\ORM\Query\ResultSetMapping;

class Stats extends EventProvider implements ServiceManagerAwareInterface
{

	/**
	 * @var ServiceManager
	 */
	protected $serviceManager;

	/**
	 * Return number of users in $game
	 * @param  unknown_type $game
	 */
	public function findEntries($game, $firstEntry=false)
	{
		$firstEntry = true;
		$em = $this->getServiceManager()->get('doctrine.entitymanager.orm_default');
		$rsm = new \Doctrine\ORM\Query\ResultSetMapping;
		$rsm->addScalarResult('c', 'c');
		$query = $em->createNativeQuery('
			SELECT COUNT(e.id) AS c
			FROM game_entry e
		    '.( $firstEntry == true ? ' RIGHT JOIN (SELECT l.id FROM game_entry l GROUP BY l.user_id) AS b ON b.id = e.id' : '' ).'
			WHERE e.game_id = '.( (int) $game ).'
		', $rsm);
		$count = $query->getSingleScalarResult();
		return $count;
	}

	/**
	 * Return number of users by optin/optinPartner
	 * @param number|unknown $type
	 */
	public function findOptin($type, $game)
	{
		$em = $this->getServiceManager()->get('doctrine.entitymanager.orm_default');

		switch ($type){
			case 'optin' :
				$filter = "AND u.optin = 1";
				break;
			case 'optinPartner' :
				$filter = "AND u.optinPartner = 1";
				break;
		}

		$query = $em->createQuery('
			SELECT COUNT(e.id)
			FROM PlaygroundGame\Entity\Entry e
			JOIN e.user u
			WHERE u.id = e.user
			AND e.game = :game
			' . $filter . '
		');

		$query->setParameter('game', $game);
		$count = $query->getSingleScalarResult();
		return $count;
	}

	/**
	 * Return count of users by $types and $sexe between $startDate and $endDate
	 *
	 * @param array $types List of types
	 * @param number|unknown $startDate
	 * @param number|unknown $endDate
	 */
	public function getListUserCountByRangeDate($types, $startDate = '', $endDate = '')
	{
		if ( empty($types) ) {
			return array();
		}
		$em = $this->getServiceManager()->get('doctrine.entitymanager.orm_default');

		$now = new \DateTime("now");
		// $now->format('Y-m-d') . ' 23:59:59';
		$startDateTime = \DateTime::createFromFormat('d/m/Y', $startDate);
		$endDateTime = \DateTime::createFromFormat('d/m/Y', $endDate);

		$rsm = new \Doctrine\ORM\Query\ResultSetMapping;

		$select = array();
		$i = 0;

		foreach($types as $type) {
			switch ($type) {
				case 'validatedMembers':
					$filter = "u.state IS NOT NULL";
					break;
				case 'activeMembers':
					$filter = "u.state=1";
					break;
				case 'optin':
					$filter = "u.optin=1";
					break;
				case 'optinPartners':
					$filter = "u.optin_partner=1";
					break;
				case 'unregistered':
					$filter = "u.state IS NULL";
					break;
				case 'suspended':
					$filter = "u.state=2";
					break;
				default:
				case 'members':
					$filter = "u.user_id";
					break;
			}

			$rsm->addScalarResult('c'.$i, 'c'.$i);
			$select[] = 'SUM(CASE WHEN '.$filter.' THEN 1 ELSE 0 END) AS c'.$i;
			$rsm->addScalarResult('c'.$i.'_male', 'c'.$i.'_male');
			$select[] = 'SUM(CASE WHEN '.$filter.' AND (u.title=\'M\' OR u.title IS NULL) THEN 1 ELSE 0 END) AS c'.$i.'_male';
			$rsm->addScalarResult('c'.$i.'_female', 'c'.$i.'_female');
			$select[] = 'SUM(CASE WHEN '.$filter.' AND u.title=\'Me\' THEN 1 ELSE 0 END) AS c'.$i++.'_female';
		}

		if ($startDate != '' && $endDate != '') {
			$dateRange = "(u.created_at >='" . $startDateTime->format('Y-m-d') . " 0:0:0' AND u.created_at <= '" . $endDateTime->format('Y-m-d') . " 0:0:0')";
		} elseif ($startDate == '' && $endDate != '') {
			$dateRange = "u.created_at <= '" . $endDateTime->format('Y-m-d') . " 0:0:0'";
		} elseif ($startDate != '' && $endDate == '') {
			$dateRange = "u.created_at >='" . $startDateTime->format('Y-m-d') . " 0:0:0'";
		} else {
			$dateRange = "u.created_at IS NOT NULL";
		}
		$query = $em->createNativeQuery('
			SELECT '.implode(',',$select).' FROM user AS u
			WHERE ' . $dateRange, $rsm);
		return array_values($query->getSingleResult());
	}


	/**
	 * Return count of users by $type and $sexe between $startDate and $endDate
	 * @param number|unknown $startDate
	 * @param number|unknown $endDate
	 */
	public function getUsersByRangeDate($type, $startDate='', $endDate='', $sexe='')
	{
		$em = $this->getServiceManager()->get('doctrine.entitymanager.orm_default');

		$now = new \DateTime("now");
		//$now->format('Y-m-d') . ' 23:59:59';
		$startDateTime = \DateTime::createFromFormat('d/m/Y', $startDate);
		$endDateTime = \DateTime::createFromFormat('d/m/Y', $endDate);

		switch ($type){
			case 'members' :
				$filter = "";
				break;
			case 'validatedMembers' :
				$filter = "AND (u.state IS NOT NULL)";
				break;
			case 'activeMembers' :
				$filter = "AND u.state=1";
				break;
			case 'optin' :
				$filter = "AND u.optin=1";
				break;
			case 'optinPartners' :
				$filter = "AND u.optinPartner=1";
				break;
			case 'unregistered' :
				$filter = "AND u.state IS NULL";
				break;
			case 'suspended' :
				$filter = "AND u.state=2";
				break;
		}

		switch ($sexe){
			case 'M' :
				$filterSexe = " AND (u.title='M' OR u.title IS NULL)";
				break;
			case 'Me':
				$filterSexe = " AND u.title='Me'";
				break;
			case '' :
				$filterSexe = "";
				break;
		}

		if ($startDate != '' && $endDate != '') {
			$dateRange = "(u.created_at >='" . $startDateTime->format('Y-m-d') . " 0:0:0' AND u.created_at <= '" . $endDateTime->format('Y-m-d') . " 0:0:0')";
		} elseif ($startDate == '' && $endDate != '') {
			$dateRange = "u.created_at <= '" . $endDateTime->format('Y-m-d') . " 0:0:0'";
		} elseif ($startDate != '' && $endDate == '') {
			$dateRange = "u.created_at >='" . $startDateTime->format('Y-m-d') . " 0:0:0'";
		} else {
			$dateRange = "u.created_at IS NOT NULL";
		}

		$query = $em->createQuery('
			SELECT COUNT(u.id) FROM PlaygroundUser\Entity\User u
			WHERE ' . $dateRange . '
			' . $filter . ' ' . $filterSexe . '
		');
		$count = $query->getSingleScalarResult();
		return $count;
	}

	/**
	 * Return count of participation between $startDate and $endDate
	 * @param number|unknown $startDate
	 * @param number|unknown $endDate
	 */
	public function getParticipationsByRangeDate($startDate='', $endDate='')
	{
		$em = $this->getServiceManager()->get('doctrine.entitymanager.orm_default');

		$startDateTime = \DateTime::createFromFormat('d/m/Y', $startDate);
		$endDateTime = \DateTime::createFromFormat('d/m/Y', $endDate);

		if ($startDate != '' && $endDate != '') {
			$dateRange = "(e.created_at >='" . $startDateTime->format('Y-m-d') . " 0:0:0' AND e.created_at <= '" . $endDateTime->format('Y-m-d') . " 0:0:0')";
		} elseif ($startDate == '' && $endDate != '') {
			$dateRange = "e.created_at <= '" . $endDateTime->format('Y-m-d') . " 0:0:0'";
		} elseif ($startDate != '' && $endDate == '') {
			$dateRange = "e.created_at >='" . $startDateTime->format('Y-m-d') . " 0:0:0'";
		} else {
			$dateRange = "e.created_at IS NOT NULL";
		}

		$query = $em->createQuery('
			SELECT COUNT(e.id) FROM PlaygroundGame\Entity\Entry e
			WHERE ' . $dateRange . '
			AND e.active=0
		');
		$count = $query->getSingleScalarResult();
		return $count;
	}

	/**
	 * Return count of active games between $startDate and $endDate
	 * @param number|unknown $startDate
	 * @param number|unknown $endDate
	 */
	public function getGamesByRangeDate($startDate='', $endDate='')
	{
		$em = $this->getServiceManager()->get('doctrine.entitymanager.orm_default');

		$startDateTime = \DateTime::createFromFormat('d/m/Y', $startDate);
		$endDateTime = \DateTime::createFromFormat('d/m/Y', $endDate);

		if ($startDate != '' && $endDate != '') {
			$dateRange = "(g.publicationDate >='" . $startDateTime->format('Y-m-d') . " 0:0:0' AND g.publicationDate <= '" . $endDateTime->format('Y-m-d') . " 0:0:0')";
		} elseif ($startDate == '' && $endDate != '') {
			$dateRange = "g.publicationDate <= '" . $endDateTime->format('Y-m-d') . " 0:0:0'";
		} elseif ($startDate != '' && $endDate == '') {
			$dateRange = "g.publicationDate >='" . $startDateTime->format('Y-m-d') . " 0:0:0'";
		} else {
			$dateRange = "g.publicationDate IS NOT NULL";
		}

		$query = $em->createQuery('
			SELECT COUNT(g.id) FROM PlaygroundGame\Entity\Game g
			WHERE ' . $dateRange . '
			AND g.active=1
		');
		$count = $query->getSingleScalarResult();
		return $count;
	}

	/**
	 * Return count of active articles between $startDate and $endDate
	 * @param number|unknown $startDate
	 * @param number|unknown $endDate
	 */
	public function getArticlesByRangeDate($startDate='', $endDate='')
	{
		$em = $this->getServiceManager()->get('doctrine.entitymanager.orm_default');

		$startDateTime = \DateTime::createFromFormat('d/m/Y', $startDate);
		$endDateTime = \DateTime::createFromFormat('d/m/Y', $endDate);

		if ($startDate != '' && $endDate != '') {
			$dateRange = "(p.publicationDate >='" . $startDateTime->format('Y-m-d') . " 0:0:0' AND p.publicationDate <= '" . $endDateTime->format('Y-m-d') . " 0:0:0')";
		} elseif ($startDate == '' && $endDate != '') {
			$dateRange = "p.publicationDate <= '" . $endDateTime->format('Y-m-d') . " 0:0:0'";
		} elseif ($startDate != '' && $endDate == '') {
			$dateRange = "p.publicationDate >='" . $startDateTime->format('Y-m-d') . " 0:0:0'";
		} else {
			$dateRange = "p.publicationDate IS NOT NULL";
		}

		$query = $em->createQuery('
			SELECT COUNT(p.id) FROM PlaygroundCms\Entity\Page p
			WHERE ' . $dateRange . '
			AND p.active=1
		');
		$count = $query->getSingleScalarResult();
		return $count;
	}

	/**
	 * Return count of shares by $type between $startDate and $endDate
	 *
	 * @param array $types List of types
	 * @param number|unknown $startDate
	 * @param number|unknown $endDate
	 */
	public function getListShareCountByRangeDate($types, $startDate = '', $endDate = '')
	{
		$em = $this->getServiceManager()->get('doctrine.entitymanager.orm_default');

		$startDateTime = \DateTime::createFromFormat('d/m/Y', $startDate);
		$endDateTime = \DateTime::createFromFormat('d/m/Y', $endDate);

		$rsm = new \Doctrine\ORM\Query\ResultSetMapping;

		$select = array();
		$i = 0;

		foreach( $types as $type ) {
			switch ($type) {
				case 'sponsoredMembers':
					$filter = "e.action_id =20";
					break;
				case 'shares':
					$filter = "e.action_id IN (13,14,15,16,17)";
					break;
				case 'profilShares':
					$filter = "e.action_id IN (13,14,15,16,17) AND e.label like '%espace client%'";
					break;
				case 'fbInvit':
					$filter = "e.action_id IN (14,15) AND e.label NOT like '%espace client%'";
					break;
				case 'twInvit':
					$filter = "e.action_id=16 AND e.label NOT like '%espace client%'";
					break;
				case 'glInvit':
					$filter = "e.action_id=17 AND e.label NOT like '%espace client%'";
					break;
				case 'mailInvit':
					$filter = "e.action_id=13 AND e.label NOT like '%espace client%'";
					break;
			}
			$rsm->addScalarResult('c'.$i, 'c'.$i);
			$select[] = 'SUM(CASE WHEN '.$filter.' THEN 1 ELSE 0 END) AS c'.$i++;
		}

		if ($startDate != '' && $endDate != '') {
			$dateRange = "(e.created_at >='" . $startDateTime->format('Y-m-d') . " 0:0:0' AND e.created_at <= '" . $endDateTime->format('Y-m-d') . " 0:0:0')";
		} elseif ($startDate == '' && $endDate != '') {
			$dateRange = "e.created_at <= '" . $endDateTime->format('Y-m-d') . " 0:0:0'";
		} elseif ($startDate != '' && $endDate == '') {
			$dateRange = "e.created_at >='" . $startDateTime->format('Y-m-d') . " 0:0:0'";
		} else {
			$dateRange = "e.created_at IS NOT NULL";
		}

		$query = $em->createNativeQuery('
			SELECT '.implode(',',$select).' FROM reward_event e
			WHERE ' . $dateRange, $rsm);
		return array_values($query->getSingleResult());
	}

	/**
	 * Return count of shares by $type between $startDate and $endDate
	 * @param number|unknown $startDate
	 * @param number|unknown $endDate
	 */
	public function getSharesByRangeDate($type, $startDate='', $endDate='')
	{
		$em = $this->getServiceManager()->get('doctrine.entitymanager.orm_default');

		$startDateTime = \DateTime::createFromFormat('d/m/Y', $startDate);
		$endDateTime = \DateTime::createFromFormat('d/m/Y', $endDate);

		switch ($type){
			case 'sponsoredMembers' :
				$filter = "e.action=20";
				break;
			case 'shares' :
				$filter = "e.action IN (13,14,15,16,17)";
				break;
			case 'profilShares' :
				$filter = "e.action IN (13,14,15,16,17) AND e.label like '%espace client%'";
				break;
			case 'fbInvit' :
				$filter = "e.action IN (14,15) AND e.label NOT like '%espace client%'";
				break;
			case 'twInvit' :
				$filter = "e.action=16 AND e.label NOT like '%espace client%'";
				break;
			case 'glInvit' :
				$filter = "e.action=17 AND e.label NOT like '%espace client%'";
				break;
			case 'mailInvit' :
				$filter = "e.action=13 AND e.label NOT like '%espace client%'";
				break;
		}

		if ($startDate != '' && $endDate != '') {
			$dateRange = "(e.createdAt >='" . $startDateTime->format('Y-m-d') . " 0:0:0' AND e.createdAt <= '" . $endDateTime->format('Y-m-d') . " 0:0:0')";
		} elseif ($startDate == '' && $endDate != '') {
			$dateRange = "e.createdAt <= '" . $endDateTime->format('Y-m-d') . " 0:0:0'";
		} elseif ($startDate != '' && $endDate == '') {
			$dateRange = "e.createdAt >='" . $startDateTime->format('Y-m-d') . " 0:0:0'";
		} else {
			$dateRange = "e.createdAt IS NOT NULL";
		}

		$query = $em->createQuery('
			SELECT COUNT(e.id) FROM PlaygroundReward\Entity\Event e
			WHERE ' . $dateRange . '
			AND ' . $filter . '
		');
		$count = $query->getSingleScalarResult();
		return $count;
	}

	/**
	 * Return count of badges by $type between $startDate and $endDate
	 * @param array $badges List of badge type
	 * @param number|unknown $startDate
	 * @param number|unknown $endDate
	 */
	public function getListBadgeCountByRangeDate($badges, $startDate='', $endDate='')
	{
		$em = $this->getServiceManager()->get('doctrine.entitymanager.orm_default');

		$startDateTime = \DateTime::createFromFormat('d/m/Y', $startDate);
		$endDateTime = \DateTime::createFromFormat('d/m/Y', $endDate);
	  
		$rsm = new \Doctrine\ORM\Query\ResultSetMapping;
	  
		$select = array();
		$i = 0;

		foreach($badges as $badge => $levels) {
			foreach($levels as $level) {
				$filter = array('a.id');
				if($badge != 'total') {
					$filter[] = 'a.category = \''.$badge.'\'';
				}
				if($level > 0){
					$filter[] = 'a.level = '.( (int) $level );
				}
				$rsm->addScalarResult('c'.$i, 'c'.$i);
				$select[] = 'SUM(CASE WHEN '.implode(' AND ',$filter).' THEN 1 ELSE 0 END) AS c'.$i++;
			}
		}

		if ($startDate != '' && $endDate != '') {
			$dateRange = "(a.created_at >='" . $startDateTime->format('Y-m-d') . " 0:0:0' AND a.created_at <= '" . $endDateTime->format('Y-m-d') . " 0:0:0')";
		} elseif ($startDate == '' && $endDate != '') {
			$dateRange = "a.created_at <= '" . $endDateTime->format('Y-m-d') . " 0:0:0'";
		} elseif ($startDate != '' && $endDate == '') {
			$dateRange = "a.created_at >='" . $startDateTime->format('Y-m-d') . " 0:0:0'";
		} else {
			$dateRange = "a.created_at IS NOT NULL";
		}

		$query = $em->createNativeQuery('
			SELECT '.implode(',',$select).' FROM reward_achievement a
			WHERE ' . $dateRange, $rsm);
		return array_values( $query->getSingleResult() );
	}

	/**
	 * Return count of badges by $type between $startDate and $endDate
	 * @param number|unknown $startDate
	 * @param number|unknown $endDate
	 */
	public function getBadgesByRangeDate($type, $level, $startDate='', $endDate='')
	{
		$em = $this->getServiceManager()->get('doctrine.entitymanager.orm_default');

		$startDateTime = \DateTime::createFromFormat('d/m/Y', $startDate);
		$endDateTime = \DateTime::createFromFormat('d/m/Y', $endDate);



		if ($startDate != '' && $endDate != '') {
			$dateRange = "(a.createdAt >='" . $startDateTime->format('Y-m-d') . " 0:0:0' AND a.createdAt <= '" . $endDateTime->format('Y-m-d') . " 0:0:0')";
		} elseif ($startDate == '' && $endDate != '') {
			$dateRange = "a.createdAt <= '" . $endDateTime->format('Y-m-d') . " 0:0:0'";
		} elseif ($startDate != '' && $endDate == '') {
			$dateRange = "a.createdAt >='" . $startDateTime->format('Y-m-d') . " 0:0:0'";
		} else {
			$dateRange = "a.createdAt IS NOT NULL";
		}

		if($type == 'total') {
			$badgeType = '';
		} else {
			$badgeType = 'AND a.category = :type';
		}

		if($level == 0){
			$levelType = '';
		} else {
			$levelType = 'AND a.level = ' . $level;
		}

		$query = $em->createQuery('
			SELECT COUNT(a.id) FROM PlaygroundReward\Entity\Achievement a
			WHERE ' . $dateRange . '
			' . $badgeType . '
			' . $levelType . '
		');
		if($type != 'total') {
			$query->setParameter('type', $type);
		}
		$count = $query->getSingleScalarResult();
		return $count;
	}

	/**
	 * Export Users
	 * @param number|unknown $data
	 */
	public function getExportRecords($data)
	{
		$em = $this->getServiceManager()->get('doctrine.entitymanager.orm_default');

		//var_dump($data);

		$gameId 		= $data['gameid'];

		$lastName 		= $data['lastname'];
		$lastNameInput 	= $data['lastname-input'];

		$firstName 		= $data['firstname'];
		$firstNameInput	= $data['firstname-input'];

		$sexe 	 		= $data['sexe'];

		$memberId 		= $data['memberid'];
		$memberIdInput 	= $data['memberid-input'];

		$email 			= $data['email'];
		$emailInput 	= $data['email-input'];

		$zipcode 		= $data['zipcode'];
		$zipcodeInput 	= $data['zipcode-input'];

		$birthdate 		= $data['birthdate'];
		$dobStartIni 	= $data['birthdate-start'];
		$dobEndIni 		= $data['birthdate-end'];
		$dobEqualIni 	= $data['birthdate-equal'];
		$dobStart 		= \DateTime::createFromFormat('d/m/Y', $dobStartIni);
		$dobEnd 		= \DateTime::createFromFormat('d/m/Y', $dobEndIni);
		$dobEqual		= \DateTime::createFromFormat('d/m/Y', $dobEqualIni);

		$hobbies 		= $data['prizeCategory'];

		$optin 			= $data['optin'];

		$optinPartner	= $data['optinpartner'];

		$suscribeDate	= $data['inscriptiondate'];
		$suscribeStartIni = $data['inscriptiondate-start'];
		$suscribeEndIni = $data['inscriptiondate-end'];
		$suscribeStart 	= \DateTime::createFromFormat('d/m/Y', $suscribeStartIni);
		$suscribeEnd 	= \DateTime::createFromFormat('d/m/Y', $suscribeEndIni);

		$hardbounce		= $data['hardbounce'];
		$hardbounceStartIni = $data['hardbounce-start'];
		$hardbounceEndIni = $data['hardbounce-end'];
		$hardbounceStart 	= \DateTime::createFromFormat('d/m/Y', $hardbounceStartIni);
		$hardbounceEnd 	= \DateTime::createFromFormat('d/m/Y', $hardbounceEndIni);

		$nbpart			= $data['nbpart'];
		$nbpartMin		= $data['nbpart-min'];
		$nbpartMax		= $data['nbpart-max'];
		$nbpartStartIni = $data['nbpart-start'];
		$nbpartEndIni 	= $data['nbpart-end'];
		$nbpartStart 	= \DateTime::createFromFormat('d/m/Y', $nbpartStartIni);
		$nbpartEnd 		= \DateTime::createFromFormat('d/m/Y', $nbpartEndIni);

		$validatedemail = $data['validatedemail'];

		$player 		= $data['player'];
		$goldfather 	= $data['goldfather'];
		$brain 			= $data['brain'];
		$ambassador 	= $data['ambassador'];
		$anniversary 	= $data['anniversary'];

		/*
		 if($gameId != '') 	: $gameidFilter = "AND u.id IN (SELECT r FROM PlaygroundGame\Entity\Entry e LEFT JOIN e.user r WHERE e.game = " . $gameId . " GROUP BY e.user)"; else :	$gameidFilter = ""; endif;

		switch ($lastName){
		case 'all' 		: $lastnameFilter = "WHERE u.lastname IS NOT NULL"; break;
		case 'start' 	: $lastnameFilter = "WHERE u.lastname like '" . $lastNameInput . "%'"; break;
		case 'contain'  : $lastnameFilter = "WHERE u.lastname like '%" . $lastNameInput . "%'"; break;
		case 'equal' 	: $lastnameFilter = "WHERE u.lastname = '" . $lastNameInput . "'"; break;
		}

		switch ($firstName){
		case 'all' 		: $firstnameFilter = ""; break;
		case 'start' 	: $firstnameFilter = "AND u.firstname like '" . $firstNameInput . "%'"; break;
		case 'contain' 	: $firstnameFilter = "AND u.firstname like '%" . $firstNameInput . "%'"; break;
		case 'equal' 	: $firstnameFilter = "AND u.firstname = '" . $firstNameInput . "'"; break;
		}

		switch ($sexe){
		case 'all' 		: $sexeFilter = ""; break;
		case 'male' 	: $sexeFilter = "AND u.title = 'M'"; break;
		case 'female' 	: $sexeFilter = "AND u.title = 'Me'"; break;
		}

		if($memberId == 'equal') : $memberidFilter = "AND u.id = " . $memberIdInput; else : $memberidFilter = ""; endif;

		switch ($email){
		case 'all' 		: $emailFilter = ""; break;
		case 'contain' 	: $emailFilter = "AND u.email like '%" . $emailInput . "%'"; break;
		case 'equal' 	: $emailFilter = "AND u.email = '" . $emailInput . "'"; break;
		}

		switch ($zipcode){
		case 'all' 		: $zipcodeFilter = ""; break;
		case 'start' 	: $zipcodeFilter = "AND u.postal_code like '" . $zipcodeInput . "%'"; break;
		case 'equal' 	: $zipcodeFilter = "AND u.postal_code = '" . $zipcodeInput . "'"; break;
		}

		switch ($birthdate){
		case 'all' 		: $birthdateFilter = ""; break;
		case 'between' 	: $birthdateFilter = "AND (u.dob >= '" . $dobStart->format('Y-m-d') . "' AND u.dob <= '" . $dobEnd->format('Y-m-d') . "')"; break;
		case 'equal' 	: $birthdateFilter = "AND u.dob = '" . $dobEqual->format('Y-m-d') . "'"; break;
		}

		if($hobbies != NULL) {
		foreach($hobbies as $key => $hobby) : $hobbiesSql[] = '\''.$hobby.'\''; endforeach;
		$hobbies = implode(',',$hobbiesSql);
		$hobbiesFilter = "AND u.id IN (SELECT t FROM PlaygroundGame\Entity\PrizeCategoryUser c LEFT JOIN c.user t WHERE c.prizeCategory IN (" . $hobbies . ") GROUP BY c.user)";
		} else {
		$hobbiesFilter = "";
		}

		switch ($optin){
		case 'all' 		: $optinFilter = ""; break;
		case 'yes' 		: $optinFilter = "AND u.optin = 1"; break;
		case 'no' 		: $optinFilter = "AND u.optin = 0"; break;
		}

		switch ($optinPartner){
		case 'all' 		: $optinPartnerFilter = ""; break;
		case 'yes' 		: $optinPartnerFilter = "AND u.optinPartner = 1"; break;
		case 'no' 		: $optinPartnerFilter = "AND u.optinPartner = 0"; break;
		}

		if($suscribeDate == 'all') : $suscribeFilter = ""; else : $suscribeFilter = "AND (u.created_at >= '" . $suscribeStart->format('Y-m-d') . " 00:00:00' AND u.created_at <= '" . $suscribeEnd->format('Y-m-d') . " 00:00:00')"; endif;

		switch ($hardbounce){
		case 'all' 		: $hardbounceFilter = ""; break;
		case 'yes' 		: $hardbounceFilter = "AND u.isHardbounce = 1"; break;
		case 'no' 		: $hardbounceFilter = "AND u.isHardbounce = 0"; break;
		case 'between' 	: $hardbounceFilter = "AND u.isHardbounce = 1 AND (u.created_at >= '" . $hardbounceStart->format('Y-m-d') . " 00:00:00' AND u.created_at <= '" . $hardbounceEnd->format('Y-m-d') . " 00:00:00')"; break;
		}

		switch ($nbpart) {
		case 'all' 		: $nbpartFilter = ""; break;
		case 'betweennb': $nbpartFilter = "AND u.id IN (SELECT l FROM PlaygroundGame\Entity\Entry m LEFT JOIN m.user l GROUP BY m.user HAVING (COUNT(m.user) >= " . $nbpartMin . " AND COUNT(m.user) <= " . $nbpartMax . "))"; break;
		case 'between' 	: $nbpartFilter = "AND u.id IN (SELECT l FROM PlaygroundGame\Entity\Entry m LEFT JOIN m.user l WHERE (m.created_at >= '" . $nbpartStart->format('Y-m-d') . " 00:00:00' AND m.created_at <= '" . $nbpartEnd->format('Y-m-d') . " 00:00:00') GROUP BY m.user)"; break;
		}

		switch ($validatedemail){
		case 'all' 		: $validatedemailFilter = ""; break;
		case 'yes' 		: $validatedemailFilter = "AND u.state IS NOT NULL"; break;
		case 'no' 		: $validatedemailFilter = "AND u.state IS NULL"; break;
		}

		switch ($player){
		case 'all' 		: $playerFilter = ""; break;
		case 'bronze' 	: $playerFilter = "AND u.id IN (SELECT p FROM PlaygroundReward\Entity\Achievement h LEFT JOIN h.user p WHERE h.category = 'player' AND h.level = 1 GROUP BY h.user)"; break;
		case 'silver' 	: $playerFilter = "AND u.id IN (SELECT p FROM PlaygroundReward\Entity\Achievement h LEFT JOIN h.user p WHERE h.category = 'player' AND h.level = 2 GROUP BY h.user)"; break;
		case 'gold' 	: $playerFilter = "AND u.id IN (SELECT p FROM PlaygroundReward\Entity\Achievement h LEFT JOIN h.user p WHERE h.category = 'player' AND h.level = 3 GROUP BY h.user)"; break;
		}

		switch ($goldfather){
		case 'all' 		: $goldfatherFilter = ""; break;
		case 'bronze' 	: $goldfatherFilter = "AND u.id IN (SELECT s FROM PlaygroundReward\Entity\Achievement a LEFT JOIN a.user s WHERE a.category = 'goldfather' AND a.level = 1 GROUP BY a.user)"; break;
		case 'silver' 	: $goldfatherFilter = "AND u.id IN (SELECT s FROM PlaygroundReward\Entity\Achievement a LEFT JOIN a.user s WHERE a.category = 'goldfather' AND a.level = 2 GROUP BY a.user)"; break;
		case 'gold' 	: $goldfatherFilter = "AND u.id IN (SELECT s FROM PlaygroundReward\Entity\Achievement a LEFT JOIN a.user s WHERE a.category = 'goldfather' AND a.level = 3 GROUP BY a.user)"; break;
		}

		switch ($brain){
		case 'all' 		: $brainFilter = ""; break;
		case 'bronze' 	: $brainFilter = "AND u.id IN (SELECT b FROM PlaygroundReward\Entity\Achievement n LEFT JOIN n.user b WHERE n.category = 'brain' AND n.level = 1 GROUP BY n.user)"; break;
		case 'silver' 	: $brainFilter = "AND u.id IN (SELECT b FROM PlaygroundReward\Entity\Achievement n LEFT JOIN n.user b WHERE n.category = 'brain' AND n.level = 2 GROUP BY n.user)"; break;
		case 'gold' 	: $brainFilter = "AND u.id IN (SELECT b FROM PlaygroundReward\Entity\Achievement n LEFT JOIN n.user b WHERE n.category = 'brain' AND n.level = 3 GROUP BY n.user)"; break;
		}

		switch ($ambassador){
		case 'all' 		: $ambassadorFilter = ""; break;
		case 'bronze' 	: $ambassadorFilter = "AND u.id IN (SELECT d FROM PlaygroundReward\Entity\Achievement f LEFT JOIN f.user d WHERE f.category = 'ambassador' AND f.level = 1 GROUP BY f.user)"; break;
		case 'silver' 	: $ambassadorFilter = "AND u.id IN (SELECT d FROM PlaygroundReward\Entity\Achievement f LEFT JOIN f.user d WHERE f.category = 'ambassador' AND f.level = 2 GROUP BY f.user)"; break;
		case 'gold' 	: $ambassadorFilter = "AND u.id IN (SELECT d FROM PlaygroundReward\Entity\Achievement f LEFT JOIN f.user d WHERE f.category = 'ambassador' AND f.level = 3 GROUP BY f.user)"; break;
		}

		switch ($anniversary){
		case 'all' 		: $anniversaryFilter = ""; break;
		case 'bronze' 	: $anniversaryFilter = "AND u.id IN (SELECT g FROM PlaygroundReward\Entity\Achievement j LEFT JOIN j.user g WHERE j.category = 'anniversary' AND j.level = 1 GROUP BY j.user)"; break;
		case 'silver' 	: $anniversaryFilter = "AND u.id IN (SELECT g FROM PlaygroundReward\Entity\Achievement j LEFT JOIN j.user g WHERE j.category = 'anniversary' AND j.level = 2 GROUP BY j.user)"; break;
		case 'gold' 	: $anniversaryFilter = "AND u.id IN (SELECT g FROM PlaygroundReward\Entity\Achievement j LEFT JOIN j.user g WHERE j.category = 'anniversary' AND j.level = 3 GROUP BY j.user)"; break;
		}

		$query = $em->createQuery('
				SELECT u FROM PlaygroundUser\Entity\User u
				' . $lastnameFilter . '
				' . $firstnameFilter . '
				' . $sexeFilter . '
				' . $memberidFilter . '
				' . $emailFilter . '
				' . $zipcodeFilter . '
				' . $birthdateFilter . '
				' . $hobbiesFilter . '
				' . $optinFilter . '
				' . $optinPartnerFilter . '
				' . $suscribeFilter . '
				' . $hardbounceFilter . '
				' . $gameidFilter . '
				' . $nbpartFilter . '
				' . $validatedemailFilter . '
				' . $playerFilter . '
				' . $goldfatherFilter . '
				' . $brainFilter . '
				' . $ambassadorFilter . '
				' . $anniversaryFilter . '
					
				');
		*/

		//$sql = "SELECT distinct u.user_id, u.username, u.email";
		$sql = "SELECT distinct u.user_id, u.username, u.email, u.title, u.firstname, u.lastname, u.dob, u.address, u.address2, u.postal_code, u.city, u.telephone, u.mobile, u.optin_partner, u.registration_source, u.optin, u.created_at, u.state";

		/*
		 if($validatedemail != 'all'){
		$sql .= ', u.state';
		}

		if($birthdate != 'all'){
		$sql .= ', u.dob';
		}

		if($suscribeDate != 'all'){
		$sql .= ', u.created_at';
		}

		if($optin != 'all'){
		$sql .= ', u.optin';
		}
		*/

		if($hardbounce != 'all'){
			$sql .= ', u.date_set_hardbounce';
		}

		$sql .= " FROM user u";

		if(($gameId != '')||($nbpart == 'between')){
			$sql .= " INNER JOIN game_entry ge ON ge.user_id = u.user_id";
		}

		if($hobbies != NULL) {
			$sql .= " INNER JOIN game_prize_category_user pcu ON pcu.user_id = u.user_id";
		}

		if($player != 'all'){
			$sql .= " INNER JOIN reward_achievement ra1 ON ra1.user_id = u.user_id AND ra1.category = 'player'";
		}
		if($goldfather != 'all'){
			$sql .= " INNER JOIN reward_achievement ra2 ON ra2.user_id = u.user_id AND ra2.category = 'goldfather'";
		}
		if($brain != 'all'){
			$sql .= " INNER JOIN reward_achievement ra3 ON ra3.user_id = u.user_id AND ra3.category = 'brain'";
		}
		if($ambassador != 'all'){
			$sql .= " INNER JOIN reward_achievement ra4 ON ra4.user_id = u.user_id AND ra4.category = 'ambassador'";
		}
		if($anniversary != 'all'){
			$sql .= " INNER JOIN reward_achievement ra5 ON ra5.user_id = u.user_id AND ra5.category = 'anniversary'";
		}

		$sql .= " WHERE 1=1";

		switch ($lastName){
			case 'start' 	: $sql .= " AND u.lastname like '" . $lastNameInput . "%'"; break;
			case 'contain'  : $sql .= " AND u.lastname like '%" . $lastNameInput . "%'"; break;
			case 'equal' 	: $sql .=  " AND u.lastname = '" . $lastNameInput . "'"; break;
		}

		switch ($firstName){
			case 'start' 	: $sql .= " AND u.firstname like '" . $firstNameInput . "%'"; break;
			case 'contain' 	: $sql .= " AND u.firstname like '%" . $firstNameInput . "%'"; break;
			case 'equal' 	: $sql .= " AND u.firstname = '" . $firstNameInput . "'"; break;
		}

		switch ($sexe){
			case 'male' 	: $sql .= " AND u.title = 'M'"; break;
			case 'female' 	: $sql .= " AND u.title = 'Me'"; break;
		}

		if($memberId == 'equal'){
			$sql .= " AND u.user_id = " . $memberIdInput;
		}

		switch ($email){
			case 'contain' 	: $sql .= " AND u.email like '%" . $emailInput . "%'"; break;
			case 'equal' 	: $sql .= " AND u.email = '" . $emailInput . "'"; break;
		}

		switch ($zipcode){
			case 'start' 	: $sql .= " AND u.postal_code like '" . $zipcodeInput . "%'"; break;
			case 'equal' 	: $sql .= " AND u.postal_code = '" . $zipcodeInput . "'"; break;
		}

		switch ($birthdate){
			case 'between' 	: $sql .= " AND (u.dob >= '" . $dobStart->format('Y-m-d') . "' AND u.dob <= '" . $dobEnd->format('Y-m-d') . "')"; break;
			case 'equal' 	: $sql .= " AND u.dob = '" . $dobEqual->format('Y-m-d') . "'"; break;
		}


		switch ($optin){
			case 'yes' 		: $sql .= " AND u.optin = 1"; break;
			case 'no' 		: $sql .= " AND u.optin = 0"; break;
		}

		switch ($optinPartner){
			case 'yes' 		: $sql .= " AND u.optin_partner = 1"; break;
			case 'no' 		: $sql .= " AND u.optin_partner = 0"; break;
		}

		if($suscribeDate != 'all'){
			$sql .= " AND (u.created_at >= '" . $suscribeStart->format('Y-m-d') . " 00:00:00' AND u.created_at <= '" . $suscribeEnd->format('Y-m-d') . " 00:00:00')";
		}

		switch ($validatedemail){
			case 'yes' 		: $sql .= " AND u.state IS NOT NULL"; break;
			case 'no' 		: $sql .= " AND u.state IS NULL"; break;
		}




		if($gameId != ''){
			$sql .= " AND ge.game_id=$gameId";
		}
		if($nbpart == 'between'){
			$sql .= " AND ge.created_at >= '" . $nbpartStart->format('Y-m-d') . " 00:00:00' AND ge.created_at <= '" . $nbpartEnd->format('Y-m-d') . " 00:00:00'";
		}

		//à retirer si la performance est mauvaise
		if($nbpart == 'betweennb'){
			$sql .= " AND u.user_id IN (SELECT ge.user_id FROM game_entry ge LEFT JOIN user v ON (v.user_id = ge.user_id) GROUP BY ge.user_id HAVING (COUNT(ge.user_id) >= " . $nbpartMin . " AND COUNT(ge.user_id) <= " . $nbpartMax . "))";
		}



		if($hobbies != NULL) {
				
			foreach($hobbies as $key => $hobby) : $hobbiesSql[] = '\''.$hobby.'\''; endforeach;
				
			$hobbies = implode(',',$hobbiesSql);
			$sql .= " AND pcu.prize_category_id IN (" . $hobbies . ")";
		}

		switch ($player){
			case 'bronze' : $sql .= " AND ra1.level=1"; break;
			case 'silver' : $sql .= " AND ra1.level=2"; break;
			case 'gold' : $sql .= " AND ra1.level=3"; break;
		}

		switch ($goldfather){
			case 'bronze' : $sql .= " AND ra2.level=1"; break;
			case 'silver' : $sql .= " AND ra2.level=2"; break;
			case 'gold' : $sql .= " AND ra2.level=3"; break;
		}

		switch ($brain){
			case 'bronze' : $sql .= " AND ra3.level=1"; break;
			case 'silver' : $sql .= " AND ra3.level=2"; break;
			case 'gold' : $sql .= " AND ra3.level=3"; break;
		}

		switch ($ambassador){
			case 'bronze' : $sql .= " AND ra4.level=1"; break;
			case 'silver' : $sql .= " AND ra4.level=2"; break;
			case 'gold' : $sql .= " AND ra4.level=3"; break;
		}

		switch ($anniversary){
			case 'bronze' : $sql .= " AND ra5.level=1"; break;
			case 'silver' : $sql .= " AND ra5.level=2"; break;
			case 'gold' : $sql .= " AND ra5.level=3"; break;
		}

		$rsm = new ResultSetMapping();

		$query = $em->createNativeQuery($sql, $rsm);

		//echo $sql;

		$method = new \ReflectionMethod($query, '_doExecute');
		$method->setAccessible(true);
		$result = $method->invokeArgs($query, array());
		//$result = $query->execute(array(),\Doctrine\ORM\Query::HYDRATE_ARRAY);
		return $result;
	}

	/**
	 * Return count of shares by $type according to $user
	 * @param number|unknown $type
	 * @param number|unknown $user
	 */
	public function getSharesByUser($type, $user)
	{
		$em = $this->getServiceManager()->get('doctrine.entitymanager.orm_default');

		switch ($type){
			case 'shares' :
				$filter = "e.action IN (13,14,15,16,17) AND e.label like '%espace client%'";
				break;
			case 'fbShares' :
				$filter = "e.action IN (14,15) AND e.label like '%espace client%'";
				break;
			case 'twShares' :
				$filter = "e.action=16 AND e.label like '%espace client%'";
				break;
			case 'glShares' :
				$filter = "e.action=17 AND e.label like '%espace client%'";
				break;
			case 'mailShares' :
				$filter = "e.action=13 AND e.label like '%espace client%'";
				break;
		}

		$query = $em->createQuery('
			SELECT COUNT(e.id) FROM PlaygroundReward\Entity\Event e
			WHERE e.user = :user
			AND ' . $filter . '
		');
		$query->setParameter('user', $user);
		$count = $query->getSingleScalarResult();
		return $count;
	}

	public function getPrizeCategoriesByUser($user, $hobbies)
	{
		$em = $this->getServiceManager()->get('doctrine.entitymanager.orm_default');

		$sql = "SELECT pcu.prize_category_id, gpc.title FROM game_prize_category_user pcu";
		$sql .= " INNER JOIN game_prize_category gpc ON pcu.prize_category_id = gpc.id";
		$sql .= " WHERE pcu.user_id = " . $user;

		if($hobbies != NULL) {
			foreach($hobbies as $key => $hobby) : $hobbiesSql[] = '\''.$hobby.'\''; endforeach;
			$hobbies = implode(',',$hobbiesSql);
			$sql .= " AND pcu.prize_category_id IN (" . $hobbies . ")";
		}

		$rsm = new ResultSetMapping();
		$query = $em->createNativeQuery($sql, $rsm);
		//echo $sql;

		$method = new \ReflectionMethod($query, '_doExecute');
		$method->setAccessible(true);
		$result = $method->invokeArgs($query, array());
		return $result;
	}

	public function getNumberEntriesByUser($user, $data)
	{
		$nbpart			= $data['nbpart'];
		$nbpartStartIni = $data['nbpart-start'];
		$nbpartEndIni 	= $data['nbpart-end'];
		$nbpartStart 	= \DateTime::createFromFormat('d/m/Y', $nbpartStartIni);
		$nbpartEnd 		= \DateTime::createFromFormat('d/m/Y', $nbpartEndIni);

		$em = $this->getServiceManager()->get('doctrine.entitymanager.orm_default');

		$sql = '';
		if( $nbpart == 'between' ){
			$sql = " AND e.created_at >= '" . $nbpartStart->format('Y-m-d') . " 00:00:00' AND e.created_at <= '" . $nbpartEnd->format('Y-m-d') . " 00:00:00'";
		}

		$query = $em->createQuery('
        	SELECT COUNT(e.id) FROM PlaygroundGame\Entity\Entry e
        	WHERE e.user = :user
        	' . $sql . '
        ');
		$query->setParameter('user', $user);
		$count = $query->getSingleScalarResult();
		return $count;
	}

	/**
	 * Retrieve service manager instance
	 *
	 * @return ServiceManager
	 */
	public function getServiceManager()
	{
		return $this->serviceManager;
	}

	/**
	 * Set service manager instance
	 *
	 * @param  ServiceManager $locator
	 * @return Action
	 */
	public function setServiceManager(ServiceManager $serviceManager)
	{
		$this->serviceManager = $serviceManager;

		return $this;
	}

}
