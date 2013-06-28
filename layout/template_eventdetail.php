<div class="mainEventContainer" id="mainEventContainer" style="position: relative; display: none;padding-bottom: 45px;">
    <div id="leftEventContainer" class="leftEventContainer">
        <div id="headerImage" class="headerImage roundedCorner">
            <img id="big_image_header" src="<?= HOSTNAME ?>images/loader.gif" width="30" height="30" border="0"/>
            <iframe id="youtube_player" style="display: none;" type="text/html" width="" height="" frameborder="0" src="<?= HOSTNAME ?>cache/index.html"></iframe>
        </div>
        <div id="eventDesc" class="eventDesc roundedCorner">
            <div class="descText">
                <p id="m_event_description">Event Description</p>
            </div>
            <div class="shareEvent"> 
                <a id="fb_share_button" class="fbIcon rounded">Facebook</a>
                <a id="tw_share_button" class="twIcon rounded">Twitter</a>
                <a id="gg_share_button" class="gpIcon rounded">Google+</a>
                <a class="mailIcon rounded" href="mailto:hi@timety.com?Subject="><?= LanguageUtils::getText("LANG_MAIL_TEXT") ?></a>
            </div>
        </div>
        <div id="m_event_write_comment" class="eventComment roundedCorner">
            <div>
                <?php
                $user_img = PAGE_GET_IMAGEURL . 'images/anonymous.png&h=45&w=45';
                if (!empty($user)) {
                    $user_img = PAGE_GET_IMAGEURL . PAGE_GET_IMAGEURL_SUBFOLDER . $user->getUserPic()."&h=45&h=45";
                }
                ?>
                <a id="usr_comment_photo" class="profileImage" style="background-image: url('<?= $user_img ?>')">
                </a>
            </div>
            <div class="textField">
                <textarea id="sendComment" class="content" eventid="" placeholder="<?= LanguageUtils::getText("LANG_PAGE_EVENT_DETAIL_COMMENT_INPUT_PLACEHOLDER") ?>"></textarea>
            </div>
            <div>
                <button class="submitComment" type="button" onclick="sendComment()"><?= LanguageUtils::getText("LANG_PAGE_EVENT_DETAIL_COMMENT_BUTTON") ?></button>
            </div>
        </div>
        <div id="m_comment_template" class="eventUserComment roundedCorner" style="display: none">
            <div>
                <a id="m_comment_user_img" class="profileImage" style="background-image: url('<?= PAGE_GET_IMAGEURL ?>images/anonymous.png&h=23&w=23')"></a>
                <a id="m_comment_user" style="cursor: pointer;">Comment User Name</a>
                <a id="m_comment_time" style="float: right; margin-right: 17px;">Comment Time</a>
                <div class="commentText">
                    <p id="m_comment_text">Comment Text</p>
                </div>
            </div>
        </div>
    </div>    
    <div id="rightEventContainer" class="rightEventContainer">
        <div id="eventDetail" class="eventDetail roundedCorner">
            <h1 id="m_event_title">Event Title</h1>
            <div class="userImage" style="cursor: pointer;">
                <img id="m_event_creator_img" src="<?= PAGE_GET_IMAGEURL ?>images/anonymous.png&h=24&w=24">
            </div>
            <h2 id="m_event_creator_name" style="display:inline-block;line-height: 22px; vertical-align: top;cursor: pointer;">Event Creator Name</h2>
            <a  
                type="button" 
                name="" 
                value="" 
                follow_id=""
                active_class="modal_follow_btn"
                passive_class="modal_followed_btn"
                f_status="follow"
                class="modal_follow_btn" 
                id="foll_modal_creator" 
                onclick="followUser(null,null,this);">
                <span class="follow_text"><?= LanguageUtils::getText("LANG_PAGE_EVENT_DETAIL_FOLLOW") ?></span>
                <span class="following_text"><?= LanguageUtils::getText("LANG_PAGE_EVENT_DETAIL_FOLLOWING") ?></span>
                <span class="unfollow_text"><?= LanguageUtils::getText("LANG_PAGE_EVENT_DETAIL_UNFOLLOW") ?></span>
            </a>
            <div class="eventDateLocation">
                <div class="eventDate"></div>
                <h2 id="m_event_date" style="display: inline-block;padding-left: 20px;vertical-align: top;">Event Date</h2>
                <div class="eventLocation" id="m_event_location_div">
                    <div class="eventLocationIcon"></div>
                    <h2 id="m_event_location" style="display: inline-block;padding-left: 40px;vertical-align: top;">Event Location</h2>
                </div>
            </div>
            <div class="eventStat" id="m_event_stat">
                <div class="eventWeather" id="m_event_weathear_div">
                    <h2><?= LanguageUtils::getText("LANG_POPUP_WEATHER") ?></h2>
                    <div class="eventWeatherIcon">
                        <a><span id="m_event_weather">22</span><span id="m_event_weather_unit">°</span></a>
                    </div>
                </div>
                <div class="eventPrice" id="m_event_price_div">
                    <h2><?= LanguageUtils::getText("LANG_POPUP_TICKET") ?></h2>
                    <div class="eventPriceIcon"><a style="padding-left: 19px;"><span id="m_event_price">22</span><span id="m_event_price_unit">$</span></a>
                    </div>
                </div>
            </div>
            <div class="joinLikeBtn">
                <div class="editEvent" id="m_event_edit_btn">
                    <a onclick="openEditEvent(null);return false;"><?= LanguageUtils::getText("LANG_SOCIAL_EDIT") ?></a>
                </div>
                <div 
                    id="m_event_join_btn"
                    class="joinMaybeEvent"
                    eventid=""
                    btntype="join"
                    class_aktif="joinMaybeEvent_active" 
                    class_pass="joinMaybeEvent"
                    class_loader="social_button_loader"
                    pressed="false">
                    <a class="m_join"><?= LanguageUtils::getText("LANG_SOCIAL_JOIN") ?></a>
                    <a class="m_joined"><?= LanguageUtils::getText("LANG_SOCIAL_JOINED") ?></a>
                </div>
                <div
                    id="m_event_maybe_btn"
                    class="joinMaybeEvent"
                    eventid=""
                    btntype="maybe"
                    class_aktif="joinMaybeEvent_active" 
                    class_pass="joinMaybeEvent"
                    class_loader="social_button_loader"
                    pressed="false">
                    <a><?= LanguageUtils::getText("LANG_SOCIAL_MAYBE") ?></a>
                </div>
                <div class="wrapperlikeReshareEvent" style="margin-left: 4px;">
                    <div 
                        id="m_event_reshare_btn"
                        class="reshareEvent"
                        class_aktif="reshareEvent_active" 
                        class_pass="reshareEvent"
                        pressed="false"
                        data-toggle="tooltip" 
                        data-placement="bottom">
                        <a class="reshareIcon"></a>
                    </div>
                    <div 
                        id="m_event_like_btn"
                        class="likeEvent"
                        class_aktif="likeEvent_active" 
                        class_pass="likeEvent"
                        pressed="false"
                        data-toggle="tooltip" 
                        data-placement="bottom">
                        <a class="likeIcon"></a>
                    </div>
                </div>
            </div>
        </div>
        <div id="eventMap_div" class="eventMap roundedCorner" style="z-index: 10000;background: none;cursor: pointer;position: absolute;"></div>
        <div id="eventMap" class="eventMap roundedCorner"></div>
        <div id="m_event_all_attendees" class="eventAttendee roundedCorner">
            <div class="joiningAttendee" id="m_event_attendees">
                <p><?= LanguageUtils::getText("LANG_POPUP_JOINING") ?></p>
            </div>
            <div class="maybeAttendee" id="m_event_maybe_attendees" style="padding-top: 8px;">
                <p><?= LanguageUtils::getText("LANG_POPUP_MAYBE") ?></p>
            </div>
        </div>     
    </div>
</div>
