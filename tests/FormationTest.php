<?php

use PHPUnit\Framework\TestCase;
use App\Entity\Formation;

/**
 * Description of FormationTest
 *
 * @author 
 */
class FormationTest extends TestCase {
    
    public function testgetPublishedAtString()
    {
        $formation = new Formation();
        $formation->setPublishedAt(new \DateTime("2025-11-25"));
        $this->assertEquals("25/11/2025", $formation->getPublishedAtString());
    }
    
}
