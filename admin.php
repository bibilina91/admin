<?php

namespace Flextype;

/**
 *
 * Flextype Admin Plugin
 *
 * @author Romanenko Sergey / Awilum <awilum@yandex.ru>
 * @link http://flextype.org
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Flextype\Component\{Arr\Arr, Http\Http, Event\Event, Filesystem\Filesystem, Session\Session, Registry\Registry};
use Symfony\Component\Yaml\Yaml;

//
// Add listner for onCurrentPageBeforeProcessed event
//
if (Http::getUriSegment(0) == 'admin') {
    Event::addListener('onShortcodesInitialized', function () {
        Admin::instance();
    });
}


class Admin {

    /**
     * An instance of the Admin class
     *
     * @var object
     * @access  protected
     */
    protected static $instance = null;

    /**
     * Is logged in
     *
     * @var bool
     * @access  protected
     */
    protected static $isLoggedIn = false;

    /**
     * Protected clone method to enforce singleton behavior.
     *
     * @access  protected
     */
    protected function __clone()
    {
        // Nothing here.
    }

    /**
     * Protected constructor since this is a static class.
     *
     * @access  protected
     */
    protected function __construct()
    {
        static::init();
    }

    protected static function init()
    {

        if (static::isLoggedIn()) {
            static::getAdminPage();
        } else {
            if (static::isUsersExists()) {
                static::getAuthPage();
            } else {
                static::getRegistrationPage();
            }
        }

        Http::requestShutdown();
    }

    protected static function getAdminPage()
    {
        switch (Http::getUriSegment(1)) {
            case 'pages':
                static::getPagesManagerPage();
            break;
            case 'settings':
                static::getSettingsPage();
            break;
        }
    }

    protected static function getPagesManagerPage()
    {
        switch (Http::getUriSegment(2)) {
            case 'delete':
                if (Http::get('page') != '') {
                    Filesystem::deleteDir(PATH['pages'] . '/' . Http::get('page'));
                    Http::redirect('admin/pages');
                }
            break;
            case 'add':


                $pages_list = Content::getPages('', false , 'slug');

                $create_page = Http::post('create_page');

                if (isset($create_page)) {
                    if (Filesystem::setFileContent(PATH['pages'] . '/' . Http::post('parent_page') . '/' . Http::post('slug') . '/page.html',
                                              '---'."\n".
                                              'title: '.Http::post('title')."\n".
                                              '---'."\n")) {

                                        Http::redirect('admin/pages/');
                    }
                }

                Themes::view('admin/views/templates/pages/add')
                    ->assign('pages_list', $pages_list)
                    ->display();
            break;
            case 'edit':

                $save_page = Http::post('save_page');

                if (isset($save_page)) {
                    Filesystem::setFileContent(PATH['pages'] . '/' . Http::post('slug') . '/page.html',
                                              '---'."\n".
                                              Http::post('frontmatter').
                                              '---'."\n".
                                              Http::post('editor'));
                }

                $page = trim(Filesystem::getFileContent(PATH['pages'] . '/' . Http::get('page') . '/page.html'));
                $page = explode('---', $page, 3);

                Themes::view('admin/views/templates/pages/editor')
                    ->assign('page_slug', Http::get('page'))
                    ->assign('page_frontmatter_data', Yaml::parse($page[1]))
                    ->assign('page_frontmatter', $page[1])
                    ->assign('page_content', $page[2])
                    ->display();
            break;
            default:
                $pages_list = Content::getPages('', false , 'title');

                Themes::view('admin/views/templates/pages/list')
                    ->assign('pages_list', $pages_list)
                    ->display();
            break;
        }
    }

    protected static function getSettingsPage()
    {
        include 'templates/settings.php';
    }

    protected static function getAuthPage()
    {

        $login = Http::post('login');

        if (isset($login)) {
            if (Filesystem::fileExists($_user_file = ACCOUNTS_PATH . '/' . Http::post('username') . '.yml')) {
                $user_file = Yaml::parseFile($_user_file);
                var_dump($user_file);
                Session::set('username', $user_file['username']);
                Session::set('role', $user_file['role']);
            }
        }

        include 'templates/auth/login.php';
    }

    protected static function getRegistrationPage()
    {

        $registration = Http::post('registration');

        if (isset($registration)) {
            if (Filesystem::fileExists($_user_file = ACCOUNTS_PATH . '/' . Http::post('username') . '.yml')) {

            } else {
                $user = ['username' => Http::post('username'),
                         'password' => Http::post('password'),
                         'role'  => 'admin',
                         'state' => 'enabled'];

                Filesystem::setFileContent(ACCOUNTS_PATH . '/' . Http::post('username') . '.yml', Yaml::dump($user));

                Http::redirect('admin');
            }
        }

        include 'templates/auth/registration.php';
    }

    public static function isUsersExists()
    {
        $users = Filesystem::getFilesList(ACCOUNTS_PATH, 'yml');
        if (count($users) > 0) {
            return true;
        } else {
            return false;
        }
    }

    public static function isLoggedIn()
    {
        return true;
        //echo Session::get('role');
        //if (Session::exists('role') && Session::get('role') == 'admin') {
        //    return true;
        //} else {
        //    return false;
        //}
    }

    /**
     * Return the Admin instance.
     * Create it if it's not already created.
     *
     * @access public
     * @return object
     */
    public static function instance()
    {
        return !isset(self::$instance) and self::$instance = new Admin();
    }
}
