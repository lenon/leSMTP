<?php
/*
 * Plugin Name: leSMTP
 * Plugin URI: http://github.com/lenon/leSMTP/
 * License: MIT
 * Description: Add SMTP support to WordPress.
 * Version: 0.1
 * Author: Lenon Marcel
 * Author URI: http://lenonmarcel.com.br/
 * Text Domain: lesmtp
 * Domain Path: /languages/
 */
/*
 * Copyright (c) 2010 Lenon Marcel <lenon.marcel@gmail.com>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

class leSMTP
{
    /**
     * Plugin version.
     */
    const VERSION = '0.1';

    /**
     * Used by get_option() to store the plugin options.
     *
     * @var array
     */
    protected static $options;

    /**
     * Used by send_test() to show the test results in the options page.
     *
     * @var bool
     */
    protected static $show_result;

    /**
     * Called on plugin activation and upgrading.
     */
    public static function setup()
    {
        if (get_option('lesmtp'))
            return;

        add_option('lesmtp', array(
            'mail_from' => '',
            'mail_from_name' => get_bloginfo('name'),
            'host' => '',
            'port' => '25',
            'secure' => '',
            'auth' => false,
            'user' => '',
            'pass' => ''
        ));
    }

    /**
     * Add 'SMTP' page to the main menu.
     */
    public static function add_options_page()
    {
        add_options_page(__('Configurações de SMTP', 'lesmtp'), __('SMTP', 'lesmtp'), 'manage_options', __FILE__, 'leSMTP::options_page');
    }

    /**
     * Register plugin settings.
     */
    public static function register_settings()
    {
        register_setting('lesmtp-options-group', 'lesmtp');
    }

    /**
     * Show the options page.
     */
    public static function options_page()
    {
        require dirname(__FILE__) . '/options.php';
    }

    /**
     * Retrieve an option value.
     *
     * @param string $name
     * @return mixed
     */
    public static function get_option($name)
    {
        if (!self::$options)
            self::$options = get_option('lesmtp');

        return isset(self::$options[$name]) ? self::$options[$name] : '';
    }

    /**
     * Called by wp_mail() before send an email.
     * This function configures all necessary options in the PHPMailer object.
     *
     * @param PHPMailer $mailer
     */
    public static function phpmailer_init($mailer)
    {
        $mailer->IsSMTP();

        $mailer->From       = self::get_option('mail_from');
        $mailer->FromName   = self::get_option('mail_from_name');
        $mailer->Host       = self::get_option('host');
        $mailer->Port       = self::get_option('port');
        $mailer->SMTPSecure = self::get_option('secure');

        $mailer->SMTPAuth = false;

        if (self::get_option('auth') == 'true') {
            $mailer->SMTPAuth = true;
            $mailer->Username = self::get_option('user');
            $mailer->Password = self::get_option('pass');
        }
    }

    /**
     * Send the test e-mail.
     */
    public static function send_test()
    {
        if ( !isset($_POST['lesmtp-test-submit']) || empty($_POST['to']) )
            return;

        check_admin_referer('lesmtp-test-form');

        $message = (isset($_POST['message']) ? $_POST['message'] : '') . "\n\n" .
                   sprintf(__('Timestamp atual: %s', 'lesmtp'), current_time('mysql'));

        self::$show_result = true;

        // Good luck :D
        wp_mail($_POST['to'], __('Mensagem de teste do seu site WordPress', 'lesmtp'), $message);
    }
}

add_action('phpmailer_init', 'leSMTP::phpmailer_init');

if ( !is_admin() )
    return;

load_plugin_textdomain('lesmtp', false, dirname(plugin_basename(__FILE__)) . '/languages/');

register_activation_hook(__FILE__, 'leSMTP::setup');

add_action('admin_menu', 'leSMTP::add_options_page');
add_action('admin_init', 'leSMTP::register_settings');
add_action('admin_init', 'leSMTP::send_test');
