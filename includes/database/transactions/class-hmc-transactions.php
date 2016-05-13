<?php


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


if ( ! class_exists( 'HMC_Transactions' ) ) {

	/**
	 * Description of class-HMC-transactions
	 *
	 * @author astaniscia
	 */
	class HMC_Transactions {
		const TABLE_VERSION_KEY = "HMC_TRANS_DB_VERSION";
		const TABLE_VERSION = "0.0.3";
		const TABLE_NAME = "HMC_TRANS";

		const COL_ID = "id";
		const COL_POSTING_DATE = "posting_date";
		const COL_VALUE_DATE = "value_date";
		const COL_VALUE = "value";
		const COL_CATEGORY_ID = "category_id";
		const COL_DESCRIPTION = "description";
		const COL_OWNER_ID = "user_id";


		static function DROP_DB() {
			global $wpdb;
			$tableName = $wpdb->prefix . self::TABLE_NAME;
			delete_option( self::TABLE_VERSION_KEY );
			$wpdb->query( "DROP TABLE IF EXISTS $tableName" );
		}

		/**
		 *
		 *
		 */
		static function BUILD_DB() {
			$installed_ver = get_option( self::TABLE_VERSION_KEY );
			global $wpdb;
			if ( $installed_ver != self::TABLE_VERSION ) {
				$tableName = $wpdb->prefix . self::TABLE_NAME;
				$sql       = 'CREATE TABLE ' . $tableName . ' (
                    ' . self::COL_ID . ' varchar(100) NOT NULL,
                    ' . self::COL_POSTING_DATE . '	datetime NOT NULL,
                    ' . self::COL_VALUE_DATE . ' datetime NOT NULL,
                    ' . self::COL_CATEGORY_ID . ' varchar(100) NOT NULL,
                    ' . self::COL_VALUE . ' DECIMAL(20,2) NOT NULL,
                    ' . self::COL_DESCRIPTION . ' TEXT,
                    ' . self::COL_OWNER_ID . ' bigint(20) NOT NULL,
                    UNIQUE KEY ' . self::COL_ID . ' ( ' . self::COL_ID . ' )
                  );';

				require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
				dbDelta( $sql );
				update_option( self::TABLE_VERSION_KEY, self::TABLE_VERSION );
			}
		}

		/**
		 *
		 * @global \HMC\Transactions\type $wpdb
		 *
		 * @param \HMC\Transactions\HMC_Field $data
		 *
		 * @return type
		 * @throws Exception
		 */
		public function add( HMC_Field $data ) {
			if ( $data == null ) {
				throw new Exception( 'Filed is Mandatory' );
			}
			global $wpdb;
			$table_name = $wpdb->prefix . self::TABLE_NAME;

			$data_to_insert = array(
				self::COL_ID           => $data->getId() != null ? $data->getId() : new Exception( self::COL_ID . ' is mandatory' ),
				self::COL_POSTING_DATE => $data->getPosting_date() != null ? $data->getPosting_date()->asMySqlString() : new Exception( self::COL_POSTING_DATE . ' is mandatory' ),
				self::COL_VALUE_DATE   => $data->getValue_date() != null ? $data->getValue_date()->asMySqlString() : new Exception( self::COL_VALUE_DATE . ' is mandatory' ),
				self::COL_VALUE        => $data->getValue() != null ? $data->getValue() : new Exception( self::COL_VALUE . ' is mandatory' ),
				self::COL_CATEGORY_ID  => $data->getCategory() != null ? $data->getCategory()->getId() : new Exception( self::COL_CATEGORY_ID . ' is mandatory' ),
				self::COL_DESCRIPTION  => $data->getDescription(),
				self::COL_OWNER_ID     => get_current_user_id()
			);


			$format = array( '%s', '%s', '%s', '%f', '%s', '%s', '%d' );


			$out = $wpdb->replace( $table_name, $data_to_insert, $format );

			return $out;
		}

		/**
		 *
		 * @return type
		 * @throws Exception
		 */
		public function del( HMC_Field $data ) {
			if ( $data->getId() == null ) {
				throw new Exception( 'Filed ID is Mandatory' );
			}
			global $wpdb;
			$table_name = $wpdb->prefix . self::TABLE_NAME;

			return $wpdb->delete( $table_name, array( self::COL_ID => $data->getId() ) );
		}


		/**
		 *
		 * @return type
		 * @throws Exception
		 */
		public function delBy( $id ) {
			global $wpdb;
			$table_name = $wpdb->prefix . self::TABLE_NAME;

			return $wpdb->delete( $table_name, array( self::COL_ID => $id ) );
		}

		/**
		 *
		 * @global type $wpdb
		 *
		 * @param type $where
		 * @param type $order_by
		 * @param type $order
		 *
		 * @return type
		 */
		public function get( $where = array(), $order_by = self::COL_POSTING_DATE, $order = "asc" ) {

			global $wpdb;
			$table_name      = $wpdb->prefix . self::TABLE_NAME;
			$table_count     = $wpdb->prefix . HMC_Category::TABLE_NAME;
			$where_condition = " WHERE cnt.id = trans.category_id ";
			if ( count( $where ) > 0 ) {
				foreach ( $where as $condition ) {
					$where_condition .= " AND " . 'trans.' . $condition . " ";
				}
			}

			// SELECT * FROM wp_HMC_TRANSACTION as trans, wp_HMC_COUNTS as cont WHERE 1 AND trans.voice_select = cont.count_id
			$select = "
            SELECT
                cnt." . HMC_Category::ID . " as cId,
                cnt." . HMC_Category::FATHER_ID . " as cfD,
                cnt." . HMC_Category::NAME . " as cName,
                cnt." . HMC_Category::DESCRIPTION . " as cDes,
                cnt." . HMC_Category::TYPE . " as cType,
                trans.*
            FROM " . $table_name . ' as trans, ' . $table_count . ' as cnt  ' . $where_condition . ' ORDER BY trans.' . $order_by . ' ' . $order;

			$transazioni = $wpdb->get_results( $select, ARRAY_A );


			$out = array();

			foreach ( $transazioni as $field ) {
				$voice = HMC_Voice::make_it( $field['cId'], $field['cName'], $field['cDes'], $field['cType'] );
				$field = new HMC_Field(
					$field['value'],
					$voice,
					$field['description'],
					$field['user_id'],
					HMC_Time::MakeFromDataBase( $field['posting_date'] ),
					HMC_Time::MakeFromDataBase( $field['value_date'] ),
					$field['id']
				);
				array_push( $out, $field );
			}


			return $out;
		}


	}

}

