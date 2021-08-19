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
        $file = __DIR__."/../../temp/csv/resultats_users.csv";
        $data = $this->resultUser->readData($file);
        $dates = $this->dateParser([
            '01/01/2021', 
            '30/04/2021', 
            '01/05/2021', 
            '31/08/2021', 
            '01/10/2021', 
            '31/12/2021'
        ]);
        
        $points = [0, 0 , 0];
        foreach($data as $index => $item_array) {
            $date = Carbon::parse(str_replace('/', '-', $item_array["date"]));
            // P1
            if( $date > $dates[0] && $date < $dates[1] ) {
                $points[0] += $this->getPointsByPeriod(
                    $item_array["produit_1"], 
                    $item_array["produit_2"], 
                    $item_array["produit_3"], 
                    $item_array["produit_4"]
                );
            }
            // P2
            else if( $date > $dates[2] && $date < $dates[3] ) {
                $points[1] += $this->getPointsByPeriod(
                    $item_array["produit_1"], 
                    $item_array["produit_2"], 
                    $item_array["produit_3"], 
                    $item_array["produit_4"]
                );
            }
            // P3
            else if( $date > $dates[4] && $date < $dates[5] ) {
                $points[2] += $this->getPointsByPeriod(
                    $item_array["produit_1"], 
                    $item_array["produit_2"], 
                    $item_array["produit_3"], 
                    $item_array["produit_4"]
                );
            }
        } unset($item_array);

        $euros  = [];
        foreach($points as $point) {
            $euros[] = $point * 0.001;
        } unset($point);

        for($i=0; $i<3; $i++) {
            $data_array[$i]['period'] = "Period " . ($i+1);
        }

        foreach($data_array as $index => &$item_array) {
            $item_array['points'] = $points[$index];
            $item_array['euros']  = $euros[$index];
        } unset($item_array);
        
        return $this->render('resultats/index.html.twig', [
            'data' => $data_array
        ]);
    }

    private function dateParser(array $arr): array {
        foreach($arr as &$item) {
            $item = Carbon::parse(str_replace('/', '-', $item));
        } unset($item);

        return $arr;
    }

    private function getPointsByPeriod(int $product1, int $product2, int $product3, int $product4): int {
        $tot =  ($product1 * 5);
        $tot += ($product2 * 5);
        $tot += ($product3 * 15);
        $tot += ($product4 * 35);
        
        return $tot;
    }
}