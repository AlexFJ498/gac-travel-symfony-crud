<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\StockHistoric;
use App\Entity\Products;

class StockHistoricController extends AbstractController
{
    /**
     * @Route("/stock/list/{id}", name="stock_list")
     */
    public function list($id): Response
    {
        $stock = $this->getDoctrine()->getRepository(StockHistoric::class)->findBy(['product' => $id]);

        $product = $this->getDoctrine()->getRepository(Products::class)->find($id);

        return $this->render('stockHistoric/stockHistorics.html.twig', ['stocks' => $stock, 'product' => $product]);
    }
}
