<?php

namespace App\Tests;

use App\Entity\Item;

class ItemTest extends \PHPUnit\Framework\TestCase
{
    private Item $item;

    protected function setUp(): void
    {
        $this->item = new Item();
        $this->item->setName("testItem");
        $this->item->setContent("testContent");
        $this->item->setCreatedAt();
        parent::setUp();
    }

    public function testisTooLongContents()
    {
        $content = str_repeat("*", 1000);
        $this->item->setContent($content);
        $this->assertFalse($this->item->isTooLongContents());
        $content = str_repeat("*", 1001);
        $this->item->setContent($content);
        $this->assertTrue($this->item->isTooLongContents());
    }
}