<?php
/**
 * Plugin Name: CoBlocks
 * Plugin URI: https://coblocks.com/
 * Description: CoBlocks is a suite of professional page building content blocks for the WordPress Gutenberg block editor. Our blocks are hyper-focused on empowering makers to build beautifully rich pages in WordPress.
 * Author: CoBlocks
 * Author URI: https://coblocks.com/
 * Version: 1.0.0
 * Text Domain: '@@textdomain'
 * Domain Path: languages
 * Tested up to: @@pkg.tested_up_to
 *
 * @@pkg.title is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with @@pkg.title. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package   @@pkg.title
 * @author    @@pkg.author
 * @license   @@pkg.license
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'CoBlocks' ) ) :
	/**
	 * Main CoBlocks Class.
	 *
	 * @since 1.0.0
	 */
	final class CoBlocks {
		/**
		 * This plugin's instance.
		 *
		 * @var CoBlocks
		 * @since 1.0.0
		 */
		private static $instance;

		/**
		 * Main CoBlocks Instance.
		 *
		 * Insures that only one instance of CoBlocks exists in memory at any one
		 * time. Also prevents needing to define globals all over the place.
		 *
		 * @since 1.0.0
		 * @static
		 * @return object|CoBlocks The one true CoBlocks
		 */
		public static function instance() {
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof CoBlocks ) ) {
				self::$instance = new CoBlocks();
				self::$instance->init();
				self::$instance->constants();
				self::$instance->asset_suffix();
				self::$instance->includes();
			}
			return self::$instance;
		}

		/**
		 * Setup plugin constants.
		 *
		 * @access private
		 * @since 1.0.0
		 * @return void
		 */
		private function constants() {
			$this->define( 'COBLOCKS_DEBUG', true );
			$this->define( 'COBLOCKS_VERSION', '@@pkg.version' );
			$this->define( 'COBLOCKS_HAS_PRO', false );
			$this->define( 'COBLOCKS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
			$this->define( 'COBLOCKS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
			$this->define( 'COBLOCKS_PLUGIN_FILE', __FILE__ );
			$this->define( 'COBLOCKS_SHOP_URL', 'https://coblocks.com/' );
		}

		/**
		 * Define constant if not already set.
		 *
		 * @param  string|string $name Name of the definition.
		 * @param  string|bool   $value Default value.
		 */
		private function define( $name, $value ) {
			if ( ! defined( $name ) ) {
				define( $name, $value );
			}
		}

		/**
		 * Include required files.
		 *
		 * @access private
		 * @since 1.0.0
		 * @return void
		 */
		private function includes() {
			require_once COBLOCKS_PLUGIN_DIR . 'includes/class-coblocks-block-assets.php';
			require_once COBLOCKS_PLUGIN_DIR . 'includes/class-coblocks-post-type.php';

			if ( is_admin() || ( defined( 'WP_CLI' ) && WP_CLI ) ) {
				require_once COBLOCKS_PLUGIN_DIR . 'includes/admin/class-coblocks-action-links.php';
				require_once COBLOCKS_PLUGIN_DIR . 'includes/admin/class-coblocks-admin-notices.php';
				require_once COBLOCKS_PLUGIN_DIR . 'includes/admin/class-coblocks-admin-styles.php';
				require_once COBLOCKS_PLUGIN_DIR . 'includes/admin/class-coblocks-install.php';
				require_once COBLOCKS_PLUGIN_DIR . 'includes/admin/class-coblocks-url-generator.php';
			}
		}

		/**
		 * Load actions
		 *
		 * @return void
		 */
		private function init() {
			add_action( 'plugins_loaded', array( $this, 'load_textdomain' ), 99 );
			add_action( 'plugins_loaded', array( $this, 'load_dynamic_blocks' ), 99 );
		}

		/**
		 * Register server-side code for individual blocks.
		 *
		 * @access public
		 */
		public function load_dynamic_blocks() {
			foreach ( glob( dirname( __FILE__ ) . '/src/blocks/*/index.php' ) as $block_logic ) {
				require $block_logic;
			}
		}

		/**
		 * Change the plugin's minified or src file name, based on debug mode.
		 *
		 * @since 1.0.0
		 */
		public function asset_suffix() {
			if ( true === COBLOCKS_DEBUG ) {
				define( 'COBLOCKS_ASSET_SUFFIX', null );
			} else {
				define( 'COBLOCKS_ASSET_SUFFIX', '.min' );
			}
		}

		/**
		 * If debug is on, serve unminified source assets.
		 *
		 * @since 1.0.0
		 * @param string|string $type The type of resource.
		 * @param string|string $directory Any extra directories needed.
		 */
		public function asset_source( $type = 'js', $directory = null ) {

			if ( 'js' === $type ) {
				if ( true === COBLOCKS_DEBUG ) {
					return COBLOCKS_PLUGIN_URL . 'src/' . $type . '/' . $directory;
				} else {
					return COBLOCKS_PLUGIN_URL . 'dist/' . $type . '/' . $directory;
				}
			} else {
				return COBLOCKS_PLUGIN_URL . 'dist/css/' . $directory;
			}
		}

		/**
		 * Check if pro exists.
		 *
		 * @access public
		 */
		public function has_pro() {
			if ( true === COBLOCKS_HAS_PRO ) {
				return true;
			} else {
				return false;
			}
		}

		/**
		 * Check if pro is activated.
		 *
		 * @access public
		 */
		public function is_pro() {

			if ( class_exists( 'CoBlocks_Pro' ) ) {
				return true;
			} else {
				return false;
			}
		}

		/**
		 * Loads the plugin language files.
		 *
		 * @access public
		 * @since 1.0.0
		 * @return void
		 */
		public function load_textdomain() {
			load_plugin_textdomain( '@@textdomain', false, dirname( plugin_basename( untrailingslashit( plugin_dir_path( '/', __FILE__ ) ) ) ) . '/languages/' );
		}
	}
endif;

/**
 * The main function for that returns CoBlocks
 *
 * The main function responsible for returning the one true CoBlocks
 * Instance to functions everywhere.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * Example: <?php $coblocks = CoBlocks(); ?>
 *
 * @since 1.0.0
 * @return object|CoBlocks The one true CoBlocks Instance.
 */
function coblocks() {
	return CoBlocks::instance();
}

// Get the plugin running. Load on plugins_loaded action to avoid issue on multisite.
if ( function_exists( 'is_multisite' ) && is_multisite() ) {
	add_action( 'plugins_loaded', 'coblocks', 90 );
} else {
	coblocks();
}
