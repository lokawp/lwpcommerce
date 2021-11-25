<?php

namespace LSD\Migration;

require LWPC_PATH . 'src/includes/libraries/php/db-migrate/dbcore/bootstrap.php';

class DB_Reports extends Migration {
	protected static $table_name;

	public function __construct() {
		static::$table_name    = 'lwpcommerce_reports';
		$this->table_meta_name = static::wpdb()->prefix . 'lwpcommerce_reportmeta';
		$this->version         = '0.1';
	}

	public function create_table() {
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$sql = "CREATE TABLE " . static::get_table_name() . " (
            report_id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            user_id bigint(20) UNSIGNED NOT NULL,
            program_id bigint(20) UNSIGNED NOT NULL,
            name mediumtext NOT NULL,
            phone mediumtext NOT NULL,
            email mediumtext NOT NULL,
            total mediumtext NOT NULL,
            currency tinytext NOT NULL,
            gateway tinytext NOT NULL,
            ip tinytext NOT NULL,
            status varchar(30) NOT NULL,
            date datetime NOT NULL,
            reference mediumtext NOT NULL,
            extra_fields mediumtext NOT NULL,
            PRIMARY KEY (report_id)
        ) CHARACTER SET utf8 COLLATE utf8_general_ci;";

		dbDelta( $sql );
		update_option( static::get_table_name() . '_db_version', $this->version );
	}

	public function create_table_meta() {
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$sql = "CREATE TABLE " . $this->table_meta_name . " (
            meta_id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            report_id bigint(20) UNSIGNED NOT NULL,
            meta_key varchar(255) NULL,
            meta_value longtext NULL,
            PRIMARY KEY (meta_id)
        ) CHARACTER SET utf8 COLLATE utf8_general_ci;";

		dbDelta( $sql );
		update_option( $this->table_meta_name . '_db_version', $this->version );
	}
}
