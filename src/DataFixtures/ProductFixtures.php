<?php

namespace App\DataFixtures;

use App\Entity\Product;
use App\Entity\Category;
use App\DataFixtures\CategoryFixtures;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class ProductFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $visE = new Product();
        $visE->setLabel('Vis étoile');
        $visE->setPriceHt('1');
        $visE->setPriceTva('5');
        $visE->setPriceTtc('1.05');
        $visE->setDescription('Vis avec une tête en forme d\'étoile pour une meilleure prise.');
        $visE->addCategory($this->getReference(CategoryFixtures::CATEGORY_QUINCAILLERIE, Category::class));
        $manager->persist($visE);

        $visA = new Product();
        $visA->setLabel('Vis Allen');
        $visA->setPriceHt('1.2');
        $visA->setPriceTva('5');
        $visA->setPriceTtc('1.26');
        $visA->setDescription('Vis avec une tête hexagonale creuse pour clé Allen.');
        $visA->addCategory($this->getReference(CategoryFixtures::CATEGORY_QUINCAILLERIE, Category::class));
        $visA->addCategory($this->getReference(CategoryFixtures::CATEGORY_CUISINE, Category::class));
        $manager->persist($visA);

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            CategoryFixtures::class,
        ];
    }
}
