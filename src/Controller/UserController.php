<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\Users\UserType;
use App\Form\Users\SignUpType;
use App\Service\UserService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserController extends AbstractController
{
    /**
     * @Route("/login", name="user_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('product_list');
        }

        // Obtiene el error de autenticación si lo hay
        $error = $authenticationUtils->getLastAuthenticationError();

        // Último nombre de usuario introducido
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('user/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * @Route("/signup", name="sign_up")
     */
    public function signUp(Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('product_list');
        }
        
        // Crear un nuevo usuario
        $user = new User();

        // Crear el formulario
        $form = $this->createForm(SignUpType::class, $user);

        // Recoger la petición
        $form->handleRequest($request);

        // Comprobar si el formulario se ha enviado
        if ($form->isSubmitted() && $form->isValid()) {
            
            // Obtenemos los datos del formulario y seteamos el resto de campos
            $user = $form->getData();
            $user->setCreatedAt(new \DateTime());
            $user->setRoles(['ROLE_ADMIN']);
            $user->setActive(true);
            
            // Encriptamos la contraseña
            $password = $user->getPassword();
            $user->setPassword($passwordHasher->hashPassword($user, $password));

            // Guardamos el usuario
            $em = $this->getDoctrine()->getManager();
            
            try{
                $em->persist($user);
                $em->flush();
            } catch (\Exception $e) {
                return $this->render('general/error.html.twig', ['message' => "Error al crear el usuario"]);
            }

            // Mostramos el mensaje de éxito
            return $this->render('general/success.html.twig');
        }

        return $this->render('user/sign_up.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/user/list", name="user_list")
     */
    public function list(): Response
    {
        $users = $this->getDoctrine()->getRepository(User::class)->findAll();

        return $this->render('user/users.html.twig', ['users' => $users]);
    }

    /**
     * @Route("/user/add", name="user_add")
     */
    public function add(Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = new User();

        // Creamos el formulario
        $form = $this->createForm(UserType::class, $user);
        
        // Recogemos los datos del formulario
        $form->handleRequest($request);
        
        // Comprobamos si el formulario se ha enviado
        if ($form->isSubmitted() && $form->isValid()) {

            // Obtenemos los datos del formulario y seteamos el resto de campos
            $user = $form->getData();
            $user->setCreatedAt(new \DateTime());
            $user->setRoles(['ROLE_ADMIN']);
            $user->setActive(true);
            
            // Encriptamos la contraseña
            $password = $user->getPassword();
            $user->setPassword($passwordHasher->hashPassword($user, $password));

            // Guardamos el usuario
            $em = $this->getDoctrine()->getManager();
            
            try{
                $em->persist($user);
                $em->flush();
            } catch (\Exception $e) {
                return $this->render('general/error.html.twig', ['message' => "Error al guardar el usuario"]);
            }

            // Redirigimos a la lista de usuarios
            return $this->redirectToRoute('user_list');
        }

        return $this->render('user/add_user.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    
    /**
     * @Route("/user/edit/{id}", name="user_edit")
     */
    public function edit(Request $request, User $user, UserPasswordHasherInterface $passwordHasher): Response
    {
        // Creamos el formulario
        $form = $this->createForm(UserType::class, $user);

        // Recogemos los datos del formulario
        $form->handleRequest($request);

        // Comprobamos si el formulario se ha enviado
        if ($form->isSubmitted() && $form->isValid()) {

            // Obtenemos los datos del formulario y seteamos el resto de campos
            $user = $form->getData();

            // Encriptamos la contraseña
            $password = $user->getPassword();
            $user->setPassword($passwordHasher->hashPassword($user, $password));

            // Guardamos el usuario
            $em = $this->getDoctrine()->getManager();

            try{
                $em->persist($user);
                $em->flush();
            } catch (\Exception $e) {
                return $this->render('general/error.html.twig', ['message' => "Error al editar el usuario"]);
            }

            // Redirigimos a la lista de usuarios
            return $this->redirectToRoute('user_list');
        }

        return $this->render('user/edit_user.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/user/delete/{id}", name="user_delete")
     */
    public function delete(User $user): Response
    {
        $em = $this->getDoctrine()->getManager();

        if($user->getId() == $this->getUser()->getId()){
            return $this->render('general/error.html.twig', ['message' => 'No puedes borrar tu propio usuario']);
        }

        try{
            $em->remove($user);
            $em->flush();
        } catch (\Exception $e) {
            return $this->render('general/error.html.twig', ['message' => "Error al borrar el usuario"]);
        }

        return $this->redirectToRoute('user_list');
    }
}
