<?php

namespace App\Services;

class Hash {
    public function generator($uR)
    {
        // Generate hash and check if it's currently in use
        $hash = md5(uniqid("", TRUE));
        $taken = $uR->findBy(['resetHash'=>$hash]);
        if($taken)
        {
            // Find unused hash
            $taken = true;
            while($taken === true)
            {
                $hash = md5(uniqid("", TRUE));
                $check = $uR->findBy(['resetHash'=>$hash]);
                if(!$check)
                {
                    $taken = false;
                }
            }
        }
        return $hash;
    }
}