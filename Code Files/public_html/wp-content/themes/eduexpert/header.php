<?php
/**
 * The header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="content">
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
  <head>
    <meta charset="<?php bloginfo( 'charset' ); ?>" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    
    <!-- Global site tag (gtag.js) - Google Analytics -->
	<script async src="https://www.googletagmanager.com/gtag/js?id=UA-168214921-1"></script>
	<script>
  	window.dataLayer = window.dataLayer || [];
  	function gtag(){dataLayer.push(arguments);}
  	gtag('js', new Date());

  	gtag('config', 'UA-168214921-1');
	</script>


	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
	
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->

	<?php wp_head(); ?>
  </head>

  <body <?php body_class(); ?>>
  
  	<?php 
	if ( function_exists( 'eduexpert_contact_info' ) ) {
		eduexpert_contact_info(); 
	}?>
	
    <header id="masthead"  class="site-header  float-header" role="banner">
		<div class="head-wrap banner-background">
			<div class="container">
				<div class="row">
					<div class="col-md-4 col-sm-6 col-xs-12">
						<?php if ( get_theme_mod('custom_logo') ) : 
							$custom_logo_id = get_theme_mod( 'custom_logo' );
							$logo_src = wp_get_attachment_image_src( $custom_logo_id , 'full' );?>
							<a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr ( bloginfo('name') ); ?>"><img class="site-logo" src="<?php echo esc_url( $logo_src[0] ); ?>" /></a>
						<?php else : ?>
							<h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php esc_html( bloginfo( 'name' ) ); ?></a></h1>
							<h5 class="site-description"><?php esc_html( bloginfo( 'description' ) ); ?></h5>	        
						<?php endif; ?>
					</div>
					<div class="col-md-8 col-sm-6 col-xs-12">
						<div class="btn-menu"></div>
						<nav id="site-navigation" class="site-navigation" role="navigation">
							<?php wp_nav_menu( array( 'theme_location' => 'primary' ) ); ?>
						</nav><!-- #site-navigation -->
					</div>
				</div>
			</div>
		</div>
    </header>
	
	<div class="eduexpert-banner-area">
		<?php if ( !empty (get_theme_mod('banner_overlay')) ) : ?>
					<div class="banner-overlay"></div>
				<?php endif; ?>
		<?php if( is_front_page() ) :
		
				$banner_type = get_theme_mod('banner_type', 'image');
				if ( $banner_type == 'video' && is_front_page() ) :
					eduexpert_banner_video();
				elseif ( $banner_type == 'carousel' && is_front_page() ) :
					eduexpert_banner_slider();
				else :
					eduexpert_banner_background();
				endif;
			else : ?>
			<div class="header-background other-header">
				<?php if ( !empty (get_theme_mod('banner_bg_overlay')) ) : ?>
					<div class="other-banner-overlay"></div>
				<?php endif; ?>
				<div class="header-content other">
					<?php if(function_exists('is_shop')) : ?>
						<h3 class="title-post entry-title">RGOAL FOUNDATION</h3>
						<!--<img src="/domains/rgoal.in/public_html/img/logo_2_." alt="logo" width="50" height="60">-->
					<?php else : ?>
						<h3 class="title-post entry-title">RGOAL FOUNDATION</h3>
					<?php endif; ?>
					<!--<hr class="divider-separator"/>-->
					
				</div>
			</div>
		<?php endif; ?>
	</div>
	
	<div id="content" class="page-wrap">
		<div class="content-wrapper">
			<div class="container">