<?php

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct script access denied.' );
}

get_header();

?>

<div id="content" <?php Avada()->layout->add_class( 'content_class' ); ?> <?php Avada()->layout->add_style( 'content_style' ); ?>>

    <?php

    function alphaSort($a, $b) {
        return strcmp($a['label'], $b['label']);
    }

    function printStores($s) {

        $cur_country = "";

        foreach($s as $store) {

            if ($cur_country != $store["label"]) {
                $store_country_letter = strtolower( substr( $store["label"], 0, 1 ) );
                if ($store["country"] != "US") {
                    echo "<h3 class='store-country-header' id=l" . $store_country_letter . ">" . $store["label"] . "</h3>";
                    $cur_country = $store["label"];
                }
            }

            ?>
            <div class="store-item">
                <a class="store-item-content" href="<?php echo $store["url"]; ?>"><img src="<?php echo $store["image"]; ?>" /></a>
                <!--<div class="store-item-meta">
                    <a href="<?php echo $store["url"]; ?>"><?php echo $store["title"] ?></a>
                    <?php
                        if ($store["locator"] != "") {
                            echo "<a href='<?php" . $store["locator"] . "'>Store Locator</a>";
                        }
                    ?>
                </div>-->
            </div>
            <?php
        }
    }

    function printActiveStoreLinkBar($s) {

        $cur_country = "";

        $return_links = "<div class='store-linkbar-wrap'>";
        $return_links .= "<div class='store-linkbar-text'>Jump to countries that start with...</div>";
        $return_links .= "<ul class='store-linkbar'>";


        $found = false;
        $letters = array("a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z");

        foreach ($letters as $l) {

            $found = false;
            foreach($s as $store) {
                $store_country_letter = strtolower( substr( $store["label"], 0, 1 ) );
                if ($store_country_letter == $l) {
                    $found = true;
                }
            }

            if ($found == true) {
                $return_links .= "<li><a href='#l" . $l . "'>" . $l . "</a></li>";
            } else {
                $return_links .= "<li>" . $l . "</li>";
            }

        }

        $return_links .= "</ul></div>";
        return $return_links;
    }


    $query = new WP_Query( array(
        'post_type' => 'store',
        'posts_per_page' => -1,
        'order' => 'ASC',
        'meta_key' => '_dpsto_store_country',
        'orderby' => 'meta_value _dpsto_store_country'
    ));

    $store_array = [];
    $store_array_us = [];

    $countries = include( dirname(__FILE__) . "/../../plugins/dpsto/admin/countries.php");  //hackish

    if ( $query->have_posts() ) : while ( $query->have_posts() ) : $query->the_post();

        $post_id = get_the_ID();

        //////
        // Collect the data
        //
        $store_title = get_the_title($post_id);
        $store_image = wp_get_attachment_image_url( get_post_thumbnail_id($post_id), "full" );
        $store_country = get_post_meta( $post_id, '_dpsto_store_country', true );
        $store_url = get_post_meta( $post_id, '_dpsto_store_url', true );
        $store_locator = get_post_meta( $post_id, '_dpsto_store_locator_url', true );
        $store_country_label = isset( $countries[ $store_country ] ) ? $countries[ $store_country ] : $store_country;

        if ($store_country == "US") {
            $store_array_us[] = array(
                'title' => $store_title,
                'image' => $store_image,
                'country' => $store_country,
                'url' => $store_url,
                'locator' => $store_locator,
                'label' => $store_country_label
            );
        } else {
            $store_array[] = array(
                'title' => $store_title,
                'image' => $store_image,
                'country' => $store_country,
                'url' => $store_url,
                'locator' => $store_locator,
                'label' => $store_country_label
            );
        }

    endwhile; endif;

    usort($store_array, "alphaSort");

    echo '<h1 class="dpsto-title">Stores</h1>';
    echo '<div class="dpsto-subtitle">In addition to our online store, 3Doodler pens, plastic and accessories can now be found at <b>' . count($store_array_us) . '</b> <a href="#us">US retailers</a> and <b>' . count($store_array) . '</b> <a href="#int">worldwide</a>. If you\'re interested in being a 3Doodler reseller <a href="mailto:sales@the3doodler.com">send us an email</a>.</div>';

    echo "<h1 id='us' class='store-section-header store-section-us'><img src='/wp-content/uploads/2017/03/us_flag.png' /> United States</h1>";
    printStores($store_array_us);

    echo "<div class='clearfix'></div>";
    echo "<h1 id='int' class='store-section-header store-section-int'><img src='/wp-content/uploads/2017/04/international.png' /> International</h1>";

    echo printActiveStoreLinkBar($store_array);

    printStores($store_array);

    ?>

</div> <!-- content -->


<?php do_action( 'avada_after_content' ); ?>
<?php get_footer();

/* Omit closing PHP tag to avoid "Headers already sent" issues. */
