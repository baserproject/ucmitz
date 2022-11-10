<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BaserCore\Test\TestCase\Middleware;

use BaserCore\Middleware\BcRedirectSubSiteFilter;
use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\TestSuite\BcTestCase;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * Class BcRedirectSubSiteFilterTest
 * @property BcRedirectSubSiteFilter $BcRedirectSubSiteFilter
 */
class BcRedirectSubSiteFilterTest extends BcTestCase
{

    /**
     * Trait
     */
    use ScenarioAwareTrait;

    /**
     * fixtures
     * @var string[]
     */
    public $fixtures = [
        'plugin.BaserCore.Factory/Sites',
        'plugin.BaserCore.Factory/Users',
        'plugin.BaserCore.Factory/SiteConfigs',
    ];

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->BcRedirectSubSiteFilter = new BcRedirectSubSiteFilter();
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->BcRedirectSubSiteFilter);
        parent::tearDown();
    }

    /**
     * test Process
     */
    public function test_process(): void
    {
        $this->loadFixtureScenario(InitAppScenario::class);
//        $request = $this->getRequest('/baser/admin/baser-core/themes/');
//        $this->loginAdmin($request);
        $a = $this->BcRedirectSubSiteFilter->process($this->getRequest('/baser/api/baser-core/themes/view/BcFront.json'), $this->Application);
//        $this->get('/baser/api/baser-core/themes/view/BcFront.json?token=' . $this->accessToken);


//        $this->get('/about');
        $request = $this->getRequest();
    }

}
