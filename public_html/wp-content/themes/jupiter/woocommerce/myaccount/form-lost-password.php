<?php
/**
 * Lost password form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/form-lost-password.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     http://docs.woothemes.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 2.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

?>

<?php wc_print_notices(); ?>

<form method="post" class="woocommerce-ResetPassword lost_reset_password">

    <?php if( 'lost_password' === $args['form'] ) : ?>

        <p><?php echo apply_filters( 'woocommerce_lost_password_message', __( 'Lost your password? Please enter your username or email address. You will receive a link to create a new password via email.', 'mk_framework' ) ); ?></p>

        <p class="woocommerce-FormRow woocommerce-FormRow--first form-row form-row-first"><label for="user_login"><?php _e( 'Username or email', 'mk_framework' ); ?></label> <input class="woocommerce-Input woocommerce-Input--text input-text" type="text" name="user_login" id="user_login" /></p>

    <?php else : ?>

        <p><?php echo apply_filters( 'woocommerce_reset_password_message', __( 'Enter a new password below.', 'mk_framework') ); ?></p>

        <p class="woocommerce-FormRow woocommerce-FormRow--first form-row form-row-first">
            <label for="password_1"><?php _e( 'New password', 'mk_framework' ); ?> <span class="required">*</span></label>
            <input type="password" class="woocommerce-Input woocommerce-Input--text input-text" name="password_1" id="password_1" />
        </p>
        <p class="woocommerce-FormRow woocommerce-FormRow--last form-row form-row-last">
            <label for="password_2"><?php _e( 'Re-enter new password', 'mk_framework' ); ?> <span class="required">*</span></label>
            <input type="password" class="woocommerce-Input woocommerce-Input--text input-text" name="password_2" id="password_2" />
        </p>

        <input type="hidden" name="reset_key" value="<?php echo isset( $args['key'] ) ? $args['key'] : ''; ?>" />
        <input type="hidden" name="reset_login" value="<?php echo isset( $args['login'] ) ? $args['login'] : ''; ?>" />

    <?php endif; ?>

    <div class="clear"></div>

    <?php do_action( 'woocommerce_lostpassword_form' ); ?>

    <p class="woocommerce-FormRow form-row">
        <input type="hidden" name="wc_reset_password" value="true" />
        <input type="submit" class="woocommerce-Button shop-flat-btn shop-skin-btn" value="<?php echo 'lost_password' === $args['form'] ? __( 'Reset Password', 'mk_framework' ) : __( 'Save', 'mk_framework' ); ?>" />
    </p>

    <?php wp_nonce_field( $args['form'] ); ?>

</form>
