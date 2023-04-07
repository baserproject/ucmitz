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

namespace BaserCore\Model\Table;

use ArrayObject;
use BaserCore\Utility\BcUtil;
use Cake\ORM\Association\BelongsToMany;
use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\Core\Configure;
use Cake\I18n\FrozenTime;
use Cake\Event\EventInterface;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use Cake\Datasource\EntityInterface;

/**
 * Class AppTable
 */
class AppTable extends Table
{

    /**
     * 一時イベント
     * イベントを一時にオフにする場合に対象のコールバック処理を一時的に格納する
     * @var array
     */
    public $tmpEvents = [];

    /**
     * 公開状態のフィールド
     * AppTable::getConditionAllowPublish() で利用
     * @var string
     */
    public $publishStatusField = 'status';

    /**
     * 公開開始日のフィールド
     * AppTable::getConditionAllowPublish() で利用
     * @var string
     */
    public $publishBeginField = 'publish_begin';

    /**
     * 公開終了日のフィールド
     * AppTable::getConditionAllowPublish() で利用
     * @var string
     */
    public $publishEndField = 'publish_end';

    /**
     * テーブルをセット
     *
     * プレフィックスを追加する
     *
     * @param string $table
     * @return AppTable
     * @checked
     * @noTodo
     */
    public function setTable(string $table)
    {
        $table = $this->addPrefix($table);
        return parent::setTable($table);
    }

    /**
     * テーブルを取得
     *
     * プレフィックスを追加する
     *
     * @return string
     * @checked
     * @noTodo
     */
    public function getTable(): string
    {
        $table = parent::getTable();
        $this->_table = $this->addPrefix($table);
        return $this->_table;
    }

    /**
     * Belongs To Many
     *
     * joinTable にプレフィックスを追加
     *
     * @param string $associated
     * @param array $options
     * @return BelongsToMany
     * @checked
     * @noTodo
     */
    public function belongsToMany(string $associated, array $options = []): BelongsToMany
    {
        if(isset($options['joinTable'])) {
            $options['joinTable'] = $this->addPrefix($options['joinTable']);
        }
        return parent::belongsToMany($associated, $options);
    }

    /**
     * テーブル名にプレフィックスを追加する
     *
     * $this->getConnection()->config() を利用するとユニットテストで問題が発生するため、BcUtil::getCurrentDbConfig()を利用する
     *
     * $this->getConnection()->config()を利用すると、
     * そのテーブルに connection が設定されてしまう。
     *
     * ユニットテストの dataProvider で、テーブルを初期化する場合、
     * タイミング的に、接続についてテスト用のエイリアスが設定されていないので、
     * テスト用の接続ではなく、 default がセットされてしまう。
     *
     * @param $table
     * @return string
     * @checked
     * @noTodo
     */
    public function addPrefix($table)
    {
        $prefix = BcUtil::getCurrentDbConfig()['prefix'];
        if(!preg_match('/^' . $prefix . '/', $table)) {
            return $prefix . $table;
        }
        return $table;
    }

    /**
     * Initialize
     *
     * @param array $config テーブル設定
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);
        FrozenTime::setToStringFormat('yyyy/MM/dd HH:mm:ss');
    }

    /**
     * Before Save
     * @param EventInterface $event
     * @param EntityInterface $entity
     * @param ArrayObject $options
     * @return bool
     */
    public function beforeSave(EventInterface $event, EntityInterface $entity, ArrayObject $options)
    {
        // TODO ucmitz 暫定措置
        // >>>
        return true;
        // <<<
        // 日付フィールドが空の場合、nullを保存する
        foreach($this->_schema as $key => $field) {
            if (('date' == $field['type'] ||
                    'datetime' == $field['type'] ||
                    'time' == $field['type']) &&
                isset($this->data[$this->name][$key])) {
                if ($this->data[$this->name][$key] == '') {
                    $this->data[$this->name][$key] = null;
                }
            }
        }
        return true;
    }

    /**
     * Saves model data to the database. By default, validation occurs before save.
     *
     * @param array $data Data to save.
     * @param boolean $validate If set, validation will be done before the save
     * @param array $fieldList List of fields to allow to be written
     * @return    mixed    On success Model::$data if its not empty or true, false on failure
     */
    // TODO 未実装の為コメントアウト
    /* >>>
    public function save($data = null, $validate = true, $fieldList = [])
    {
        if (!$data) {
            $data = $this->data;
        }

        // created,modifiedが更新されないバグ？対応
        if (!$this->exists()) {
            if (isset($data[$this->alias])) {
                $data[$this->alias]['created'] = null;
            } else {
                $data['created'] = null;
            }
        }
        if (isset($data[$this->alias])) {
            $data[$this->alias]['modified'] = null;
        } else {
            $data['modified'] = null;
        }

        return parent::save($data, $validate, $fieldList);
    }
    <<< */

