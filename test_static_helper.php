<?php
/**
 * Test script for Static Helper Library
 */

// Include configuration and autoload
include_once "application/config/config.php";
include_once "application/library/autoload.php";

// Initialize static helper
StaticHelper::init();

?>
include  "application/config/connection.php";
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Static Helper Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        .test { border: 1px solid #ddd; padding: 20px; margin: 20px 0; }
        .success { color: green; }
        .error { color: red; }
        pre { background: #f4f4f4; padding: 10px; border-radius: 5px; }
    </style>
</head>
<body>
    <h1>Static Helper Library Test</h1>
    
    <div class="test">
        <h2>1. Configuration</h2>
        <p><strong>BASE_URL:</strong> <?= BASE_URL ?></p>
        <p><strong>Static URL:</strong> <?= StaticHelper::url('') ?></p>
        <p><strong>Static Directory:</strong> <?= StaticHelper::path('') ?></p>
    </div>
    
    <div class="test">
        <h2>2. URL Generation</h2>
        
        <h3>Test URLs:</h3>
        <ul>
            <li><strong>CSS:</strong> <?= htmlspecialchars(static_url('css/bootstrap.min.css')) ?></li>
            <li><strong>JS:</strong> <?= htmlspecialchars(static_url('js/jquery.min.js')) ?></li>
            <li><strong>Image:</strong> <?= htmlspecialchars(static_url('images/logo.png')) ?></li>
        </ul>
        
        <h3>File Existence Check:</h3>
        <ul>
            <li>css/bootstrap.min.css: <?= StaticHelper::exists('css/bootstrap.min.css') ? '<span class="success">Exists</span>' : '<span class="error">Missing</span>' ?></li>
            <li>js/jquery.min.js: <?= StaticHelper::exists('js/jquery.min.js') ? '<span class="success">Exists</span>' : '<span class="error">Missing</span>' ?></li>
            <li>images/logo.png: <?= StaticHelper::exists('images/logo.png') ? '<span class="success">Exists</span>' : '<span class="error">Missing</span>' ?></li>
        </ul>
    </div>
    
    <div class="test">
        <h2>3. HTML Tag Generation</h2>
        
        <h3>CSS Link Tag:</h3>
        <pre><?= htmlspecialchars(static_css('css/bootstrap.min.css')) ?></pre>
        
        <h3>JavaScript Tag:</h3>
        <pre><?= htmlspecialchars(static_js('js/jquery.min.js', ['defer' => 'defer'])) ?></pre>
        
        <h3>Image Tag:</h3>
        <pre><?= htmlspecialchars(static_img('images/logo.png', ['width' => '100', 'height' => '50', 'alt' => 'Logo'])) ?></pre>
    </div>
    
    <div class="test">
        <h2>4. Real Output</h2>
        
        <h3>CSS Output (rendered):</h3>
        <?= static_css('css/bootstrap.min.css') ?>
        
        <h3>JavaScript Output (rendered):</h3>
        <?= static_js('js/jquery.min.js', ['defer' => 'defer']) ?>
        
        <h3>Image Output (rendered):</h3>
        <?= static_img('images/logo.png', ['width' => '100', 'height' => '50', 'alt' => 'Logo']) ?>
    </div>
    
    <div class="test">
        <h2>5. Usage Examples</h2>
        
        <h3>Example 1: Complete HTML Page</h3>
        <pre>
&lt;?php
// At the top of your PHP file
include_once "application/library/autoload.php";
StaticHelper::init();
?&gt;
&lt;!DOCTYPE html&gt;
&lt;html&gt;
&lt;head&gt;
    &lt;?= static_css('css/bootstrap.min.css') ?&gt;
    &lt;?= static_css('css/custom.css') ?&gt;
    &lt;title&gt;My Page&lt;/title&gt;
&lt;/head&gt;
&lt;body&gt;
    &lt;header&gt;
        &lt;?= static_img('images/logo.png', ['alt' => 'Logo']) ?&gt;
    &lt;/header&gt;
    
    &lt;div class="content"&gt;
        &lt;!-- Content here --&gt;
    &lt;/div&gt;
    
    &lt;?= static_js('js/jquery.min.js') ?&gt;
    &lt;?= static_js('js/app.js', ['defer' => 'defer']) ?&gt;
&lt;/body&gt;
&lt;/html&gt;
        </pre>
        
        <h3>Example 2: Checking File Existence</h3>
        <pre>
&lt;?php
if (StaticHelper::exists('css/custom-theme.css')) {
    echo static_css('css/custom-theme.css');
} else {
    echo static_css('css/default-theme.css');
}
?&gt;
        </pre>
    </div>
    
    <div class="test">
        <h2>6. Integration Status</h2>
        
        <p>The following files have been updated to use the Static Helper:</p>
        <ul>
            <li>✅ login.php</li>
            <li>✅ master.php</li>
            <li>✅ navtop.php</li>
            <li>✅ gate1.php</li>
            <li>✅ gate1_new.php</li>
            <li>✅ foto_gate1.php</li>
            <li>✅ foto_vaksin.php</li>
            <li>✅ N_foto_gate1.php</li>
            <li>✅ N_gate1.php</li>
            <li>✅ utility_function.php</li>
            <li>✅ cek_kpi.php</li>
            <li>✅ report.php</li>
            <li>✅ temuan.php</li>
        </ul>
        
        <p><strong>Note:</strong> Some files may still use direct static references. Use the search function to find remaining instances:</p>
        <pre>
Search for: href="static/
Search for: src="static/
Search for: static/css/
Search for: static/js/
Search for: static/images/
        </pre>
    </div>
    
    <div class="test">
        <h2>7. Next Steps</h2>
        
        <ol>
            <li>Update any remaining files with static references</li>
            <li>Consider adding versioning configuration for production</li>
            <li>Update documentation for team members</li>
            <li>Test in development environment</li>
            <li>Deploy to production</li>
        </ol>
    </div>
    
    <hr>
    <p><strong>Static Helper Documentation:</strong> See STATIC_HELPER.md for complete documentation.</p>
    <p><strong>Migration Guide:</strong> Use the search patterns above to find and update remaining static references.</p>
</body>
</html>