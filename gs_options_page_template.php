<div id="gs-admin-settings-page">
    <br />

    <a href="http://www.gosquared.com/" title="Go to the GoSquared homepage" target="_blank"><div id="gosquaredlogo"></div></a>

    <?php
        if ($success_msg && $success_msg != "") {
            gs_success($success_msg);
        }

        if ($warn_msg && $warn_msg != "") {
            gs_warn($warn_msg);
        }

        if ($fail_msg && $fail_msg != "") {
            gs_fail($fail_msg);
        }
    ?>

    <div class="gs-admin-header">

        <?php
        $default_text = $acct ? $acct : 'GSN-000000-X';

        $default_apiKey = $apiKey ? $apiKey : "";

        if (!$trackAdmin)
            $trackAdmin = 'Yes';

        if (!$trackPreview)
            $trackPreview = 'Yes';

        if (!$trackUser)
            $trackUser = 'Username';

        if (!$cacheTimeout)
            $cacheTimeout = 30;
        ?>

        <h2>GoSquared Widgets - Share your stats with your audience.</h2>
        <?php

        $cachedir_help_link = '<a href = "http://www.gosquared.com/support/wiki/wordpress_plugin#cachedir" target="_blank">What does this mean?</a>';
        $permissions_help = '<a href = "http://www.gosquared.com/support/wiki/wordpress_plugin#cachedir-permissions" target="_blank">More information</a>';
        //$dismiss_anchor = '<form name="gs-notice-dismiss" action="" method = "post"><input type="hidden" name="cachedir_dismiss" value="yes"/></form>';
        if (!file_exists(GS_API_CACHE_DIR)) {
            if (!mkdir(GS_API_CACHE_DIR, 0766)) {
                gs_warn('Unable to create the cache directory at ' . GS_API_CACHE_DIR . " $cachedir_help_link");
            }
        }
        elseif (!is_writeable(GS_API_CACHE_DIR)) {
            gs_warn('The cache directory at ' . GS_API_CACHE_DIR . ' is not writeable.<br />Data for GoSquared widgets will not be cached. ' . $cachedir_help_link . '<br />To fix this, change the permissions of this directory to 766 ' . $permissions_help);
        }

        ?>
        <p>
            Go to you <a href='<?php echo admin_url('widgets.php'); ?>'>Wordpress Widget settings</a> to enable
            GoSquared widgets.
        </p>

        <p>
            Ensure you <b>enter both your Site Token and API Key</b> in the fields below first.
        </p>

        <a href='<?php echo admin_url('widgets.php'); ?>'><img
            src="<?php echo WP_PLUGIN_URL; echo '/' . GS_PLUGIN_DIR;?>/wordpress_plugin_01_150x160.png" align="top"
            alt="GoSquared Widget 01" class="hero_preview"/></a>
        <a href='<?php echo admin_url('widgets.php'); ?>'><img
            src="<?php echo WP_PLUGIN_URL;echo '/' . GS_PLUGIN_DIR; ?>/wordpress_plugin_02_150x160.png" align="top"
            alt="GoSquared Widget 02" class="hero_preview"/></a>
        <a href='<?php echo admin_url('widgets.php'); ?>'><img
            src="<?php echo WP_PLUGIN_URL; echo '/' . GS_PLUGIN_DIR;?>/wordpress_plugin_03_170x160.png" align="top"
            alt="GoSquared Widget 03" class="hero_preview"/></a>

    </div>

    <form name="gs-options" action="" method = "post">

        <h2>Site Token - Start tracking "<?php echo get_bloginfo('name'); ?>" with GoSquared.</h2>

        <p>Your Site Token enables GoSquared to monitor your Wordpress site's traffic. <a href="https://www.gosquared.com/join/" title="Sign up to GoSquared for free to start monitoring your site in real-time" target="_blank">Sign up for free</a> to register your site with GoSquared.</p>

        <div class="input-field">
            <span class="input-label">Your GoSquared Site Token </span>
            <input class="gs-text-input" type="text" name="gs_acct" value = "<?php echo $default_text ?>"
                   onclick="if(this.value=='<?php echo $default_text ?>')this.value=''"
                   onblur="if(this.value=='')this.value='<?php $default_text ?>'"/>&nbsp;
            <a href="http://www.gosquared.com/support/wiki/faqs#faq-site-token" target="_blank">What's this?</a>
        </div>

        <h2>API Key - Share your stats via GoSquared Widgets.</h2>
        <p>Your API Key enables you to share your stats with your blog visitors via Widgets. Widgets will not work
            without an API Key.</p>

        <div class="input-field">
            <span class="input-label">Your GoSquared API Key </span>
            <input class="gs-text-input" type="text" name="gs_apiKey" value="<?php echo $default_apiKey ?>"
                   onclick="if(this.value=='<?php echo $default_apiKey ?>')this.value=''"
                   onblur="if(this.value=='')this.value='<?php echo $default_apiKey ?>'"/>&nbsp;
            <a href="http://www.gosquared.com/support/wiki/faqs#faq-API-key" target="_blank">What's this?</a>
        </div>

        <h2>Advanced Settings</h2>
        <table class="gs-settings">
            <tr>
                <td class="label">Track admin pages </td>
                <td><input type="radio" name="gs_trackAdmin" value="Yes" id="trackAdmin" <?php if ($trackAdmin == 'Yes')
                    echo 'checked="checked" '; ?>/> Yes</td>
                <td><input type="radio" name="gs_trackAdmin" value="No" id="trackAdmin" <?php if ($trackAdmin == 'No')
                    echo 'checked="checked" '; ?>/> No</td>
            </tr>
            <tr>
                <td class="label">Track post preview pages</td>
                <td><input type="radio" name="gs_trackPreview" value="Yes" id="trackPreview" <?php if ($trackPreview == 'Yes')
                    echo 'checked="checked" '; ?>/> Yes</td>
                <td><input type="radio" name="gs_trackPreview" value="No" id="trackPreview" <?php if ($trackPreview == 'No')
                    echo 'checked="checked" '; ?>/> No</td>
            </tr>
            <tr>
                <td class="label">Tag individual users with </td>
                <td><input type="radio" name="gs_trackUser" value="Off" id="trackUser" <?php if ($trackUser == 'Off')
                    echo 'checked="checked" '; ?>/> Off</td>
                <td><input type="radio" name="gs_trackUser" value="UserID" id="trackUser" <?php if ($trackUser == 'UserID')
                    echo 'checked="checked" '; ?>/> User ID</td>
                <td><input type="radio" name="gs_trackUser" value="Username" id="trackUser" <?php if ($trackUser == 'Username')
                    echo 'checked="checked" '; ?> /> Username</td>
                <td class="wide"><input type="radio" name="gs_trackUser" value="DisplayName" id="trackUser" <?php if ($trackUser == 'DisplayName')
                    echo 'checked="checked" '; ?>/> Display Name</td>
            </tr>
            <tr>
                <td class="label">Set widget cache timeout</td>
                <td><input type="radio" name="gs_cacheTimeout" value="5"
                           id="chacheTimeout" <?php if ($cacheTimeout == 5)
                        echo 'checked="checked"'; ?>/>5s
                </td>
                <td><input type="radio" name="gs_cacheTimeout" value="15" id="chacheTimeout" <?php if ($cacheTimeout ==
                    15
                )
                    echo 'checked="checked"'; ?>/>15s
                </td>
                <td><input type="radio" name="gs_cacheTimeout" value="30" id="chacheTimeout" <?php if ($cacheTimeout ==
                    30
                )
                    echo 'checked="checked"'; ?>/>30s
                </td>
                <td><input type="radio" name="gs_cacheTimeout" value="60" id="chacheTimeout" <?php if ($cacheTimeout ==
                    60
                )
                    echo 'checked="checked"'; ?>/>60s
                </td>
            </tr>
        </table>
        <input type="submit" value="Save Settings" class="button-primary" />
    </form>
</div>
