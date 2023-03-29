<?php

namespace App\Controller;

use App\Entity\User;

trait ResponseTrait
{
    public function getResponseContent(string $templatePath, User $user=null): String
    {
        $divToReplace = "<div>UserInformation</div>";

        if (!is_null($user)) {
            $divUserInformation = "<div class='userInformation'>"
                . "<div>User : " . $user->getFirstname() . " " . $user->getLastname() . "</div>"
                . "<div>Email : " . $user->getEmail() . "</div>"
                . "</div>";
        } else {
            $divUserInformation = "<div class='userInformation'><div>Not connected</div></div>";
        }

        return str_replace(
            $divToReplace,
            $divUserInformation,
            file_get_contents($templatePath)
        );
    }
}
