<?php

namespace App\Tests\Validations;

use App\Entity\Formation;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Description of FormationValidationsTest
 *
 * @author 
 */
class FormationValidationsTest extends KernelTestCase {
    
    public function getFormation() : Formation
    {
        return new Formation();
    }
    
    public function assertErrors(Formation $formation, int $erreursAttendues)
    {
        self::bootKernel();
        $validator = self::getContainer()->get(ValidatorInterface::class);
        $error = $validator->validate($formation);
        $this->assertCount($erreursAttendues, $error);
    }
    
    public function testSetPublishedAt()
    {
        $formation = $this->getFormation()->setPublishedAt(new DateTime("2025-11-26"));
        $this->assertErrors($formation, 1);
    }
}
