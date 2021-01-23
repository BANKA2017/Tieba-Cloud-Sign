<?php
define('SYSTEM_DEV', true);
define('SYSTEM_NO_CHECK_VER', true);
define('SYSTEM_NO_CHECK_LOGIN', true);
define('SYSTEM_NO_PLUGIN', true);
include __DIR__ . '/../init.php';
global $m,$i;
$cv = option::get('core_version');
if (!empty($cv) && $cv >= '4.93') {
    msg('您的云签到已升级到 V4.93 版本，请勿重复更新<br/><br/>请立即删除 /setup/update4.92to4.93.php');
}
$m->query("ALTER TABLE `" . DB_PREFIX . "baiduid` DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci;", true);
$m->query("ALTER TABLE `" . DB_PREFIX . "baiduid` DROP INDEX `name`;", true);
$m->query("ALTER TABLE `" . DB_PREFIX . "baiduid` CHANGE `name` `name` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL;", true);
$m->query("ALTER TABLE `" . DB_PREFIX . "baiduid` ADD `name_show` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL AFTER `name`, ADD `portrait` VARCHAR(40) NOT NULL AFTER `name_show`;", true);
$m->query("ALTER TABLE `" . DB_PREFIX . "baiduid` ADD INDEX(`portrait`);", true);

//update name_show and portrait
$result = $m->query("SELECT bduss FROM ".DB_PREFIX."baiduid;");
while ($row = $result->fetch_assoc()) {
    $baiduUserInfo = getBaiduUserInfo($row["bduss"]);
    if (!empty($baiduUserInfo["portrait"])) {
        $baidu_name = sqladds($baiduUserInfo["name"]);
        $baidu_name_show = sqladds($baiduUserInfo["name_show"]);
        $baidu_name_portrait = sqladds($baiduUserInfo["portrait"]);
        $m->query("UPDATE ".DB_PREFIX."baiduid SET `name` = '{$baidu_name}', `name_show` = '{$baidu_name_show}', `portrait` = '{$baidu_name_portrait}' WHERE `bduss` = '{$row["bduss"]}';");
    }
}

option::set('core_version' , '4.93');
unlink(__FILE__);
msg('您的云签到已成功升级到 V4.93 版本，请立即删除 /setup/update4.92to4.93.php，谢谢', SYSTEM_URL);