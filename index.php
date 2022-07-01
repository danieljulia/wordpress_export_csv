<?php
/*
Plugin Name: Export post types and fields as CSV
Description: Sample exporting data to csv that must be changed to your use case
Version: 0.3
Author: Daniel Julià
Author URI: http://www.pimpampum.net
License: GPL2
*/


/**
define urls for each entry point
*/

add_action('template_redirect','export_csv_template_redirect');


function export_csv_template_redirect() {

  if(strpos($_SERVER["REQUEST_URI"], 'download/users.csv')){
    show_csv_users(true);
    exit();
  }


  if(strpos($_SERVER["REQUEST_URI"], 'download/spaces.csv')){
    show_csv_spaces(true);
    exit();
  }

}


/**
Add menu options in admin
*/

add_action( 'admin_menu', 'export_csv_admin_menu' );

function export_csv_admin_menu() {
	add_menu_page( 'Export csv', 'Export csv', 'manage_options', 'export_csv_custom', 'export_csv_admin_page', 'dashicons-database-export', 16  );
}

function export_csv_admin_page(){

  //
  if(isset($_GET['download'])){

    show_csv();
    exit();
  }
	?>
	<div class="wrap">
		<h2>Export csv data</h2>
    <ul>
    <li><a href="<?php echo home_url('download/users.csv')?>">Download users</a></li>
    <li><a href="<?php echo home_url('download/spaces.csv')?>">Download spaces</a></li>
</ul>
  </div>
	<?php
}

//download=false display results instead of downloading

function show_csv_users($download){

  //  $download=false;

    if($download)
    header("Content-type: application/x-msdownload",true,200); //uf , ha costat
    //header('Content-type: application/octet-stream');
    //header('Content-Type: text/csv; charset= utf-8');
    //header("Content-type: application/vnd.ms-excel");
	   //header( "Content-Disposition: attachment;filename=\"$file\"" );
    if($download)
    header("Content-disposition: attachment; filename=" . date("d-m-Y") . "_users.csv");
  	header( "Pragma: no-cache" );
  	header( "Expires: 0" );
  	$csv = fopen('php://output', 'w');
  	$done = false;

  // define columns in the csv
  $cols=array("Name","Login","Nicename","Email","Registered");

  fputcsv( $csv, $cols );

  $args = array(
    'number' => -1,
  );
  $user_query = new WP_User_Query( $args );
  if ( ! empty( $user_query->get_results() ) ) {
  	foreach ( $user_query->get_results() as $user ) {

    	$values = array();

      //get_user_meta( $user_id, 'member_zip_code', true );
      //print_r($user);
      $values[]=$user->display_name ;
      $values[]=$user->user_login ;
      $values[]=$user->user_nicename ;
      $values[]=$user->user_email ;
      $values[]=$user->user_registered ;

      fputcsv( $csv, $values );

    }

  }
  wp_reset_query();

  // Download it
  fclose( $csv );
  exit();

}

function show_csv_spaces($download){

    if($download)
  header("Content-type: application/x-msdownload",true,200); //uf , ha costat
    if($download)
  header("Content-disposition: attachment; filename=" . date("d-m-Y") . "_spaces.csv");
	header( "Pragma: no-cache" );
	header( "Expires: 0" );
	$csv = fopen('php://output', 'w');
	$done = false;

$cols=array("Name","Course","Theme","School_es","School_in","Space Code");

fputcsv( $csv, $cols );

    $args = array(
        'post_type' => 'espacio',
        'posts_per_page' => -1
    );

    $my_query = new WP_Query( $args );

    if ( $my_query->have_posts() ) {
        while ( $my_query->have_posts() ) {
          $my_query->the_post();
          global $post;
          $values = array();
        //  print_r($post);
          $values[]=$post->post_title;
            $curso=get_the_terms(get_the_ID(),"edicion");

          $values[]=$curso[0]->name;
          $values[]=get_post_meta(get_the_ID(),"ppp_tema",true);
          $se=get_post_meta(get_the_ID(),"ppp_school_es",false);

          $values[]=$se[0]['name'];

          $in=get_the_terms(get_the_ID(),"escuela_in");

          $values[]=$in[0]->name;
          $values[]=get_post_meta(get_the_ID(),"ppp_code",true);
          fputcsv( $csv, $values );
      }
    }


// Download it
fclose( $csv );
exit();


}
