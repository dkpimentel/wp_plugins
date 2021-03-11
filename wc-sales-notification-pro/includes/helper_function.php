<?php
/*
 * Plugisn Options value
 */
function wcsales_get_option_pro( $option, $section, $default = '' ){
    $options = get_option( $section );
    if ( isset( $options[$option] ) ) {
        return $options[$option];
    }
    return $default;
}

/*
 * Plugisn API Data Fetch
 */
function wcsales_get_org_plugins_pro( $author = 'htplugins' ) {
    
    $plcachekey = 'hastech_plugins';
    $plugins_data = get_transient( $plcachekey );

    if ( !$plugins_data ) {

        $args    = (object) array(
            'author'   => $author,
            'per_page' => '120',
            'page'     => '1',
            'fields'   => array( 'slug', 'name', 'version', 'downloaded', 'active_installs' )
        );
        $request = array( 'action' => 'query_plugins', 'timeout' => 15, 'request' => serialize( $args ) );

        //https://codex.wordpress.org/WordPress.org_API
        $url = 'http://api.wordpress.org/plugins/info/1.0/';
        $response = wp_remote_post( $url, array( 'body' => $request ) );
        if ( ! is_wp_error( $response ) ) {
            $plugins_data = array();
            $plugins  = unserialize( $response['body'] );
            if ( isset( $plugins->plugins ) && ( count( $plugins->plugins ) > 0 ) ) {
                foreach ( $plugins->plugins as $pl_info ) {
                    $plugins_data[] = array(
                        'slug'            => $pl_info->slug,
                        'name'            => $pl_info->name,
                        'version'         => $pl_info->version,
                        'downloaded'      => $pl_info->downloaded,
                        'active_installs' => $pl_info->active_installs
                    );
                }
            }
            set_transient( $plcachekey, $plugins_data, 24 * HOUR_IN_SECONDS );
        }
    }

    if ( is_array( $plugins_data ) && ( count( $plugins_data ) > 0 ) ) {
        array_multisort( array_column( $plugins_data, 'active_installs' ), SORT_DESC, $plugins_data );
        foreach ( $plugins_data as $pl_data ) {
            ?>
                <div class="htoptions-single-plugins htfree-plugins">
                    <div class="htoptions-img">
                        <img src="<?php echo esc_url ( WCPRO_SALENOTIFICATION_PL_URL.'/admin/assets/images/'.$pl_data['slug'].'.png' ); ?>" alt="<?php echo esc_attr__( $pl_data['name'], 'wc-sales-notification-pro' ); ?>">                        
                    </div>
                    <div class="htoptions-plugins-content">
                        <a href="https://wordpress.org/plugins/<?php echo  $pl_data['slug']; ?>/" target="_blank">
                            <h3><?php echo esc_html__( $pl_data['name'], 'wc-sales-notification-pro' ); ?></h3>
                        </a>
                        <span class="plugin_info">Version <?php echo esc_html__( $pl_data['version'],'wc-sales-notification-pro' );?></span>
                        <a class="htoptions-button" href="<?php echo esc_url( admin_url() ); ?>plugin-install.php?s=<?php echo $pl_data['slug']; ?>&tab=search&type=term" target="_blank"><?php echo esc_html__( 'Install Now', 'wc-sales-notification-pro' ); ?></a>
                    </div>
                </div>
            <?php
        }
    }
    
}