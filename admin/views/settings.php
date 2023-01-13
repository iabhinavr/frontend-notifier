<div class="wrap">
    <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

    <form action="<?php echo esc_html( admin_url( 'admin-post.php' ) ); ?>" method="post">
        <div id="update-data-container">
            <h2>List of new and updated posts</h2>

            <div class="options">
                <p>
                    <textarea name="all-posts" id="msg-1" class="large-text code" rows="10"><?php echo esc_attr( json_encode($this->update_data) ); ?></textarea>
                </p>
                <p>
                    <input type="text" class="large-text code" value="<?php echo esc_attr( json_encode($this->current)); ?>">
                </p>
            </div>
        </div>
        <input type="hidden" name="action" value="send_notification">
        <?php
            wp_nonce_field( 'send_notification', 'notification_nonce' );
            submit_button('Revalidate');
        ?>
    </form>

    <form action="<?php echo esc_html(admin_url( 'admin-post.php' ) ); ?>" method="post">
        <input type="hidden" name="action" value="revalidate_home">
        <?php
            wp_nonce_field( 'revalidate_home', 'revalidate_home_nonce');
            submit_button('Revalidate Blog Home')
        ?>
    </form>

    <form action="<?php echo esc_html( admin_url( 'admin-post.php' ) ); ?>" method="post">
        <div id="slug-data-container">
            <h2>List of Post Slugs</h2>
            <div class="options">
                <p>
                    <textarea name="post-slugs" id="slugs-1" class="large-text code" rows="10"><?php echo esc_attr( json_encode($this->slug_list) ); ?></textarea>
                </p>
            </div>
        </div>
        <input type="hidden" name="action" value="fetch_post_slugs">
        <?php
            wp_nonce_field( 'fetch_post_slugs', 'slug_nonce' );
            submit_button('Fetch Post Slugs');
        ?>
    </form>
</div><!--wrap-->


