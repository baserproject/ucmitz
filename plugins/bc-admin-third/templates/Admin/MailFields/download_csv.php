<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package            Mail.View
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * [ADMIN] CSVダウンロード
 */
$this->BcCsv->encoding = $encoding;
$this->BcCsv->addModelDatas('MailMessage' . $mailContent['MailContent']['id'], $messages);
$this->BcCsv->download($contentName);
