/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @since           baserCMS v 4.0.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * コンテンツ一覧
 */

$(function () {

    var contentsIndexSearchOpened = $("#SearchBoxOpened").html();

    $.bcTree.init({
        isAdmin: $("#AdminContentsIndexScript").attr('data-isAdmin'),
        isUseMoveContents: $("#AdminContentsIndexScript").attr('data-isUseMoveContents'),
        adminPrefix: $("#AdminContentsIndexScript").attr('data-adminPrefix'),
        baserCorePrefix: $("#AdminContentsIndexScript").attr('data-baserCorePrefix'),
        editInIndexDisabled: $("#AdminContentsIndexScript").attr('data-editInIndexDisabled'),
    });

    // マウスダウンイベント
    $(window).bind("mousedown", $.bcTree.updateShiftAndCtrlOnAnchor);

    // サイト変更時
    $("#viewsetting-site-id").change(function () {
        $.bcUtil.showLoader();
        var siteId = $("#viewsetting-site-id").val();
        if (siteId == undefined) {
            siteId = 0;
        }
        // メニューを再構築する必要があるため、ajax ではなく遷移させる
        location.href = $.baseUrl() + $.bcTree.config.baserCorePrefix + $.bcTree.config.adminPrefix + '/' + 'baser-core' + '/contents/index?current_site_id=' + siteId + '\&list_type=1';
    });

    if (location.pathname === "/baser/admin/baser-core/contents/index" && $("input[name='ViewSetting[list_type]']:checked").val() == 1) {
        // 初回アクセス時
        loadView();
    }

    // 表示変更時
    $("input[name='ViewSetting[list_type]']").change(() => {
        switch ($("input[name='ViewSetting[list_type]']:checked").val()) {
            case "1":
                url = $.baseUrl() + $.bcTree.config.baserCorePrefix + $.bcTree.config.adminPrefix + '/' + 'baser-core' + '/contents/index?site_id=' + $("#viewsetting-site-id").val() + '\&list_type=1';
                // FIXME:　条件をうまく取れないので確認する
                let extraParams = {
                    // 'name' : '',
                    // 'type' : '',
                    // 'self_status' : "1",
                    // 'author_id' : '',
                };
                let extraQuery = $.param(extraParams);
                location.href = url + '&' + extraQuery;
                // $("#ContentIndexForm").attr('action', url);
                // $.baserAjaxDataList.search();
                break;
            case "2":
                loadView();
                break;
        }
    });

    // 新規追加クリック時
    $("#BtnAddContent").click($.bcTree.showMenuByOuter);

    // ドラッグ＆ドロップイベント
    $(document).on("dnd_stop.vakata", $.bcTree.orderContent);
    $(document).on("dnd_start.vakata", $.bcTree.changeDnDCursor);

    $.bcUtil.disabledHideMessage = true;
    $($.bcTree).bind('loaded', function () {
        $.bcUtil.disabledHideMessage = false;
    });
    $($.baserAjaxDataList).bind('searchLoaded', function () {
        $.bcUtil.disabledHideMessage = false;
    });

    $.baserAjaxDataList.config.methods.del.confirm = bcI18n.confirmMessage1;
    $.baserAjaxBatch.config.methods.del.confirm = bcI18n.confirmMessage2;
    $.baserAjaxBatch.config.methods.unpublish.result = function () {
        $.bcUtil.showLoader();
        loadTable();
    };
    $.baserAjaxBatch.config.methods.publish.result = function () {
        $.bcUtil.showLoader();
        loadTable();
    };
    $.baserAjaxDataList.config.methods.publish.result = null;
    $.baserAjaxDataList.config.methods.unpublish.result = null;
    $.baserAjaxDataList.config.methods.copy.result = function (row, result) {
        // ローダーが一瞬切れるので強制的に表示
        $.bcUtil.showLoader();
        $("#ToTop a").click();
        loadTable();
        $.bcUtil.showNoticeMessage(bcI18n.infoMessage1.sprintf($.parseJSON(result).title));
    };
    $.baserAjaxDataList.init();
    $.baserAjaxBatch.init({url: $.baseUrl() + $.bcTree.config.baserCorePrefix + $.bcTree.config.adminPrefix + '/' + 'baser-core' + '/contents/ajax_batch'});

    //$("#Search").before($("#ViewSetting"));

    // 検索の際強制的に表示設定を表敬式に設定
    // ここで検索処理を登録する代わりに basreAjaxDataList側のイベントを削除
    $("#BtnSearchSubmit").click(function () {
        contentsIndexSearchOpened = true;
        $("input[name='ViewSetting[list_type]']:eq(1)").prop('checked', true);
        loadView();
        return false;
    });
    $._data($("#BtnSearchSubmit").get(0)).events.click.shift();
    $._data($("#ContentIndexForm").get(0)).events.submit.shift();

    $("#BtnOpenTree").click(function () {
        $.bcTree.jsTree.open_all();
    });
    $("#BtnCloseTree").click(function () {
        $.bcTree.jsTree.close_all();
        $.bcTree.jsTree.open_node($.bcTree.jsTree.get_json(), false, false);
    });

    /**
     * 表示初期化
     */
    function loadView(e) {
        // サイトが変わった場合はリセット
        if (e !== undefined && e.target.id == 'viewsetting-site-id') {
            $("#BtnSearchClear").click();
            $.ajax({
                url: $.baseUrl() + $.bcTree.config.baserCorePrefix + $.bcTree.config.adminPrefix + '/' + 'baser-core' + '/contents/ajax_get_content_folder_list/' + $(this).val(),
                type: "GET",
                dataType: "json",
                beforeSend: function () {
                    $("#ContentFolderId").prop('disabled', true);
                },
                complete: function () {
                    $("#ContentFolderId").removeAttr("disabled");
                },
                success: function (result) {
                    $("#ContentFolderId").empty();
                    var optionItems = [];
                    optionItems.push(new Option("指定なし", ""));
                    for (key in result) {
                        optionItems.push(new Option(result[key].replace(/&nbsp;/g, "\u00a0"), key));
                    }
                    $("#ContentFolderId").append(optionItems);
                }
            });
        }
        var mode = $("#viewsetting-mode").val();
        var listType = $("input[name='ViewSetting[list_type]']:checked").val();
        if (listType == undefined || mode == 'trash') {
            listType = "1";
        }
        switch (listType) {
            case "1":
                $.bcTree.load();
                $("#BtnAddContent").parent().show();
                if ($("#Search").is(":hidden")) {
                    contentsIndexSearchOpened = false;
                } else {
                    contentsIndexSearchOpened = true;
                }
                $("#GrpChangeTreeOpenClose").show();
                break;
            case "2":
                loadTable();
                $("#BtnAddContent").parent().hide();
                if (contentsIndexSearchOpened) {
                    $("#Search").show();
                } else {
                    $("#Search").hide();
                }
                $("#GrpChangeTreeOpenClose").hide();
                break;
        }

    }

    /**
     * 表形式のリストをロードする
     */
    function loadTable() {
        url = $.baseUrl() + $.bcTree.config.baserCorePrefix + $.bcTree.config.adminPrefix + '/' + 'baser-core' + '/contents/index?site_id=' + $("#viewsetting-site-id").val() + '\&list_type=2';
        let extraParams = {
            'open' : '1',
            'name' : '',
            'folder_id' : '',
            'type' : '',
            'self_status' : '1',
            'author_id' : '',
        };
        let extraQuery = $.param(extraParams);
        location.href = url + '&' + extraQuery;
        // $("#ContentIndexForm").attr('action', url);
        // $.baserAjaxDataList.search();
    }

});
