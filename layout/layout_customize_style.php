<?php
if (!empty($p_user)) {
    $settings = UserSettingsUtil::getUserSettings($p_user->id);
    if (!empty($settings)) {
        ?>
        <style>
            .bg{
                <?php if ($settings->getBgImageActive() == 1) { ?>
                background-image:  url('<?= $settings->getBgImage()?>') !important;
                background-repeat: <?= $settings->getBgImageRepeat() ?> !important;
                    background-attachment: fixed !important;
                <?php } ?>
                <?php if ($settings->getBgColorActive() == 1) { ?>
                    background-color:  <?= $settings->getBgColor() ?> !important;
                <?php } ?>
            }
        </style>
        <?php
    }
}
?>