<?php
if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

class Uzn_Promos_Promos_List_Table extends WP_List_Table
{
    function __construct()
    {
        global $status, $page;

        parent::__construct(array(
            'singular' => 'promo',
            'plural' => 'promos',
        ));
    }

    function column_default($item, $column_name)
    {
        return $item[$column_name];
    }

    function column_cb($item)
    {
        return sprintf(
            '<input type="checkbox" name="id[]" value="%s" />',
            $item['id']
        );
    }

    function column_name($item)
    {
        $actions = array(
            'edit' => sprintf('<a href="?page=promos_form&id=%s">%s</a>', $item['id'], __('Edit', 'uzn_promos')),
            'delete' => sprintf('<a href="?page=%s&action=delete&id=%s">%s</a>', $_REQUEST['page'], $item['id'], __('Suspend', 'uzn_promos')),
        );

        return sprintf('%s %s',
            $item['name'],
            $this->row_actions($actions)
        );
    }

    function column_permalink($item)
    {
		$string = sprintf('%s | <a href="%s" target="_blank">%s</a>',
			$item['permalink'],
			get_home_url(get_current_blog_id(), '?promo=' . $item['permalink']),
    		__('preview', 'uzn_promos')
    	);

        return $string;
    }

    function column_status($item)
    {
    	$status_array = array(
    		1 => 'Active',
    		2 => 'Suspended'
    	);
    	if (isset($status_array[$item['status']])) {
    		$status = $status_array[$item['status']];
    	} else {
    		$status = 'Unknown';
    	}
        return $status;
    }

    function column_count_entries($item)
    {
    	$string = sprintf('<a href="%s&promos_id=%s">%s %s</a>',
    		get_admin_url(get_current_blog_id(), 'admin.php?page=promo_entries'),
    		$item['id'],
    		$item['count_entries'],
    		__('entries', 'uzn_promos')
    	);

        return $string;
    }

    function get_columns()
    {
        $columns = array(
            'cb' => '<input type="checkbox" />',
            'name' => __('Name', 'uzn_promos'),
            'permalink' => __('Permalink', 'uzn_promos'),
        	'count_entries' => __('Entries', 'uzn_promos'),
            'status' => __('Status', 'uzn_promos'),
        	'date_added' => __('Date Added', 'uzn_promos'),
        	'date_updated' => __('Date Updated', 'uzn_promos'),
        );
        return $columns;
    }

    function get_sortable_columns()
    {
        $sortable_columns = array(
            'name' => array('name', true),
            'count_entries' => array('count_entries', false),
        	'status' => array('status', false),
            'date_added' => array('date_added', false),
        );
        return $sortable_columns;
    }

    function get_bulk_actions()
    {
        $actions = array(
            'activate' => 'Activate',
        	'suspend' => 'Suspend'
        );
        return $actions;
    }

    function process_bulk_action()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'uzn_promos';

        if ('activate' === $this->current_action()) {
            $ids = isset($_REQUEST['id']) ? $_REQUEST['id'] : array();
            if (is_array($ids)) $ids = implode(',', $ids);

            if (!empty($ids)) {
                $wpdb->query("UPDATE $table_name SET status = 1 WHERE id IN($ids)");
            }

        } else if ('suspend' === $this->current_action()) {
            $ids = isset($_REQUEST['id']) ? $_REQUEST['id'] : array();
            if (is_array($ids)) $ids = implode(',', $ids);

            if (!empty($ids)) {
                $wpdb->query("UPDATE $table_name SET status = 2 WHERE id IN($ids)");
            }
        }
    }

    function prepare_items()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'uzn_promos';

        $per_page = 20;

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array($columns, $hidden, $sortable);

        $this->process_bulk_action();

        $total_items = $wpdb->get_var("SELECT COUNT(id) FROM $table_name");

        if (isset($_REQUEST['paged'])) {
        	$paged = max(0, intval($_REQUEST['paged']) - 1);
        } else {
        	$pages = 0;
        }

        if ((isset($_REQUEST['orderby']) && in_array($_REQUEST['orderby'], array_keys($this->get_sortable_columns())))) {
        	$orderby = $_REQUEST['orderby'];
        } else {
        	$orderby = 'status asc, date_added desc';
        }

        if ((isset($_REQUEST['order']) && in_array($_REQUEST['order'], array('asc', 'desc')))) {
        	$order = $_REQUEST['order'];
        } else {
        	$order = '';
        }

        $this->items = $wpdb->get_results($wpdb->prepare(
        	"SELECT * FROM $table_name ORDER BY $orderby $order LIMIT %d OFFSET %d", $per_page, $paged), ARRAY_A
        );

        $this->set_pagination_args(array(
            'total_items' => $total_items,
            'per_page' => $per_page,
            'total_pages' => ceil($total_items / $per_page)
        ));
    }
}
