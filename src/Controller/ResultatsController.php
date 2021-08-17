<?php

namespace App\Controller;

use phpDocumentor\Reflection\Types\Integer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

class ResultatsController extends AbstractController
{
    /**
     * @Route("/resultats", name="resultats")
     */
    public function index(): Response
    {
        $serializer = new Serializer([new ObjectNormalizer()], [new CsvEncoder()]);
        $file = __DIR__."/../../temp/csv/resultats_users.csv";
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
        
        return $this->render('resultats/index.html.twig', [
            'controller_name' => 'ResultatsController',
            'data'            => $data
        ]);
    }
}
