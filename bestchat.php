<?php
/**
 * Plugin Name: bestchat
 * Plugin URI: http://wordpress.org/plugins/bestchat/
 * Description: bestchat is a Livechat plugin
 * Author: bestchat
 * Version: 0.1
 * Author URI: https://www.bestchat.com
 * Text Domain: bestchat
 */

add_action("admin_menu", "bestchat_create_menu");

function bestchat_create_menu()
{
    add_menu_page(
        __("BestChat", "bestchat"),
        __("BestChat", "bestchat"),
        "administrator", __FILE__,
        "bestchat_plugin_settings_page",
        plugins_url("/assets/images/logo.svg", __FILE__)
    );

    add_action("admin_init", "register_bestchat_plugin_settings");
    add_action("admin_init", "register_bestchat_plugin_onboarding");
    add_action("admin_notices", "register_bestchat_plugin_notice");
    add_action("admin_enqueue_scripts", "register_bestchat_admin_enqueue");
    add_action("plugins_loaded", "register_bestchat_plugin_textdomain");
}

function register_bestchat_plugin_textdomain()
{
    load_plugin_textdomain("bestchat", FALSE, basename(dirname(__FILE__)) . "/languages/");
}

function register_bestchat_plugin_onboarding()
{
    $onboarding = get_option("bestchat_onboarding");
    $website_cid = get_option("website_cid");

    if (empty($website_cid) && (empty($onboarding) || !$onboarding)) {
        update_option("bestchat_onboarding", true);
        wp_redirect(admin_url("admin.php?page=" . plugin_basename(__FILE__)));
    }
}

function register_bestchat_plugin_notice()
{
    $website_cid = get_option("website_cid");

    if (empty($website_cid) && $_GET["page"] != plugin_basename(__FILE__)) {
        $admin_url = admin_url("admin.php?page=" . plugin_basename(__FILE__));
        ?>
        <div class="notice notice-warning is-dismissible notice-bestchat">
            <p>
                <img src="<?php echo plugins_url("/assets/images/logo.svg", __FILE__); ?>" height="16"
                     style="margin-bottom: -3px"/>
                &nbsp;
                <?php
                echo sprintf(
                    esc_html(__('The bestchat plugin isn’t connected right now. To display bestchat widget on your WordPress site, %1$sconnect the plugin now%2$s. The configuration only takes 1 minute!', "bestchat")),
                    "<a href='$admin_url'>",
                    "</a>"
                );
                ?>
            </p>
        </div>
        <?php
    }
}

function register_bestchat_plugin_settings()
{
    register_setting("bestchat-plugin-settings-group", "website_cid");
    add_option("bestchat_onboarding", false);
}

function register_bestchat_admin_enqueue()
{
    wp_enqueue_style("admin_bestchat_style", plugins_url("/assets/stylesheets/style.css", __FILE__));
}

function bestchat_plugin_settings_page()
{
    add_action("admin_enqueue_scripts", "bestchat_admin_enqueue");

    if (current_user_can("administrator")) {
        if (wp_verify_nonce($_GET['_wpnonce'])) {
            if (isset($_GET["bestchat_website_cid"]) && !empty($_GET["bestchat_website_cid"])) {
                update_option("website_cid", sanitize_text_field($_GET["bestchat_website_cid"]));

                // Clear WP Rocket Cache if needed
                if (function_exists("rocket_clean_domain")) {
                    rocket_clean_domain();
                }

                // Clear WP Super Cache if needed
                if (function_exists("wp_cache_clean_cache")) {
                    global $file_prefix;
                    wp_cache_clean_cache($file_prefix, true);
                }
            }

            if (isset($_GET["bestchat_verify"]) && !empty($_GET["bestchat_verify"])) {
                update_option("website_verify", sanitize_key($_GET["bestchat_verify"]));
            }
        }
    }

    $website_cid = get_option("website_cid");
    $is_bestchat_working = isset($website_cid) && !empty($website_cid);
    $http_callback = sanitize_url("http" . (($_SERVER["SERVER_PORT"] == 443) ? "s://" : "://") . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
    $http_callback = urlencode(wp_nonce_url($http_callback));
    $admin_email = wp_get_current_user()->user_email;
    $admin_name = wp_get_current_user()->display_name;
    $website_name = get_bloginfo("name");
    $website_domain = get_site_url();
    $website_domain = str_replace("http://", "", $website_domain);
    $website_domain = str_replace("https://", "", $website_domain);
    $website_domain = preg_replace("(:[0-9]{1,6})", "", $website_domain);

    $add_to_bestchat_link = esc_url_raw("https://msa.bestchat.com/chat/wordpress/install.jsp?payload=$http_callback&user_email=$admin_email&user_name=$admin_name&website_name=$website_name&website_domain=$website_domain");

    if ($is_bestchat_working) {
        include_once(plugin_dir_path(__FILE__) . "views/settings_installed.php");
    } else {
        include_once(plugin_dir_path(__FILE__) . "views/settings_install.php");
    }
}

add_action("wp_enqueue_scripts", "bestchat_enqueue_script");
add_action("script_loader_tag", "bestchat_enqueue_async", 10, 2);

function bestchat_enqueue_script()
{
    $website_cid = esc_js(get_option("website_cid"));

    if (!isset($website_cid) || empty($website_cid)) {
        return;
    }

    wp_enqueue_script("bestchat", "https://msa.bestchat.com/chat/b.js?uuid=" . $website_cid . "&isPreview=1", array(), "", true);
}

function bestchat_enqueue_async($tag, $handle)
{
    if ("bestchat" !== $handle) {
        return $tag;
    }

    return str_replace("src", " async src", $tag);
}
