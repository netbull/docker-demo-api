<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class DefaultController
 * @package App\Controller
 */
class DefaultController extends AbstractController
{
    /**
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function indexAction()
    {
       return $this->json('Cool docker demo api!');
    }
}
