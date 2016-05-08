<?php


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class HMC_Transaction_Table extends WP_List_Table {

	private $from_date;
	private $to_date;
	private $allAvviableCount;
	private $selectedCount;

	public function setAllAvviableCount( $allAvviableCount ) {
		$this->allAvviableCount = $allAvviableCount;
	}

	/**
	 * @param mixed $count
	 */
	public function setCount( $count ) {
		$this->selectedCount = $count;
	}

	/**
	 * @return mixed
	 */
	public function getCount() {
		return $this->selectedCount;
	}

	/**
	 * @param mixed $from_date
	 */
	public function setFromDate( $from_date ) {
		$this->from_date = MHF_Transaction_Handler::mysql_date( $from_date );
	}

	/**
	 * @return mixed
	 */
	public function getFromDate() {
		return $this->from_date;
	}

	/**
	 * @param mixed $to_date
	 */
	public function setToDate( $to_date ) {
		$this->to_date = MHF_Transaction_Handler::mysql_date( $to_date );
	}

	/**
	 * @return mixed
	 */
	public function getToDate() {
		return $this->to_date;
	}

	function no_items() {
		_e( 'No transaction found, dude.' );
	}

	function extra_tablenav( $which ) {
		if ( $which == "top" ) {
			$date_from = $this->getFromDate();
			$date_to   = $this->getToDate();
			?>
			<div class="alignleft actions bulkactions transaction-filter">
				<form action="?" method="get">
					<select id="id_id_user" name="id_user">
						<?php
						$out = "";
						$out .= " <option value=\"\" > All </option>";
						$blogusers = get_users( array() );
						// Array of WP_User objects.
						foreach ( $blogusers as $user ) {
							$checked = "";
							if ( $this->selectedCount === $single_count[ MHF_Transaction_Handler::COL_OWNER ] ) {
								$checked = "selected";
							}
							$out .= "\n <option value=\"" . $user->ID . "\" " . $checked . " >" . $user->user_email . "</option>";
						}
						echo $out;
						?>
					</select>


					<select id="id_voice_select" name="voice_select">
						<?php
						$out = "";
						$out .= " <option value=\"\" > All </option>";
						foreach ( $this->allAvviableCount as $single_count ) {
							$checked = "";
							if ( $this->selectedCount === $single_count[ MHF_Count_Handler::COL_COUNT_ID ] ) {
								$checked = "selected";
							}
							$out .= "\n <option value=\"" . $single_count[ MHF_Count_Handler::COL_COUNT_ID ] . "\" " . $checked . " >" . $single_count[ MHF_Count_Handler::vCOL_COUNT_FULL_NAME ] . "</option>";
						}
						echo $out;
						?>
					</select>
					<input type="hidden" name="page" value="my-home-finance-id-menu-transaction-list">
					<label for="name_date_picker_from"> From </label><input type="text" id="id_date_picker_from"
					                                                        name="date_from"
					                                                        value="<?php echo MHF_Transaction_Handler::ui_date( $date_from ) ?>">
					<label for="name_date_picker_to"> to </label><input type="text" id="id_date_picker_to"
					                                                    name="date_to"
					                                                    value="<?php echo MHF_Transaction_Handler::ui_date( $date_to ) ?>">
					<input type="submit" class="button button-primary" value="Filter">
				</form>
			</div>
			<?php
		}
	}

	function column_default( $item, $column_name ) {
		//TODO: Facile da aggirare!
		if ( $item['user_id'] == get_current_user_id() || current_user_can( 'manage_transaction' ) ) {
			$actions = array(
				'edit'   => sprintf( '<a href="?page=%s&action=%s&transaction_id=%s">Edit</a>', 'my-home-finance-id-menu-transaction', 'edit', $item['transaction_id'] ),
				'delete' => sprintf( '<a href="?page=%s&action=%s&transaction_id=%s">Delete</a>', 'my-home-finance-id-menu-transaction', 'delete', $item['transaction_id'] ),
			);
		} else {
			$actions = array();
		}

		switch ( $column_name ) {
			case 'voice_ammount':
			case 'voice_pertinence':
			case 'count_name':
			case 'voice_description':
				return sprintf( '%1$s %2$s', $item[ $column_name ], $this->row_actions( $actions ) );
			case 'posting_date':
			case 'value_date':
				return sprintf( '%1$s %2$s', MHF_Transaction_Handler::ui_date( $item[ $column_name ] ), $this->row_actions( $actions ) );
			default:
				return print_r( $item, true ); //Show the whole array for troubleshooting purposes
		}
	}

	function column_voice_description( $item ) {

		$parts       = preg_split( '/([\s\n\r]+)/', $item['voice_description'], null, PREG_SPLIT_DELIM_CAPTURE );
		$parts_count = count( $parts );

		if ( $parts_count > 249 ) {
			return sprintf( '%1$s ...', $this->tokenTruncate( $item['voice_description'], 250 ) );
		} else {
			return sprintf( '%1$s', $item['voice_description'] );

		}
	}

	function column_voice_pertinence( $item ) {
		$value     = $item['voice_pertinence'];
		$gaugeName = "pertinence-gauge-" . $item['transaction_id'];
//        return sprintf(
//                '<div id="%s" style="width:100px; height:80px"></div>
//               <script>
//                    new JustGage({
//                       id: \'%s\',
//                       value: %s,
//                       min: 0,
//                       max: 100,
//                       title: " ",
//                       label: "%%",
//                       levelColors: ["#aeaeae"]
//                    });
//                </script>
//             ', $gaugeName, $gaugeName, $value);
		return sprintf(
			'<div id="%s">%s</div>', $gaugeName, $value );
	}

	function column_voice_ammount( $item ) {

		if ( $item['voice_nature'] == 'OUT' ) {
			$item_nature      = "Expencive, this is the amount that has to be paid or given up in order to get something.";
			$item_nature_sign = " - ";
		} else {
			$item_nature      = "Revenue, this is a income generated from sale of goods or services, or any other use of capital or assets, associated with the main operations of an organization";
			$item_nature_sign = " + ";
		}

		return sprintf( '<span class="%2$s-nature" title="%3$s" >%4$s %1$s</span>', $item['voice_ammount'], strtolower( $item['voice_nature'] ), $item_nature, $item_nature_sign );
	}

	function get_sortable_columns() {
		$sortable_columns = array(
			'posting_date'      => array( 'posting_date', false ),
			'value_date'        => array( 'value_date', false ),
			'voice_ammount'     => array( 'voice_ammount', false ),
			'count_name'        => __( 'count_name', 'false' ),
			'voice_pertinence'  => array( 'voice_pertinence', false ),
			'voice_description' => array( 'voice_description', false )
		);

		return $sortable_columns;
	}

	function get_columns() {
		$columns = array(
			'cb' => '<input type="checkbox" />',

			'posting_date' => __( 'Posting date', 'mylisttable' ),
			'value_date'   => __( 'Value date', 'mylisttable' ),

			'voice_ammount' => __( 'Ammount', 'mylisttable' ),

			'count_name'        => __( 'Voice', 'mylisttable' ),
			'user_id'           => __( 'User', 'mylisttable' ),
			'voice_description' => __( 'Description', 'mylisttable' ),

		);

		return $columns;
	}

	function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="transaction_id[]" value="%s" />', $item['transaction_id']
		);
	}

	function column_user_id( $item ) {
		$userid    = $item['user_id'];
		$img       = get_avatar( $userid, 32 );
		$user_info = get_userdata( $userid );
		$user_link = get_edit_user_link( $userid );

		return sprintf(
			'<a href="%s">%s %s</a>
               ', $user_link, $img, $user_info->display_name
		);
	}

	function get_bulk_actions() {
		$actions = array(
			'delete' => 'Delete'
		);

		return $actions;
	}

	function prepare_items() {
		$orderby = ( ! empty( $_GET['orderby'] ) ) ? $_GET['orderby'] : MHF_Transaction_Handler::COL_POSTING_DATE;
		$order   = ( ! empty( $_GET['order'] ) ) ? $_GET['order'] : 'asc';

		$where = array(
			MHF_Transaction_Handler::COL_POSTING_DATE . " >= \"" . $this->from_date . "\"",
			MHF_Transaction_Handler::COL_POSTING_DATE . " <= \"" . $this->to_date . "\""
		);

		if ( $this->selectedCount != null && ! ( empty( $this->selectedCount ) ) ) {
			array_push( $where, MHF_Transaction_Handler::COL_VOICE_ID . " = \"" . $this->selectedCount . "\"" );
		}


		$data = MHF_Transaction_Handler::get( $where, $orderby, $order );


		$per_page     = 45;
		$current_page = $this->get_pagenum();
		$total_items  = count( $data );

		// only ncessary because we have sample data
		$this->found_data = array_slice( $data, ( ( $current_page - 1 ) * $per_page ), $per_page );

		$this->set_pagination_args( array(
			'total_items' => $total_items, //WE have to calculate the total number of items
			'per_page'    => $per_page //WE have to determine how many items to show on a page
		) );

		$columns               = $this->get_columns();
		$hidden                = array();
		$sortable              = $this->get_sortable_columns();
		$this->_column_headers = array( $columns, $hidden, $sortable );

		$this->items = $data;
	}

	private function tokenTruncate( $string, $your_desired_width ) {
		$parts       = preg_split( '/([\s\n\r]+)/', $string, null, PREG_SPLIT_DELIM_CAPTURE );
		$parts_count = count( $parts );

		$length    = 0;
		$last_part = 0;
		for ( ; $last_part < $parts_count; ++ $last_part ) {
			$length += strlen( $parts[ $last_part ] );
			if ( $length > $your_desired_width ) {
				break;
			}
		}

		return implode( array_slice( $parts, 0, $last_part ) );
	}

}

//class