<?php

namespace App\Controller;

use App\Entity\Products;
use App\Form\Products\ProductType;
use Symfony\Component\HttpFoundation\Request;
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

        return $this->render('product/products.html.twig', ['products' => $products]);
    }

    /**
     * @Route("/product/add", name="product_add")
     */
    public function add(Request $request): Response
    {
        $product = new Products();

        // Creamos el formulario
        $form = $this->createForm(ProductType::class, $product);
        
        // Recogemos los datos del formulario
        $form->handleRequest($request);
        
        // Comprobamos si el formulario se ha enviado
        if ($form->isSubmitted() && $form->isValid()) {

            // Obtenemos los datos del formulario y seteamos el resto de campos
            $product = $form->getData();
            $product->setCreatedAt(new \DateTime());
            $product->setStock(0);

            // Guardamos el producto
            $em = $this->getDoctrine()->getManager();
            
            try{
                $em->persist($product);
                $em->flush();
            } catch (\Exception $e) {
                return $this->render('general/error.html.twig', ['message' => "Error al guardar el producto"]);
            }

            // Redirigimos a la lista de productos
            return $this->redirectToRoute('product_list');
        }

        return $this->render('product/add_product.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/product/edit/{id}", name="product_edit")
     */
    public function edit(Request $request, $id): Response
    {
        $product = $this->getDoctrine()->getRepository(Products::class)->find($id);

        // Creamos el formulario
        $form = $this->createForm(ProductType::class, $product);

        // Recogemos los datos del formulario
        $form->handleRequest($request);

        // Comprobamos si el formulario se ha enviado
        if($form->isSubmitted() && $form->isValid()) {
            
            // Obtenemos los datos del formulario y seteamos el resto de campos
            $product = $form->getData();
            
            // Guardamos el producto
            $em = $this->getDoctrine()->getManager();

            try{
                $em->persist($product);
                $em->flush();
            } catch (\Exception $e) {
                return $this->render('general/error.html.twig', ['message' => "Error al guardar el producto"]);
            }

            // Redirigimos a la lista de productos
            return $this->redirectToRoute('product_list');
        }

        return $this->render('product/edit_product.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/product/delete/{id}", name="product_delete")
     */
    public function delete(Request $request, $id): Response
    {
        $product = $this->getDoctrine()->getRepository(Products::class)->find($id);

        // Guardamos el producto
        $em = $this->getDoctrine()->getManager();

        try{
            $em->remove($product);
            $em->flush();
        } catch (\Exception $e) {
            return $this->render('general/error.html.twig', ['message' => "Error al eliminar el producto"]);
        }

        // Redirigimos a la lista de productos
        return $this->redirectToRoute('product_list');
    }

    /**
     * @Route("/product/stock/{id}", name="product_stock")
     */
    public function stock(Request $request): Response
    {
        // Obtenemos los datos
        $id = $request->request->get('id');
        $stock = $request->request->get('stock');
        
        try{
            // Obtenemos el producto
            $product = $this->getDoctrine()->getRepository(Products::class)->find($id);
    
            // Guardamos el producto
            $em = $this->getDoctrine()->getManager();

            $product->setStock($product->getStock() + $stock);

            // Comprobamos que el stock no sea negativo
            if($product->getStock() < 0) {
                $product->setStock(0);
            }

            $em->persist($product);
            $em->flush();
        } catch (\Exception $e) {
            return $this->render('general/error.html.twig', ['message' => "Error al guardar el producto"]);
        }

        // Devolvemos una respuesta correcta
        return new Response('OK');
    }
}
