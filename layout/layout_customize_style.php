<?php
if (!empty($p_user)) {
    $settings = UserSettingsUtil::getUserSettings($p_user->id);
    if (!empty($settings)) {
        ?>
        <style>
            .bg{
                <?php if ($settings->getBgImageActive() == 1) { ?>
                    background-image:  url(<?= $settings->getBgImage() ?>);
                    background-repeat: <?= $settings->getBgImageRepeat() ?>;
                    background-attachment: fixed;
                <?php } ?>
                <?php if ($settings->getBgColorActive() == 1) { ?>
                    background-color:  <?= $settings->getBgColor() ?>;
                <?php } ?>
            }
        </style>
        <?php
    }
}
?>