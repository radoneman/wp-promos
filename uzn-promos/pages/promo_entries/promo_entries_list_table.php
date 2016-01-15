<?php
if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

class Uzn_Promos_Promo_Entries_List_Table extends WP_List_Table
{
	private $table_name;

    function __construct()
    {
        global $status, $page, $wpdb;

        parent::__construct(array(
            'singular' => 'promo',
            'plural' => 'promos',
        ));

        $this->table_name = $wpdb->prefix . 'uzn_promos_entries';
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

    function column_status($item)
    {
    	$status_array = array(
    		0 => '',
			1 => 'Winner'
    	);
    	if (isset($status_array[$item['status']])) {
    		$status = $status_array[$item['status']];
    	} else {
    		$status = 'Unknown';
    	}
        return $status;
    }

    function column_user_id($item)
    {
    	$string = sprintf('<a href="%s">%d %s</a>',
    		get_admin_url(get_current_blog_id(), 'user-edit.php?user_id=' . $item['user_id']),
    		$item['user_id'],
    		__(' | view profile', 'uzn_promos')
    	);

        return $string;
    }

    function get_columns()
    {
        $columns = array(
            'cb' => '<input type="checkbox" />',
            'id' => __('Entry Id', 'uzn_promos'),
            'first_name' => __('First Name', 'uzn_promos'),
        	'last_name' => __('Last Name', 'uzn_promos'),
            'email' => __('Email', 'uzn_promos'),
			'user_id' => __('User Id', 'uzn_promos'),
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
            'date_added' => array('date_added', false),
        );
        return $sortable_columns;
    }

    function get_bulk_actions()
    {
        $actions = array(
            'winner' => 'Set Winner',
        	'unset' => 'Un-Set Winner',
        );
        return $actions;
    }

    function process_bulk_action()
    {
        global $wpdb;

        if ('winner' === $this->current_action()) {
            $ids = isset($_REQUEST['id']) ? $_REQUEST['id'] : array();
            if (is_array($ids)) $ids = implode(',', $ids);

            if (!empty($ids)) {
                $wpdb->query("UPDATE $this->table_name SET status = 1 WHERE id IN($ids)");
            }

        } else if ('unset' === $this->current_action()) {
            $ids = isset($_REQUEST['id']) ? $_REQUEST['id'] : array();
            if (is_array($ids)) $ids = implode(',', $ids);

            if (!empty($ids)) {
                $wpdb->query("UPDATE $this->table_name SET status = 0 WHERE id IN($ids)");
            }
        }
    }

    function prepare_items($params = array())
    {
        global $wpdb;

        $per_page = 20; // records per page

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array($columns, $hidden, $sortable);

        $this->process_bulk_action();

		$total_items = $wpdb->get_var($wpdb->prepare(
        	"SELECT COUNT(id) FROM $this->table_name WHERE promos_id = '%d'", $params['promos_id']
        ));

     	if (isset($_REQUEST['paged'])) {
        	$paged = max(0, intval($_REQUEST['paged']) - 1);
        } else {
        	$pages = 0;
        }

        if ((isset($_REQUEST['orderby']) && in_array($_REQUEST['orderby'], array_keys($this->get_sortable_columns())))) {
        	$orderby = $_REQUEST['orderby'];
        } else {
        	$orderby = 'status desc, id desc';
        }

        if ((isset($_REQUEST['order']) && in_array($_REQUEST['order'], array('asc', 'desc')))) {
        	$order = $_REQUEST['order'];
        } else {
        	$order = '';
        }

        $this->items = $wpdb->get_results($wpdb->prepare(
        	"SELECT * FROM $this->table_name WHERE promos_id = '%d' ORDER BY $orderby $order LIMIT %d OFFSET %d",
        	$params['promos_id'], $per_page, $paged), ARRAY_A
        );

        $this->set_pagination_args(array(
            'total_items' => $total_items, // total items defined above
            'per_page' => $per_page, // per page constant defined at top of method
            'total_pages' => ceil($total_items / $per_page) // calculate pages count
        ));
    }
}
