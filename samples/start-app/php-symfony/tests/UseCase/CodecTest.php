<?php

namespace App\Tests\UseCase;

use App\UseCase\Codec;
use PHPUnit\Framework\TestCase;

class CodecTest  extends TestCase
{

    /**
     * @test
     *
     * encode()
     */
    public function testEncode() : void
    {
        $payload = 'thisIsATest';
        $key = 'randomKey';

        list($data, $iv) = Codec::encode($payload, $key);

        $this->assertIsString($data);
        $this->assertIsString($iv);

        $encryptedIv = hex2bin($iv);
        $decryptedPayload = openssl_decrypt($data, Codec::CYPHER, $key, 0, $encryptedIv);

        $this->assertEquals($payload, $decryptedPayload);
    }

    /**
     * @test
     *
     * decode()
     */
    public function testDecode() : void
    {
        $payload = 'thisIsATest';
        $key = 'randomKey';

        list($data, $iv) = Codec::encode($payload, $key);

        $decryptedPayload = Codec::decode($data, $key, $iv);

        $this->assertEquals($payload, $decryptedPayload);
    }

}
