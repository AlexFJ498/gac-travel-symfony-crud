<?php

namespace App\Service;

use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserService
{
    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    /**
     * Función para crear un usuario
     */
    public function createUser($params): json
    {
        // Respuesta por defecto
        $response = [
            'status' => false,
            'message' => 'No se pudo crear el usuario',
            'data' => []
        ];

        // Crear un nuevo usuario
        $user = new User();

        // Asignar los valores a los atributos
        $user->setUsername($params['username']);
        $user->setCreatedAt(new \DateTime());
        $user->setRoles(['ROLE_ADMIN']);
        $user->setActive(true);
        
        // Encriptar la contraseña
        $password = $params['password'];
        $user->setPassword($this->passwordHasher->hashPassword($user, $password));

        // Guardar el usuario
        try{
            $this->em->persist($user);
            $this->em->flush();
        } catch (\Exception $e) {
            return $response;
        }

        // Respuesta correcta
        $response = [
            'status' => true,
            'message' => 'Usuario creado correctamente',
            'data' => $user
        ];

        return $response;
    }
}