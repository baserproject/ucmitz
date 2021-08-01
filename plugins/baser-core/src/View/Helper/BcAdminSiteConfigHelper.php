<?php
namespace BaserCore\View\Helper;
use BaserCore\Service\Admin\SiteConfigManageServiceInterface;
use BaserCore\Utility\BcContainerTrait;
use BaserCore\Utility\BcUtil;
use Cake\Core\Configure;

class BcAdminSiteConfigHelper extends \Cake\View\Helper
{

    /**
     * Trait
     */
    use BcContainerTrait;

    /**
     * User Manage Service
     * @var SiteConfigManageServiceInterface
     */
    public $SiteConfigManage;

    /**
     * initialize
     * @param array $config
     * @checked
     * @noTodo
     * @unitTest
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->SiteConfigManage = $this->getService(SiteConfigManageServiceInterface::class);
    }

    /**
     * .env が書き込み可能かどうか
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function isWritableEnv()
    {
        return $this->SiteConfigManage->isWritableEnv();
    }

    /**
     * 管理画面テーマリストを取得
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getAdminThemeList()
    {
        return BcUtil::getAdminThemeList();
    }

    /**
     * ウィジェットエリアリストを取得
     * @return array
     * @checked
     * @unitTest
     */
    public function getWidgetAreaList()
    {
        // TODO 未実装のため代替措置
        // >>>
        //$this->BcAdminForm->getControlSource('WidgetArea.id'), 'empty' => __d('baser', 'なし')]
        // ---
        return [];
        // <<<
    }

    /**
     * エディタリストを取得
     * @return array|false[]|mixed
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getEditorList()
    {
        return Configure::read('BcApp.editors');
    }

    /**
     * メールエンコードリストを取得
     * @return array|false[]|mixed
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getMailEncodeList()
    {
        return Configure::read('BcEncode.mail');
    }

    /**
     * アプリケーションモードリストを取得
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getModeList()
    {
        return $this->SiteConfigManage->getModeList();
    }

}
