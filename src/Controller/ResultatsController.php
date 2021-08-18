<?php

namespace App\Controller;

use App\Strategy\ResultUser;
use Carbon\Carbon;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ResultatsController extends AbstractController
{
    private $resultUser;
  
    public function __construct(ResultUser $resultUser)
    {
        $this->resultUser = $resultUser;
    }

    /**
     * @Route("/resultats", name="resultats")
     */
    public function index(): Response
    {
        $file = __DIR__."/../../temp/csv/resultats_users.csv";
        $data = $this->resultUser->read($file);
        $dates = $this->dateParser(['01/01/2021', '30/04/2021', '01/05/2021', '31/08/2021', '01/10/2021', '31/12/2021']);
        
        $p1_pts=0;
        $p2_pts=0;
        $p3_pts=0;
        foreach($data as $item_array) {
            $date = Carbon::parse(str_replace('/', '-', $item_array["date"]));

            // P1
            if( $date > $dates[0] && $date < $dates[1] ) {
                $p1_pts += ($item_array["produit_1"] * 5);
                $p1_pts += ($item_array["produit_2"] * 5);
                $p1_pts += ($item_array["produit_3"] * 15);
                $p1_pts += ($item_array["produit_4"] * 35);
            }
            // P2
            else if( $date > $dates[2] && $date < $dates[3] ) {
                $p2_pts += ($item_array["produit_1"] * 5);
                $p2_pts += ($item_array["produit_2"] * 5);
                $p2_pts += ($item_array["produit_3"] * 15);
                $p2_pts += ($item_array["produit_4"] * 35);
            }
            // P3
            else if( $date > $dates[4] && $date < $dates[5] ) {
                $p3_pts += ($item_array["produit_1"] * 5);
                $p3_pts += ($item_array["produit_2"] * 5);
                $p3_pts += ($item_array["produit_3"] * 15);
                $p3_pts += ($item_array["produit_4"] * 35);
            }
        } unset($item_array);

        $p1_euros = ($p1_pts * 0.001);
        $p2_euros = ($p2_pts * 0.001);
        $p3_euros = ($p3_pts * 0.001);

        $data_array[0]['period'] = "Period 1";
        $data_array[0]['points'] = $p1_pts;
        $data_array[0]['euros'] = $p1_euros;

        $data_array[1]['period'] = "Period 2";
        $data_array[1]['points'] = $p2_pts;
        $data_array[1]['euros'] = $p2_euros;

        $data_array[3]['period'] = "Period 3";
        $data_array[3]['points'] = $p3_pts;
        $data_array[3]['euros'] = $p3_euros;
    
        dd($data_array);
        
        return $this->render('resultats/index.html.twig', [
            'data' => $data
        ]);
    }

    private function dateParser(array $arr): array {
        foreach($arr as &$item) {
            $item = Carbon::parse(str_replace('/', '-', $item));
        } unset($item);

        return $arr;
    }
}