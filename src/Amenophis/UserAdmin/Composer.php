<?php

namespace Amenophis\UserAdmin;

use Composer\Script\Event;

class Composer
{
    public static function postPackageInstall(Event $event)
    {
        echo "install";
    }

    public static function postPackageUpdate(Event $event)
    {
        echo "update";
    }
    
    public static function prePackageUninstall(Event $event)
    {
        echo "uninstall";
    }
}