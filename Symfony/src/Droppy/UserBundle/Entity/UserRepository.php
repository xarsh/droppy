<?php

namespace Droppy\UserBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Droppy\UserBundle\Entity\User;
use Droppy\EventBundle\Entity\Event;
use Droppy\UserBundle\Entity\UserDropsUser;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * UserRepository
 */
class UserRepository extends EntityRepository
{
	public function getUser(User $user)
	{
		$qb = $this->getEntityManager()->createQueryBuilder();
		$qb->select('u')
           ->from('DroppyUserBundle:User', 'u')
           ->join('u.personalDatas', 'p')
           ->addSelect('p')
           ->join('p.iconSet', 'i')
           ->addSelect('i')
           ->join('u.settings', 's')
           ->addSelect('s')
           ->join('s.privacySettings', 'ps')
           ->addSelect('ps')
           ->leftJoin('ps.authorizedUsers', 'au')
           ->addSelect('au')
           ->join('s.timezone', 't')
           ->addSelect('t')
           ->join('s.language', 'l')
           ->addSelect('l')
           ->join('s.color', 'c')
           ->addSelect('c')
           ->where('u = :user')
           ->setParameter('user', $user);
		$query = $qb->getQuery();
		return $query->getSingleResult();
	}
	
    public function getDroppingUsers(User $user, $offset = 0, $maxResults=20)
	{
        $query = $this->getEntityManager()->createQuery('
			SELECT r, current_user, dropped_user, dropped_user_droppers_r, dropped_user_dropper
			FROM DroppyUserBundle:UserDropsUserRelation r
			JOIN r.dropping current_user
			JOIN r.dropped dropped_user
			LEFT JOIN dropped_user.droppers dropped_user_droppers_r
			LEFT JOIN dropped_user_droppers_r.dropping dropped_user_dropper
			WHERE current_user = :user
            AND user.locked = false
			ORDER BY r.date DESC
                ')->setParameter('user', $user);
        $query->setFirstResult($offset)->setMaxResults($maxResults);
        $paginator = new Paginator($query, $fetchJoinCollection = true);
        $results = new ArrayCollection(iterator_to_array($paginator));
        return $results->map(function(UserDropsUserRelation $rel) {
        	return $rel->getDropped();
        });
		
    }

	public function getDroppers(User $user, $offset = 0, $maxResults=20)
	{
        $query = $this->getEntityManager()->createQuery('
			SELECT r, current_user, dropping_user, dropping_user_droppers_r, dropping_user_dropper
			FROM DroppyUserBundle:UserDropsUserRelation r
			JOIN r.dropped current_user
			JOIN r.dropping dropping_user
			LEFT JOIN dropping_user.droppers dropping_user_droppers_r
			LEFT JOIN dropping_user_droppers_r.dropping dropping_user_dropper
			WHERE current_user = :user
			ORDER BY r.date DESC
                ')->setParameter('user', $user);
        $query->setFirstResult($offset)->setMaxResults($maxResults);
        $paginator = new Paginator($query, $fetchJoinCollection = true);
        $results = new ArrayCollection(iterator_to_array($paginator));
        return $results->map(function(UserDropsUserRelation $rel) { 
        	return $rel->getDropping(); 
        });
    }

	public function getPopularUsers(User $user = null, $offset = 0, $maxResults = 20)
	{
		$query = $this->getEntityManager()->createQuery('
		        SELECT u
		        FROM DroppyUserBundle:User u
		        WHERE u.id != (:id)
		        AND u.locked = false 
		        ORDER BY u.droppersNumber DESC
		        ')->setParameter('id', $user === null ? -1 : $user->getId());
		$query->setFirstResult($offset)->setMaxResults($maxResults);
	    return new ArrayCollection($query->getResult());
	}
	
	public function getSelectedUsers($offset = 0, $maxResults = 20)
	{
		$query = $this->getEntityManager()->createQuery("
		        SELECT u
		        FROM DroppyUserBundle:User u
                WHERE u.username IN ('startup_event',
                    'nadeshiko_japan',
                    'gakusai_matome',
                    'tokyo_hanabi',
                    'waseda_seikei',
                    'shukatsu_2013',
                    'gakusei_party',
                    'busicon_matome')
		        ORDER BY u.droppersNumber DESC
		        ");
		$query->setFirstResult($offset)->setMaxResults($maxResults);
	    return new ArrayCollection($query->getResult());
	}

    
	public function getAetelRecommendedUsers($offset = 0, $maxResults = 20)
	{
        $maxResults = 50;
		$query = $this->getEntityManager()->createQuery("
		        SELECT u
		        FROM DroppyUserBundle:User u
                WHERE u.username IN (
                    'gorin_rikujo',
                    'internship_info',
                    'shukatsu_2013',
                    'kigyo_kessan',
                    'london_judo',
                    'london_tennis',
                    'music_fes',
                    'giants_unofficial',
                    'soccer_samurai',
                    'machicon_matome',
                    'sansoken',
                    'shukatsu_2013',
                    'ruby_tokyo',
                    'gakusai_matome',
                    'cambrian_unofficial',
					'preorder1341809774',
					'preorder1341927581',
					'preorder1341927658',
					'preorder1341927837',
					'preorder1341928150',
                    'UEFA_C_Leage2012',
                    'tokyo_international_party',
                    'gakusei_party',
                    'ametalk_unofficial',
                    'asean_kung_fu_unofficial',
                    'gosetsu_kansai',
                    'ishikawa_ryo',
                    'nadeshiko_japan',
                    'gorin_takkyu_joshi',
                    'chiba_hanabi',
                    'gaia_unofficial',
                    'manU',
                    'urawa_reds',
                    'shingeki_kyojin',
                    'onepiece_shinkan',
                    'gorin_takkyu_danshi',
                    'waseda_icc',
                    'wasesho',
                    'waseda_seikei',
                    'waseda_riko',
                    'atnd1340608753',
                    'atnd1340598032',
                    'otoya_unofficial',
                    'matsuya_unofficial',
                    'yamadadenki_chirashi',
                    'atnd1340693884',
                    'atnd1340693205',
                    'atnd1340618006',
                    'atnd1340616575',
                    'atnd1340617718',
                    'startup_event',
                    'atnd1340617440',
                    'atnd1340616328',
                    'atnd1340694225',
                    'atnd1340617182'
                ) 
                ORDER BY u.droppersNumber DESC
		        ");
		$query->setFirstResult($offset)->setMaxResults($maxResults);
	    return new ArrayCollection($query->getResult());
	}

	public function getIsobeSelectedUsers($offset = 0, $maxResults = 20)
	{
		$query = $this->getEntityManager()->createQuery("
		        SELECT u
		        FROM DroppyUserBundle:User u
                WHERE u.username IN ('startup_event',
                    'gorin_taiso',
                    'nadeshiko_japan',
                    'ametalk_unofficial',
                    'tokyo_international_party',
                    'gakusai_matome',
                    'tokyo_hanabi',
                    'atnd1340608753',
                    'manU',
                    'urawa_reds',
                    'gorin_takkyu_danshi',
                    'tokyo_natsumatsuri',
                    'gorin_rikujo',
                    'shukatsu_2013',
                    'kigyo_kessan',
                    'london_judo',
                    'london_tennis',
                    'music_fes',
                    'shukatsu_2013',
                    'machicon_matome',
                    'gakusei_party',
                    'busicon_matome')
		        ORDER BY u.droppersNumber DESC
		        ");
		$query->setFirstResult($offset)->setMaxResults($maxResults);
	    return new ArrayCollection($query->getResult());
	}

	public function isLikingEvent(User $user, Event $event)
	{
		$query = $this->getEntityManager()->createQuery('
		    			SELECT u.id, e.id
		    			FROM DroppyUserBundle:User u
		    			JOIN u.likedEvents e
		    			WHERE e.id = :id
		    		')->setParameter('id', $event->getId());
		return $query->getOneOrNullResult() !== null;
	}
	
	public function getRelation(User $dropping, User $dropped)
	{
		$query = $this->getEntityManager()->createQuery('
				SELECT r, dg, dd
				FROM DroppyUserBundle:UserDropsUserRelation r
				JOIN r.dropping dg
				JOIN r.dropped dd
				WHERE dg = :dropping
				AND dd = :dropped
			')->setParameter('dropping', $dropping)->setParameter('dropped', $dropped);
		$results = $query->getResult();
		if(count($results) === 0) 
		{
		    return null;
		}
		else if(count($results) === 1)
		{
		    return $results[0];
		}
		else
		{
		    return $results;
		}
		//return $query->getOneOrNullResult();
	} 

    public function searchUsers($keywords, $places, $offset=0, $maxResults=20)
    {   
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('u')
           ->from('DroppyUserBundle:User', 'u')
           ->join('u.personalDatas', 'pd')
           ->addSelect('pd');

        foreach(explode(' ', $keywords . ' ' . $places) as $keyword) {
            if($keyword == ' ' || strlen($keyword) < 1) continue;
            $qb->andWhere($qb->expr()->orx(
                $qb->expr()->like("u.username", $qb->expr()->literal('%' . $keyword . '%')),
                $qb->expr()->like("pd.displayedName", $qb->expr()->literal('%' . $keyword . '%')),
                $qb->expr()->like("pd.introduction", $qb->expr()->literal('%' . $keyword . '%'))
            ));
        }

        $qb->andWhere('u.locked = false')
           ->orderBy('u.lastLogin', 'DESC');
        $query = $qb->getQuery();
        $query->setFirstResult($offset)->setMaxResults($maxResults);
		
        return new ArrayCollection($query->getResult());
    }
}
