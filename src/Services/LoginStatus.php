<?php

namespace App\Services;

class LoginStatus{
    public function checkLoginStatus($session)
    {
        $logged = $session->get('user');

        if($logged)
        {
            return true;
        }
        else{
            return false;
        }
    }
}