<?php

namespace App\Controller;

use App\Entity\Categories;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    /**
     * @Route("/category/list", name="category_list")
     */
    public function list(): Response
    {
        $categories = $this->getDoctrine()->getRepository(Categories::class)->findAll();

        return $this->render('category/categories.html.twig', ['users' => $categories]);
    }

    /**
     * @Route("/category/add", name="category_add")
     */
    public function add(): Response
    {
        return $this->render('category/add_category.html.twig');
    }
}
