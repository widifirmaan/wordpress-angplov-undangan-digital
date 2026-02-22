<?php
/**
 * Review request notice with AJAX dismissal.
 *
 * Shows "Enjoying [Plugin Name]?" after 7 days, with Yes / No (Support) / Dismiss.
 * Only on this plugin's admin pages. Dismissal via AJAX with nonce.
 *
 * @package Wedding_Party_RSVP
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WGRSVP_Review_Request' ) ) {

	/**
	 * Class WGRSVP_Review_Request
	 */
	class WGRSVP_Review_Request {

		const OPT_INSTALLED_AT = 'wgrsvp_review_installed_at';
		const OPT_DISMISSED    = 'wgrsvp_review_dismissed';
		const AJAX_ACTION      = 'wgrsvp_review_action';
		const NONCE_ACTION     = 'wgrsvp_review_nonce';
		const DAYS_BEFORE_ASK  = 7;

		/** @var string */
		private $plugin_name;

		/** @var string */
		private $plugin_slug;

		/** @var string */
		private $support_url;

		/** @var string */
		private $review_url;

		/**
		 * Constructor.
		 */
		public function __construct() {
			$this->plugin_name = __( 'Wedding Party RSVP', 'wedding-party-rsvp' );
			$this->plugin_slug = 'wedding-party-rsvp';
			$this->support_url = apply_filters(
				'wgrsvp_review_request_support_url',
				'https://landtechwebdesigns.com/wedding-party-rsvp-wordpress-plugin/'
			);
			$this->review_url  = 'https://wordpress.org/support/plugin/' . $this->plugin_slug . '/reviews/#new-post';

			add_action( 'admin_init', array( $this, 'maybe_set_install_date' ) );
			add_action( 'admin_notices', array( $this, 'maybe_show_notice' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'maybe_enqueue_script' ) );
			add_action( 'wp_ajax_' . self::AJAX_ACTION, array( $this, 'ajax_handle_dismiss' ) );
		}

		/**
		 * Set install date option if missing (so 7-day countdown can run).
		 */
		public function maybe_set_install_date() {
			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}
			if ( get_option( self::OPT_INSTALLED_AT, 0 ) ) {
				return;
			}
			update_option( self::OPT_INSTALLED_AT, time() );
		}

		/**
		 * Whether we're on one of this plugin's admin pages.
		 *
		 * @return bool
		 */
		private function is_plugin_admin_page() {
			$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
			if ( ! $screen || ! isset( $screen->id ) ) {
				return false;
			}
			// Main: toplevel_page_wedding-rsvp-main; submenus: *wedding-rsvp* (e.g. wedding-rsvp-main_page_wedding-rsvp-menu).
			return ( $screen->id === 'toplevel_page_wedding-rsvp-main' || strpos( $screen->id, 'wedding-rsvp' ) !== false );
		}

		/**
		 * Whether the notice should be shown.
		 *
		 * @return bool
		 */
		private function should_show_notice() {
			if ( ! current_user_can( 'manage_options' ) ) {
				return false;
			}
			if ( ! $this->is_plugin_admin_page() ) {
				return false;
			}
			if ( get_option( self::OPT_DISMISSED, 0 ) ) {
				return false;
			}
			$installed_at = (int) get_option( self::OPT_INSTALLED_AT, 0 );
			if ( ! $installed_at ) {
				return false;
			}
			$days_elapsed = ( time() - $installed_at ) / DAY_IN_SECONDS;
			return $days_elapsed >= self::DAYS_BEFORE_ASK;
		}

		/**
		 * Output the review request notice (only on plugin admin pages, after 7 days).
		 */
		public function maybe_show_notice() {
			if ( ! $this->should_show_notice() ) {
				return;
			}
			$nonce = wp_create_nonce( self::NONCE_ACTION );
			?>
			<div class="notice notice-info wgrsvp-review-request-notice" id="wgrsvp-review-request-notice" style="position:relative;">
				<p>
					<?php
					echo esc_html(
						sprintf(
							/* translators: %s: plugin name */
							__( 'Enjoying %s?', 'wedding-party-rsvp' ),
							$this->plugin_name
						)
					);
					?>
				</p>
				<p>
					<button type="button" class="button button-primary wgrsvp-review-btn" data-action="yes" data-nonce="<?php echo esc_attr( $nonce ); ?>" data-review-url="<?php echo esc_url( $this->review_url ); ?>">
						<?php esc_html_e( 'Yes', 'wedding-party-rsvp' ); ?>
					</button>
					<button type="button" class="button wgrsvp-review-btn" data-action="no" data-nonce="<?php echo esc_attr( $nonce ); ?>" data-support-url="<?php echo esc_url( $this->support_url ); ?>">
						<?php esc_html_e( 'No / Support', 'wedding-party-rsvp' ); ?>
					</button>
					<button type="button" class="button button-link wgrsvp-review-btn" data-action="dismiss" data-nonce="<?php echo esc_attr( $nonce ); ?>">
						<?php esc_html_e( 'Dismiss', 'wedding-party-rsvp' ); ?>
					</button>
				</p>
			</div>
			<?php
		}

		/**
		 * Enqueue inline script only on this plugin's admin pages when notice is shown.
		 */
		public function maybe_enqueue_script( $hook_suffix ) {
			if ( ! $this->should_show_notice() ) {
				return;
			}
			wp_enqueue_script( 'jquery' );
			$script = "
			(function() {
				var notice = document.getElementById('wgrsvp-review-request-notice');
				if (!notice) return;
				notice.addEventListener('click', function(e) {
					var btn = e.target.closest('.wgrsvp-review-btn');
					if (!btn) return;
					e.preventDefault();
					var action = btn.getAttribute('data-action');
					var nonce = btn.getAttribute('data-nonce');
					var url = action === 'yes' ? btn.getAttribute('data-review-url') : (action === 'no' ? btn.getAttribute('data-support-url') : null);
					var xhr = new XMLHttpRequest();
					xhr.open('POST', " . wp_json_encode( admin_url( 'admin-ajax.php' ) ) . ", true);
					xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
					xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
					xhr.onreadystatechange = function() {
						if (xhr.readyState !== 4) return;
						try {
							var res = JSON.parse(xhr.responseText);
							if (res.success) {
								notice.style.opacity = '0';
								notice.style.transition = 'opacity 0.2s';
								setTimeout(function() { notice.remove(); }, 250);
								if (url) window.open(url, '_blank');
							}
						} catch (err) {}
					};
					xhr.send('action=" . wp_json_encode( self::AJAX_ACTION ) . "&nonce=' + encodeURIComponent(nonce) + '&choice=' + encodeURIComponent(action));
				});
			})();
			";
			wp_add_inline_script( 'jquery', $script, 'after' );
		}

		/**
		 * AJAX handler: verify nonce, save dismissal, return JSON.
		 */
		public function ajax_handle_dismiss() {
			check_ajax_referer( self::NONCE_ACTION, 'nonce' );

			if ( ! current_user_can( 'manage_options' ) ) {
				wp_send_json_error( array( 'message' => 'Forbidden' ) );
			}

			$choice = isset( $_POST['choice'] ) ? sanitize_text_field( wp_unslash( $_POST['choice'] ) ) : '';
			$allowed = array( 'yes', 'no', 'dismiss' );
			if ( ! in_array( $choice, $allowed, true ) ) {
				wp_send_json_error( array( 'message' => 'Invalid choice' ) );
			}

			update_option( self::OPT_DISMISSED, 1 );
			wp_send_json_success( array( 'choice' => $choice ) );
		}
	}
}
