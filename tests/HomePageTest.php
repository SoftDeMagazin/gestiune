<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

include_once('./autentificare/server.php');

include_once('./home/server.php');

class HomePageTest extends TestCase {

    public function testProductListing() {
        $obj = login(['username' => 'admin', 'pass'=>'acces']);
        $this->assertInstanceOf('xajaxResponse', $obj);

        $objResponse = lista([], ["pagesize"=>30, "curentpage"=>1]);
        $this->assertInstanceOf('xajaxResponse', $objResponse);
    }
}
