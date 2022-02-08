<?php
get_header();
wp_enqueue_style("lwp-grid");
?>

<!-- Google Rich Snippet -->
<script type="application/ld+json">
    {
        "@context": "https://schema.org/",
        "@type": "Product",
        "name": "<?php the_title(); ?>",
        "image": [
            "https://example.com/photos/1x1/photo.jpg",
            "https://example.com/photos/4x3/photo.jpg",
            "https://example.com/photos/16x9/photo.jpg"
        ],
        "description": "Example product is the best example product out there. Make sure to get the one and only -- the original.",
        "sku": "0374984678",
        "mpn": "738930",
        "brand": {
            "@type": "Brand",
            "name": "Example"
        },
        "review": {
            "@type": "Review",
            "reviewRating": {
                "@type": "Rating",
                "ratingValue": "4",
                "bestRating": "5"
            },
            "author": {
                "@type": "Person",
                "name": "Hank Williams"
            }
        },
        "aggregateRating": {
            "@type": "AggregateRating",
            "ratingValue": "4.7",
            "reviewCount": "1455"
        },
        "offers": {
            "@type": "Offer",
            "url": "https://example.com/product",
            "priceCurrency": "USD",
            "price": "49.99",
            "priceValidUntil": "2021-11-20",
            "itemCondition": "https://schema.org/NewCondition",
            "availability": "https://schema.org/InStock"
        }
    }
</script>

<div class="lwpcommerce lwp-container">
    <?php require_once LWPC_PATH . 'src/templates/component/navigation.php'; ?>

    <div class="lwp-product row">
        <div class="col-xs-12 col-sm-12">
            <?php the_post_thumbnail(); ?>
        </div>
        <div class="col-xs-12 col-sm-12 row gutter" style="margin-top:8px;">
            <div class="col-xs-9">
                <?php the_title(); ?>
                <?php lwpc_get_price_html(); ?>
            </div>
            <div class="col-xs-3 end-sm">
                <?php lwpc_add_to_cart_html(); ?>
                <?php lwpc_get_stock_html(); ?>
            </div>
        </div>
        <div class="col-sm-12 gutter">
            <?php the_content(); ?>
        </div>
    </div>
</div>

<style>
    .lwp-container {
        /* max-width: 960px; */
        max-width: 420px;
        margin: 0 auto;
    }
</style>

<?php get_footer(); ?>