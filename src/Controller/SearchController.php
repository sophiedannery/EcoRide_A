<?php

namespace App\Controller;

use App\Repository\TrajetRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class SearchController extends AbstractController
{
    #[Route('/search', name: 'app_search')]
    public function search(Request $request, TrajetRepository $trajet_repository): Response
    {

        $from = $request->query->get('depart', '');
        $to = $request->query->get('arrivee', '');
        $date = new \DateTime($request->query->get('date', 'now'));

        $ecoParam = $request->query->get('eco');
        $eco = $ecoParam !== null;

        $maxPriceParam = $request->query->get('maxPrice');
        $maxPrice = ($maxPriceParam !== null && $maxPriceParam !== '') ? (int) $maxPriceParam : null;

        $durationTimeParam = $request->query->get('maxDurationTime');
        if ($durationTimeParam !== null && $durationTimeParam !== '') {
            $dt = \DateTime::createFromFormat('H:i', $durationTimeParam);
            if ($dt) {
                $maxDuration = ((int) $dt->format('H')) * 60 + ((int) $dt->format('i'));
            } else {
                $maxDuration = null;
            }
        } else {
            $maxDuration = null;
        }

        $minRatingParam = $request->query->get('minRating');
        $minRating = ($minRatingParam !== null && $minRatingParam !== '') ? (float) $minRatingParam : null;



        $trajets = $trajet_repository->searchTrips($from, $to, $date, $eco, $maxPrice, $maxDuration, $minRating);

        // $nextDate = null;
        // if (empty($trajets)) {
        //     $nextDate = $trajet_repository->findNextAvailableTripDate($from, $to, $date);
        // }


        return $this->render('search/results.html.twig', [
            'trajets' => $trajets,
            // 'nextDate' => $nextDate,
        ]);
    }
}
