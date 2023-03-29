<?php

declare(strict_types=1);

namespace App\Tests\Mocks;

use App\UseCase\OpenIdConnect;

final class OpenIdPublicKeyMock
{
    const API_URL = OpenIdConnect::OPENID_PUBLIC_KEY;

    public static array $response = [
        'private_key' => <<<EOD
        -----BEGIN RSA PRIVATE KEY-----
        MIIEowIBAAKCAQEAp53OmdrsF+z4F8QK37Ol5VnKERKyFpl0PAXRsQ4kFiDK72Q1
        HeBVJXIjYKVEfCuZxNBSA9HwRz/uk6WFtoUql9rl3fTYqucKHrurNw8eZ4e4pARc
        rIApV3g9axNxNWV7OZNNCjpS/aKVK9/VNUonH8ZQWRgWKeZ+Cp5u16bskaUGLT0j
        9ZTw/Lf8xAg0mpdUg9jOD3rv585om/7RamrfWmL7uvPCZVsmj5EFXeJRg1ZtQwXn
        w9EnqcEy/KaS4t+t04n2BNXNhEdtFtkaUaKPbwnHrtmaiRxgIAvGgOn271nBH7Ad
        jT5LUH6IKdMaGByqFq3I79tBSwnurqsNs6/TrwIDAQABAoIBAQCZmINU+4bzmcPy
        S0RHn+D6nWAMaxxklzQrOI3h+HpbCKvN2P7PpN2BlekTS/7I5k9fSiKNu5i6QbyG
        8dvo2HiidQXFcfQGD/d3GDQaHTGYX4d0TUb7D+pSbC2j7EoGSzszFGc5Yx7fq1eU
        v+UpkwztEhZYvji5Jq0DYBFkbmNERf/8Dhu7Ai+1CgHQ+5N7NETW5+dNQ4eV78kC
        CiXd7NXy86N31cxxr45Kj5lt+Wqd1Vj1HvqY1EgPtmx3LEdnRfEgJec8PlU4PdgZ
        YRqofUnuvUgGuRvWszfn4e2vBRNV33ANL/6LczlKBMID39fJ8/ASlH1BfGGuQXZG
        gO8aAkKRAoGBANN7pimu+aQfzF+gGiyAJoIth+Ne3NOaZd30FnD7FadiTr0bnIZ2
        t+UTtDm3czDJWiOSji0c0KldmLOTEY7C82TAgz7pa+uwSVPAkMS/f1S/SOngUcEz
        +Zx025Wztpj9MEx/BQS9WLBRCLL14hk3hpBmk3eA5G/jH9WquzLXk8fpAoGBAMrm
        SRxY+JD9SwCQoxPcZJ8V1IUhA7bMwbgCFNrPU8UTaM2dWmm2T0MybJQS+AcxBwfo
        oCQqPFn6k1ArhHUEaw7mapTgYn8/1OKaLc18/psoK9DX/CFfvt0uTqICBGtxKuR3
        ioJfiBPOxm29IEqpicj93a1B9Z+raiT8QBK9lRfXAoGATKl9EY0xR7KTNnLmKg2G
        aBgzM1P2/unwVWYfIHOUZOn5GwpAUmoEvZDH+0gqBJxpBY4h5e6VIWAhc68zZSn8
        ayByM0MQcQ6ldivPDtNQSBtTRhyf5kZ+DJ4732eXIJxffstouVAeL0QgwCbsADIl
        oIDeoipiJMETkDPkykhldMkCgYBDPbQm/9lIe5jalBRnZZe3HQ30HK/VaeAdMhtl
        NGZnnnoSAj7PX8q+0zwmim7GO1pAJbjEo3aivycI35MjEJbGNlhGBGLAFxBfFIdv
        XSgnlVWH/bwh91ASjuEiUZbTYIq9clxlvnEjV1htT8se0zbFdFR9salMBAvGPA6j
        5048uwKBgDFERFeU/ic/zKZG6BBvbU3HwYhZnDxREYWg4O7jprIveGxpZ3TkneAY
        B9YVT9XPwo9T4ufQttedtCXLKxNXVRMv5zgaKjbRfVfs1uzbqywZjzmXCYs/QCw7
        qoYlxvlCDX2qgHZEBrkY6Sc0InreIa9Ha3IaAovi/0OOPKkxjGSm
        -----END RSA PRIVATE KEY-----
        EOD,
        'public_key' => <<<EOD
        -----BEGIN PUBLIC KEY-----
        MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAp53OmdrsF+z4F8QK37Ol
        5VnKERKyFpl0PAXRsQ4kFiDK72Q1HeBVJXIjYKVEfCuZxNBSA9HwRz/uk6WFtoUq
        l9rl3fTYqucKHrurNw8eZ4e4pARcrIApV3g9axNxNWV7OZNNCjpS/aKVK9/VNUon
        H8ZQWRgWKeZ+Cp5u16bskaUGLT0j9ZTw/Lf8xAg0mpdUg9jOD3rv585om/7Ramrf
        WmL7uvPCZVsmj5EFXeJRg1ZtQwXnw9EnqcEy/KaS4t+t04n2BNXNhEdtFtkaUaKP
        bwnHrtmaiRxgIAvGgOn271nBH7AdjT5LUH6IKdMaGByqFq3I79tBSwnurqsNs6/T
        rwIDAQAB
        -----END PUBLIC KEY-----
        EOD
    ];
}
