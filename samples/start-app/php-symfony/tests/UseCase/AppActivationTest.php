<?php

namespace App\Tests\UseCase;

use App\Entity\Exception\QueryParametersException;
use App\UseCase\AppActivation;
use PHPUnit\Framework\TestCase;

class AppActivationTest extends TestCase
{

    private AppActivation $appActivation;

    protected function setUp(): void
    {
        $this->appActivation = new AppActivation('oauth_client_id');
    }

    /**
     * @test
     *
     * execute() with empty PIM URL
     *
     * @return void
     * @throws QueryParametersException
     */
    public function testExecuteWithEmptyPimUrl() : void
    {
        $this->expectException(QueryParametersException::class);
        $this->expectExceptionMessage(QueryParametersException::MISSING_PIM_URL);

        $pim_url = '';
        $session = array();

        $this->appActivation->execute($session, $pim_url);
    }

    /**
     * @test
     *
     * execute() valid case
     *
     * @return void
     * @throws QueryParametersException
     */
    public function testExecute()
    {
        $pimUrl = 'http://a_random_pim_url.com';
        $session = array();

        $actual = $this->appActivation->execute($session, $pimUrl);

        $this->assertStringContainsString($pimUrl, $actual);
        foreach (AppActivation::OAUTH_SCOPES as $scope) {
            $this->assertStringContainsString($scope, $actual);
        }
    }
}
