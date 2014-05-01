<?php

namespace App\Controller;

use App\Entity\TrackEvent;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CommonController extends Controller
{


	public function appGlobalsAction(Request $request)
	{

		$uri = str_replace($request->getBaseUrl() . '/', '', $request->getRequestUri());
		$uri = str_replace(strrchr($uri, '.'), '', $uri);

		$response = new Response('', 200, array(
			'Content-type' => 'text/javascript'
		));

		return $this->render('App:Common:appGlobals.html.twig', array(
			'uri' => $uri,
			'data' => array(
				'debug' => $this->getParameter('kernel.debug'),
				'base-url' => $request->getBaseUrl() . '/',
				'instagram-client-id' => $this->getParameter('instagram_client_id')
			)), $response);
	}

	public function homepageAction()
	{
		return array(
			'instagram_client_id' => $this->getParameter('instagram_client_id'),
			'ga_tracking' => $this->getParameter('ga_tracking')
		);
	}

	public function profileAction()
	{
		return array();
	}

	public function imageProxyAction(Request $request)
	{
		$fileUrl = $request->query->get('url');

		if (preg_match('/http:\/\/distillery.+amazon.+com\/.+jpg$/', $fileUrl)
			|| preg_match('/http:\/\/origincache\/.+jpg$/', $fileUrl)) {

			$headers = [
				'Accept:text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
				'Accept-Encoding:gzip,deflate,sdch',
				'Cache-Control:no-cache',
				'Pragma:no-cache',
//				'Connection' => 'close',
				'User-Agent:Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Ubuntu Chromium/33.0.1750.152 Chrome/33.0.1750.152 Safari/537.36'
			];

			$opts = array(
				'http' => array(
					'method' => "GET",
					'header' => implode("\r\n", $headers)
				)
			);

			$context = stream_context_create($opts);

			$fileData = file_get_contents($fileUrl, false, $context);

			$response = new Response($fileData, 200, [
				'Content-type' => 'image/jpeg',
				'Accept-Ranges' => 'bytes',
//				'Connection:',
				'Connection' => 'keep-alive',
				'ETag' => sha1($fileUrl),
				'Cache-Control' => 'max-age=290304000, public'
			]);

			$response->headers->addCacheControlDirective('max-age', 290304000);
			$response->headers->addCacheControlDirective('public', true);

			return $response;
		}

		throw $this->createNotFoundException('Resource not found');

	}

	public function trackEventAction(Request $request) {

		$event = new TrackEvent();

		/** @var Form $form */
		$form = $this->createFormBuilder($event, [ 'csrf_protection' => false ])
			->add('category', 'text')
			->add('action', 'text')
			->add('value', 'number')
			->getForm();

		$form->handleRequest($request);

		if ($form->isValid()) {
			$this->persist($event, true);
			$this->addFlash('success');

		}

		return new Response();
	}

	public function imagesCountTextAction() {

		/** @var QueryBuilder $qb */
		$qb = $this->getRepository('App:TrackEvent')
			->createQueryBuilder('te');

		$value = $qb->select('SUM(te.value) as value')
			->getQuery()->getSingleScalarResult();

		return $this->render('App:Common:imagesCountText.html.twig', [
			'count' => $value
		]);
	}
} 