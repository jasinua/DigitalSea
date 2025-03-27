<?php 

    function isLoggedIn($check) {
        if($check) {
            return true;
        } else {
            return false;
        }
    }


    function isAdmin($user) {
        if($user == 0 || $user == "0") {
            return false;
        } else {
            return true;
        }
    }

