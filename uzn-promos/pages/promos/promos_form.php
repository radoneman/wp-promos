<?php
/**
 * promos_form
 */
function uzn_promos_pages_promos_form()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'uzn_promos';

    $message = '';
    $notice = '';

    $default = array(
        'id' => 0,
        'name' => '',
        'permalink' => '',
    	'description' => null,
    	'text_success' => null,
    	'image' => null,
        'status' => 0
    );

    if (wp_verify_nonce($_REQUEST['nonce'], basename(__FILE__))) {

        $item = shortcode_atts($default, $_REQUEST);

        // validate
        $item_valid = uzn_promos_promos_form_validate($item);
        if ($item_valid === true) {

        	$item['name'] = stripslashes($item['name']);
        	$item['description'] = stripslashes($item['description']);
        	$item['text_success'] = stripslashes($item['text_success']);

        	// insert
            if ($item['id'] == 0) {
            	$item['date_added'] = date('Y-m-d H:i:s');
                $result = $wpdb->insert($table_name, $item);
                $item['id'] = $wpdb->insert_id;

                if ($result) {
                    $message = __('Item was successfully saved', 'uzn_promos');
                } else {
                    $notice = __('There was an error while saving item', 'uzn_promos');
                }

            // update
            } else {
            	$item['date_updated'] = date('Y-m-d H:i:s');
                $result = $wpdb->update($table_name, $item, array('id' => $item['id']));

                if ($result) {
                    $message = __('Item was successfully updated', 'uzn_promos');
                } else {
                    $notice = __('There was an error while updating item', 'uzn_promos');
                }
            }

            // upload image
            if (isset($_FILES['imagefile']['name']) && is_uploaded_file($_FILES['imagefile']['tmp_name'])) {
				if ( ! function_exists( 'wp_handle_upload' ) ) require_once( ABSPATH . 'wp-admin/includes/file.php' );
				$imagefile = $_FILES['imagefile'];
				$upload_overrides = array( 'test_form' => false );
				$movefile = wp_handle_upload( $imagefile, $upload_overrides );
				if ( $movefile ) {
					$update = array('image' => $movefile['url']);
					$item['image'] = $movefile['url'];
                	$wpdb->update($table_name, $update, array('id' => $item['id']));
				} else {
					$notice = __('There was an error uploading image', 'uzn_promos');
				}
            }

            if (isset($_REQUEST['imagefile-delete']) && (int)$_REQUEST['imagefile-delete'] == 1) {
				$update = array('image' => '');
               	$wpdb->update($table_name, $update, array('id' => $item['id']));
               	$item['image'] = '';
            }

        } else {
            $notice = $item_valid;
        }

    } else {
          $item = $default;
        if (isset($_REQUEST['id'])) {
            $item = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $_REQUEST['id']), ARRAY_A);
            if (!$item) {
                $item = $default;
                $notice = __('Item not found', 'uzn_promos');
            }
        }
    }

    add_meta_box('promos_form_meta_box', 'Promo data', 'uzn_promos_promos_form_meta_box', 'promo', 'normal', 'default');
?>
<div class="wrap">
    <div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
    <h2><?php _e('Promos', 'uzn_promos')?> <a class="add-new-h2"
		href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=promos');?>"><?php _e('back to list', 'uzn_promos')?></a>
    </h2>

    <?php if (!empty($notice)): ?>
    <div id="notice" class="error"><p><?php echo $notice ?></p></div>
    <?php endif;?>
    <?php if (!empty($message)): ?>
    <div id="message" class="updated"><p><?php echo $message ?></p></div>
    <?php endif;?>

    <form id="form" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="nonce" value="<?php echo wp_create_nonce(basename(__FILE__)); ?>"/>
        <input type="hidden" name="id" value="<?php echo $item['id']; ?>"/>

        <div class="metabox-holder" id="poststuff">
            <div id="post-body">
                <div id="post-body-content">
                    <?php do_meta_boxes('promo', 'normal', $item); ?>
                    <input type="submit" value="<?php _e('Save', 'uzn_promos'); ?>" id="submit" class="button-primary" name="submit">
                </div>
            </div>
        </div>
    </form>
</div>
<?php
}

