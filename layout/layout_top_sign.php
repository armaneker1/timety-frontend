<?php
if (!isset($checkUserStatus)) {
    $checkUserStatus = true;
}
$user = SessionUtil::checkLoggedinUser($checkUserStatus);
?>
<div style="position:absolute;left: 50%;margin-left: -317px;z-index: 1000000;font-size: 30px;top: 83px;"><span><?= LanguageUtils::getText("LANG_PAGE_REGISTER_TOP_NO_USER_HEADER_TEXT") ?></span></div>
<div class="u_bg"></div>

<!--Loader animation-->
<div class="loader" style="display: none"></div>

<!--information popup-->
<div class="info_popup_open" style="display: none">
    <button class="info_popup_close" style="cursor: pointer"></button>
</div>

<div id="top_blm">
    <div id="top_blm_sol">
        <div class="logo" style="position: absolute;left: 50%;margin-left: -100px;"><a href="<?= HOSTNAME ?>"><img src="<?= HOSTNAME ?>images/timety.png" width="120" height="45" border="0" /></a></div>
    </div>
    <div id="top_blm_sag">
        <?php
        $signin_class = "";
        $create_class = "";

        if (!empty($page_id) && $page_id == "createaccount") {
            $create_class = "cr_acc_hover";
        }

        if (!empty($page_id) && $page_id == "signin") {
            $signin_class = "sgn_in_hover";
        }
        ?>
        <div class="t_account"><a href="<?= PAGE_SIGNUP ?>" class="cr_acc <?= $create_class ?>"><?= LanguageUtils::getText('LANG_PAGE_REGISTER_TOP_NO_USER_CREATE_ACCOUNT') ?></a><a href="<?= PAGE_LOGIN ?>" class="sgn_in <?= $signin_class ?>"><?= LanguageUtils::getText('LANG_PAGE_REGISTER_TOP_NO_USER_SIGNIN') ?></a></div>

    </div>
</div>

<script>
    jQuery(document).ready(function(){
        try{
            checkFollowerList();
        }catch(exp){
            console.log(exp);
        }
    });
</script>
