<?php
/*
Plugin Name: Wedding Party RSVP
Description: Simple and secure RSVP system. Manage guest lists and adult menu choices.
Version: 7.3.1
Author: Land Tech Web Designs, Corp
Author URI: https://landtechwebdesigns.com
Plugin URI: https://landtechwebdesigns.com/wedding-party-rsvp-wordpress-plugin/
Requires at least: 6.0
Tested up to: 6.9.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WGRSVP_Wedding_RSVP' ) ) :

	class WGRSVP_Wedding_RSVP {

		// --- CONFIGURATION ---
		private $table_name;
		private $opt_menu_adult   = 'wgrsvp_menu_options';
		private $opt_settings     = 'wgrsvp_general_settings';
		private $opt_license      = 'wgrsvp_license_key';

		public function __construct() {
			global $wpdb;
			$this->table_name = $wpdb->prefix . 'wedding_rsvps';

			register_activation_hook( __FILE__, array( $this, 'activate_plugin' ) );
			
			// Init hook for Form Processing (Redirects)
			add_action( 'init', array( $this, 'process_frontend_submissions' ) );
			
			add_action( 'admin_menu', array( $this, 'create_admin_menu' ) );
			add_shortcode( 'wedding_rsvp_form', array( $this, 'render_frontend_form' ) );
			add_action( 'admin_init', array( $this, 'handle_csv_export' ) );
			
			// Load CSS
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_styles' ) );
		}

		public function activate_plugin() {
			global $wpdb;
			$charset_collate = $wpdb->get_charset_collate();

			// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery
			$sql = "CREATE TABLE $this->table_name (
				id mediumint(9) NOT NULL AUTO_INCREMENT,
				party_id varchar(50) NOT NULL,
				guest_name varchar(100) NOT NULL,
				is_child tinyint(1) DEFAULT 0,
				rsvp_status varchar(20) DEFAULT 'Pending',
				menu_choice varchar(100) DEFAULT '',
				child_menu_choice varchar(100) DEFAULT '',
				appetizer_choice varchar(100) DEFAULT '',
				hors_doeuvre_choice varchar(100) DEFAULT '',
				phone varchar(20) DEFAULT '',
				email varchar(100) DEFAULT '',
				address text DEFAULT '',
				dietary_restrictions text DEFAULT '',
				allergies text DEFAULT '',
				song_request text DEFAULT '',
				guest_message text DEFAULT '',
				admin_notes text DEFAULT '',
				table_number varchar(20) DEFAULT '',
				PRIMARY KEY  (id),
				KEY party_id (party_id)
			) $charset_collate;";

			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
			dbDelta( $sql );
		}

		// --- HELPER: Cache Clearing ---
		private function clear_stats_cache() {
			wp_cache_delete( 'wgrsvp_total_accepted', 'wedding_rsvp' );
			wp_cache_delete( 'wgrsvp_total_declined', 'wedding_rsvp' );
			wp_cache_delete( 'wgrsvp_total_pending', 'wedding_rsvp' );
			wp_cache_delete( 'wgrsvp_total_guests', 'wedding_rsvp' );
			wp_cache_delete( 'wgrsvp_stats_menu_adult', 'wedding_rsvp' );
		}

		public function create_admin_menu() {
			add_menu_page( 'Wedding RSVP', 'Wedding RSVP', 'manage_options', 'wedding-rsvp-main', array( $this, 'admin_page_guests' ), 'dashicons-groups', 6 );
			add_submenu_page( 'wedding-rsvp-main', 'Menu Options', 'Menu Options', 'manage_options', 'wedding-rsvp-menu', array( $this, 'admin_page_menu' ) );
			add_submenu_page( 'wedding-rsvp-main', 'Settings', 'Settings', 'manage_options', 'wedding-rsvp-settings', array( $this, 'admin_page_settings' ) );
			add_submenu_page( 'wedding-rsvp-main', 'Email Invites', 'Email Invites', 'manage_options', 'wedding-rsvp-email', array( $this, 'admin_page_email' ) );
			add_submenu_page( 'wedding-rsvp-main', 'SMS Invites', 'SMS Invites', 'manage_options', 'wedding-rsvp-sms', array( $this, 'admin_page_sms' ) );
		}

		private function get_sort_link( $col, $current_by, $current_order ) {
			$new_order = ( $current_by === $col && $current_order === 'ASC' ) ? 'DESC' : 'ASC';
			return add_query_arg( array( 'orderby' => $col, 'order' => $new_order ) );
		}

		// --- CSS HANDLERS ---
		public function enqueue_admin_styles() {
			wp_register_style( 'wgrsvp-admin-style', false, array(), '7.2' );
			wp_enqueue_style( 'wgrsvp-admin-style' );
			$css = $this->get_custom_css();
			wp_add_inline_style( 'wgrsvp-admin-style', wp_strip_all_tags( $css ) );
		}

		public function enqueue_frontend_styles() {
			wp_register_style( 'wgrsvp-front-style', false, array(), '7.2' );
			wp_enqueue_style( 'wgrsvp-front-style' );
			$css = $this->get_custom_css();
			wp_add_inline_style( 'wgrsvp-front-style', wp_strip_all_tags( $css ) );
		}

		private function get_custom_css() {
			$s     = get_option( $this->opt_settings, array() );
			$color = isset( $s['primary_color'] ) && ! empty( $s['primary_color'] ) ? $s['primary_color'] : '#333';
			$font  = isset( $s['font_size'] ) && ! empty( $s['font_size'] ) ? $s['font_size'] : '16';
			
			return '
				/* FRONTEND STYLES */
				.wpr-wrapper { max-width: 600px; margin: 0 auto; font-size: ' . esc_attr( $font ) . 'px; }
				.wpr-guest-card { border: 1px solid #ddd; padding: 15px; margin-bottom: 15px; background: #f9f9f9; border-radius:5px; }
				.wpr-field { margin-bottom: 12px; }
				.wpr-field label { display: block; font-weight: bold; margin-bottom: 5px; }
				.wpr-field input[type=text], .wpr-field input[type=email], .wpr-field select, .wpr-field textarea { width: 100%; padding: 8px; border:1px solid #ccc; border-radius:3px; }
				.wpr-button { background: ' . esc_attr( $color ) . '; color: #fff; padding: 12px 25px; border: none; cursor: pointer; font-size:1.1em; border-radius:3px; }
				.wpr-button:hover { opacity: 0.9; }
				.wpr-checkbox-group label { display:inline-block; margin-right:10px; font-weight:normal; }
				.wpr-honey { display:none !important; visibility:hidden; }
				
				/* --- PRO PLACEHOLDERS --- */
				.wpr-pro-placeholder {
					background: #f0f0f1;
					color: #8c8f94;
					font-size: 10px;
					text-align: center;
					padding: 5px;
					border: 1px dashed #c3c4c7;
					border-radius: 4px;
					width: 100%;
					box-sizing: border-box;
					display: block;
					margin-top: 2px;
				}
				.wpr-pro-placeholder a { text-decoration:none; color:inherit; }
				.wpr-pro-link { font-size: 11px; margin-left: 5px; color: #2271b1; text-decoration: none; }
				
				/* --- ADMIN DASHBOARD GRID --- */
				.wpr-dashboard-grid {
					display: grid !important;
					grid-template-columns: 1fr 1fr 1fr !important;
					gap: 20px !important;
					width: 100% !important;
					box-sizing: border-box !important;
					margin-bottom: 30px !important;
				}
				
				.wpr-stat-box { 
					display: flex !important;
					flex-direction: column !important;
					align-items: center !important;
					justify-content: center !important;
					padding: 40px 20px !important;
					border-radius: 6px !important; 
					text-align: center !important; 
					text-decoration: none !important;
					box-shadow: 0 4px 6px rgba(0,0,0,0.1) !important;
					transition: transform 0.2s ease !important;
					box-sizing: border-box !important;
					min-height: 160px !important;
				}
				
				.wpr-stat-box:hover { transform: translateY(-3px) !important; opacity: 0.95 !important; }

				.wpr-stat-box h2 { 
					display: block !important;
					width: 100% !important;
					margin: 0 0 5px 0 !important; 
					padding: 0 !important;
					font-size: 56px !important; 
					line-height: 1 !important;
					font-weight: 800 !important;
					color: inherit !important;
				}
				
				.wpr-stat-box small { 
					display: block !important;
					font-size: 16px !important; 
					font-weight: 600 !important; 
					text-transform: uppercase !important; 
					letter-spacing: 1px !important;
					color: inherit !important;
					opacity: 0.9 !important;
				}
				
				.wpr-meal-tag { display:inline-block; margin:2px; padding:6px 10px; background:#f0f0f1; border:1px solid #ccc; border-radius:12px; font-size:12px; text-decoration:none; color:#333; }
				.wpr-meal-tag:hover { background:#fff; border-color:#0073aa; color:#0073aa; }
				.wpr-meal-tag.active { background:#0073aa; color:#fff; border-color:#0073aa; }

				/* Flex Helpers */
				.wpr-flex-row { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; }
				.wpr-justify-between { justify-content: space-between; }
				
				/* Mobile */
				@media (max-width: 782px) {
					.wpr-dashboard-grid { grid-template-columns: 1fr !important; gap: 10px !important; }
					.wpr-flex-row { flex-direction: column !important; align-items: stretch !important; }
					.wpr-flex-row input[type=text], .wpr-flex-row input[type=search], .wpr-flex-row select { width: 100% !important; height: 40px; margin-bottom: 5px; }
					.wpr-flex-row .button { width: 100% !important; padding: 10px !important; text-align: center; margin-bottom: 5px; }
					
					.wp-list-table.widefat { border: 0 !important; box-shadow: none !important; background: transparent !important; }
					.wp-list-table thead { display: none; }
					.wp-list-table tbody tr { display: block; background: #fff; border: 1px solid #ccc; margin-bottom: 15px; padding: 15px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
					.wp-list-table tbody td { display: block; text-align: left; padding: 5px 0 !important; border-bottom: 1px solid #eee !important; }
					.wp-list-table tbody td:last-child { border-bottom: none !important; display: flex; gap: 10px; margin-top: 10px; padding-top: 15px !important; justify-content: space-between; }
					.wp-list-table input, .wp-list-table select, .wp-list-table textarea { width: 100% !important; height: 40px; font-size: 16px !important; margin-bottom: 5px; }
					.wp-list-table td:last-child button { flex: 1; height: 40px; }
				}';
		}

		// --- PAGE: Guest List & Dashboard ---
		public function admin_page_guests() {
			// Security: Check user capabilities
			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'wedding-party-rsvp' ) );
			}

			global $wpdb;
			add_thickbox();

			// Actions
			$this->handle_admin_actions();

			// Handle CSV Import
			if ( isset( $_POST['wgrsvp_import_nonce'] ) ) {
				if ( wp_verify_nonce( sanitize_key( $_POST['wgrsvp_import_nonce'] ), 'wgrsvp_import_nonce' ) ) {
					if ( isset( $_POST['wgrsvp_import_csv'] ) ) {
						// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
						$csv_file = isset( $_FILES['csv_file']['tmp_name'] ) ? sanitize_text_field( $_FILES['csv_file']['tmp_name'] ) : '';
						if ( ! empty( $csv_file ) ) {
							$this->handle_csv_import( $csv_file );
						}
					}
				}
			}

			// Stats (Cached)
			$total_accepted = wp_cache_get( 'wgrsvp_total_accepted', 'wedding_rsvp' );
			if ( false === $total_accepted ) {
				// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
				$total_accepted = $wpdb->get_var( "SELECT COUNT(*) FROM {$this->table_name} WHERE rsvp_status = 'Accepted'" );
				wp_cache_set( 'wgrsvp_total_accepted', $total_accepted, 'wedding_rsvp', 3600 );
			}
			$total_declined = wp_cache_get( 'wgrsvp_total_declined', 'wedding_rsvp' );
			if ( false === $total_declined ) {
				// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
				$total_declined = $wpdb->get_var( "SELECT COUNT(*) FROM {$this->table_name} WHERE rsvp_status = 'Declined'" );
				wp_cache_set( 'wgrsvp_total_declined', $total_declined, 'wedding_rsvp', 3600 );
			}
			$total_pending = wp_cache_get( 'wgrsvp_total_pending', 'wedding_rsvp' );
			if ( false === $total_pending ) {
				// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
				$total_pending = $wpdb->get_var( "SELECT COUNT(*) FROM {$this->table_name} WHERE rsvp_status = 'Pending'" );
				wp_cache_set( 'wgrsvp_total_pending', $total_pending, 'wedding_rsvp', 3600 );
			}
			$total_guests = wp_cache_get( 'wgrsvp_total_guests', 'wedding_rsvp' );
			if ( false === $total_guests ) {
				// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
				$total_guests = $wpdb->get_var( "SELECT COUNT(*) FROM {$this->table_name}" );
				wp_cache_set( 'wgrsvp_total_guests', $total_guests, 'wedding_rsvp', 3600 );
			}

			// Only Adult Menu Stats relevant now for Free
			$menu_stats_adult = wp_cache_get( 'wgrsvp_stats_menu_adult', 'wedding_rsvp' );
			if ( false === $menu_stats_adult ) {
				// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
				$menu_stats_adult = $wpdb->get_results( "SELECT menu_choice, COUNT(*) as count FROM {$this->table_name} WHERE rsvp_status = 'Accepted' AND menu_choice != '' GROUP BY menu_choice" );
				wp_cache_set( 'wgrsvp_stats_menu_adult', $menu_stats_adult, 'wedding_rsvp', 3600 );
			}

			// Filters
			$search_query   = isset( $_GET['s'] ) ? sanitize_text_field( wp_unslash( $_GET['s'] ) ) : '';
			$filter_status  = isset( $_GET['filter_status'] ) ? sanitize_text_field( wp_unslash( $_GET['filter_status'] ) ) : '';
			$filter_menu    = isset( $_GET['filter_menu'] ) ? sanitize_text_field( wp_unslash( $_GET['filter_menu'] ) ) : '';
			
			$orderby        = isset( $_GET['orderby'] ) ? sanitize_text_field( wp_unslash( $_GET['orderby'] ) ) : 'party_id';
			$order          = isset( $_GET['order'] ) ? sanitize_text_field( wp_unslash( $_GET['order'] ) ) : 'ASC';
			$allowed_orders = array( 'party_id', 'guest_name', 'id', 'table_number', 'is_child', 'rsvp_status', 'menu_choice' );
			if ( ! in_array( $orderby, $allowed_orders, true ) ) {
				$orderby = 'party_id';
			}
			$order = ( 'DESC' === $order ) ? 'DESC' : 'ASC';

			$sql_args = array();
			$sql_where = array();

			if ( $search_query ) {
				$sql_where[] = '(guest_name LIKE %s OR party_id LIKE %s)';
				$sql_args[] = '%' . $search_query . '%';
				$sql_args[] = '%' . $search_query . '%';
			}
			if ( $filter_status ) {
				$sql_where[] = 'rsvp_status = %s';
				$sql_args[] = $filter_status;
			}
			if ( $filter_menu ) {
				$sql_where[] = 'menu_choice = %s'; 
				$sql_args[] = $filter_menu;
			}

			// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$query = "SELECT * FROM {$this->table_name}";
			if ( ! empty( $sql_where ) ) {
				$query .= ' WHERE ' . implode( ' AND ', $sql_where );
			}
			$query .= " ORDER BY " . esc_sql( $orderby ) . " " . esc_sql( $order );

			if ( ! empty( $sql_args ) ) {
				// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery, PluginCheck.Security.DirectDB.UnescapedDBParameter
				$guests = $wpdb->get_results( $wpdb->prepare( $query, $sql_args ) );
			} else {
				// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery, PluginCheck.Security.DirectDB.UnescapedDBParameter
				$guests = $wpdb->get_results( $query );
			}

			$menus_adult = get_option( $this->opt_menu_adult, array() );
			$settings    = get_option( $this->opt_settings, array() );

			?>
			<div class="wrap">
				<h1 style="display:flex; align-items:center; flex-wrap:wrap; gap:10px;">
					<?php esc_html_e( 'Wedding Dashboard', 'wedding-party-rsvp' ); ?>
					<span style="background:#46b450; color:#fff; font-size:12px; padding:3px 8px; border-radius:10px;">Unlimited Guests</span>
				</h1>
				
				<div class="wpr-dashboard-grid">
					<a href="?page=wedding-rsvp-main&filter_status=Accepted" class="wpr-stat-box" style="background:#46b450; color:#fff;">
						<h2><?php echo esc_html( $total_accepted ); ?></h2>
						<small><?php esc_html_e( 'Attending', 'wedding-party-rsvp' ); ?></small>
					</a>
					<a href="?page=wedding-rsvp-main&filter_status=Declined" class="wpr-stat-box" style="background:#dc3232; color:#fff;">
						<h2><?php echo esc_html( $total_declined ); ?></h2>
						<small><?php esc_html_e( 'Regrets', 'wedding-party-rsvp' ); ?></small>
					</a>
					<a href="?page=wedding-rsvp-main&filter_status=Pending" class="wpr-stat-box" style="background:#ffb900; color:#23282d;">
						<h2><?php echo esc_html( $total_pending ); ?></h2>
						<small><?php esc_html_e( 'Pending', 'wedding-party-rsvp' ); ?></small>
					</a>
				</div>

				<?php if ( ! empty( $menu_stats_adult ) ) : ?>
				<div style="background:#fff; border:1px solid #ccd0d4; padding:10px; margin-bottom:20px;">
					<strong><?php esc_html_e( 'Menu Breakdown:', 'wedding-party-rsvp' ); ?></strong><br>
					<div style="margin-top:5px;">
						<?php foreach ( $menu_stats_adult as $stat ) : 
							$active = ( $filter_menu === $stat->menu_choice ) ? 'active' : '';
						?>
							<a href="<?php echo esc_url( '?page=wedding-rsvp-main&filter_menu=' . urlencode( $stat->menu_choice ) ); ?>" class="wpr-meal-tag <?php echo esc_attr( $active ); ?>"><?php echo esc_html( $stat->menu_choice ); ?> (<?php echo intval( $stat->count ); ?>)</a>
						<?php endforeach; ?>
					</div>
				</div>
				<?php endif; ?>
				
				<div class="wpr-flex-row wpr-justify-between" style="margin-bottom:10px;">
					<div style="background:#fff; padding:10px; border:1px solid #ccd0d4; flex-grow:1;">
						<form method="post" class="wpr-flex-row">
							<?php wp_nonce_field( 'wgrsvp_add_guest', 'wgrsvp_add_guest' ); ?>
							<strong><?php esc_html_e( 'Add Guest:', 'wedding-party-rsvp' ); ?></strong>
							<input type="text" name="party_id" required placeholder="<?php esc_attr_e( 'Party ID', 'wedding-party-rsvp' ); ?>" style="width:100px;">
							<input type="text" name="guest_name" required placeholder="<?php esc_attr_e( 'Name', 'wedding-party-rsvp' ); ?>" style="width:120px;">
							<div class="wpr-pro-placeholder" style="width:60px; display:inline-block; margin:0 5px;">Kid (Pro)</div>
							<input type="submit" name="wgrsvp_add_guest_btn" class="button button-primary" value="<?php esc_attr_e( 'Add', 'wedding-party-rsvp' ); ?>">
						</form>
					</div>
					<form method="get" class="wpr-flex-row">
						<input type="hidden" name="page" value="wedding-rsvp-main">
						<?php if($filter_status): ?><input type="hidden" name="filter_status" value="<?php echo esc_attr($filter_status); ?>"><?php endif; ?>
						<input type="search" name="s" value="<?php echo esc_attr( $search_query ); ?>" placeholder="<?php esc_attr_e( 'Search...', 'wedding-party-rsvp' ); ?>">
						<input type="submit" class="button" value="<?php esc_attr_e( 'Search', 'wedding-party-rsvp' ); ?>">
						<?php if ( $search_query || $filter_status ) : ?>
							<a href="?page=wedding-rsvp-main" class="button"><?php esc_html_e( 'Reset', 'wedding-party-rsvp' ); ?></a>
						<?php endif; ?>
					</form>
				</div>

				<div class="wpr-flex-row wpr-justify-between" style="background:#fff; padding:15px; border:1px solid #ccd0d4; margin-bottom:20px;">
					<form method="post" enctype="multipart/form-data" class="wpr-flex-row">
						<?php wp_nonce_field( 'wgrsvp_import_nonce', 'wgrsvp_import_nonce' ); ?>
						<strong><?php esc_html_e( 'CSV Import:', 'wedding-party-rsvp' ); ?></strong>
						<input type="file" name="csv_file" accept=".csv" required>
						<input type="submit" name="wgrsvp_import_csv" class="button" value="<?php esc_attr_e( 'Upload', 'wedding-party-rsvp' ); ?>">
					</form>
					<form method="post">
						<?php wp_nonce_field( 'wgrsvp_export_nonce', 'wgrsvp_export_nonce' ); ?>
						<input type="submit" name="wgrsvp_export_csv" class="button button-secondary" value="<?php esc_attr_e( 'Export CSV', 'wedding-party-rsvp' ); ?>">
					</form>
				</div>

				<table class="wp-list-table widefat fixed striped">
					<thead><tr>
						<th width="8%"><a href="<?php echo esc_url( $this->get_sort_link( 'party_id', $orderby, $order ) ); ?>"><?php esc_html_e( 'Party ID', 'wedding-party-rsvp' ); ?></a></th>
						<th width="15%"><a href="<?php echo esc_url( $this->get_sort_link( 'guest_name', $orderby, $order ) ); ?>"><?php esc_html_e( 'Name', 'wedding-party-rsvp' ); ?></a></th>
						<th width="3%"><?php esc_html_e( 'Kid', 'wedding-party-rsvp' ); ?></th>
						<th width="8%"><a href="<?php echo esc_url( $this->get_sort_link( 'rsvp_status', $orderby, $order ) ); ?>"><?php esc_html_e( 'RSVP', 'wedding-party-rsvp' ); ?></a></th>
						<th width="12%"><?php esc_html_e( 'Menu', 'wedding-party-rsvp' ); ?></th>
						<th width="5%"><?php esc_html_e( 'Tbl', 'wedding-party-rsvp' ); ?></th>
						<th width="18%"><?php esc_html_e( 'Contact/Info', 'wedding-party-rsvp' ); ?></th>
						<th width="15%"><?php esc_html_e( 'Admin Notes', 'wedding-party-rsvp' ); ?></th>
						<th width="16%"><?php esc_html_e( 'Actions', 'wedding-party-rsvp' ); ?></th>
					</tr></thead>
					<tbody>
						<?php
						foreach ( $guests as $guest ) :
							?>
							<tr><form method="post">
								<input type="hidden" name="id" value="<?php echo esc_attr( $guest->id ); ?>">
								<?php wp_nonce_field( 'wgrsvp_edit_guest', 'wgrsvp_edit_guest' ); ?>
								
								<td><input type="text" name="party_id" value="<?php echo esc_attr( $guest->party_id ); ?>" style="width:100%" placeholder="Party ID"></td>
								<td><input type="text" name="guest_name" value="<?php echo esc_attr( $guest->guest_name ); ?>" style="width:100%" placeholder="Name"></td>
								
								<td style="text-align:center;">
									<div class="wpr-pro-placeholder">Pro</div>
								</td>
								
								<td><select name="rsvp_status" style="width:100%"><option value="Pending" <?php selected( $guest->rsvp_status, 'Pending' ); ?>>?</option><option value="Accepted" <?php selected( $guest->rsvp_status, 'Accepted' ); ?>><?php esc_html_e( 'Yes', 'wedding-party-rsvp' ); ?></option><option value="Declined" <?php selected( $guest->rsvp_status, 'Declined' ); ?>><?php esc_html_e( 'No', 'wedding-party-rsvp' ); ?></option></select></td>
								
								<td>
									<select name="menu_choice" style="width:100%; margin-bottom:2px; font-size:11px;">
										<option value=""><?php esc_html_e( '(Adult)', 'wedding-party-rsvp' ); ?></option>
										<?php
										foreach ( $menus_adult as $m ) {
											echo '<option value="' . esc_attr( $m ) . '" ' . selected( $guest->menu_choice, $m, false ) . '>' . esc_html( $m ) . '</option>';
										}
										?>
									</select>
									
									<div class="wpr-pro-placeholder" style="margin-bottom:2px;">Child Menu (Available in Pro)</div>
									<div style="display:flex; gap:2px;">
										<div class="wpr-pro-placeholder">Appetizer (Pro)</div>
										<div class="wpr-pro-placeholder">Hors (Pro)</div>
									</div>
								</td>

								<td><div class="wpr-pro-placeholder"># (Pro)</div></td>
								
								<td>
									<input type="text" name="email" value="<?php echo esc_attr( $guest->email ); ?>" placeholder="<?php esc_attr_e( 'Email', 'wedding-party-rsvp' ); ?>" style="width:100%; margin-bottom:2px; font-size:11px;">
									<input type="text" name="phone" value="<?php echo esc_attr( $guest->phone ); ?>" placeholder="<?php esc_attr_e( 'Phone', 'wedding-party-rsvp' ); ?>" style="width:100%; font-size:11px;">
									<div style="font-size:10px; color:#666; margin-top:3px;">
										<?php
										if ( ! empty( $guest->allergies ) ) {
											echo '! ' . esc_html( $guest->allergies ) . '<br>';
										}
										if ( ! empty( $guest->guest_message ) ) {
											echo '&#9993; "' . esc_html( substr( $guest->guest_message, 0, 20 ) ) . '..."';
										}
										?>
									</div>
								</td>

								<td>
									<div class="wpr-pro-placeholder" style="height:50px; line-height:50px;">Admin Notes (Available in Pro)</div>
								</td>

								<td style="white-space:nowrap;">
									<button type="submit" name="wgrsvp_update_guest" class="button button-primary button-small" title="Save"><span class="dashicons dashicons-saved"></span> Save</button>
									<button type="submit" name="wgrsvp_delete_guest" class="button button-small button-link-delete" title="Delete" onclick="return confirm('<?php esc_attr_e( 'Delete?', 'wedding-party-rsvp' ); ?>')"><span class="dashicons dashicons-trash"></span></button>
								</td>
							</form></tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
			<?php
		}

		// --- PAGE: General Settings ---
		public function admin_page_settings() {
			// Security: Check user capabilities
			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'wedding-party-rsvp' ) );
			}

			// SECURITY FIX: Use filter_input with sanitized flag
			$reset_nonce = filter_input( INPUT_POST, 'wgrsvp_reset_nonce', FILTER_SANITIZE_SPECIAL_CHARS );
			if ( $reset_nonce && wp_verify_nonce( $reset_nonce, 'wgrsvp_reset_nonce' ) ) {
				if ( isset( $_POST['wgrsvp_factory_reset'] ) ) {
					// DELETE ALL DATA
					global $wpdb;
					// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery
					$wpdb->query( "TRUNCATE TABLE {$this->table_name}" );

					delete_option( $this->opt_menu_adult );
					delete_option( $this->opt_settings );
					delete_option( $this->opt_license );

					$this->clear_stats_cache();

					echo '<div class="notice notice-warning is-dismissible"><p><strong>' . esc_html__( 'System Reset Complete. All data and settings have been cleared.', 'wedding-party-rsvp' ) . '</strong></p></div>';
				}
			}

			// 2. Save Settings
			$settings_nonce = filter_input( INPUT_POST, 'wgrsvp_settings_nonce', FILTER_SANITIZE_SPECIAL_CHARS );
			if ( $settings_nonce && wp_verify_nonce( $settings_nonce, 'wgrsvp_settings_nonce' ) ) {
				if ( isset( $_POST['wgrsvp_save_settings'] ) ) {
					$settings = array(
						// phpcs:ignore WordPress.Security.ValidatedSanitizedInput
						'rsvp_page_url'  => isset( $_POST['rsvp_page_url'] ) ? esc_url_raw( wp_unslash( $_POST['rsvp_page_url'] ) ) : '',
						// phpcs:ignore WordPress.Security.ValidatedSanitizedInput
						'deadline_date'  => isset( $_POST['deadline_date'] ) ? sanitize_text_field( wp_unslash( $_POST['deadline_date'] ) ) : '',
						// phpcs:ignore WordPress.Security.ValidatedSanitizedInput
						'redirect_url'   => isset( $_POST['redirect_url'] ) ? esc_url_raw( wp_unslash( $_POST['redirect_url'] ) ) : '',
						// phpcs:ignore WordPress.Security.ValidatedSanitizedInput
						'welcome_title'  => isset( $_POST['welcome_title'] ) ? sanitize_text_field( wp_unslash( $_POST['welcome_title'] ) ) : '',
						// Appearance and Toggles removed from save logic (Pro)
					);
					update_option( $this->opt_settings, $settings );

					// phpcs:ignore WordPress.Security.ValidatedSanitizedInput
					$new_key = isset( $_POST['wgrsvp_license_key'] ) ? sanitize_text_field( wp_unslash( $_POST['wgrsvp_license_key'] ) ) : '';
					update_option( $this->opt_license, $new_key );

					echo '<div class="notice notice-success"><p>' . esc_html__( 'Settings Saved.', 'wedding-party-rsvp' ) . '</p></div>';
				}
			}

			$s   = get_option( $this->opt_settings, array() );
			$lic = get_option( $this->opt_license, '' );
			?>
			<div class="wrap">
				<h1><?php esc_html_e( 'General Settings', 'wedding-party-rsvp' ); ?></h1>
				<form method="post">
					<?php wp_nonce_field( 'wgrsvp_settings_nonce', 'wgrsvp_settings_nonce' ); ?>
					
					<div style="background:#fff; padding:20px; border:1px solid #ddd; margin-bottom:20px; border-left:4px solid #666;">
						<h3><?php esc_html_e( 'License / Support', 'wedding-party-rsvp' ); ?></h3>
						<p><?php esc_html_e( 'Enter your license key below for Priority Support and to unlock Pro features.', 'wedding-party-rsvp' ); ?></p>
						
						<p style="margin-bottom:15px;">
							<a href="<?php echo esc_url( 'https://landtechwebdesigns.com/wedding-party-rsvp-wordpress-plugin/' ); ?>" target="_blank" class="button"><?php esc_html_e( 'Purchase License Key', 'wedding-party-rsvp' ); ?></a>
						</p>

						<input type="text" name="wgrsvp_license_key" value="<?php echo esc_attr( $lic ); ?>" style="width:100%; max-width:400px;" placeholder="License Key">
					</div>

					<div style="background:#fff; padding:20px; border:1px solid #ddd; margin-bottom:20px;">
						<h3><?php esc_html_e( 'Frontend Display', 'wedding-party-rsvp' ); ?></h3>
						<p><label><strong><?php esc_html_e( 'Custom Welcome Title:', 'wedding-party-rsvp' ); ?></strong></label><br>
						<input type="text" name="welcome_title" value="<?php echo esc_attr( $s['welcome_title'] ?? '' ); ?>" style="width:100%" placeholder="e.g. Welcome to Sarah & John's Wedding!"><br>
						<small><?php esc_html_e( 'Replaces the default "Party: [ID]" title.', 'wedding-party-rsvp' ); ?></small></p>
					</div>

					<div style="background:#fff; padding:20px; border:1px solid #ddd; margin-bottom:20px;">
						<h3><?php esc_html_e( 'Logistics', 'wedding-party-rsvp' ); ?></h3>
						<p><label><strong><?php esc_html_e( 'RSVP Page URL:', 'wedding-party-rsvp' ); ?></strong></label><br><input type="text" name="rsvp_page_url" value="<?php echo esc_attr( $s['rsvp_page_url'] ?? '' ); ?>" style="width:100%" placeholder="e.g. https://mysite.com/rsvp"></p>
						<p><label><strong><?php esc_html_e( 'RSVP Deadline:', 'wedding-party-rsvp' ); ?></strong></label><br><input type="date" name="deadline_date" value="<?php echo esc_attr( $s['deadline_date'] ?? '' ); ?>"></p>
						<p><label><strong><?php esc_html_e( 'Redirect Success URL:', 'wedding-party-rsvp' ); ?></strong></label><br><input type="text" name="redirect_url" value="<?php echo esc_attr( $s['redirect_url'] ?? '' ); ?>" style="width:100%"></p>
					</div>

					<div style="background:#fff; padding:20px; border:1px solid #ddd; margin-bottom:20px;">
						<h3><?php esc_html_e( 'Appearance Settings', 'wedding-party-rsvp' ); ?></h3>
						<div class="wpr-pro-placeholder" style="padding:20px;">
							<p><?php esc_html_e( 'Button Colors and Font Sizes are available in the Pro version.', 'wedding-party-rsvp' ); ?></p>
							<a href="<?php echo esc_url( 'https://landtechwebdesigns.com/wedding-party-rsvp-wordpress-plugin/' ); ?>" target="_blank" class="wpr-pro-link">Upgrade Now</a>
						</div>
					</div>

					<div style="background:#fff; padding:20px; border:1px solid #ddd; margin-bottom:20px;">
						<h3><?php esc_html_e( 'Visibility Toggles', 'wedding-party-rsvp' ); ?></h3>
						<div class="wpr-pro-placeholder" style="padding:20px;">
							<p><?php esc_html_e( 'Options to hide Song Requests and Meal Courses are available in the Pro version.', 'wedding-party-rsvp' ); ?></p>
							<a href="<?php echo esc_url( 'https://landtechwebdesigns.com/wedding-party-rsvp-wordpress-plugin/' ); ?>" target="_blank" class="wpr-pro-link">Upgrade Now</a>
						</div>
					</div>

					<div style="display:flex; gap:10px;">
						<input type="submit" name="wgrsvp_save_settings" class="button button-primary" value="<?php esc_attr_e( 'Save Settings', 'wedding-party-rsvp' ); ?>">
					</div>
				</form>
				
				<form method="post" style="margin-top:50px;">
					<?php wp_nonce_field( 'wgrsvp_reset_nonce', 'wgrsvp_reset_nonce' ); ?>
					<div style="background:#fff; padding:20px; border:1px solid #dc3232; border-left:4px solid #dc3232;">
						<h3 style="color:#dc3232; margin-top:0;"><?php esc_html_e( 'Danger Zone: Factory Reset', 'wedding-party-rsvp' ); ?></h3>
						<p><?php esc_html_e( 'This will DELETE ALL GUESTS, RESET ALL SETTINGS, and REMOVE THE LICENSE KEY. This action cannot be undone.', 'wedding-party-rsvp' ); ?></p>
						<input type="submit" name="wgrsvp_factory_reset" class="button button-link-delete" style="color:red; text-decoration:none; border:1px solid red; padding:5px 15px;" value="<?php esc_attr_e( 'Reset Program to Default', 'wedding-party-rsvp' ); ?>" onclick="return confirm('WARNING: Are you sure you want to delete ALL data and reset the plugin?');">
					</div>
				</form>
			</div>
			<?php
		}

		// --- PAGE: Menu Options ---
		public function admin_page_menu() {
			// Security: Check user capabilities
			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'wedding-party-rsvp' ) );
			}

			// SECURITY FIX: Use filter_input with sanitized flag & Verify Nonce
			$menu_nonce = filter_input( INPUT_POST, 'wgrsvp_menu_nonce', FILTER_SANITIZE_SPECIAL_CHARS );
			if ( $menu_nonce && wp_verify_nonce( $menu_nonce, 'wgrsvp_menu_nonce' ) ) {
				if ( isset( $_POST['wgrsvp_save_menu'] ) ) {
					// FIX: Added sanitization to $_POST inputs before logic
					$menu_options_raw = isset( $_POST['menu_options'] ) ? sanitize_textarea_field( wp_unslash( $_POST['menu_options'] ) ) : '';
					$this->save_menu_option( $this->opt_menu_adult, $menu_options_raw );
					// Child, App, Hors removed from save logic (Pro)
					echo '<div class="notice notice-success"><p>' . esc_html__( 'Adult Menu Options Saved.', 'wedding-party-rsvp' ) . '</p></div>';
				}
			}

			$curr_adult = get_option( $this->opt_menu_adult, array() );
			?>
			<div class="wrap">
				<h1><?php esc_html_e( 'Manage Menu Options', 'wedding-party-rsvp' ); ?></h1>
				<form method="post">
					<?php wp_nonce_field( 'wgrsvp_menu_nonce', 'wgrsvp_menu_nonce' ); ?>
					<div style="display:flex; gap:20px; flex-wrap:wrap;">
						<div style="flex:1; min-width:250px;"><h3><?php esc_html_e( 'Adult Entrées', 'wedding-party-rsvp' ); ?></h3><textarea name="menu_options" rows="8" style="width:100%;"><?php echo esc_textarea( implode( "\n", $curr_adult ) ); ?></textarea></div>
						<div style="flex:1; min-width:250px;">
							<h3><?php esc_html_e( 'Child Menu Options', 'wedding-party-rsvp' ); ?></h3>
							<div class="wpr-pro-placeholder" style="height:150px; display:flex; align-items:center; justify-content:center;">
								<a href="<?php echo esc_url( 'https://landtechwebdesigns.com/wedding-party-rsvp-wordpress-plugin/' ); ?>" target="_blank" class="wpr-pro-link">Upgrade to manage Child Menus</a>
							</div>
						</div>
					</div>
					<div style="display:flex; gap:20px; flex-wrap:wrap; margin-top:20px;">
						<div style="flex:1; min-width:250px;">
							<h3><?php esc_html_e( 'Appetizers', 'wedding-party-rsvp' ); ?></h3>
							<div class="wpr-pro-placeholder" style="height:150px; display:flex; align-items:center; justify-content:center;">
								<a href="<?php echo esc_url( 'https://landtechwebdesigns.com/wedding-party-rsvp-wordpress-plugin/' ); ?>" target="_blank" class="wpr-pro-link">Upgrade to manage Appetizers</a>
							</div>
						</div>
						<div style="flex:1; min-width:250px;">
							<h3><?php esc_html_e( 'Hors d\'oeuvres', 'wedding-party-rsvp' ); ?></h3>
							<div class="wpr-pro-placeholder" style="height:150px; display:flex; align-items:center; justify-content:center;">
								<a href="<?php echo esc_url( 'https://landtechwebdesigns.com/wedding-party-rsvp-wordpress-plugin/' ); ?>" target="_blank" class="wpr-pro-link">Upgrade to manage Hors d'oeuvres</a>
							</div>
						</div>
					</div>
					<br><input type="submit" name="wgrsvp_save_menu" class="button button-primary button-large" value="<?php esc_attr_e( 'Save Adult Options', 'wedding-party-rsvp' ); ?>">
				</form>
			</div>
			<?php
		}
		private function save_menu_option( $key, $clean_raw ) {
			// Expects already sanitized string
			update_option( $key, array_filter( array_map( 'trim', explode( "\n", $clean_raw ) ) ) );
		}

		// --- ADMIN ACTIONS ---
		private function handle_admin_actions() {
			// Security: Check user capabilities for all admin actions
			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}

			global $wpdb;

			// SECURITY FIX: Use filter_input with sanitized flag
			$add_nonce = filter_input( INPUT_POST, 'wgrsvp_add_guest', FILTER_SANITIZE_SPECIAL_CHARS );
			if ( $add_nonce && wp_verify_nonce( $add_nonce, 'wgrsvp_add_guest' ) ) {
				if ( isset( $_POST['wgrsvp_add_guest_btn'] ) ) {
					// Removed Guest Limit Check
					
					$wpdb->insert(
						$this->table_name,
						array(
							// phpcs:ignore WordPress.Security.ValidatedSanitizedInput
							'party_id'   => isset( $_POST['party_id'] ) ? sanitize_text_field( wp_unslash( $_POST['party_id'] ) ) : '',
							// phpcs:ignore WordPress.Security.ValidatedSanitizedInput
							'guest_name' => isset( $_POST['guest_name'] ) ? sanitize_text_field( wp_unslash( $_POST['guest_name'] ) ) : '',
							// is_child removed (Pro)
						)
					);
					
					$this->clear_stats_cache(); // Clear cache on add
				}
			}

			// 2. EDIT/DELETE (Share same nonce)
			$edit_nonce = filter_input( INPUT_POST, 'wgrsvp_edit_guest', FILTER_SANITIZE_SPECIAL_CHARS );
			if ( $edit_nonce && wp_verify_nonce( $edit_nonce, 'wgrsvp_edit_guest' ) ) {
				
				if ( isset( $_POST['wgrsvp_update_guest'] ) ) {
					// phpcs:ignore WordPress.Security.ValidatedSanitizedInput
					$id = isset( $_POST['id'] ) ? intval( $_POST['id'] ) : 0;

					$wpdb->update(
						$this->table_name,
						array(
							// phpcs:ignore WordPress.Security.ValidatedSanitizedInput
							'party_id'            => isset( $_POST['party_id'] ) ? sanitize_text_field( wp_unslash( $_POST['party_id'] ) ) : '',
							// phpcs:ignore WordPress.Security.ValidatedSanitizedInput
							'guest_name'          => isset( $_POST['guest_name'] ) ? sanitize_text_field( wp_unslash( $_POST['guest_name'] ) ) : '',
							// phpcs:ignore WordPress.Security.ValidatedSanitizedInput
							'rsvp_status'         => isset( $_POST['rsvp_status'] ) ? sanitize_text_field( wp_unslash( $_POST['rsvp_status'] ) ) : '',
							// phpcs:ignore WordPress.Security.ValidatedSanitizedInput
							'menu_choice'         => isset( $_POST['menu_choice'] ) ? sanitize_text_field( wp_unslash( $_POST['menu_choice'] ) ) : '',
							
							// REMOVED ALL PRO FIELDS FROM SAVE LOGIC: 
							// child_menu_choice, appetizer_choice, hors_doeuvre_choice, table_number, admin_notes, is_child
							
							// phpcs:ignore WordPress.Security.ValidatedSanitizedInput
							'email'               => isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '',
							// phpcs:ignore WordPress.Security.ValidatedSanitizedInput
							'phone'               => isset( $_POST['phone'] ) ? sanitize_text_field( wp_unslash( $_POST['phone'] ) ) : '',
						),
						array( 'id' => $id )
					);
					
					$this->clear_stats_cache(); // Clear cache on update
				}

				if ( isset( $_POST['wgrsvp_delete_guest'] ) ) {
					// phpcs:ignore WordPress.Security.ValidatedSanitizedInput
					$id = isset( $_POST['id'] ) ? intval( $_POST['id'] ) : 0;
					$wpdb->delete( $this->table_name, array( 'id' => $id ) );
					$this->clear_stats_cache(); // Clear cache on delete
				}
			}
		}

		// --- CSV IMPORT/EXPORT ---
		public function handle_csv_export() {
			// Security: Check user capabilities
			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'wedding-party-rsvp' ) );
			}

			// SECURITY FIX: Use filter_input with sanitized flag
			$export_nonce = filter_input( INPUT_POST, 'wgrsvp_export_nonce', FILTER_SANITIZE_SPECIAL_CHARS );
			if ( $export_nonce && wp_verify_nonce( $export_nonce, 'wgrsvp_export_nonce' ) ) {
				if ( isset( $_POST['wgrsvp_export_csv'] ) ) {
					global $wpdb;
					// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery
					$guests = $wpdb->get_results( "SELECT * FROM {$this->table_name}", ARRAY_A );
					header( 'Content-Type: text/csv' );
					header( 'Content-Disposition: attachment; filename="wedding-rsvp-export.csv"' );
					// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fopen
					$f = fopen( 'php://output', 'w' );
					fputcsv( $f, array( 'Party ID', 'Name', 'Child', 'Table', 'RSVP', 'Menu', 'Child Menu', 'Appetizer', 'Hors', 'Dietary', 'Allergies', 'Song', 'Message', 'Notes', 'Email', 'Phone' ) );
					foreach ( $guests as $r ) {
						fputcsv(
							$f,
							array(
								$r['party_id'],
								$r['guest_name'],
								$r['is_child'],
								$r['table_number'],
								$r['rsvp_status'],
								$r['menu_choice'],
								$r['child_menu_choice'],
								$r['appetizer_choice'],
								$r['hors_doeuvre_choice'],
								$r['dietary_restrictions'],
								$r['allergies'],
								$r['song_request'],
								$r['guest_message'],
								$r['admin_notes'],
								$r['email'],
								$r['phone'],
							)
						);
					}
					// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose
					fclose( $f );
					exit;
				}
			}
		}
		// REWRITTEN SIGNATURE to avoid direct $_FILES access
		private function handle_csv_import( $csv_filepath ) {
			if ( ! empty( $csv_filepath ) ) {
				global $wpdb;
				// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fopen
				$file = fopen( $csv_filepath, 'r' );
				if ( false !== $file ) {
					fgetcsv( $file ); // Skip header
					while ( ( $row = fgetcsv( $file ) ) !== false ) {
						if ( isset( $row[0] ) ) {

							// Removed Guest Limit Check

							$wpdb->insert(
								$this->table_name,
								array(
									'party_id'   => sanitize_text_field( $row[0] ),
									'guest_name' => sanitize_text_field( $row[1] ),
									'email'      => isset( $row[2] ) ? sanitize_email( $row[2] ) : '',
									'phone'      => isset( $row[3] ) ? sanitize_text_field( $row[3] ) : '',
									// is_child removed (Pro)
								)
							);
						}
					}
					// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose
					fclose( $file );
					$this->clear_stats_cache(); // Clear cache on import
				}
			}
		}

		// --- INIT: Handle Frontend Form (Redirection Logic) ---
		public function process_frontend_submissions() {
			
			// SECURITY FIX: Use filter_input
			$frontend_nonce = filter_input( INPUT_POST, '_wpnonce', FILTER_SANITIZE_SPECIAL_CHARS );

			if ( $frontend_nonce && wp_verify_nonce( $frontend_nonce, 'wpr_frontend_save' ) ) {
				
				if ( isset( $_POST['wpr_submit_rsvp'] ) ) {
					global $wpdb;
					
					// phpcs:ignore WordPress.Security.ValidatedSanitizedInput
					$party_id = isset( $_POST['party_id'] ) ? sanitize_text_field( wp_unslash( $_POST['party_id'] ) ) : '';

					// Honeypot
					if ( ! empty( $_POST['wpr_honey'] ) ) {
						return;
					}

					// phpcs:ignore WordPress.Security.ValidatedSanitizedInput
					if ( isset( $_POST['guest'] ) && is_array( $_POST['guest'] ) ) {
						// phpcs:ignore WordPress.Security.ValidatedSanitizedInput
						foreach ( $_POST['guest'] as $id => $data ) {
							$name      = sanitize_text_field( wp_unslash( $data['name_edit'] ?? $data['name_hidden'] ) );
							$allergies = isset( $data['allergies'] ) ? implode( ', ', array_map( 'sanitize_text_field', $data['allergies'] ) ) : '';

							$wpdb->update(
								$this->table_name,
								array(
									'guest_name'           => $name,
									'rsvp_status'          => sanitize_text_field( $data['rsvp'] ),
									'menu_choice'          => sanitize_text_field( $data['menu'] ?? '' ),
									'dietary_restrictions' => sanitize_text_field( $data['dietary'] ?? '' ),
									'allergies'            => $allergies,
									'song_request'         => sanitize_text_field( $data['song'] ?? '' ),
									'guest_message'        => sanitize_textarea_field( $data['message'] ?? '' ),
									'email'                => sanitize_email( $data['email'] ),
									'phone'                => sanitize_text_field( $data['phone'] ),
									'address'              => sanitize_textarea_field( $data['address'] ),
								),
								array(
									'id'       => intval( $id ),
									'party_id' => $party_id,
								)
							);
						}
						
						$this->clear_stats_cache();
					}

					$settings = get_option( $this->opt_settings, array() );
					if ( ! empty( $settings['redirect_url'] ) ) {
						wp_safe_redirect( $settings['redirect_url'] );
						exit;
					} else {
						// Set transient to show success message on next page load if no redirect
						set_transient( 'wgrsvp_success_msg', '1', 60 );
						// Safe refresh
						wp_safe_redirect( remove_query_arg( 'wpr_submit_rsvp' ) );
						exit;
					}
				}
			}
		}

		// --- HELPER: Generate Tags for Email/SMS ---
		private function get_replacement_tags( $guest ) {
			$gen_settings = get_option( $this->opt_settings, array() );
			$base_url     = ! empty( $gen_settings['rsvp_page_url'] ) ? $gen_settings['rsvp_page_url'] : home_url( '/' );

			$rsvp_link = add_query_arg( 'party_id', urlencode( $guest->party_id ), $base_url );

			return array(
				'{name}'      => $guest->guest_name,
				'{party_id}'  => $guest->party_id,
				'{rsvp_link}' => $rsvp_link,
			);
		}

		// --- FRONTEND RENDER ---
		public function render_frontend_form() {
			global $wpdb;
			$settings = get_option( $this->opt_settings, array() );

			if ( ! empty( $settings['deadline_date'] ) && current_time( 'Y-m-d' ) > $settings['deadline_date'] ) {
				return '<div class="wpr-wrapper"><div class="wpr-guest-card" style="text-align:center;color:red;"><h3>' . esc_html__( 'RSVPs are now Closed', 'wedding-party-rsvp' ) . '</h3><p>' . esc_html__( 'Please contact the couple directly.', 'wedding-party-rsvp' ) . '</p></div></div>';
			}

			// Check for Success Message from Init Redirect
			if ( get_transient( 'wgrsvp_success_msg' ) ) {
				delete_transient( 'wgrsvp_success_msg' );
				return '<div class="wpr-wrapper"><div style="color:green;border:1px solid green;padding:15px;margin-bottom:20px;background:#eaffea;">' . esc_html__( 'Thank you! Your RSVP has been updated.', 'wedding-party-rsvp' ) . '</div></div>';
			}

			$output = '<div class="wpr-wrapper">';

			// --- LOGIN FORM CHECK ---
			$login_nonce = filter_input( INPUT_POST, 'wpr_login_nonce', FILTER_SANITIZE_SPECIAL_CHARS );
			$party_id = '';

			// 1. Check POST Login
			if ( $login_nonce && wp_verify_nonce( $login_nonce, 'wpr_login_action' ) ) {
				if ( isset( $_POST['wpr_party_id'] ) ) {
					$party_id = sanitize_text_field( wp_unslash( $_POST['wpr_party_id'] ) );
				}
			}

			// 2. Check URL Param
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended
			if ( empty( $party_id ) && isset( $_GET['party_id'] ) ) {
				// phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$party_id = sanitize_text_field( wp_unslash( $_GET['party_id'] ) );
			}

			// 3. Query Guests (SQL Safe)
			// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery, PluginCheck.Security.DirectDB.UnescapedDBParameter, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$guests = $party_id ? $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$this->table_name} WHERE party_id = %s", $party_id ) ) : array();

			if ( empty( $guests ) ) {
				$output .= '<form method="post">';
				$output .= wp_nonce_field( 'wpr_login_action', 'wpr_login_nonce', true, false );
				$output .= '<div class="wpr-field"><label>' . esc_html__( 'Party ID:', 'wedding-party-rsvp' ) . '</label><input type="text" name="wpr_party_id" required></div><button name="wpr_login_action" class="wpr-button">Find Invitation</button></form>';
			} else {
				$menus_adult = get_option( $this->opt_menu_adult, array() );
				$welcome_title = ! empty( $settings['welcome_title'] ) ? stripslashes( $settings['welcome_title'] ) : 'Party: ' . esc_html( $party_id );

				$output .= '<form method="post">' . wp_nonce_field( 'wpr_frontend_save', '_wpnonce', true, false ) . '<input type="hidden" name="party_id" value="' . esc_attr( $party_id ) . '">';
				$output .= '<h2>' . esc_html( $welcome_title ) . '</h2>';
				$output .= '<input type="text" name="wpr_honey" class="wpr-honey">';

				foreach ( $guests as $g ) {
					$output .= '<div class="wpr-guest-card">';
					$is_placeholder = in_array( strtolower( $g->guest_name ), array( 'guest', 'plus one', '+1' ) );
					if ( $is_placeholder ) {
						$output .= '<div class="wpr-field"><label>' . esc_html__( 'Guest Name:', 'wedding-party-rsvp' ) . '</label><input type="text" name="guest[' . $g->id . '][name_edit]" value="' . esc_attr( $g->guest_name ) . '"></div>';
					} else {
						$output .= '<h3>' . esc_html( $g->guest_name ) . '</h3>';
						$output .= '<input type="hidden" name="guest[' . $g->id . '][name_hidden]" value="' . esc_attr( $g->guest_name ) . '">';
					}

					// Table Display Removed (Pro)

					$output .= '<div class="wpr-field"><label>' . esc_html__( 'Attending?', 'wedding-party-rsvp' ) . '</label><select name="guest[' . $g->id . '][rsvp]" required>';
					$output .= '<option value="Pending" ' . selected( $g->rsvp_status, 'Pending', false ) . '>' . esc_html__( 'Select...', 'wedding-party-rsvp' ) . '</option>';
					$output .= '<option value="Accepted" ' . selected( $g->rsvp_status, 'Accepted', false ) . '>' . esc_html__( 'Delighted to attend', 'wedding-party-rsvp' ) . '</option>';
					$output .= '<option value="Declined" ' . selected( $g->rsvp_status, 'Declined', false ) . '>' . esc_html__( 'Unable to attend', 'wedding-party-rsvp' ) . '</option></select></div>';

					// Only render Adult Menu in Free Version
					$output .= '<div class="wpr-field"><label>' . esc_html__( 'Entrée', 'wedding-party-rsvp' ) . '</label><select name="guest[' . $g->id . '][menu]"><option value="">' . esc_html__( 'Select...', 'wedding-party-rsvp' ) . '</option>';
					foreach ( $menus_adult as $m ) {
						$output .= '<option value="' . esc_attr( $m ) . '" ' . selected( $g->menu_choice, $m, false ) . '>' . esc_html( $m ) . '</option>';
					}
					$output .= '</select></div>';

					$output .= '<div class="wpr-field"><label>' . esc_html__( 'Dietary Restrictions', 'wedding-party-rsvp' ) . '</label>';
					$allergies       = array( 'Gluten Free', 'Dairy Free', 'Vegetarian', 'Vegan', 'Nut Allergy' );
					$saved_allergies = explode( ', ', $g->allergies );
					$output         .= '<div class="wpr-checkbox-group">';
					foreach ( $allergies as $a ) {
						$checked = in_array( $a, $saved_allergies ) ? 'checked' : '';
						$output .= '<label><input type="checkbox" name="guest[' . $g->id . '][allergies][]" value="' . esc_attr( $a ) . '" ' . $checked . '> ' . esc_html( $a ) . '</label>';
					}
					$output .= '</div><input type="text" name="guest[' . $g->id . '][dietary]" value="' . esc_attr( $g->dietary_restrictions ) . '" placeholder="' . esc_attr__( 'Other...', 'wedding-party-rsvp' ) . '"></div>';

					$output .= '<div class="wpr-field"><label>' . esc_html__( 'I promise to dance if you play:', 'wedding-party-rsvp' ) . '</label><input type="text" name="guest['.$g->id.'][song]" value="'.esc_attr($g->song_request).'"></div>';

					$output .= '<div class="wpr-field"><label>' . esc_html__( 'Message to Couple:', 'wedding-party-rsvp' ) . '</label><textarea name="guest['.$g->id.'][message]" rows="2" placeholder="Note to the bride & groom...">' . esc_textarea( $g->guest_message ) . '</textarea></div>';

					$output .= '<div class="wpr-field"><label>' . esc_html__( 'Email', 'wedding-party-rsvp' ) . '</label><input type="email" name="guest[' . $g->id . '][email]" value="' . esc_attr( $g->email ) . '"></div>';
					$output .= '<div class="wpr-field"><label>' . esc_html__( 'Phone', 'wedding-party-rsvp' ) . '</label><input type="text" name="guest[' . $g->id . '][phone]" value="' . esc_attr( $g->phone ) . '"></div>';
					$output .= '<div class="wpr-field"><label>' . esc_html__( 'Mailing Address', 'wedding-party-rsvp' ) . '</label><textarea name="guest[' . $g->id . '][address]">' . esc_textarea( $g->address ) . '</textarea></div>';

					$output .= '</div>';
				}
				$output .= '<button name="wpr_submit_rsvp" class="wpr-button">Submit RSVP</button></form>';
			}
			return $output . '</div>';
		}
		
		// --- EMAIL PAGE (Upsell Placeholder) ---
		public function admin_page_email() {
			?>
			<div class="wrap">
				<h1><?php esc_html_e( 'Email Invites', 'wedding-party-rsvp' ); ?></h1>
				<div style="background:#fff; border:1px solid #ccc; padding:30px; text-align:center; max-width:600px; margin-top:20px;">
					<h2><?php esc_html_e( 'Send Invites Directly', 'wedding-party-rsvp' ); ?></h2>
					<p><?php esc_html_e( 'The Pro version includes a complete Email Invitation system. Send customized invites to your guests with one click.', 'wedding-party-rsvp' ); ?></p>
					<a href="<?php echo esc_url( 'https://landtechwebdesigns.com/wedding-party-rsvp-wordpress-plugin/' ); ?>" target="_blank" class="button button-primary button-large"><?php esc_html_e( 'Upgrade to Pro', 'wedding-party-rsvp' ); ?></a>
				</div>
			</div>
			<?php
		}

		// --- SMS PAGE (Upsell Placeholder) ---
		public function admin_page_sms() {
			?>
			<div class="wrap">
				<h1><?php esc_html_e( 'SMS Invites', 'wedding-party-rsvp' ); ?></h1>
				<div style="background:#fff; border:1px solid #ccc; padding:30px; text-align:center; max-width:600px; margin-top:20px;">
					<h2><?php esc_html_e( 'Text Your Guests', 'wedding-party-rsvp' ); ?></h2>
					<p><?php esc_html_e( 'Upgrade to the Pro version to integrate with Twilio and send SMS invitations directly to your guest list.', 'wedding-party-rsvp' ); ?></p>
					<a href="<?php echo esc_url( 'https://landtechwebdesigns.com/wedding-party-rsvp-wordpress-plugin/' ); ?>" target="_blank" class="button button-primary button-large"><?php esc_html_e( 'Upgrade to Pro', 'wedding-party-rsvp' ); ?></a>
				</div>
			</div>
			<?php
		}
	}

	new WGRSVP_Wedding_RSVP();

	// Load and run review request (admin only, after 7 days).
	add_action(
		'admin_init',
		function () {
			require_once plugin_dir_path( __FILE__ ) . 'includes/class-review-request.php';
			new WGRSVP_Review_Request();
		}
	);

endif;
