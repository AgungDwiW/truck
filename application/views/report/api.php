
<?php
// Debuger::show();
Debuger::dump($_GET);
Debuger::dump($_POST);
Debuger::dump($_SESSION['_csrf_tokens']);
if (!CSRF::validate("report_del", $_POST['csrf'])){
    redirect_page(route("report_index"));
}
$a = CSRF::generate("test");
Debuger::dump([$_SESSION['_csrf_tokens'], $a]);
$table = new Table("tbl_checklist");
$table->delete()->where("no='{$_POST['id']}'")->execute();
?>
