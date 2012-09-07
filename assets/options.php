<div class="header">
    <h1>GoSquared</h1>
    <p>Real-time web analytics</p>
</div>

<?php if(has_tracking_code()): ?>
    <div class="tracking-success">
        <h2>Tracking code installed!</h2>
        <p>Your tracking code has been successfully added, and you'll be getting data instantly. <a href="http://gosquared.com/dashboard">Go check out your dashboard!</a></p>
    </div>
<?php else: ?>
    <div class="tracking-help">
        <h2>Welcome to GoSquared for WordPress!</h2>
        <p>You&rsquo;re seconds away from the best analytics platform out there. We just need to verify your account. Don't have one? <a href="http://gosquared.com/join">Get a free GoSquared account here</a>.</p>
    </div>
<?php endif; ?>

<form action="" method="post">
    <?php foreach($gs_options as $value => $description): ?>
    <p>
        <label for="option-<?php echo $value; ?>"><?php echo preg_replace('^\..*^', '', str_replace('Your GoSquared', '', $description)); ?>:</label>
        <input id="<?php echo $value; ?>" name="<?php echo $value; ?>" placeholder="<?php echo isset($placeholders[$value]) ? $placeholders[$value] : ''; ?>" value="<?php echo isset($post[$value]) ? $post[$value] : get_option($value); ?>">
        
        <?php if(isset($errors[$value])): ?>
            <em class="error"><?php echo $errors[$value]; ?></em>
        <?php else: ?>
            <em class="help"><?php echo $description; ?></em>
        <?php endif; ?>
    </p>
    <?php endforeach; ?>
    
    <button name="submit" type="submit"><?php echo has_tracking_code() ? 'Update' : 'Add'; ?> tracking code</button>
</form>

<h2>Theme functions</h2>
<p>By installing the GoSquared plugin, a number of functions are now available to you to use.</p>

<dl>
    <dt><code>has_tracking_code()</code></dt>
        <dd>Check if the site has the tracking code set up properly.</dd>
    
    <dt><code>live_visitors()</code></dt>
        <dd>Show a running count of total visitors online. Example output: <?php function_exists('live_visitors') && live_visitors(); ?></dd>

    <dt><code>top_content()</code></dt>
        <dd>Show a list of the most popular pages on your site. Example output:<br>
            <?php function_exists('top_content') && top_content(); ?>
        </dd>
</dl>