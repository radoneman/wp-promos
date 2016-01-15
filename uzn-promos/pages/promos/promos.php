<?php
/**
 * List page
 */
function uzn_promos_pages_promos()
{
    global $wpdb;

    $table = new Uzn_Promos_Promos_List_Table();
    $table->prepare_items();

    $message = '';
	if ('activate' === $table->current_action()) {
        $message =
        	'<div class="updated below-h2" id="message"><p>' .
        	 sprintf(__('Items activated: %d', 'uzn_promos'), count($_REQUEST['id'])) .
        	'</p></div>';
    } else if ('suspend' === $table->current_action()) {
        $message =
        	'<div class="updated below-h2" id="message"><p>' .
        	 sprintf(__('Items suspended: %d', 'uzn_promos'), count($_REQUEST['id'])) .
        	'</p></div>';
    }
?>
<div class="wrap">

    <div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
    <h2><?php _e('Promos', 'uzn_promos')?> <a class="add-new-h2"
		href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=promos_form');?>"><?php _e('Add new', 'uzn_promos')?></a>
    </h2>
    <?php echo $message; ?>

    <form id="persons-table" method="GET">
        <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>
        <?php $table->display() ?>
    </form>

</div>
<?php
}
?>