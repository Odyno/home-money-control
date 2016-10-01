<?php
/**
 * Copyright 2012  Alessandro Staniscia  (email : alessandro@staniscia.net)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'HMC_Statistics' ) ) {

	class HMC_Statistics {

		public function get_category_type_summary( $category_type = 0, $from = null, $to = null ){
			global $wpdb;
			$table_name_trans      = $wpdb->prefix .'HMC_TRANS';
			$table_name_cat        = $wpdb->prefix .'HMC_CATEGORY';

			//$from='YEAR(NOW()) - MONTH(NOW()) - 01';
			//$to  ='YEAR(NOW()) - MONTH(NOW()) - LAST_DAY(NOW())';

			$select = ' select
						  cat.name as name,
						  SUM(trans.value) as total
						FROM
						  '.$table_name_trans.' as trans,
						  '.$table_name_cat.' as cat
						WHERE
						  1
						  and trans.category_id = cat.id
						  and trans.user_id = '.get_current_user_id().'
						  and cat.type='.$category_type.'
						  and trans.value_date BETWEEN "'.$from.'" AND "'.$to.'"
						  GROUP BY cat.id ';

			return $wpdb->get_results( $select, ARRAY_A );
		}

		public function get_summary( $from = 0, $to = 0 ){
			global $wpdb;
			$table_name_trans      = $wpdb->prefix .'HMC_TRANS';
			$table_name_cat        = $wpdb->prefix .'HMC_CATEGORY';

			$select='
			select
			  SUM(trans.value) as total,
			  cat.type as type
			FROM
						  '.$table_name_trans.' as trans,
						  '.$table_name_cat.' as cat
			WHERE
			  1
			  and trans.category_id = cat.id
			  and trans.user_id = '.get_current_user_id().'
			  and trans.value_date BETWEEN "'.$from.'" AND "'.$to.'"
			  GROUP BY cat.type
			';


			return $wpdb->get_results( $select, ARRAY_A );
		}

		public function get_mounth_summary($type = array()){

			global $wpdb;
			$table_name_trans      = $wpdb->prefix .'HMC_TRANS';
			$table_name_cat        = $wpdb->prefix .'HMC_CATEGORY';

			$select='
				select
				  SUM(trans.value) as sum,
				  DATE_FORMAT(trans.value_date,\'%M %X\') as date
				FROM
						  '.$table_name_trans.' as trans,
						  '.$table_name_cat.' as cat
				WHERE
				  1
				  and trans.category_id = cat.id
				  and cat.type in ('.implode(",",$type).')
				  GROUP BY MONTH(trans.value_date)
			';


			return $wpdb->get_results( $select, ARRAY_A );
		}

	}

}