<?php

namespace App\Controller;

use App\Strategy\ResultUserInterface;
use Carbon\Carbon;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

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
    public function index(CacheInterface $dataInCache): Response
    {
        $file      = __DIR__."/../../temp/csv/resultats_users.csv";
        $data      = $this->resultUser->readData($file);
        $day       = (3600 * 24);
        $user      = $this->resultUser->getUser($data);
        $dataArray = $this->resultUser->getData($data);

        return $this->render('resultats/index.html.twig', [
            'user' => $user,
            'data' => $dataInCache->get('data_in_cache', function(ItemInterface $item) use($dataArray, $day) {
                $item->expiresAfter($day);
                return $dataArray;
            })
        ]);
    }
}