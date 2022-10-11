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

namespace BaserCore\Controller;

use BaserCore\Service\Front\PagesFrontServiceInterface;
use BaserCore\Model\Table\PagesTable;
use BaserCore\Utility\BcContainerTrait;
use BaserCore\Service\PagesServiceInterface;
use BaserCore\Service\ContentFoldersServiceInterface;
use BaserCore\Controller\Component\BcFrontContentsComponent;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;

/**
 * PagesController
 * @property PagesTable $Pages
 * @property BcFrontContentsComponent $BcFrontContents
 */
class PagesController extends BcFrontAppController
{

    /**
     * Trait
     * NOTE: BcAppControllerにもあるので、移行時に取り除く
     */
    use BcContainerTrait;

    /**
     * initialize
     * @return void
     * @checked
     * @unitTest
     * @noTodo
     */
    public function initialize(): void
    {
        parent::initialize();
        $this->loadComponent('BaserCore.BcFrontContents');
    }

	/**
	 * ビューを表示する
	 * @param PagesServiceInterface $pageService
	 * @param ContentFoldersServiceInterface $contentFolderService
	 * @return \Cake\Http\Response|void
     * @checked
     * @unitTest
     * @noTodo
	 */
	public function view(PagesFrontServiceInterface $pageService)
	{
        $page = $pageService->get(
            $this->request->getAttribute('currentContent')->entity_id,
            ['status' => 'publish']
        );
        $this->set($pageService->getViewVarsForView($page, $this->getRequest()));
        $this->render($pageService->getPageTemplate($page));
	}

}