/**
 * This function renders our custom meta box
 * $item is row
 *
 * @param $item
 */
function uzn_promos_promos_form_meta_box($item)
{
?>

<table cellspacing="2" cellpadding="5" style="width: 100%;" class="form-table">
    <tbody>
    <tr class="form-field">
        <th valign="top" scope="row">
            <label for="name"><?php _e('Name', 'uzn_promos')?></label>
        </th>
        <td>
            <input id="name" name="name" type="text" style="width: 95%" value="<?php echo esc_attr(stripslashes($item['name'])); ?>"
                   size="50" class="code" required>
        </td>
    </tr>
    <tr class="form-field">
        <th valign="top" scope="row">
            <label for="permalink"><?php _e('Permalink', 'uzn_promos')?></label>
        </th>
        <td>
            <input id="permalink" name="permalink" type="text" style="width: 95%" value="<?php echo esc_attr($item['permalink'])?>"
                   size="50" class="code" required>
			<p class="help">Defines landing page URL. Has to be unique. Has to contain only letters, numbers and dash. Eg. facebook-promo-tickets</p>
        </td>
    </tr>
	<tr class="form-field">
        <th valign="top" scope="row">
            <label for="description"><?php _e('Description', 'uzn_promos')?></label>
        </th>
        <td>
            <textarea class="code" name="description" rows="3" style="width: 95%"><?php echo esc_textarea($item['description']); ?></textarea>
			<p class="help">Text that appears on landing page</p>
        </td>
    </tr>
    <tr class="form-field">
        <th valign="top" scope="row">
            <label for="image"><?php _e('Image', 'uzn_promos')?></label>
        </th>
        <td>
        	<?php if (empty($item['image'])) { ?>
            - no image -
            <?php } else { ?>
            <div style="text-align: left;">
            	<img src="<?php echo $item['image']; ?>">
            	<input style="margin-left: 20px; width: auto;" type="checkbox" name="imagefile-delete" value="1"> delete image
            	<input type="hidden" name="image" value="<?php echo $item['image']; ?>">
            </div>
            <?php } ?>
			<input type="file" name="imagefile" id="imagefile">
        </td>
    </tr>
	<tr class="form-field">
        <th valign="top" scope="row">
            <label for="text_success"><?php _e('Success page', 'uzn_promos')?></label>
        </th>
        <td>
            <textarea class="code" name="text_success" rows="3" style="width: 95%"><?php echo esc_textarea($item['text_success']); ?></textarea>
			<p class="help">Text that appears on success page</p>
        </td>
    </tr>
    <tr class="form-field">
        <th valign="top" scope="row">
            <label for="status"><?php _e('Status', 'uzn_promos')?></label>
        </th>
        <td>
			<select name="status">
				<option value="1" <?php echo ($item['status'] == 1) ? 'selected="selected"' : ''; ?>>Active</option>
				<option value="2" <?php echo ($item['status'] == 2) ? 'selected="selected"' : ''; ?>>Suspended</option>
			</select>
        </td>
    </tr>
    </tbody>
</table>
<?php
}

/**
 * Simple function that validates data and retrieve bool on success
 * and error message(s) on error
 *
 * @param $item
 * @return bool|string
 */
function uzn_promos_promos_form_validate($item)
{
    $messages = array();

    if (empty($item['name'])) {
		$messages[] = __('Name is required', 'uzn_promos');
	} else if (strlen($item['name']) > 120) {
		$messages[] = __('Name is too long. Max 120 chars', 'uzn_promos');
	}

	if (empty($item['permalink'])) {
		$messages[] = __('Permalink is required', 'uzn_promos');
	} else if (strlen($item['permalink']) > 120) {
		$messages[] = __('Permalink is too long. Max 120 chars', 'uzn_promos');
	} else if (preg_match('/[^0-9a-z\-]+/', $item['permalink'])) {
		$messages[] = __('Permalink has to contain only letters, numbers and dash', 'uzn_promos');
	}

	if ($item['status'] != 1 && $item['status'] != 2) {
		$messages[] = __('Invalid status', 'uzn_promos');
	}

    if (empty($messages)) {
		return true;
	}

    return implode('<br>', $messages);
}