<?php

namespace Flextype;

use Flextype\Component\Registry\Registry;

class InformationManager
{
    public static function getInformationPage()
    {
        Registry::set('sidebar_menu_item', 'infomation');
        Themes::view('admin/views/templates/system/information/list')->display();
    }
}
