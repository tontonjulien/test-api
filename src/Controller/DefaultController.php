<?php


namespace App\Controller;


use App\Helper\EventHelper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController
{
    /**
     * @Route("/", name="home", methods={"GET", "POST"})
     * @param Request $request
     * @param EventHelper $eventHelper
     * @return Response
     */
    public function index(Request $request, EventHelper $eventHelper): Response
    {
        $rawData = $request->getContent();
        $formattedData = $eventHelper->getEventsByHour($rawData);
        $multipliedData = $eventHelper->addMultiplierColumn($formattedData);
        $eventHelper->arrayToCsv($multipliedData);

        $response = new Response();
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="testing.csv"');

        return $response;
    }
}
