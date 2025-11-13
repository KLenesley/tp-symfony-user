<?php

namespace App\Tests\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\User\InMemoryUser;

final class UserRoleTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $userRepository;
    private string $path = '/user/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        // $testUser = new InMemoryUser('user-test@gmail.com', 'user-test', ['ROLE_USER']);
        // $this->client->loginUser($testUser);

        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->userRepository = $this->manager->getRepository(User::class);

        foreach ($this->userRepository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndexUser(): void
    {
        $userTest = new InMemoryUser('user-test@gmail.com', 'user-test', ['ROLE_USER']);
        $this->client->loginUser($userTest);

        $this->client->followRedirects();
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(403);
        // self::assertPageTitleContains('Log in!');

        self::assertFalse($this->client->getResponse()->isSuccessful());

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first()->text());
    }

    public function testIndexAdmin(): void
    {
        $adminUser = new InMemoryUser('s-admin-test@gmail.com', 's-admin-test', ['ROLE_SUPER_ADMIN']);
        $this->client->loginUser($adminUser);

        $this->client->followRedirects();
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Liste des utilisateurs');

        self::assertTrue($this->client->getResponse()->isSuccessful());
    }
}
