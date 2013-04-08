<?php

class SessionUtil {

    public static function checkNotLoggedinUser() {
        if (isset($_SESSION['id'])) {
            $user = new User();
            $user = UserUtils::getUserById($_SESSION['id']);
        } else {
            //check cookie
            $rmm = false;
            if (isset($_COOKIE[COOKIE_KEY_RM]))
                $rmm = $_COOKIE[COOKIE_KEY_RM];
            if ($rmm && isset($_COOKIE[COOKIE_KEY_UN]) && isset($_COOKIE[COOKIE_KEY_PSS])) {
                $uname = base64_decode($_COOKIE[COOKIE_KEY_UN]);
                $upass = base64_decode($_COOKIE[COOKIE_KEY_PSS]);
                if (!empty($uname) && !empty($upass)) {
                    $user = UserUtils::login($uname, $upass);
                    if (!empty($user))
                        $_SESSION['id'] = $user->id;
                }
            }
        }
        if (isset($_SESSION['id'])) {
            $user = UserUtils::getUserById($_SESSION['id']);
            if (!empty($user) && !empty($user->id) && !empty($user->userName)) {
                header("location: " . HOSTNAME);
            }
        }
    }

    //check user status  0 -> new user, user should see registerPI.php
    // 1-> user entered name,surname email.. user should see registerII.php
    // 2-> user entered his interests.. user should go friend requeste
    // 3-> user finished register
    public static function checkUserStatus(User $user) {
        if (!empty($user)) {
            $status = $user->status;
            if ($status == 0) {
                header("location: " . PAGE_ABOUT_YOU);
            } else if ($status == 1) {
                header("location: " . PAGE_LIKES);
            } else if ($status == 2) {
                header("location: " . PAGE_WHO_TO_FOLLOW);
            }
        }
    }

}

?>
