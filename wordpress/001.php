<?php
// FUNZIONI:
// *F001* filtro per permettere il redirect
//allow redirection, even if your theme starts to send output to the browser
add_action('init', 'clean_output_buffer');
function clean_output_buffer() {
    ob_start();
}

//*************************************
// *F002* filtro per offuscare il title e il titolo di pagina e non visualizzare
//il nome del profilo se questo non è un operatore con capability SAL
add_filter('wp_title', 'new_title', 10, 2);
function new_title($title, $id) {
    if (!current_user_can( 'sal_viewer' ) ) {
        if ( is_singular( 'salplesk_profili' ) )
            $title = 'Profilo ' . get_the_ID();
    }
    return $title;
}

//*************************************
// *F003* rimuovo il pannello amministrazione se sono un profilo azienda o privato
add_action('after_setup_theme', 'remove_admin_bar');
function remove_admin_bar() {

    $user_info = get_userdata(get_current_user_id());
    $identita = $user_info->roles[0];

    if ($identita == 'utente_azienda' || $identita == 'utente_profilo') {
        show_admin_bar(false);
    }
}

//*************************************
// *F004* limito l'accesso al backend ai soli amministratori e operatori SAL
add_action( 'init', 'blockusers_init' );
function blockusers_init() {
    //recupero dati utente corrente
    $user_info = get_userdata(get_current_user_id());
    $username = $user_info->data->user_login;
    $identita = $user_info->roles[0];

    if(is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX )) {
        if ($identita == 'utente_azienda' || $identita == 'utente_profilo') {
            wp_redirect( home_url() );
        }
    }
}