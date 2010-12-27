<?php
if (!defined('WP_ADMIN')) // NO HACKERS PLZ
    return wp_die('Please, get out');
?>

<div class="wrap">

<h2><?php _e('Configurações de SMTP', 'lesmtp') ?></h2>

<form id="lesmtp-form" method="post" action="options.php">
    <?php settings_fields('lesmtp-options-group'); ?>

    <table class="form-table"><!-- eca, tabelas :\ -->

        <!-- mail_from -->
        <tr valign="top">
            <th scope="row">
                <label for="lesmtp_mail_from"><?php _e('E-mail do remetente', 'lesmtp') ?></label>
            </th>
            <td>
                <input type="text" id="lesmtp_mail_from" class="regular-text code" name="lesmtp[mail_from]" value="<?php echo self::get_option('mail_from') ?>"/>
            </td>
        </tr>

        <!-- mail_from_name -->
        <tr valign="top">
            <th scope="row">
                <label for="lesmtp_mail_from_name"><?php _e('Nome do remetente', 'lesmtp') ?></label>
            </th>
            <td>
                <input type="text" id="lesmtp_mail_from_name" class="regular-text" name="lesmtp[mail_from_name]" value="<?php echo self::get_option('mail_from_name') ?>"/>
            </td>
        </tr>

        <!-- host -->
        <tr valign="top">
            <th scope="row">
                <label for="lesmtp_host"><?php _e('Servidor SMTP', 'lesmtp') ?></label>
            </th>
            <td>
                <input type="text" id="lesmtp_host" class="regular-text code" name="lesmtp[host]" value="<?php echo self::get_option('host') ?>"/>
            </td>
        </tr>

        <!-- port -->
        <tr valign="top">
            <th scope="row">
                <label for="lesmtp_port"><?php _e('Porta', 'lesmtp') ?></label>
            </th>
            <td>
                <input type="text" id="lesmtp_port" class="small-text" name="lesmtp[port]" value="<?php echo self::get_option('port') ?>"/>
            </td>
        </tr>

        <!-- secure -->
        <tr valign="top">
            <th scope="row">
                <label for="lesmtp_secure"><?php _e('Segurança', 'lesmtp') ?></label>
            </th>
            <td>
                <select id="lesmtp_secure" name="lesmtp[secure]">

                    <?php
                    $secure = self::get_option('secure');

                    $options = array(
                        array(__('Nenhuma', 'lesmtp'), ''),
                        array(__('SSL', 'lesmtp'), 'ssl'),
                        array(__('TLS', 'lesmtp'), 'tls')
                    );

                    foreach ($options as $option) {
                        $selected = ($secure == $option[1] ? ' selected' : '');
                        echo "<option value=\"{$option[1]}\"{$selected}>{$option[0]}</option>";
                    }

                    ?>
                </select>
            </td>
        </tr>

        <!-- auth -->
        <tr valign="top">
            <th scope="row"></th>
            <td>
                <label for="lesmtp_auth">
                    <input type="checkbox" id="lesmtp_auth" name="lesmtp[auth]" value="true" <?php echo self::get_option('auth') == 'true' ? 'checked' : '' ?>/>
                    <?php _e('Usar autenticação', 'lesmtp') ?>
                </label>
            </td>
        </tr>

        <!-- user -->
        <tr valign="top">
            <th scope="row">
                <label for="lesmtp_user"><?php _e('Usuário', 'lesmtp') ?></label>
            </th>
            <td>
                <input type="text" id="lesmtp_user" name="lesmtp[user]" value="<?php echo self::get_option('user') ?>"/>
            </td>
        </tr>

        <!-- pass -->
        <tr valign="top">
            <th scope="row">
                <label for="lesmtp_pass"><?php _e('Senha', 'lesmtp') ?></label>
            </th>
            <td>
                <input type="password" id="lesmtp_pass" name="lesmtp[pass]" value="<?php echo self::get_option('pass') ?>"/>
            </td>
        </tr>
    </table>

    <p class="submit">
        <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
    </p>

</form>

<h2><?php _e('Enviar e-mail de teste', 'lesmtp') ?></h2>

<?php if (self::$show_result) : global $phpmailer; ?>
    
    <?php if (empty($phpmailer->ErrorInfo)) : ?>
    <div class="updated">
        <p><?php _e('E-mail de teste enviado!', 'lesmtp') ?></p>
    </div>

    <?php else : ?>
    <div class="error">
        <p><?php _e('Ocorreu um erro ao tentar enviar seu e-mail de teste', 'lesmtp') ?></p>
        <code>
            <?php echo $phpmailer->ErrorInfo ?><br/>
            <?php echo $phpmailer->smtp->error['error'] ?><br/>
            <?php echo $phpmailer->smtp->error['errstr'] ?>
        </code>
    </div>
    <?php endif; ?>

<?php endif; ?>

<form id="lesmtp-test-form" method="post">

    <?php wp_nonce_field('lesmtp-test-form'); ?>

    <table class="form-table">

        <!-- to -->
        <tr valign="top">
            <th scope="row">
                <label for="lesmtp_to"><?php _e('Enviar para', 'lesmtp') ?></label>
            </th>
            <td>
                <?php $user = wp_get_current_user(); ?>
                <input type="text" id="lesmtp_to" name="to" value="<?php echo $user->user_email ?>" class="code" style="width:292px;"/>
            </td>
        </tr>

        <!-- message -->
        <tr valign="top">
            <th scope="row">
                <label for="lesmtp_message"><?php _e('Mensagem', 'lesmtp') ?></label>
            </th>
            <td>
                <textarea id="lesmtp_message" class="code" rows="3" name="message" style="width:292px;"><?php
                    _e('Olá, o envio de e-mails do seu site está funcionando!', 'lesmtp')
                ?></textarea>
            </td>
        </tr>
    </table>

    <p class="submit">
        <input type="submit" class="button" name="lesmtp-test-submit" value="<?php _e('Enviar mensagem de teste', 'lesmtp') ?>" />
    </p>

</form>

</div>