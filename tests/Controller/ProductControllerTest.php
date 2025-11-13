<?php

namespace App\Tests\Controller;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use Symfony\Component\Security\Core\User\InMemoryUser;

final class ProductControllerTest extends WebTestCase
{
    
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $productRepository;
    private string $path = '/product/';
    

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $testUser = new InMemoryUser('test@gmail.com', 'test', ['ROLE_ADMIN']);
        $this->client->loginUser($testUser);

        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->productRepository = $this->manager->getRepository(Product::class);

        foreach ($this->productRepository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Liste des produits');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first()->text());
    }

    public function testNew(): void
    {
        // $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'product[label]' => 'Testing',
            'product[price_ht]' => 100,
            'product[price_tva]' => 20,
            'product[price_ttc]' => 120,
            'product[description]' => 'Testing',
            // 'product[categories]' => 'Testing',
        ]);

        self::assertResponseRedirects('/product');

        self::assertSame(1, $this->productRepository->count([]));
    }

    public function testShow(): void
    {
        // $this->markTestIncomplete();
        $fixture = new Product();
        $fixture->setLabel('My Title');
        $fixture->setPriceHt(100);
        $fixture->setPriceTva(20);
        $fixture->setPriceTtc(120);
        $fixture->setDescription('My Title');
        // $fixture->setCategories('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Product');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        // $this->markTestIncomplete();
        $fixture = new Product();
        $fixture->setLabel('Value');
        $fixture->setPriceHt(1000);
        $fixture->setPriceTva(10);
        $fixture->setPriceTtc(1100);
        $fixture->setDescription('Value');
        // $fixture->setCategories('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'product[label]' => 'Something New',
            'product[price_ht]' => 100,
            'product[price_tva]' => 20,
            'product[price_ttc]' => 120,
            'product[description]' => 'Something New',
            // 'product[categories]' => 'Something New',
        ]);

        self::assertResponseRedirects('/product');

        $fixture = $this->productRepository->findAll();

        self::assertSame('Something New', $fixture[0]->getLabel());
        self::assertSame(100.0, (float) $fixture[0]->getPriceHt());
        self::assertSame(20.0, (float) $fixture[0]->getPriceTva());
        self::assertSame(120.0, (float) $fixture[0]->getPriceTtc());
        self::assertSame('Something New', $fixture[0]->getDescription());
        // self::assertSame('Something New', $fixture[0]->getCategories());
    }

    public function testRemove(): void
    {
        // $this->markTestIncomplete();
        $fixture = new Product();
        $fixture->setLabel('Value');
        $fixture->setPriceHt(1000);
        $fixture->setPriceTva(10);
        $fixture->setPriceTtc(1100);
        $fixture->setDescription('Value');
        // $fixture->setCategories('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/product');
        self::assertSame(0, $this->productRepository->count([]));
    }
}
