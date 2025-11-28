<?php


namespace App\Tests\Repository;

use App\Repository\FormationRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Description of FormationRepositoryTest
 *
 * @author 
 */
class FormationRepositoryTest extends KernelTestCase {
    
    public function getRepository() : FormationRepository
    {
        self::bootkernel();
        $repository = self::getContainer()->get(FormationRepository::class);
        return $repository;
    }
    
    public function testfindAllForOneCategory()
    {
        $repository = $this->getRepository();
        $formations = $repository->findAllForOneCategory(1);
        $this->assertEquals(13, count($formations));
    }
    
}
 