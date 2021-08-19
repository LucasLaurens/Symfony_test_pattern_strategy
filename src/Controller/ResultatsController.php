<?php

namespace App\Controller;

use App\Strategy\ResultUserInterface;
use Carbon\Carbon;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ResultatsController extends AbstractController
{
    private $resultUser;
  
    public function __construct(ResultUserInterface $resultUser)
    {
        $this->resultUser = $resultUser;
    }

    /**
     * @Route("/resultats", name="resultats")
     */
    public function index(): Response
    {
        $euros     = [];
        $dataArray = [];
        $points    = [0, 0 , 0];
        $file      = __DIR__."/../../temp/csv/resultats_users.csv";

        $data      = $this->resultUser->readData($file);
        $dates     = $this->resultUser->dateParser(['01/01/2021', '30/04/2021', '01/05/2021', '31/08/2021', '01/10/2021', '31/12/2021']);
        $points    = $this->resultUser->getPointsByPeriod($data, $dates, $points);
        $euros     = $this->resultUser->getEurosByPeriod($points, $euros); 
        $dataArray = $this->resultUser->getPeriods($dataArray);
        $dataArray = $this->resultUser->addLastValuesInFinalArray($dataArray, $points, $euros);
        $user      = $this->resultUser->getUser($data);

        return $this->render('resultats/index.html.twig', [
            'user' => $user,
            'data' => $dataArray
        ]);
    }
}