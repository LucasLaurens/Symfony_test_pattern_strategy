<?php

namespace App\Controller;

use App\Strategy\ResultUser;
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
        
        return $this->render('resultats/index.html.twig', [
            'controller_name' => 'ResultatsController',
            'data'            => $data
        ]);
    }
}
