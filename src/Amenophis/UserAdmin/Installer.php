<?php
namespace Amenophis\UserAdmin;

use Composer\Installer\LibraryInstaller;

class Installer extends LibraryInstaller
{
    public function install(InstalledRepositoryInterface $repo, PackageInterface $package)
    {
        parent::install($repo, $package);
        var_dump($package);
        echo "install";
    }
    
    public function update(InstalledRepositoryInterface $repo, PackageInterface $initial, PackageInterface $target)
    {
        parent::install($repo, $initial, $target);
        echo "update";
    }
    
    public function uninstall(InstalledRepositoryInterface $repo, PackageInterface $package)
    {
        parent::uninstall($repo, $package);
        echo "uninstall";
    }
}