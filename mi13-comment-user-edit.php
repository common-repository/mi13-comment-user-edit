<?php
/*
Plugin Name: mi13 comment user edit
Version:     1.8
Plugin URI:  https://wordpress.org/plugins/mi13-comment-user-edit/
Description: This plugin allows guests to edit their comments on your site.
Author:      mi13
License:     GPL2+
*/

if( !defined( 'ABSPATH') ) exit();

function mi13_comment_user_edit_languages() {
	load_plugin_textdomain( 'mi13-comment-user-edit', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}
add_action( 'plugins_loaded', 'mi13_comment_user_edit_languages' );

function mi13_comment_user_edit_install() {
	$default_settings = array(
		'subject' => 'Подтвердите изменение вашего комментария на сайте SITE_URL', 
		'message' => "Уважаемый(ая) COMMENT_AUTHOR,\nВы или кто-то от вашего имени редактировал ваш комментарий GET_COMMENT_LINK на сайте SITE_URL\n\nВаш комментарии\n----\nCOMMENT_OLD\n----\nбудет изменен на\n----\nCOMMENT_NEW\n----\nДругие данные также могут быть изменены.\n\nДля подтверждения изменении перейдите по ссылке CONFIRM_URL\nДля того чтобы удалить свой комментарии (GET_COMMENT_LINK) с нашего сайта перейдите по ссылке DELETE_URL\n(Ссылки действительны в течений суток.)\n\nСпасибо, что участвуете в обсуждении на нашем сайте!\nЕсли Вы ничего не меняли, приносим извинения за этот email, просто проигнорируйте его.\n\nС уважением, SITE_NAME",
		'user_fields' => '',
		'add_to_comment' => 0,
		'button' => '<input type="button" onclick="ON_CLICK_FUNCTION" value="GET_EDIT_TEXT">'
	);
	add_option('mi13_comment_user_edit', $default_settings);
}
register_activation_hook(__FILE__,'mi13_comment_user_edit_install');

function mi13_comment_user_edit_deactivate() {
	unregister_setting('mi13_comment_user_edit', 'mi13_comment_user_edit');
	delete_option('mi13_comment_user_edit');
}
register_deactivation_hook(__FILE__, 'mi13_comment_user_edit_deactivate');

function mi13_comment_user_edit_scripts() {
	if( is_singular() && comments_open() ) {
		wp_enqueue_script('mi13_comment_user_edit', plugins_url('/js/mi13-comment-user-edit.js',__FILE__), array(),'1',true);
    }
}
add_action('wp_enqueue_scripts', 'mi13_comment_user_edit_scripts');

function mi13_comment_user_edit_menu() {
	$page = add_options_page(
		'mi13 comment user edit',
		'mi13 comment user edit', 
		'manage_options', 
		'mi13_comment_user_edit', 
		'mi13_comment_user_edit_page'
	);
}
add_action('admin_menu', 'mi13_comment_user_edit_menu');

function mi13_comment_user_edit_valid($settings) {
		$default_settings = array(
		'subject' => 'Подтвердите изменение вашего комментария на сайте SITE_URL', 
		'message' => "Уважаемый(ая) COMMENT_AUTHOR,\nВы или кто-то от вашего имени редактировал ваш комментарий GET_COMMENT_LINK на сайте SITE_URL\n\nВаш комментарии\n----\nCOMMENT_OLD\n----\nбудет изменен на\n----\nCOMMENT_NEW\n----\nДругие данные также могут быть изменены.\n\nДля подтверждения изменении перейдите по ссылке CONFIRM_URL\nДля того чтобы удалить свой комментарии (GET_COMMENT_LINK) с нашего сайта перейдите по ссылке DELETE_URL\n(Ссылки действительны в течений суток.)\n\nСпасибо, что участвуете в обсуждении на нашем сайте!\nЕсли Вы ничего не меняли, приносим извинения за этот email, просто проигнорируйте его.\n\nС уважением, SITE_NAME",
		'button' => '<input type="button" onclick="ON_CLICK_FUNCTION" value="GET_EDIT_TEXT">'
	);
	$settings['subject'] = sanitize_text_field($settings['subject']);
	$settings['message'] = sanitize_textarea_field($settings['message']);
	$settings['user_fields'] = sanitize_text_field($settings['user_fields']);
	$settings['add_to_comment'] = isset($settings['add_to_comment']) ? intval($settings['add_to_comment']) : 0;
	$settings['button'] = force_balance_tags($settings['button']);
	if( empty($settings['subject']) ) $settings['subject'] = $default_settings['subject'];
	if( empty($settings['message']) ) $settings['message'] = sanitize_textarea_field($default_settings['message']);
	if( empty($settings['button']) ) $settings['button'] = $default_settings['button'];
	
	return $settings;
}
function mi13_comment_user_edit_init() {
	register_setting( 'mi13_comment_user_edit', 'mi13_comment_user_edit', 'mi13_comment_user_edit_valid' );
}
add_action('admin_init', 'mi13_comment_user_edit_init');	

function mi13_comment_user_edit_page() {
	$settings = get_option('mi13_comment_user_edit');
	?>
    <div class="wrap">
		<h2><?php echo get_admin_page_title(); ?></h2>
		<div style="margin-top:16px;"><span><?php _e( 'This plugin allows guests to edit their comments on your site.', 'mi13-comment-user-edit' ); ?></span></div>
		<form  method="post" action="options.php">
		  <?php settings_fields( 'mi13_comment_user_edit' ); ?>
	    <h2><?php _e('Settings'); ?></h2>
			<table class="form-table"style="background:#ffedc0">
				<tbody>
				<tr>
					<th style="padding-left:8px" scope="row"><?php _e( 'Subject:', 'mi13-comment-user-edit' ); ?></th>
					<td><input type="text" name="mi13_comment_user_edit[subject]" value="<?php echo esc_attr($settings['subject']); ?>" size="100">
					<p  class="description"><?php _e( 'Note: Please use constant', 'mi13-comment-user-edit' ); ?> <strong>SITE_URL</strong>.</p></td>
				</tr>
				<tr>
					<th style="padding-left:8px" scope="row"><?php _e( 'Message:', 'mi13-comment-user-edit' ); ?></th>
					<td><textarea name="mi13_comment_user_edit[message]" rows="10" cols="150"><?php echo esc_textarea($settings['message']); ?></textarea>
					<p  class="description"><?php _e( 'Note: Please use constants', 'mi13-comment-user-edit' ); ?> <strong>COMMENT_AUTHOR, GET_COMMENT_LINK, SITE_URL, COMMENT_OLD, COMMENT_NEW, CONFIRM_URL, DELETE_URL, SITE_NAME</strong>.</p></td>
				</tr>
				<tr>
					<th style="padding-left:8px" scope="row"><?php _e( 'html code for button:', 'mi13-comment-user-edit' ); ?></th>
					<td><input type="text" name="mi13_comment_user_edit[button]" value="<?php echo esc_attr($settings['button']); ?>" size="100">
					<p  class="description"><?php _e( 'Note: Please use constants', 'mi13-comment-user-edit' ); ?> <strong>ON_CLICK_FUNCTION, GET_EDIT_TEXT</strong>.</p></td>
				</tr>
				<tr>
					<th style="padding-left:8px" scope="row"><?php _e( 'Automatically add a button to the body of comment', 'mi13-comment-user-edit' ); ?></th>
					<td><input type="checkbox" name="mi13_comment_user_edit[add_to_comment]" value="1" <?php checked(1,$settings['add_to_comment']); ?> ></td>
				</tr>
				<tr>
					<th style="padding-left:8px" scope="row">Code Snippets:</th>
					<td><p  class="description"><strong>&lt;?php if( function_exists( 'mi13_comment_user_edit_button' ) ) echo mi13_comment_user_edit_button( get_comment_ID() ); ?&gt;</strong><br>/The second parameter is optional (your comment_content); Default empty./<br><strong>&lt;?php add_filter('mi13_comment_user_edit_button_filter', 'your_filter'); ?&gt;</strong><br>/Parameter $button/</p></td>
				</tr>
				<tr>
					<th style="padding-left:8px" scope="row"><?php _e( 'Your custom fields:', 'mi13-comment-user-edit' ); ?></th>
					<td><input type="text" name="mi13_comment_user_edit[user_fields]" placeholder="phone,sity" value="<?php echo esc_attr($settings['user_fields']); ?>" size="100">
					<p  class="description"><?php _e( 'Note: Please, separate words with commas.', 'mi13-comment-user-edit' ); ?></p></td>
				</tr>
				<tr>
					<th style="padding-left:8px" scope="row"></th>
					<td><p  class="description"><strong>&lt;!--mi13-comment-user-edit-not-edit--&gt;</strong></p>
					<p  class="description"><?php _e( 'Please, add this comment to the comment text to prohibit editing.', 'mi13-comment-user-edit' ); ?></p>
					</td>
				</tr>
				 </tbody>
				</table>
				<?php submit_button(); ?> 
	    </form>
	</div>
	<?php
}

add_action( 'comment_form_logged_in_after', 'mi13_comment_user_edit_field' );
add_action( 'comment_form_after_fields', 'mi13_comment_user_edit_field' );
function mi13_comment_user_edit_field($fields) {
	echo '<input id="mi13_comment_user_edit_id" name="mi13_comment_user_edit_id" type="hidden" value="" />';
}

function mi13_comment_user_edit_button($comment_id, $comment_content='') {
	$button = '';
	if( is_singular() ) {
		$comment = get_comment($comment_id);
		if( $comment ) {
			if( empty($comment_content) ) {
				$comment_content = $comment->comment_content;
			}
			if( $comment->comment_author_email && ( $comment->comment_type == 'comment' ) && ( $comment->comment_approved == '1' ) && ( strpos( $comment_content, '<!--mi13-comment-user-edit-not-edit-->' ) === false ) ) {
				$users = get_users( array( 'role'   => 'administrator', 'fields' => ['user_email'] ) );
				$emails = wp_list_pluck( $users, 'user_email' );
				if( in_array( $comment->comment_author_email, $emails ) == false ) {
					$button = get_option('mi13_comment_user_edit')['button'];
					$on_click_function = 'mi13commentedit(\'' . esc_js( __('Edit') ) . '\', \'' . esc_js($comment_id) . '\', \'' . esc_js($comment_content) . '\');';
					$button = str_replace( 'ON_CLICK_FUNCTION', $on_click_function, $button );
					$button = str_replace( 'GET_EDIT_TEXT', __('Edit'), $button );
					$button = apply_filters( 'mi13_comment_user_edit_button_filter', $button);
				}
			}
		}
	}
	return $button;
}

add_filter( 'comment_text', 'mi13_comment_user_edit_comment_text_filter', 99, 1 );
function mi13_comment_user_edit_comment_text_filter( $comment_text ) {
	if( get_option('mi13_comment_user_edit')['add_to_comment'] ) {
		$comment_text .= mi13_comment_user_edit_button( get_comment_ID() );
	}
	return $comment_text;
}

add_filter( 'preprocess_comment', 'mi13_comment_user_edit_preprocess_comment', 99);
function mi13_comment_user_edit_preprocess_comment($comment) {
	if( isset( $_POST['mi13_comment_user_edit_id'] ) && !empty( $_POST['mi13_comment_user_edit_id'] ) ) {
		$id = intval( sanitize_text_field( $_POST['mi13_comment_user_edit_id'] ) );
		$old_comment = get_comment( $id );
		if( $old_comment && $old_comment->comment_author_email && ( $old_comment->comment_type == 'comment' ) && ( $old_comment->comment_approved == '1' ) && ( strpos( $old_comment->comment_content, '<!--mi13-comment-user-edit-not-edit-->' ) === false ) ) {
			$author_email_old = $old_comment->comment_author_email;
			$users = get_users( array( 'role'   => 'administrator', 'fields' => ['user_email'] ) );
			$emails = wp_list_pluck( $users, 'user_email' );
			if( in_array( $author_email_old, $emails ) == false ) {
				$author_email_new = $comment['comment_author_email'];
				$back = '<p style="text-align: center;"><input type="button" onclick="history.back();" value="'. __( '&laquo; Back' ) . '"/></p>';
				if( $author_email_new  <> $author_email_old ) {
					//wp_die( '<p style="text-align: center;">' . __('Invalid email address in request.') . '</p>' . $back );
				} else {
					$settings = get_option('mi13_comment_user_edit');
					
					$to_save = array();
					$to_save['code'] = wp_generate_password( 4, false );
					$to_save['comment']['comment_content'] = wp_kses( wp_unslash($comment['comment_content']), 'post' );
					$to_save['comment']['comment_author'] = sanitize_text_field($comment['comment_author']);
					
					$ajax_url = admin_url('admin-ajax.php') . '?action=mi13_comment_user_edit&id=' . $id . '&code=' . $to_save['code'] . '&delete=false';
					$delete_ajax_url = admin_url('admin-ajax.php') . '?action=mi13_comment_user_edit&id=' . $id . '&code=' . $to_save['code'] . '&delete=true';
					$site_url = get_bloginfo('url');
					$site_name = get_bloginfo('name');
					$site_email = get_bloginfo('admin_email');
					
					$subject = str_replace( 'SITE_URL', $site_url, $settings['subject'] );
					$message = $settings['message'];
					$message = str_replace( 'COMMENT_AUTHOR', $to_save['comment']['comment_author'], $message );
					$message = str_replace( 'GET_COMMENT_LINK', get_comment_link($id), $message );
					$message = str_replace( 'SITE_URL', $site_url, $message );
					$message = str_replace( 'COMMENT_OLD', $old_comment->comment_content, $message );
					$message = str_replace( 'COMMENT_NEW', $to_save['comment']['comment_content'], $message );
					$message = str_replace( 'CONFIRM_URL', $ajax_url, $message );
					$message = str_replace( 'DELETE_URL', $delete_ajax_url, $message );
					$message = str_replace( 'SITE_NAME', $site_name, $message );
					$headers = "From: $site_url <$site_email>";
					if(wp_mail( $author_email_new, $subject, $message, $headers)) {
						$user_fields = $settings['user_fields'];
						if( $user_fields ) {
							$user_fields_array = explode(',',$user_fields);
							foreach( $user_fields_array as $user_field){
								if ( isset( $_POST[$user_field] ) ) {
									$to_save['comment']['comment_meta'][$user_field] = sanitize_text_field($_POST[$user_field]);
								}
							}
						}
						
						$to_save['comment']['comment_author_url'] = sanitize_text_field($comment['comment_author_url']);
						$to_save['comment']['comment_author_IP'] = sanitize_text_field($comment['comment_author_IP']);
						$to_save['comment']['comment_agent'] = sanitize_text_field($comment['comment_agent']);
						
						set_transient( 'mi13_comment_user_edit_' . $id, $to_save, DAY_IN_SECONDS );
					} else {
						wp_die( '<p style="text-align: center;">' . __( 'Error: it was not possible to send an email to confirm the changes! Try again later!', 'mi13-comment-user-edit' ) . '</p>' . $back, 'Success', 200 );
					}
				}
				
				wp_die( '<p style="text-align: center;">' . __( 'Thanks. Please check your mailbox for a link to confirm your action.', 'mi13-comment-user-edit' ) . '</p>' . $back, 'Success', 200 ); // Используем обман, чтобы обеспечить конфиденциальность и ввести в заблуждение постороннего юзера.
				
			} else {
				wp_die('Access Denied!');
			}
		} else {
			wp_die('Access Denied!');
		}
	}
	return $comment;
}

add_action('wp_ajax_mi13_comment_user_edit', 'mi13_comment_user_edit');
add_action('wp_ajax_nopriv_mi13_comment_user_edit', 'mi13_comment_user_edit');
function mi13_comment_user_edit() {
	if( isset($_GET['code'],$_GET['id'],$_GET['delete']) ) {
		$code = sanitize_text_field( $_GET['code'] );
		$id = intval(sanitize_text_field($_GET['id']) );
		$delete = sanitize_text_field( $_GET['delete'] );
		$err = false;
		if( ($id>0) && (strlen($code)===4) ) {
			$mes = '';
			$home = '<p style="text-align: center;"><a href="' . get_home_url() . '">' . __('Home') . '</a></p>';
			$transient = get_transient('mi13_comment_user_edit_' . $id);
			if($transient && ($transient['code'] == $code)) {
				delete_transient('mi13_comment_user_edit_' . $id);
				$comment = get_comment( $id );
				if ( !$comment ) {
					$mes .= __( 'Error: there is no such comment on the site!', 'mi13-comment-user-edit' );
					$err = true;
				} elseif( $delete == 'true' ) {
					$crash = wp_delete_comment( $id );
					if( $crash ) {
						$mes .= __( 'The comment has been deleted!', 'mi13-comment-user-edit' );
					} else {
						$mes .= __( 'Error: couldn\'t delete comment!', 'mi13-comment-user-edit' );
						$err = true;
					}
				} elseif( $delete == 'false' ) {
						$transient['comment']['comment_ID'] = $id;
						$transient['comment']['comment_approved'] = 0;
						$transient['comment']['comment_date'] = current_time('mysql');
						wp_update_comment( $transient['comment'] );
						wp_notify_moderator( $id );
						$mes .= __( 'Your comment is awaiting moderation.' );
				} else {
					$mes .= 'Access Denied!';
					$err = true;
				}
			} else {
				$mes .= __( 'Error: this link is outdated!', 'mi13-comment-user-edit' );
				$err = true;
			}
			if( $err ) {
				wp_die('<p style="text-align: center;">' . $mes . '</p>' . $home);
			} else {
				wp_die('<p style="text-align: center;">' . $mes . '</p>' . $home, 'Success', 200);
			}
		}
	}
	wp_die('Access Denied!');	
}

?>