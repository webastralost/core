<?php
/**
 * Installer
 *
 * @package Installer
 * @copyright see the html/credits.html file in this release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Installer
 * @link http://xaraya.com/index.php/release/200.html
 */

/* Do not allow this script to run if the install script has been removed.
 * This assumes the install.php and index.php are in the same directory.
 * @author Paul Rosania
 * @author Marcel van der Boom <marcel@hsdev.com>
 */

/**
 * Bootstrap Xaraya
 *
 * @access private
 */
function installer_admin_bootstrap()
{
    if (!file_exists('install.php')) { throw new Exception('Already installed');}
    xarVarFetch('install_language','str::',$install_language, 'en_US.utf-8', XARVAR_NOT_REQUIRED);
    xarVarSetCached('installer','installing', true);

    // load modules into *_modules table
//    if (!xarMod::apiFunc('modules', 'admin', 'regenerate'))
//        throw new Exception("regenerating module list failed");

# --------------------------------------------------------
# Create DD configuration and sample objects
#
    $objects = array(
                   'configurations',
                   'sample',
                   'dynamicdata_tablefields',
                   'module_settings',
                     );

    if(!xarMod::apiFunc('modules','admin','standardinstall',array('module' => 'dynamicdata', 'objects' => $objects))) return;
# --------------------------------------------------------
# Create wrapper DD overlay objects for the modules and roles modules
#
    $objects = array(
                   'modules',
//                   'modules_hooks',
//                   'modules_modvars',
                     );
    if(!xarMod::apiFunc('modules','admin','standardinstall',array('module' => 'modules', 'objects' => $objects))) return;

    $objects = array(
//                   'roles_roles',
                   'roles_users',
                   'roles_groups',
                   'roles_user_settings',
                     );

    if(!xarMod::apiFunc('modules','admin','standardinstall',array('module' => 'roles', 'objects' => $objects))) return;

    $objects = array('themes_user_settings');

    if(!xarMod::apiFunc('modules','admin','standardinstall',array('module' => 'themes', 'objects' => $objects))) return;

# --------------------------------------------------------
# Set up the standard module variables for the core modules
# Never use createItem with modvar storage. Instead, you update itemid == 0
#
    $modules = array(
                        'authsystem',
                        'blocks',
                        'base',
                        'dynamicdata',
                        'mail',
                        'modules',
                        'privileges',
                        'roles',
                        'themes',
                    );

    foreach ($modules as $module) {
        $data['module_settings'] = xarMod::apiFunc('base','admin','getmodulesettings',array('module' => $module));
        $data['module_settings']->initialize();
    }

   $modlist = array('roles');
    foreach ($modlist as $mod) {
        $regid=xarMod::getRegID($mod);
        if (!xarMod::apiFunc('modules','admin','activate',
                           array('regid'=> $regid)))
            throw new Exception("activation of $regid failed");//return;
    }

    // load modules into *_modules table
    if (!xarMod::apiFunc('modules', 'admin', 'regenerate')) return;

    // load themes into *_themes table
    if (!xarMod::apiFunc('themes', 'admin', 'regenerate')) {
        throw new Exception("themes regeneration failed");
    }

    // Set the state and activate the following themes
    $themelist = array('print','rss','default');
    foreach ($themelist as $theme) {
        // Set state to inactive
        $regid = xarThemeGetIDFromName($theme);
        if (isset($regid)) {
            if (!xarMod::apiFunc('themes','admin','setstate', array('regid'=> $regid,'state'=> XARTHEME_STATE_INACTIVE))){
                throw new Exception("Setting state of theme with regid: $regid failed");
            }
            // Activate the theme
            if (!xarMod::apiFunc('themes','admin','activate', array('regid'=> $regid)))
            {
                throw new Exception("Activation of theme with regid: $regid failed");
            }
        }
    }

    xarResponse::redirect(xarModURL('installer', 'admin', 'create_administrator',array('install_language' => $install_language)));
}

?>