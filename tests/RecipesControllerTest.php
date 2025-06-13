<?php

namespace App\Tests;

use App\Entity\Recipe;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class RecipesControllerTest extends WebTestCase
{
    private $client;

    private $entityManager;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->entityManager = $this->client->getContainer()->get('doctrine')->getManager();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();
        $this->entityManager = null;
    }

    public function testShowRecipeWithNonExistentId(): void
    {
        $this->client->request('GET', '/recipes/999999');

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function testShowRecipeWithInvalidIdFormat(): void
    {
        $this->client->request('GET', '/recipes/invalid');

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function testShowRecipeWithNegativeId(): void
    {
        $this->client->request('GET', '/recipes/-1');

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function testShowRecipeWithZeroId(): void
    {
        $this->client->request('GET', '/recipes/0');

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function testShowRecipeWithFloatId(): void
    {
        $this->client->request('GET', '/recipes/1.5');

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function testShowRecipeWithVeryLargeValidId(): void
    {
        // Test with a large but valid integer ID that doesn't exist
        $this->client->request('GET', '/recipes/2147483647'); // Max 32-bit integer

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function testShowRecipeRouteNameIsCorrect(): void
    {
        $recipe = $this->createTestRecipe();

        // Tester que la route génère la bonne URL avec le paramètre locale
        $url = $this->client->getContainer()->get('router')->generate('recipes.show', [
            'id' => $recipe->getId(),
        ]);

        $this->assertEquals('/recipes/' . $recipe->getId(), $url);

        $this->client->request('GET', $url);
        $this->assertResponseIsSuccessful();
    }

    public function createTestRecipe(): Recipe
    {
        $user = new User();
        $user->setEmail('test@example.com');
        $user->setUsername('testuser' . uniqid());
        $user->setPassword('$2y$13$test.password.hash'); // Ajout du password obligatoire
        $user->setRoles(['ROLE_USER']);
        $this->entityManager->persist($user);

        $recipe = new Recipe();
        $recipe->setTitle('Test Recipe ' . uniqid());
        $recipe->setSlug('test-recipe-' . uniqid());
        $recipe->setContent('This is a test recipe content for testing purposes that is long enough to pass validation.');
        $recipe->setDuration(30);
        $recipe->setUser($user);
        $recipe->setCreatedAt(new \DateTimeImmutable());
        $recipe->setUpdatedAt(new \DateTimeImmutable());

        $this->entityManager->persist($recipe);

        $this->entityManager->flush();

        return $recipe;
    }
}
