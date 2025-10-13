
    <?php
    /**
     * Implementation of Tableau trusted auth for php
     *
     * http://onlinehelp.tableausoftware.com/v8.1/server/en-us/trusted_auth_webURL.htm
     */
    
    
    function get_trusted_url($user, $server, $view_url, $site) {
      $params = ':embed=yes&:toolbar=yes:tabs=no';
      $ticket = get_trusted_ticket($server, $user, $_SERVER['REMOTE_ADDR'], $site);
    
      return "http://$server/trusted/$ticket/$view_url?$params";
    }
    
    // Note that this function requires the pecl_http extension.
    // See: http://pecl.php.net/package/pecl_http
    
    // the client_ip parameter isn't necessary to send in the POST unless you have
    // wgserver.extended_trusted_ip_checking enabled (it's disabled by default)
    function get_trusted_ticket($wgserver, $user, $remote_addr, $site) {
      $params = array(
        'username' => $user,
        'client_ip' => $remote_addr,
        'target_site' => $site
      );
    
      return http_parse_message(http_post_fields("http://$wgserver/trusted", $params))->body;
    }
    ?>

	

    