<?php
// *F001* clean_output_buffer() - filtro per permettere il redirect
// *F002* hide_title($title, $id) - filtro per offuscare il title e il titolo di pagina (pagine singole) a meno di non avere la capability corretta
// *F003* remove_admin_bar() - rimuovo il pannello amministrazione se l'utente appartiene ad uno specifico ruolo
// *F004* blockusers_init() - limito l'accesso al backend ai soli amministratori
// *F005* getRoleNameofCurrentUser() - recupero dati utente corrente


// FUNZIONI:
// *F001* filtro per permettere il redirect
// #redirect #301
add_action('init', 'clean_output_buffer');
function clean_output_buffer() {
    ob_start();
}

// *F002* filtro per offuscare il title e il titolo di pagina (pagine singole) a meno di non avere la capability corretta
// #hide #title
add_filter('wp_title', 'hide_title', 10, 2);
function hide_title($title, $id) {
    if (!current_user_can( 'edit_posts' ) ) {
        if ( is_singular() )
            $title = 'Titolo post: ' . get_the_ID();
    }
    return $title;
}

// *F003* rimuovo il pannello amministrazione se l'utente appartiene ad uno specifico ruolo
// #remove #admin_bar
add_action('after_setup_theme', 'remove_admin_bar');
function remove_admin_bar() {
    $user_info = get_userdata(get_current_user_id());
    $identita = $user_info->roles[0];
    if ($identita == 'custom_role') {
        show_admin_bar(false);
    }
}

// *F004* limito l'accesso al backend ai soli amministratori
// #block #admin #sequrity #users
add_action( 'init', 'blockusers_init' );
function blockusers_init() {
    //ma permetto l'accesso per usare l'uploader ajax in frontend
    //REF: https://facetwp.com/is_admin-and-ajax-in-wordpress/
    if(is_admin() && ! current_user_can( 'administrator' )&& ! defined( 'DOING_AJAX' ) || ! DOING_AJAX )) {
		wp_redirect( home_url() );
		exit;
    }
}

// *F005* recupero dati utente corrente
// #users
function getRoleNameofCurrentUser() {
	$user_info = get_userdata( get_current_user_id() );
	$username  = $user_info->data->user_login;
	return $user_info->roles[0];
}