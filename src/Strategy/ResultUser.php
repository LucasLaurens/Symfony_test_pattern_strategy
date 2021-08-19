<?php   

namespace App\Strategy;

use App\Strategy\ResultUserInterface;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class ResultUser implements ResultUserInterface
{    
    public function readData(string $file): array
    {
        $serializer = new Serializer([new ObjectNormalizer()], [new CsvEncoder()]);
        $fileString = file_get_contents($file);
        $fileExtension = pathinfo($file, PATHINFO_EXTENSION);
        $data = $serializer->decode($fileString, $fileExtension, [CsvEncoder::DELIMITER_KEY => ';']);

        foreach( $data as &$item_array ) {
            $item_array = array_combine(
                array_map(
                    function ($str) {
                        return strtolower(str_replace(" ", "_", $str));
                    },
                    array_keys($item_array)
                ),
                array_values($item_array)
            );
        } unset($item_array);

        return $data ?? [];
    }
}