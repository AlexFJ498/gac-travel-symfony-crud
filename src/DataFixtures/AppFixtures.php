<?php

namespace App\DataFixtures;

use App\Entity\Products;
use App\Entity\Categories;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(HttpClientInterface $client, UserPasswordHasherInterface $passwordHasher)
    {
        $this->client = $client;
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager)
    {
        // Obtenemos los usuarios de la API
        $users = $this->client->request('GET', 'https://fakestoreapi.com/users');
        $users = json_decode($users->getContent(), true);

        foreach ($users as $userAux) {
            $user = new User();
            $user->setUsername($userAux['username']);
            $user->setPassword($this->passwordHasher->hashPassword($user, $userAux['password']));
            $user->setRoles(['ROLE_ADMIN']);
            $user->setActive(true);
            $user->setCreatedAt(new \DateTime());
            $manager->persist($user);
        }

        $manager->flush();

        // Obtenemos las categorías de la API
        $categories = $this->client->request('GET', 'https://fakestoreapi.com/products/categories');
        $categories = json_decode($categories->getContent(), true);

        foreach ($categories as $categoryAux) {
            $category = new Categories();
            $category->setName($categoryAux);
            $category->setCreatedAt(new \DateTime());
            $manager->persist($category);
        }

        $manager->flush();

        // Obtenemos los productos de la API
        $products = $this->client->request('GET', 'https://fakestoreapi.com/products');
        $products = json_decode($products->getContent(), true);

        foreach ($products as $productAux) {
            $product = new Products();
            $product->setName($productAux['title']);
            $product->setCreatedAt(new \DateTime());
            $product->setStock(10);
            $category = $manager->getRepository(Categories::class)->findOneBy(['name' => $productAux['category']]);
            $product->setCategory($category);
            $manager->persist($product);
        }

        $manager->flush();
    }
}