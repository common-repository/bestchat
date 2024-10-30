<div class="bestchat-wrap">
    <div class="bestchat-logo"></div>
    <div class="bestchat-actions">
        <?php
        if (isset($_GET["bestchat_website_cid"])) {
            ?>
            <h2 class="bestchat-title"
                data-icon="success"><?php _e("bestchat is now added to your site!", "bestchat"); ?></h2>

            <p class="bestchat-subtitle"><?php _e("We automatically added bestchat on your site. You can browse on your homepage. You should see bestchat 🎉", "bestchat"); ?></p>
            <?php
        } else {
            ?>
            <h2 class="bestchat-title"><?php _e("Welcome to your bestchat Integration", "bestchat"); ?></h2>

            <p class="bestchat-subtitle"><?php _e("bestchat is currently added to your site and you can receive chat requests from your website visitors.", "bestchat"); ?></p>
            <?php
        }
        ?>

        <a class="bestchat-button bestchat-button-green u-mb20" href="<?php echo esc_url($add_to_bestchat_link) ?>"
           target="_blank"> <?php _e("Open bestchat Inbox", "bestchat"); ?></a>

        <p class="bestchat-notice"><?php _e("Loving bestchat <b style='color:red'>♥</b> ? Rate us on the <a target='_blank' href='https://wordpress.org/support/plugin/bestchat/reviews/?filter=5'>WordPress Plugin Directory</a>", "bestchat"); ?></p>
    </div>

    <div class="bestchat-side">
        <div class="bestchat-side-illustration"></div>

        <?php
        if (isset($_GET["bestchat_website_cid"])) {
            ?>
            <div class="pyro">
                <div class="before"></div>
                <div class="after"></div>
            </div>
            <?php
        }
        ?>
    </div>
</div>