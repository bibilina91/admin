<?php
namespace Flextype;
use Flextype\Component\{Http\Http, Registry\Registry, I18n\I18n, Token\Token};
?>

<?php Themes::view('admin/views/partials/head')->display(); ?>

<div class="dark-panel">
    <div class="dark-panel-header">
        <h3 class="h3">
            <?php echo I18n::find('admin_pages_heading', 'admin', Registry::get('system.locale')); ?>
            <a class="float-right" href="<?php echo Http::getBaseUrl(); ?>/admin/pages/add" role="button"><?php echo I18n::find('admin_pages_create_new', 'admin', Registry::get('system.locale')); ?></a>
        </h3>
    </div>
    <div class="dark-panel-body padding-hard">
        <table class="table">
          <thead>
            <tr>
              <th scope="col"><?php echo I18n::find('admin_pages_name', 'admin', Registry::get('system.locale')); ?></th>
              <th scope="col"><?php echo I18n::find('admin_pages_url', 'admin', Registry::get('system.locale')); ?></th>
              <th scope="col"></th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($pages_list as $page) { ?>
            <tr>
              <td scope="row"><a href="<?php echo Http::getBaseUrl(); ?>/admin/pages/edit?page=<?php if ($page['slug'] != '') echo $page['slug']; else echo Registry::get('system.pages.main'); ?>"><?php echo $page['title']; ?></a></td>
              <td scope="row"><a target="_blank"  href="<?php echo Http::getBaseUrl(); ?>/<?php echo $page['slug']; ?>">/<?php echo $page['slug']; ?></a></td>
              <td scope="row" class="text-right">
                  <a href="<?php echo Http::getBaseUrl(); ?>/admin/pages/rename?page=<?php if ($page['slug'] != '') echo $page['slug']; else echo Registry::get('system.pages.main'); ?>"><?php echo I18n::find('admin_pages_rename', 'admin', Registry::get('system.locale')); ?></a>
                  &nbsp;
                  <a href="<?php echo Http::getBaseUrl(); ?>/admin/pages/delete?page=<?php echo $page['slug']; ?>&token=<?php echo Token::generate(); ?>"><?php echo I18n::find('admin_pages_delete', 'admin', Registry::get('system.locale')); ?></a>
              </td>
            </tr>
            <?php } ?>
          </tbody>
        </table>
    </div>
</div>

<?php Themes::view('admin/views/partials/footer')->display(); ?>
