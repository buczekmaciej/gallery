<?php

namespace App\Services;

class Hash
{
    public static function generator($uR)
    {
        // Generate hash and check if it's currently in use
        $hash = md5(uniqid("", TRUE));
        $taken = $uR->findBy(['resetHash' => $hash]);
        if ($taken) {
            // Find unused hash
            while ($taken) {
                $hash = md5(uniqid("", TRUE));
                $check = $uR->findBy(['resetHash' => $hash]);
                if (!$check) {
                    return $hash;
                }
            }
        }
        return $hash;
    }
}
