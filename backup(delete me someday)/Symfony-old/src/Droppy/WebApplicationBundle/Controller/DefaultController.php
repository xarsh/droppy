<?php

namespace Droppy\WebApplicationBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Droppy\WebApplicationBundle\Entity\Event;
use Droppy\WebApplicationBundle\Form\Type\MakeEventIncludeFormType;
use Droppy\WebApplicationBundle\Form\Handler\MakeEventFormHandler;

//TODO divide controllers
class DefaultController extends Controller
{
	public function getScheduleInformation($dt)
	{
		$dt = new \DateTime($dt);
		$dt->modify('first day of this month');

		$blank = ($dt->format('w') + 6) % 7;
		$prev = $dt->modify("-1 month")->format('Y-m');
		$daysOfLastMonth = $dt->format('t');
		$next = $dt->modify("+2 month")->format('Y-m');
		$dt = $dt->modify("-1 month");

		return array('dt' => $dt,
			     'blank' => $blank,
                             'start_date' => $daysOfLastMonth - $blank + 1,
			     'prev' => $prev,
			     'next' => $next);
	}

	public function getMainScheduleAction($date)
	{
		return $this->render('DroppyWebApplicationBundle:Includes/index_logged_center:main_schedule.html.twig', array(
                                     'schedule_information' => $this->getScheduleInformation($date)));
	}

	public function indexAction($date)
	{
		$em = $this->getDoctrine()->getEntityManager();

		$user = $this->get('security.context')->getToken()->getUser();

		$form = $this->get('droppy_webapp.make_event_include.form');
		$formHandler = $this->get('droppy_webapp.make_event.form.handler');
		if($formHandler->process($user))
		{
			return $this->redirect($this->generateUrl('droppy_webapp_index'));
		}

		$day = array();
		$month = array();
		$all = array();
		$popularSchedules = array('day' => $day, 'month' => $month, 'all' => $all);

		$recommendedSchedules = $em->getRepository('DroppyWebApplicationBundle:Schedule')
			->getRecommendedSchedules();	

		$officialSchedules = array(); //$em->getRepository('DroppyWebApplicationBundle:Schedule')->getOfficialSchedules();
		
		return $this->render('DroppyWebApplicationBundle:Logged:index.html.twig',array(
					'user' => $user,
					'personal_datas' => $user->getPersonalDatas(),
					'drop_events' => $user->getDroppedEventsEntities(),
					'drop_schedules' => $user->getDroppedSchedulesEntities(),
					'my_schedules' => $user->getCreatedSchedules(),
					'main_schedule' => $user->getDroppedSchedules(),
					'schedule_information' => $this->getScheduleInformation($date),
					'popular_schedules' => $popularSchedules,
					'recommended_schedules' => $recommendedSchedules,
					'official_schedules' => $officialSchedules,
					'form' => $form->createView()
					));
	}
	
	public function topAction()
	{
		$em = $this->getDoctrine()->getEntityManager();

		$recommended_schedules = $em->getRepository('DroppyWebApplicationBundle:Schedule')
			->getRecommendedSchedules();	

		$official_schedules = $em->getRepository('DroppyWebApplicationBundle:Schedule')
			->getOfficialSchedules();	

		$new_schedules = $em->getRepository('DroppyWebApplicationBundle:Schedule')
			->getNewSchedules();

		//		$tags = $em->getRepository('WebApplicationBundle:Tag')
		//			->getTags();
		//
		//		$genres = $em->getRepository('WebApplicationBundle:Genre')
		//			->getGenres();

		return $this->render('DroppyWebApplicationBundle:Unlogged:index.html.twig', array(
					'recommended_schedules' => $recommended_schedules,
					'official_schedules' => $official_schedules,
					'new_schedules' => $new_schedules,
					'tags' => array(),
					'genres' => array()));
	}

	public function dropScheduleAction()
	{
		$em = $this->getDoctrine()->getEntityManager();

		$schedules = $em->getRepository('DroppyWebApplicationBundle:Schedule')
			->getPopularSchedules(21);

		return $this->render('DroppyWebApplicationBundle:Unlogged:drop_schedule.html.twig', array(
					'schedules' => $schedules));
	}
	
	public function facebookDropAction()
	{
		$em = $this->getDoctrine()->getEntityManager();

		$schedules = $em->getRepository('DroppyWebApplicationBundle:Schedule')
			->getPopularSchedules(21);	

		return $this->render('DroppyWebApplicationBundle:Unlogged:facebookdrop.html.twig', array(
					'schedules' => $schedules));
	}
}
