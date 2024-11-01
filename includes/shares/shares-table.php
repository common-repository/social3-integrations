<?php

class Share_Bars_Table extends WP_List_Table {

	/** @var  S3_Connector */
	protected $connector;

	/** @var  S3_AdminNotice */
	protected $notice;

	public function __construct( $connector, $screen, $notice )
	{
		$this->connector = $connector;
		$this->notice    = $notice;

		//Set parent defaults
		parent::__construct( array(
			'singular' => 's3_share',
			'plural'   => 's3_shares',
			'screen'   => $screen,
			'ajax'     => false        //does this table support ajax?
		) );

	}

	public function column_default( $item, $column_name )
	{
		switch ( $column_name ) {
			default:
				return print_r( $item, true );
		}
	}

	public function column_id( $item )
	{
		return $item->id;
	}

	public function column_created_at( $item )
	{
		return $item->created_at;
	}

	public function column_status( $item )
	{
		return ($item->status == 1) ? 'Enable' : 'Disabled';
	}

	public function column_action( $item )
	{
		$actions = '<div class="row-actions" style="left: 0px;">';

		$actions .= sprintf( '<a href="#" class="change-status-row" data-account-id="%s" data-action="%s">%s</a>',
			$item->id,
			($item->status) ? 'disable' : 'active',
			($item->status) ? 'Disable' : 'Enable');

		$actions .= ' | '. sprintf( '<a href="?page=%s&action=%s&account_id=%s">%s</a>',
			's3_menu_share_bar',
			'edit',
			$item->id,
			'Edit');

//		$actions .= ' | '. sprintf( '<span class="trash"><a href="#" class="delete-row" data-account-id="%s">%s</a></span>',
//			$item->id,
//			'Delete');

		$actions .= '</div>';

		return $actions;
	}

	public function get_columns()
	{
		$columns = array(
			'id'            => __( 'ID', 'social3' ),
			'created_at'    => __( 'Created', 'social3' ),
			'status'        => __( 'Status', 'social3' ),
			'action'        => __( 'Actions', 'social3' ),
		);

		return $columns;
	}

	public function get_sortable_columns()
	{
		$sortable_columns = array(
			'id'            => array( 'id', true ),     //true means it's already sorted
			'created_at'    => array( 'created_at', false ),
			'status'        => array( 'status', false )
		);

		return $sortable_columns;
	}

	public function prepare_items()
	{
		$_SERVER['REQUEST_URI'] = remove_query_arg( '_wp_http_referer', $_SERVER['REQUEST_URI'] );

		$per_page = $this->get_items_per_page('s3_share_bars_per_page', 10);
		$columns  = $this->get_columns();
		$hidden   = array();
		$sortable = $this->get_sortable_columns();

		$this->_column_headers = array( $columns, $hidden, $sortable );

		$current_page = $this->get_pagenum();

		$options = array(
			'per_page'     => $per_page,
			'page'         => ($current_page) ? : 1,
			'order_by'     => ( ! empty( $_REQUEST['orderby'] ) ) ? $_REQUEST['orderby'] : 'id',
			'order_direct' => ( ! empty( $_REQUEST['order'] ) ) ? $_REQUEST['order'] : 'asc'
		);

        $brand   = $this->connector->get_brand();
        $site_id = $brand->site_id;

		if (!$site_id) {
			return;
		}

		$result = $this->connector->do_request( '/share/accounts/list/'.$site_id, $options);

		$total_items = $result->total;
		$this->items = $result->data;

		$this->set_pagination_args( array(
			'total_items' => $total_items,
			'per_page'    => $per_page,
			'total_pages' => ceil( $total_items / $per_page ),
		) );
	}
}