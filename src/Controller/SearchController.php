<?php

namespace App\Controller;

use App\Repository\AvisRepository;
use App\Repository\TrajetRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class SearchController extends AbstractController
{

    #[Route('/search/index', name: 'app_search_index')]
    public function index(): Response
    {
        return $this->render('search/index.html.twig');
    }


    #[Route('/search', name: 'app_search')]
    public function search(Request $request, TrajetRepository $trajet_repository): Response
    {

        $from = $request->query->get('depart', '');
        $to = $request->query->get('arrivee', '');
        $date = new \DateTime($request->query->get('date', 'now'));

        $eco = $request->query->getBoolean('eco');

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

        $nextDate = null;
        if (empty($trajets)) {
            $nextDate = $trajet_repository->findNextAvailableTripDate($from, $to, $date);
        }


        return $this->render('search/results.html.twig', [
            'trajets' => $trajets,
            'nextDate' => $nextDate,
        ]);
    }



    #[Route('/trajet/{id}', name: 'app_trajet_detail', requirements: ['id' => '\d+'])]
    public function detail(int $id, TrajetRepository $repo, AvisRepository $avisRepo): Response
    {

        $trip = $repo->findTripById($id);
        if (empty($trip)) {
            throw $this->createNotFoundException("Trajet #$id introuvable");
        }

        $reviews = $repo->getTripReviews($id);
        $chauffeurId = $trip['chauffeur_id'];
        $reviews = $avisRepo->findAvisByChauffeur($chauffeurId);
        $preferences = $repo->getDriverPreferences($trip['chauffeur_id']);
        $avgRating = $repo->getDriverAverageRating($trip['chauffeur_id']);
        $reviewsCount = count($reviews);

        dump($reviews);

        return $this->render('search/details.html.twig', [
            'trip' => $trip,
            'reviews' => $reviews,
            'preferences' => $preferences,
            'avgRating' => $avgRating,
            'reviewCount' => $reviewsCount,
        ]);
    }
}
