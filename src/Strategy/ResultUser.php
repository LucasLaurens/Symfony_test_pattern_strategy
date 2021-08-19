<?php   

namespace App\Strategy;

use App\Strategy\ResultUserInterface;
use Carbon\Carbon;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class ResultUser implements ResultUserInterface
{    
    /**
     * Retrieve the final data which are euros and points per period
     */
    public function getData(array $data): array
    {
        $euros     = [];
        $dataArray = [];
        $points    = [0, 0, 0];

        $dates     = $this->dateParser(['01/01/2021', '30/04/2021', '01/05/2021', '31/08/2021', '01/10/2021', '31/12/2021']);
        $points    = $this->getPointsByPeriod($data, $dates, $points);
        $euros     = $this->getEurosByPeriod($points, $euros); 
        $dataArray = $this->getPeriods($dataArray);
        
        return $this->addLastValuesInFinalArray($dataArray, $points, $euros);
    }


    /**
     * Read the csv data
     */
    public function readData(string $file): array
    {
        $serializer    = new Serializer([new ObjectNormalizer()], [new CsvEncoder()]);
        $fileString    = file_get_contents($file);
        $fileExtension = pathinfo($file, PATHINFO_EXTENSION);
        $data          = $serializer->decode($fileString, $fileExtension, [CsvEncoder::DELIMITER_KEY => ';']);

        foreach( $data as &$itemArray ) {
            $itemArray = array_combine(
                array_map(
                    function ($str) {
                        return strtolower(str_replace(" ", "_", $str));
                    },
                    array_keys($itemArray)
                ),
                array_values($itemArray)
            );
        } unset($itemArray);

        return $data ?? [];
    }

    /**
     * Retrieve the current user
     */
    public function getUser(array $data): string
    {
        if(isset($data[0]["utilisateur"]) && !empty($data[0]["utilisateur"])) {
            return $data[0]["utilisateur"];
        }

        return "";
    }

    /**
     * Have the number of points per given period
     */
    private function getPointsByPeriod(array $data, array $dates, array $points): array
    {
        foreach($data as $itemArray) {
            $date = Carbon::parse(str_replace('/', '-', $itemArray["date"]));
            
            // P1
            if( $date > $dates[0] && $date < $dates[1] ) {
                $points[0] += $this->getTotalPoints(
                    $itemArray["produit_1"], 
                    $itemArray["produit_2"], 
                    $itemArray["produit_3"], 
                    $itemArray["produit_4"]
                );
            }
            // P2
            else if( $date > $dates[2] && $date < $dates[3] ) {
                $points[1] += $this->getTotalPoints(
                    $itemArray["produit_1"], 
                    $itemArray["produit_2"], 
                    $itemArray["produit_3"], 
                    $itemArray["produit_4"]
                );
            }
            // P3
            else if( $date > $dates[4] && $date < $dates[5] ) {
                $points[2] += $this->getTotalPoints(
                    $itemArray["produit_1"], 
                    $itemArray["produit_2"], 
                    $itemArray["produit_3"], 
                    $itemArray["produit_4"]
                );
            }
        } unset($itemArray);

        return $points;
    }

    /**
     * Have the number of euros per given period
     */
    private function getEurosByPeriod(array $points, array $euros): array
    {
        foreach($points as $point) {
            $euros[] = $point * 0.001;
        } unset($point);

        return $euros;
    }

    /**
     * Add the three periods to the final table
     */
    private function getPeriods(array $dataArray): array
    {
        for($i=0; $i<3; $i++) {
            $dataArray[$i]['period'] = "Period " . ($i+1);
        }
        
        return $dataArray;
    }

    /**
     * Add the key/value model to the final data table
     */
    private function addLastValuesInFinalArray(array $dataArray, array $points, array $euros): array
    {
        foreach($dataArray as $index => &$itemArray) {
            $itemArray['points'] = $points[$index];
            $itemArray['euros']  = $euros[$index];
        } unset($itemArray);

        return $dataArray;
    }

    /**
     * Get the correct date format
     */
    private function dateParser(array $arr): array {
        foreach($arr as &$item) {
            $item = Carbon::parse(str_replace('/', '-', $item));
        } unset($item);

        return $arr;
    }

    /**
     * Calculate the number of points per given period and products
     */
    private function getTotalPoints (int $product1, int $product2, int $product3, int $product4): int {
        $tot =  ($product1 * 5);
        $tot += ($product2 * 5);
        $tot += ($product3 * 15);
        $tot += ($product4 * 35);
        
        return $tot;
    }
}