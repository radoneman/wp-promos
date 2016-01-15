<?php
/**
 * promo_entries
 */
function uzn_promos_pages_promo_entries()
{
    global $wpdb;

	if (!isset($_REQUEST['promos_id'])) {
		exit('record not found');
	}

    $promos_id = $_REQUEST['promos_id'];

	if (is_array($promos_id) && isset($promos_id[0])) {
		$promos_id = $promos_id[0];
	} else {
		$promos_id = (int)$promos_id;
	}

	if ($promos_id == 0) {
		exit('record not found');
	}

	$promo = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}uzn_promos WHERE id = '%d'", $promos_id));
	if (empty($promo)) {
		exit('record not found');
	}

    $table = new Uzn_Promos_Promo_Entries_List_Table();
    $table->prepare_items(array('promos_id' => $promo->id));

    $message = '';
    if ('winner' === $table->current_action()) {
        $message =
        	'<div class="updated below-h2" id="message"><p>' .
        	 sprintf(__('Users set as winners: %d', 'uzn_promos'), count($_REQUEST['id'])) .
        	'</p></div>';
    }
?>
<div class="wrap">

	<div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
    <h2><?php _e('Promo Entries for: ', 'uzn_promos'); ?> <?php echo $promo->name; ?>
	    <a class="add-new-h2"
			href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=promos');?>"><?php _e('back to promos', 'uzn_promos')?></a>
    </h2>
    <?php echo $message; ?>

    <form id="persons-table" method="GET">
        <input type="hidden" name="page" value="promo_entries">
        <input type="hidden" name="promos_id" value="<?php echo $promos_id; ?>">
        <?php $table->display() ?>
    </form>

</div>
<?php
}
?>