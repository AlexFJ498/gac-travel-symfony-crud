<?php

namespace App\Controller;

use App\Entity\Products;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    /**
     * @Route("/product/list", name="product_list")
     */
    public function list(): Response
    {
        $products = $this->getDoctrine()->getRepository(Products::class)->findAll();

        return $this->render('product/products.html.twig', ['users' => $products]);
    }

    /**
     * @Route("/product/add", name="product_add")
     */
    public function add(): Response
    {
        return $this->render('product/add_product.html.twig');
    }
}
