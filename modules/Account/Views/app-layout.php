<?php
/**
 * Custom App Layout for Logged-In WooCommerce Account Dashboard
 * This template omits get_header() and get_footer() to create a standalone app feel.
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php wp_title('|', true, 'right'); ?></title>
    <?php wp_head(); ?>
    <style>
        body.aa-app-layout {
            margin: 0;
            padding: 0;
            background: #f3f4f6;
            overflow-x: hidden;
        }
        /* Reset any theme margin/padding on the main element */
        .aa-app-main-wrapper {
            width: 100%;
            min-height: 100vh;
        }
    </style>
</head>
<body <?php body_class('aa-app-layout'); ?>>

<div class="aa-app-main-wrapper">
    <?php
    if ( have_posts() ) {
        while ( have_posts() ) {
            the_post();
            the_content();
        }
    }
    ?>
</div>

<?php wp_footer(); ?>
</body>
</html>
