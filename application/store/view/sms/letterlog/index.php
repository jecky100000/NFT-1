<div class="row-content am-cf">
    <div class="row">
        <div class="am-u-sm-12 am-u-md-12 am-u-lg-12">
            <div class="widget am-cf">
                <div class="widget-head am-cf">
                    <div class="widget-title am-cf">短信发送日志</div>
                </div>
                <!-- 工具栏 -->
                <div class="page_toolbar am-margin-bottom-xs am-cf">
                    <form class="toolbar-form" action="">
                        <input type="hidden" name="s" value="/<?= $request->pathinfo() ?>">
                        <div class="am-u-sm-12 am-u-md-9 am-u-sm-push-3">
                            <div class="am fr">
                                <div class="am-form-group am-fl">
                                    <div class="am-input-group am-input-group-sm tpl-form-border-form">
                                        <input type="text" class="am-form-field" name="form_mobile"
                                           placeholder="请输入发送手机号"
                                           value="<?= $request->get('form_mobile') ?>">
                                    </div>
                                </div>
                                <div class="am-form-group am-fl">
                                    <div class="am-input-group am-input-group-sm tpl-form-border-form">
                                        <input type="text" class="am-form-field" name="mobile"
                                               placeholder="请输入接收手机号"
                                               value="<?= $request->get('mobile') ?>">
                                        <div class="am-input-group-btn">
                                            <button class="am-btn am-btn-default am-icon-search"
                                                    type="submit"></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="widget-body am-fr">
                    <div class="am-u-sm-12 am-u-md-6 am-u-lg-6">
                        <div class="am-form-group">
                            <div class="am-btn-toolbar">
                            </div>
                        </div>
                    </div>
                    <div class="am-scrollable-horizontal am-u-sm-12">
                        <table width="100%" class="am-table am-table-compact am-table-striped
                         tpl-table-black am-text-nowrap">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>内容</th>
                                <th>发送手机号</th>
                                <th>接收手机号</th>
                                <th>发送用户</th>
                                <th>接收用户</th>
                                <th>发送时间</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if (!$list->isEmpty()): foreach ($list as $item): ?>
                                <tr>
                                    <td class="am-text-middle"><?= $item['id'] ?></td>
                                    <td class="am-text-middle"><?= $item['re'] ?></td>
                                    <td class="am-text-middle"><?= $item['form_mobile'] ?></td>
                                    <td class="am-text-middle"><?= $item['mobile'] ?></td>
                                    <td class="am-text-middle"><?= $item->formuser['nickName']?></td>
                                    <td class="am-text-middle"><?= isset($item->touser)?$item->touser['nickName']:''?></td>
                                    <td class="am-text-middle"><?= $item['create_time'] ?></td>
                                </tr>
                            <?php endforeach; else: ?>
                                <tr>
                                    <td colspan="10" class="am-text-center">暂无记录</td>
                                </tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="am-u-lg-12 am-cf">
                        <div class="am-fr"><?= $list->render() ?> </div>
                        <div class="am-fr pagination-total am-margin-right">
                            <div class="am-vertical-align-middle">总记录：<?= $list->total() ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(function () {
    });
</script>

