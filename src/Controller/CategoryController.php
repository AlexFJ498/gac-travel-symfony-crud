<?php

namespace App\Controller;

use App\Entity\Categories;
use App\Form\Categories\CategoryType;
use Symfony\Component\HttpFoundation\Request;
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

        return $this->render('category/categories.html.twig', ['categories' => $categories]);
    }

    /**
     * @Route("/category/add", name="category_add")
     */
    public function add(Request $request): Response
    {
        $category = new Categories();

        // Creamos el formulario
        $form = $this->createForm(CategoryType::class, $category);
        
        // Recogemos los datos del formulario
        $form->handleRequest($request);
        
        // Comprobamos si el formulario se ha enviado
        if ($form->isSubmitted() && $form->isValid()) {

            // Obtenemos los datos del formulario y seteamos el resto de campos
            $category = $form->getData();
            $category->setCreatedAt(new \DateTime());

            // Guardamos la categoría
            $em = $this->getDoctrine()->getManager();
            
            try{
                $em->persist($category);
                $em->flush();
            } catch (\Exception $e) {
                return $this->render('general/error.html.twig', ['message' => "Error al guardar la categoría"]);
            }

            // Redirigimos a la lista de categorías
            return $this->redirectToRoute('category_list');
        }

        return $this->render('category/add_category.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/category/edit/{id}", name="category_edit")
     */
    public function edit(Request $request, $id): Response
    {
        $category = $this->getDoctrine()->getRepository(Categories::class)->find($id);

        // Creamos el formulario
        $form = $this->createForm(CategoryType::class, $category);

        // Recogemos los datos del formulario
        $form->handleRequest($request);

        // Comprobamos si el formulario se ha enviado
        if ($form->isSubmitted() && $form->isValid()) {

            // Obtenemos los datos del formulario y seteamos el resto de campos
            $category = $form->getData();

            // Guardamos la categoría
            $em = $this->getDoctrine()->getManager();

            try{
                $em->persist($category);
                $em->flush();
            } catch (\Exception $e) {
                return $this->render('general/error.html.twig', ['message' => "Error al editar la categoría"]);
            }

            // Redirigimos a la lista de categorías
            return $this->redirectToRoute('category_list');
        }

        return $this->render('category/edit_category.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/category/delete/{id}", name="category_delete")
     */
    public function delete(Categories $category): Response
    {
        $em = $this->getDoctrine()->getManager();

        try{
            $em->remove($category);
            $em->flush();
        } catch (\Exception $e) {
            return $this->render('general/error.html.twig', ['message' => "Error al eliminar la categoría"]);
        }

        return $this->redirectToRoute('category_list');
    }
}