    /**
     * 配列の文字コードを変換する
     *
     * TODO GLOBAL グローバルな関数として再配置する必要あり
     *
     * @param array $data 変換前のデータ
     * @param string $outenc 変換後の文字コード
     * @param string $inenc 変換元の文字コード
     * @return array 変換後のデータ
     */
    public function convertEncodingByArray($data, $outenc, $inenc)
    {
        foreach($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = $this->convertEncodingByArray($value, $outenc, $inenc);
            } else {
                if (mb_detect_encoding($value) <> $outenc) {
                    $data[$key] = mb_convert_encoding($value, $outenc, $inenc);
                }
            }
        }
        return $data;
    }

    /**
     * データベースログを記録する
     *
     * @param string $message
     * @return boolean
     */
    public function saveDbLog($message)
    {
        // TODO 未実装の為コメントアウト
        /* >>>
        // ログを記録する
        $Dblog = ClassRegistry::init('Dblog');
        $logdata['Dblog']['name'] = $message;
        $logdata['Dblog']['user_id'] = @$_SESSION['Auth'][Configure::read('BcPrefixAuth.Admin.sessionKey')]['id'];
        return $Dblog->save($logdata);
        <<< */
    }

    /**
     * コントロールソースを取得する
     *
     * 継承先でオーバーライドする事
     *
     * @param $field
     * @return array
     */
    public function getControlSource($field)
    {
        return [];
    }

    /**
     * 子カテゴリのIDリストを取得する
     *
     * treeビヘイビア要
     *
     * @param mixed $id ページカテゴリーID
     * @return    array
     */
    public function getChildIdsList($id)
    {
        // TODO 未実装の為コメントアウト
        /* >>>
        $ids = [];
        if ($this->childCount($id)) {
            $children = $this->children($id);
            foreach($children as $child) {
                $ids[] = (int)$child[$this->name]['id'];
            }
        }
        return $ids;
        <<< */
    }

    /**
     * 機種依存文字の変換処理
     *
     * 内部文字コードがUTF-8である必要がある。
     * 多次元配列には対応していない。
     *
     * @param string    変換対象文字列
     * @return    string    変換後文字列
     * TODO AppExModeに移行すべきかも
     */
    public function replaceText($str)
    {
        $ret = $str;
        $arr = [
            "\xE2\x85\xA0" => "I",
            "\xE2\x85\xA1" => "II",
            "\xE2\x85\xA2" => "III",
            "\xE2\x85\xA3" => "IV",
            "\xE2\x85\xA4" => "V",
            "\xE2\x85\xA5" => "VI",
            "\xE2\x85\xA6" => "VII",
            "\xE2\x85\xA7" => "VIII",
            "\xE2\x85\xA8" => "IX",
            "\xE2\x85\xA9" => "X",
            "\xE2\x85\xB0" => "i",
            "\xE2\x85\xB1" => "ii",
            "\xE2\x85\xB2" => "iii",
            "\xE2\x85\xB3" => "iv",
            "\xE2\x85\xB4" => "v",
            "\xE2\x85\xB5" => "vi",
            "\xE2\x85\xB6" => "vii",
            "\xE2\x85\xB7" => "viii",
            "\xE2\x85\xB8" => "ix",
            "\xE2\x85\xB9" => "x",
            "\xE2\x91\xA0" => "(1)",
            "\xE2\x91\xA1" => "(2)",
            "\xE2\x91\xA2" => "(3)",
            "\xE2\x91\xA3" => "(4)",
            "\xE2\x91\xA4" => "(5)",
            "\xE2\x91\xA5" => "(6)",
            "\xE2\x91\xA6" => "(7)",
            "\xE2\x91\xA7" => "(8)",
            "\xE2\x91\xA8" => "(9)",
            "\xE2\x91\xA9" => "(10)",
            "\xE2\x91\xAA" => "(11)",
            "\xE2\x91\xAB" => "(12)",
            "\xE2\x91\xAC" => "(13)",
            "\xE2\x91\xAD" => "(14)",
            "\xE2\x91\xAE" => "(15)",
            "\xE2\x91\xAF" => "(16)",
            "\xE2\x91\xB0" => "(17)",
            "\xE2\x91\xB1" => "(18)",
            "\xE2\x91\xB2" => "(19)",
            "\xE2\x91\xB3" => "(20)",
            "\xE3\x8A\xA4" => "(上)",
            "\xE3\x8A\xA5" => "(中)",
            "\xE3\x8A\xA6" => "(下)",
            "\xE3\x8A\xA7" => "(左)",
            "\xE3\x8A\xA8" => "(右)",
            "\xE3\x8D\x89" => "ミリ",
            "\xE3\x8D\x8D" => "メートル",
            "\xE3\x8C\x94" => "キロ",
            "\xE3\x8C\x98" => "グラム",
            "\xE3\x8C\xA7" => "トン",
            "\xE3\x8C\xA6" => "ドル",
            "\xE3\x8D\x91" => "リットル",
            "\xE3\x8C\xAB" => "パーセント",
            "\xE3\x8C\xA2" => "センチ",
            "\xE3\x8E\x9D" => "cm",
            "\xE3\x8E\x8F" => "kg",
            "\xE3\x8E\xA1" => "m2",
            "\xE3\x8F\x8D" => "K.K.",
            "\xE2\x84\xA1" => "TEL",
            "\xE2\x84\x96" => "No.",
            "\xE3\x8B\xBF" => "令和",
            "\xE3\x8D\xBB" => "平成",
            "\xE3\x8D\xBC" => "昭和",
            "\xE3\x8D\xBD" => "大正",
            "\xE3\x8D\xBE" => "明治",
            "\xE3\x88\xB1" => "(株)",
            "\xE3\x88\xB2" => "(有)",
            "\xE3\x88\xB9" => "(代)",
        ];

        return str_replace(array_keys($arr), array_values($arr), $str);
    }

    /**
     * スキーマファイルを利用してデータベース構造を変更する
     *
     * @param array    データベース設定名
     * @param string    スキーマファイルのパス
     * @param string    テーブル指定
     * @param string    更新タイプ指定
     * @return    boolean
     */
    public function loadSchema($dbConfigName, $path, $filterTable = '', $filterType = '', $excludePath = [], $dropField = true)
    {
        // TODO 未実装の為コメントアウト
        /* >>>
        // テーブルリストを取得
        $db = ConnectionManager::get($dbConfigName);
        $db->cacheSources = false;
        $listSources = $db->listSources();
        $prefix = $db->config['prefix'];
        $Folder = new Folder($path);
        $files = $Folder->read(true, true);

        $result = true;

        foreach($files[1] as $file) {
            if (in_array($file, $excludePath)) {
                continue;
            }
            if (preg_match('/^(.*?)\.php$/', $file, $matches)) {
                $type = 'create';
                $table = $matches[1];
                if (preg_match('/^create_(.*?)\.php$/', $file, $matches)) {
                    $type = 'create';
                    $table = $matches[1];
                    if (in_array($prefix . $table, $listSources)) {
                        continue;
                    }
                } elseif (preg_match('/^alter_(.*?)\.php$/', $file, $matches)) {
                    $type = 'alter';
                    $table = $matches[1];
                    if (!in_array($prefix . $table, $listSources)) {
                        continue;
                    }
                } elseif (preg_match('/^drop_(.*?)\.php$/', $file, $matches)) {
                    $type = 'drop';
                    $table = $matches[1];
                    if (!in_array($prefix . $table, $listSources)) {
                        continue;
                    }
                } else {
                    if (in_array($prefix . $table, $listSources)) {
                        continue;
                    }
                }
                if ($filterTable && $filterTable != $table) {
                    continue;
                }
                if ($filterType && $filterType != $type) {
                    continue;
                }
                $tmpdir = TMP . 'schemas' . DS;
                copy($path . DS . $file, $tmpdir . $table . '.php');
                if (!$db->loadSchema(['type' => $type, 'path' => $tmpdir, 'file' => $table . '.php', 'dropField' => $dropField])) {
                    $result = false;
                }
                @unlink($tmpdir . $table . '.php');
            }
        }
        ClassRegistry::flush();
        BcUtil::clearAllCache();
        return $result;
        <<< */
    }

    /**
     * CSVを読み込む
     *
     * @param array    データベース設定名
     * @param string    CSVパス
     * @param string    テーブル指定
     * @return    boolean
     */
    public function loadCsv($dbConfigName, $path, $options = [])
    {
        // TODO 未実装の為コメントアウト
        /* >>>
        $options = array_merge([
            'filterTable' => ''
        ], $options);

        // テーブルリストを取得
        $db = ConnectionManager::get($dbConfigName);
        $db->cacheSources = false;
        $listSources = $db->listSources();
        $prefix = $db->config['prefix'];
        $Folder = new Folder($path);
        $files = $Folder->read(true, true);
        $result = true;
        foreach($files[1] as $file) {
            if (preg_match('/^(.*?)\.csv$/', $file, $matches)) {
                $table = $matches[1];
                if (in_array($prefix . $table, $listSources)) {
                    if ($options['filterTable'] && $options['filterTable'] != $table) {
                        continue;
                    }

                    if (!$db->loadCsv(['path' => $path . DS . $file, 'encoding' => 'auto'])) {
                        $result = false;
                        break;
                    }
                }
            }
        }
        ClassRegistry::flush();
        BcUtil::clearAllCache();
        return $result;
        <<< */
    }

    /**
     * 範囲を指定しての長さチェック
     *
     * @param mixed $check 対象となる値
     * @param int $min 値の最短値
     * @param int $max 値の最長値
     * @param boolean
     */
    public function between($check, $min, $max)
    {
        $check = (is_array($check))? current($check) : $check;
        $length = mb_strlen($check, Configure::read('App.encoding'));
        return ($length >= $min && $length <= $max);
    }

    /**
     * 指定フィールドのMAX値を取得する
     *
     * 現在数値フィールドのみ対応
     *
     * @param string $field
     * @param array $conditions
     * @return int
     * @checked
     * @unitTest
     * @noTodo
     */
    public function getMax(string $field, array $conditions = []): int
    {
        $max = $this->find()->where($conditions)->all()->max($field);
        return $max->{$field} ?? 0;
    }

    /**
     * テーブルにフィールドを追加する
     *
     * @param array $options [ field / column / table ]
     * @return boolean
     */
    public function addField($options)
    {
        return true;
        // TODO 未実装の為コメントアウト
        /* >>>
        extract($options);

        if (!isset($field) || !isset($column)) {
            return false;
        }

        if (!isset($table)) {
            $table = $this->useTable;
        }

        $this->_schema = null;
        $db = ConnectionManager::get($this->useDbConfig);
        $options = ['field' => $field, 'table' => $table, 'column' => $column];
        $ret = $db->addColumn($options);
        $this->deleteModelCache();
        ClassRegistry::flush();
        return $ret;
        <<< */
    }

    /**
     * フィールド構造を変更する
     *
     * @param array $options [ field / column / table ]
     * @return    boolean
     */
    public function editField($options)
    {
        // TODO 未実装の為コメントアウト
        /* >>>
        extract($options);

        if (!isset($field) || !isset($column)) {
            return false;
        }

        if (!isset($table)) {
            $table = $this->useTable;
        }

        $this->_schema = null;
        $db = ConnectionManager::get($this->useDbConfig);
        $options = ['field' => $field, 'table' => $table, 'column' => $column];
        $ret = $db->changeColumn($options);
        $this->deleteModelCache();
        ClassRegistry::flush();
        return $ret;
        <<< */
    }

    /**
     * フィールドを削除する
     *
     * @param array $options [ field / table ]
     * @return    boolean
     */
    public function delField($options)
    {
        return true;
        // TODO 未実装の為コメントアウト
        /* >>>
        extract($options);

        if (!isset($field)) {
            return false;
        }

        if (!isset($table)) {
            $table = $this->useTable;
        }

        $this->_schema = null;
        $db = ConnectionManager::get($this->useDbConfig);
        $options = ['field' => $field, 'table' => $table];
        $ret = $db->dropColumn($options);
        $this->deleteModelCache();
        ClassRegistry::flush();
        return $ret;
        <<< */
    }

    /**
     * フィールド名を変更する
     *
     * @param array $options [ new / old / table ]
     * @param array $column
     * @return boolean
     */
    public function renameField($options)
    {
        // TODO 未実装の為コメントアウト
        /* >>>
        extract($options);

        if (!isset($new) || !isset($old)) {
            return false;
        }

        if (!isset($table)) {
            $table = $this->useTable;
        }

        $this->_schema = null;
        $db = ConnectionManager::get($this->useDbConfig);
        $options = ['new' => $new, 'old' => $old, 'table' => $table];
        $ret = $db->renameColumn($options);
        $this->deleteModelCache();
        ClassRegistry::flush();
        return $ret;
        <<< */
    }

    /**
     * テーブルの存在チェックを行う
     * @param string $tableName
     * @return boolean
     */
    public function tableExists($tableName)
    {
        // TODO 未実装の為コメントアウト
        /* >>>
        $db = ConnectionManager::get($this->useDbConfig);
        $db->cacheSources = false;
        $tables = $db->listSources();
        return in_array($tableName, $tables);
        <<< */
    }

    /**
     * 英数チェック
     *
     * @param string $value チェック対象文字列
     * @return boolean
     */
    public static function alphaNumeric($value)
    {
        if (!$value) {
            return true;
        }
        if (preg_match("/^[a-zA-Z0-9]+$/", $value)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 一つ位置を上げる
     * @param string $id
     * @param array $conditions
     * @return boolean
     */
    public function sortup($id, $conditions)
    {
        return $this->changeSort($id, -1, $conditions);
    }

    /**
     * 一つ位置を下げる
     *
     * @param string $id
     * @param array $conditions
     * @return boolean
     */
    public function sortdown($id, $conditions)
    {
        return $this->changeSort($id, 1, $conditions);
    }

    /**
     * 並び順を変更する
     *
     * @param string $id
     * @param int $offset
     * @param array $options
     *   - conditions: データ取得条件
     *   - sortFieldName: ソートフィールドのカラム名 (初期値: sort)
     * @return boolean
     */
    public function changeSort($id, $offset, $options = [])
    {
        $options += [
            'conditions' => [],
            'sortFieldName' => 'sort',
        ];
        $offset = intval($offset);
        if ($offset === 0) {
            return true;
        }

        $conditions = $options['conditions'];

        $current = $this->get($id);

        // currentを含め変更するデータを取得
        if ($offset > 0) { // DOWN
            $order = [$options['sortFieldName']];
            $conditions[$options['sortFieldName'] . " >="] = $current->{$options['sortFieldName']};
        } else { // UP
            $order = [$options['sortFieldName'] . " DESC"];
            $conditions[$options['sortFieldName'] . " <="] = $current->{$options['sortFieldName']};
        }

        $result = $this->find()
            ->where($conditions)
            ->select(["id", $options['sortFieldName']])
            ->order($order)
            ->limit(abs($offset) + 1)
            ->all();

        $count = $result->count();
        if (!$count) {
            return false;
        }
        $entities = $result->toList();
        //データをローテーション
        $currentNewValue = $entities[$count - 1]->{$options['sortFieldName']};
        for($i = $count - 1; $i > 0; $i--) {
            $entities[$i]->{$options['sortFieldName']} = $entities[$i - 1]->{$options['sortFieldName']};
        }
        $entities[0]->{$options['sortFieldName']} = $currentNewValue;

        if (!$this->saveMany($entities)) {
            return false;
        }

        return true;
    }

    /**
     * Deconstructs a complex data type (array or object) into a single field value.
     *
     * @param string $field The name of the field to be deconstructed
     * @param mixed $data An array or object to be deconstructed into a field
     * @return mixed The resulting data that should be assigned to a field
     */
    public function deconstruct($field, $data)
    {
        if (!is_array($data)) {
            return $data;
        }

        $type = $this->getColumnType($field);

        // >>> CUSTOMIZE MODIFY 2013/11/10 ryuring 和暦対応
        /* if (!in_array($type, array('datetime', 'timestamp', 'date', 'time'))) { */
        // ---
        if (!in_array($type, ['string', 'text', 'datetime', 'timestamp', 'date', 'time'])) {
            // <<<
            return $data;
        }

        $useNewDate = (isset($data['year']) || isset($data['month']) ||
            isset($data['day']) || isset($data['hour']) || isset($data['minute']));

        // >>> CUSTOMIZE MODIFY 2013/11/10 ryuring 和暦対応
        /* $dateFields = array('Y' => 'year', 'm' => 'month', 'd' => 'day', 'H' => 'hour', 'i' => 'min', 's' => 'sec'); */
        // ---
        $dateFields = ['W' => 'wareki', 'Y' => 'year', 'm' => 'month', 'd' => 'day', 'H' => 'hour', 'i' => 'min', 's' => 'sec'];
        // <<<
        $timeFields = ['H' => 'hour', 'i' => 'min', 's' => 'sec'];
        $date = [];

        if (isset($data['meridian']) && empty($data['meridian'])) {
            return null;
        }

        if (isset($data['hour']) &&
            isset($data['meridian']) &&
            !empty($data['hour']) &&
            $data['hour'] != 12 &&
            $data['meridian'] === 'pm'
        ) {
            $data['hour'] = $data['hour'] + 12;
        }
        if (isset($data['hour']) && isset($data['meridian']) && $data['hour'] == 12 && $data['meridian'] === 'am') {
            $data['hour'] = '00';
        }
        if ($type === 'time') {
            foreach($timeFields as $key => $val) {
                if (!isset($data[$val]) || $data[$val] === '0' || $data[$val] === '00') {
                    $data[$val] = '00';
                } elseif ($data[$val] !== '') {
                    $data[$val] = sprintf('%02d', $data[$val]);
                }
                if (!empty($data[$val])) {
                    $date[$key] = $data[$val];
                } else {
                    return null;
                }
            }
        }

        // >>> CUSTOMIZE MODIFY 2013/11/10 ryuring 和暦対応
        /* if ($type === 'datetime' || $type === 'timestamp' || $type === 'date') { */
        // ---
        if ($type == 'text' || $type == 'string' || $type === 'datetime' || $type === 'timestamp' || $type === 'date') {
            // <<<
            foreach($dateFields as $key => $val) {
                if ($val === 'hour' || $val === 'min' || $val === 'sec') {
                    if (!isset($data[$val]) || $data[$val] === '0' || $data[$val] === '00') {
                        $data[$val] = '00';
                    } else {
                        $data[$val] = sprintf('%02d', $data[$val]);
                    }
                }

                // >>> CUSTOMIZE ADD 2013/11/10 ryuring 和暦対応
                if ($val == 'wareki' && !empty($data['wareki'])) {
                    $warekis = ['m' => 1867, 't' => 1911, 's' => 1925, 'h' => 1988, 'r' => 2018];
                    if (!empty($data['year'])) {
                        [$wareki, $year] = explode('-', $data['year']);
                        $data['year'] = $year + $warekis[$wareki];
                    }
                }
                // <<<
                // >>> CUSTOMIZE ADD 2013/11/10 ryuring 和暦対応
                /* if (!isset($data[$val]) || isset($data[$val]) && (empty($data[$val]) || $data[$val][0] === '-')) {
                  return null; */
                // ---
                if ($val != 'wareki' && !isset($data[$val]) || isset($data[$val]) && (empty($data[$val]) || (isset($data[$val][0]) && $data[$val][0] === '-'))) {
                    if ($type == 'text' || $type == 'string') {
                        return $data;
                    } else {
                        return null;
                    }
                }
                if (isset($data[$val]) && !empty($data[$val])) {
                    $date[$key] = $data[$val];
                }
            }
        }

        if ($useNewDate && !empty($date)) {
            // >>> CUSTOMIZE MODIFY 2013/11/10 ryuring 和暦対応
            /* $format = $this->getDataSource()->columns[$type]['format']; */
            // ---
            if ($type == 'text' || $type == 'string') {
                $format = 'Y-m-d H:i:s';
            } else {
                $format = $this->getDataSource()->columns[$type]['format'];
            }
            // <<<

            foreach(['m', 'd', 'H', 'i', 's'] as $index) {
                if (isset($date[$index])) {
                    $date[$index] = sprintf('%02d', $date[$index]);
                }
            }
            return str_replace(array_keys($date), array_values($date), $format);
        }
        return $data;
    }

    /**
     * 指定したモデル以外のアソシエーションを除外する
     *
     * @param array $auguments アソシエーションを除外しないモデル。
     * 　「.（ドット）」で区切る事により、対象モデルにアソシエーションしているモデルがさらに定義しているアソシエーションを対象とする事ができる
     * 　（例）UserGroup.Permission
     * @param boolean $reset バインド時に１回の find でリセットするかどうか
     * @return void
     */
    public function reduceAssociations($arguments, $reset = true)
    {
        $models = [];

        foreach($arguments as $index => $argument) {
            if (is_array($argument)) {
                if (count($argument) > 0) {
                    $arguments = am($arguments, $argument);
                }
                unset($arguments[$index]);
            }
        }

        foreach($arguments as $index => $argument) {
            if (!is_string($argument)) {
                unset($arguments[$index]);
            }
        }

        if (count($arguments) == 0) {
            $models[$this->name] = [];
        } else {
            foreach($arguments as $argument) {
                if (strpos($argument, '.') !== false) {
                    $model = substr($argument, 0, strpos($argument, '.'));
                    $child = substr($argument, strpos($argument, '.') + 1);

                    if ($child == $model) {
                        $models[$model] = [];
                    } else {
                        $models[$model][] = $child;
                    }
                } else {
                    $models[$this->name][] = $argument;
                }
            }
        }

        $relationTypes = ['belongsTo', 'hasOne', 'hasMany', 'hasAndBelongsToMany'];

        foreach($models as $bindingName => $children) {
            $model = null;

            foreach($relationTypes as $relationType) {
                $currentRelation = (isset($this->$relationType)? $this->$relationType : null);
                if (isset($currentRelation) && isset($currentRelation[$bindingName]) &&
                    is_array($currentRelation[$bindingName]) && isset($currentRelation[$bindingName]['className'])) {
                    $model = $currentRelation[$bindingName]['className'];
                    break;
                }
            }

            if (!isset($model)) {
                $model = $bindingName;
            }

            if (isset($model) && $model != $this->name && isset($this->$model)) {
                if (!isset($this->__backInnerAssociation)) {
                    $this->__backInnerAssociation = [];
                }
                $this->__backInnerAssociation[] = $model;
                $this->$model->reduceAssociations($children, $reset);
            }
        }

        if (isset($models[$this->name])) {
            foreach($models as $model => $children) {
                if ($model != $this->name) {
                    $models[$this->name][] = $model;
                }
            }

            $models = array_unique($models[$this->name]);
            $unbind = [];

            foreach($relationTypes as $relation) {
                if (isset($this->$relation)) {
                    foreach($this->$relation as $bindingName => $bindingData) {
                        if (!in_array($bindingName, $models)) {
                            $unbind[$relation][] = $bindingName;
                        }
                    }
                }
            }
            if (count($unbind) > 0) {
                $this->unbindModel($unbind, $reset);
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function deleteAll($conditions): int
    {
        $result = parent::deleteAll($conditions);
        if ($result) {
            // TODO 未実装の為コメントアウト
            /* >>>
            if ($this->Behaviors->attached('BcCache') && $this->Behaviors->enabled('BcCache')) {
                $this->delCache($this);
            }
            <<< */
        }
        return $result;
    }

    /**
     * @inheritDoc
     */
    public function updateAll($fields, $conditions): int
    {
        $result = parent::updateAll($fields, $conditions);
        if ($result) {
            // TODO 未実装の為コメントアウト
            /* >>>
            if ($this->Behaviors->attached('BcCache') && $this->Behaviors->enabled('BcCache')) {
                $this->delCache($this);
            }
            <<< */
        }
        return $result;
    }

    /**
     * Used to report user friendly errors.
     * If there is a file app/error.php or app/app_error.php this file will be loaded
     * error.php is the AppError class it should extend ErrorHandler class.
     *
     * @param string $method Method to be called in the error class (AppError or ErrorHandler classes)
     * @param array $messages Message that is to be displayed by the error class
     */
    public function cakeError($method, $messages = [])
    {
        //======================================================================
        // router.php がロードされる前のタイミング（bootstrap.php）でエラーが発生した場合、
        // AppControllerなどがロードされていない為、Object::cakeError() を実行する事ができない。
        // router.php がロードされる前のタイミングでは、通常のエラー表示を行う
        //======================================================================
        if (!Configure::read('BcRequest.routerLoaded')) {
            trigger_error($method, E_USER_ERROR);
        } else {
            parent::cakeError($method, $messages);
        }
    }

    /**
     * Creates a new Query for this repository and applies some defaults based on the
     * type of search that was selected.
     *
     * ### Model.beforeFind event
     *
     * Each find() will trigger a `Model.beforeFind` event for all attached
     * listeners. Any listener can set a valid result set using $query
     *
     * By default, `$options` will recognize the following keys:
     *
     * - fields
     * - conditions
     * - order
     * - limit
     * - offset
     * - page
     * - group
     * - having
     * - contain
     * - join
     *
     * ### Usage
     *
     * Using the options array:
     *
     * ```
     * $query = $articles->find('all', [
     *   'conditions' => ['published' => 1],
     *   'limit' => 10,
     *   'contain' => ['Users', 'Comments']
     * ]);
     * ```
     *
     * Using the builder interface:
     *
     * ```
     * $query = $articles->find()
     *   ->where(['published' => 1])
     *   ->limit(10)
     *   ->contain(['Users', 'Comments']);
     * ```
     *
     * ### Calling finders
     *
     * The find() method is the entry point for custom finder methods.
     * You can invoke a finder by specifying the type:
     *
     * ```
     * $query = $articles->find('published');
     * ```
     *
     * Would invoke the `findPublished` method.
     *
     * @param string $type the type of query to perform
     * @param array $options An array that will be passed to Query::applyOptions()
     * @return \Cake\ORM\Query The query builder
     */
    public function find(string $type = 'all', array $options = []): Query
    {

        $query = $this->query();
        $query->select();

        return $this->callFinder($type, $query, $options);

        // TODO 未実装の為コメントアウト
        /* >>>
        // CUSTOMIZE MODIFY 2012/04/23 ryuring
        // キャッシュビヘイビアが利用状態の場合、モデルデータキャッシュを読み込む
        //
        // 【AppModelではキャッシュを定義しない事】
        // 自動的に生成されるクラス定義のない関連モデルの処理で勝手にキャッシュを利用されないようにする為
        // （HABTMの更新がうまくいかなかったので）
        // >>>
        //$results = $this->getDataSource()->read($this, $query);
        // ---
        $cache = true;
        if (isset($query['cache']) && is_bool($query['cache'])) {
            $cache = $query['cache'];
            unset($query['cache']);
        }
        if (BcUtil::isInstalled() && isset($this->Behaviors) && $this->Behaviors->attached('BcCache') &&
            $this->Behaviors->enabled('BcCache') && Configure::read('debug') == 0) {
            // ===========================================================================================
            // 2016/09/22 ryuring
            // PHP 7.0.8 環境にて、コンテンツ一覧追加時、検索インデックス作成の為、BcContentsComponent 経由で
            // 呼び出されるが、その際だけ、モデルのマジックメソッドの戻り値を返すタイミングで処理がストップしてしまう。
            // その為、ビヘイビアのメソッドを直接実行して対処した。
            // CakePHPも、PHP自体のエラーも発生せず、ただ止まる。PHP7のバグ？PHP側のメモリーを256Mにしても変わらず。
            // ===========================================================================================
            $results = $this->Behaviors->BcCache->readCache($this, $cache, $type, $query);
        } else {
            $results = $this->getDataSource()->read($this, $query);
        }
        // <<<

        $this->resetAssociations();

        if ($query['callbacks'] === true || $query['callbacks'] === 'after') {
            $results = $this->_filterResults($results);
        }

        $this->findQueryType = null;

        if ($type === 'all') {
            return $results;
        } else {
            if ($this->findMethods[$type] === true) {
                return $this->{'_find' . ucfirst($type)}('after', $query, $results);
            }
        }
        <<< */
    }

    /**
     * データが公開済みかどうかチェックする
     *
     * @param boolean $status 公開ステータス
     * @param string $publishBegin 公開開始日時
     * @param string $publishEnd 公開終了日時
     * @return bool
     */
    public function isPublish($status, $publishBegin, $publishEnd)
    {
        // TODO 未実装の為コメントアウト
        /* >>>
        $Content = ClassRegistry::init('Content');
        return $Content->isPublish($status, $publishBegin, $publishEnd);
        <<< */
    }

    /**
     * @inheritDoc
     */
    public function exists($conditions): bool
    {
        return parent::exists($conditions);

        // TODO 未実装の為コメントアウト
        /* >>>
        if ($this->Behaviors->loaded('SoftDelete')) {
            return $this->existsAndNotDeleted($id);
        } else {
            return parent::exists($conditions);
        }
        <<< */
    }

    /**
     * {@inheritDoc}
     *
     * For HasMany and HasOne associations records will be removed based on
     * the dependent option. Join table records in BelongsToMany associations
     * will always be removed. You can use the `cascadeCallbacks` option
     * when defining associations to change how associated data is deleted.
     *
     * ### Options
     *
     * - `atomic` Defaults to true. When true the deletion happens within a transaction.
     * - `checkRules` Defaults to true. Check deletion rules before deleting the record.
     *
     * ### Events
     *
     * - `Model.beforeDelete` Fired before the delete occurs. If stopped the delete
     *   will be aborted. Receives the event, entity, and options.
     * - `Model.afterDelete` Fired after the delete has been successful. Receives
     *   the event, entity, and options.
     * - `Model.afterDeleteCommit` Fired after the transaction is committed for
     *   an atomic delete. Receives the event, entity, and options.
     *
     * The options argument will be converted into an \ArrayObject instance
     * for the duration of the callbacks, this allows listeners to modify
     * the options used in the delete operation.
     *
     * @param \Cake\Datasource\EntityInterface $entity The entity to remove.
     * @param array|\ArrayAccess $options The options for the delete.
     * @return bool success
     */
    public function delete(EntityInterface $entity, $options = []): bool
    {
        $result = parent::delete($entity, $options);
        // TODO ucmitz 未実装の為コメントアウト
        // if ($result === false && $this->Behaviors->enabled('SoftDelete')) {
        //     $this->getEventManager()->dispatch(new CakeEvent('Model.afterDelete', $this));
        //     return (bool)$this->field('deleted', ['deleted' => 1]);
        // }
        return $result;
    }

    /**
     * コンテンツのURLにマッチする候補を取得する
     *
     * @param string $url
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getUrlPattern($url)
    {
        $parameter = preg_replace('|^/|', '', $url);
        $paths = [];
        $paths[] = '/' . $parameter;
        if (preg_match('|/$|', $paths[0])) {
            $paths[] = $paths[0] . 'index';
        } elseif (preg_match('|^(.*?/)index$|', $paths[0], $matches)) {
            $paths[] = $matches[1];
        } elseif (preg_match('|^(.+?)\.html$|', $paths[0], $matches)) {
            $paths[] = $matches[1];
            if (preg_match('|^(.*?/)index$|', $matches[1], $matches)) {
                $paths[] = $matches[1];
            }
        }
        return $paths;
    }

    /**
     * 公開状態となっているデータを取得するための conditions 値を取得
     *
     * 公開状態（初期値：status）、公開開始日（初期値：publish_begin）、公開終了日（初期値：publish_end）
     * の組み合わせてによって配列を生成する。
     *
     * 公開状態が true であったとしても、公開期間が設定されている場合はそちらを優先する。
     *
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getConditionAllowPublish()
    {
        $conditions[$this->getAlias() . '.' . $this->publishStatusField] = true;
        $conditions[] = ['or' => [[$this->getAlias() . '.' . $this->publishBeginField . ' <=' => date('Y-m-d H:i:s')],
            [$this->getAlias() . '.' . $this->publishBeginField . ' IS' => null]]];
        $conditions[] = ['or' => [[$this->getAlias() . '.' . $this->publishEndField . ' >=' => date('Y-m-d H:i:s')],
            [$this->getAlias() . '.' . $this->publishEndField . ' IS' => null]]];
        return $conditions;
    }

    /**
     * イベントを一時的にオフにする
     * @param string $eventKey
     */
    public function offEvent($eventKey)
    {
        $eventManager = $this->getEventManager();
        $this->tmpEvents[$eventKey] = $eventManager->listeners($eventKey);
        $eventManager->off($eventKey);
    }

    /**
     * 一時的にオフにしたイベントをオンにする
     * BcModelEventDispatcherは対象外とする
     * @param string $eventKey
     * @checked
     * @noTodo
     * @unitTest
     */
    public function onEvent($eventKey)
    {
        if (!isset($this->tmpEvents[$eventKey])) return;
        $eventManager = $this->getEventManager();
        foreach($this->tmpEvents[$eventKey] as $listener) {
            if (get_class($listener['callable'][0]) !== 'BaserCore\Event\BcModelEventDispatcher') {
                $eventManager->on($eventKey, [], $listener['callable']);
            }
        }
        unset($this->tmpEvents[$eventKey]);
    }

}
