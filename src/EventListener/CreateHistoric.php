<?php

namespace App\EventListener;

use App\Entity\Products;
use App\Entity\StockHistoric;
use App\Entity\User;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class CreateHistoric
{
    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * Función que se ejecuta cuando se hace una persistencia de una entidad
     */
    public function postPersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();
        $entityManager = $args->getObjectManager();

        // Solo se ejecuta cuando se persiste una entidad de tipo Product
        if (!$entity instanceof Products) {
            return;
        }

        // Obtenemos el usuario de la sesión
        $token = $this->tokenStorage->getToken();
        if(empty($token)) {
            // Obtenemos el usuario con nombre Anonimo (esto es para que funcione el fixture)
            $user = $entityManager->getRepository(User::class)->findOneBy(['username' => 'Anonimo']);
            if(empty($user)){
                // Creamos un usuario ficticio 
                $user = new User();
                $user->setUsername('Anonimo');
                $user->setActive(true);
                $user->setCreatedAt(new \DateTime());
                $user->setRoles(['ROLE_ADMIN']);
                $user->setPassword('anonimo');

                $entityManager->persist($user);
                $entityManager->flush();
            }
        } else {
            $user = $token->getUser();
        }

        // Se crea un nuevo registro de StockHistoric
        $stockHistoric = new StockHistoric();
        $stockHistoric->setStock($entity->getStock());
        $stockHistoric->setCreatedAt(new \DateTime());
        $stockHistoric->setUser($user);
        $stockHistoric->setProduct($entity);

        $entityManager->persist($stockHistoric);
        $entityManager->flush();
    }

    /**
     * Función que se ejecuta cuando se hace una persistencia de una entidad
     */
    public function postUpdate(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        // Solo se ejecuta cuando se persiste una entidad de tipo Product
        if (!$entity instanceof Products) {
            return;
        }

        // Obtenemos el usuario de la sesión
        $user = $this->tokenStorage->getToken()->getUser();

        $entityManager = $args->getObjectManager();

        // Se crea un nuevo registro de StockHistoric
        $stockHistoric = new StockHistoric();
        $stockHistoric->setStock($entity->getStock());
        $stockHistoric->setCreatedAt(new \DateTime());
        $stockHistoric->setUser($user);
        $stockHistoric->setProduct($entity);
        
        $entityManager->persist($stockHistoric);
        $entityManager->flush();
    }
}