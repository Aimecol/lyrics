<?php
/**
 * Common functions for Song Lyrics Platform
 */

/**
 * Sanitize input data
 */
function sanitize($data) {
    if (is_array($data)) {
        return array_map('sanitize', $data);
    }
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

/**
 * Generate clean URL slug from text
 */
function createSlug($text) {
    $text = strtolower($text);
    $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
    $text = preg_replace('/[\s-]+/', '-', $text);
    return trim($text, '-');
}

/**
 * Format lyrics for display (preserve line breaks and structure)
 */
function formatLyrics($lyrics) {
    $lyrics = sanitize($lyrics);
    $lyrics = nl2br($lyrics);
    
    // Style section headers like [Verse 1], [Chorus], etc.
    $lyrics = preg_replace('/\[([^\]]+)\]/', '<span class="lyrics-section">[$1]</span>', $lyrics);
    
    return $lyrics;
}

/**
 * Truncate text to specified length
 */
function truncateText($text, $length = 100, $suffix = '...') {
    if (strlen($text) <= $length) {
        return $text;
    }
    return substr($text, 0, $length) . $suffix;
}

/**
 * Generate pagination HTML
 */
function generatePagination($currentPage, $totalPages, $baseUrl, $queryParams = []) {
    if ($totalPages <= 1) return '';
    
    $html = '<nav class="pagination" aria-label="Page navigation">';
    $html .= '<ul class="pagination-list">';
    
    // Previous button
    if ($currentPage > 1) {
        $prevUrl = $baseUrl . '?page=' . ($currentPage - 1);
        if (!empty($queryParams)) {
            $prevUrl .= '&' . http_build_query($queryParams);
        }
        $html .= '<li><a href="' . $prevUrl . '" class="pagination-link" aria-label="Previous page">&laquo; Previous</a></li>';
    }
    
    // Page numbers
    $start = max(1, $currentPage - 2);
    $end = min($totalPages, $currentPage + 2);
    
    if ($start > 1) {
        $url = $baseUrl . '?page=1';
        if (!empty($queryParams)) {
            $url .= '&' . http_build_query($queryParams);
        }
        $html .= '<li><a href="' . $url . '" class="pagination-link">1</a></li>';
        if ($start > 2) {
            $html .= '<li><span class="pagination-ellipsis">...</span></li>';
        }
    }
    
    for ($i = $start; $i <= $end; $i++) {
        $url = $baseUrl . '?page=' . $i;
        if (!empty($queryParams)) {
            $url .= '&' . http_build_query($queryParams);
        }
        
        if ($i == $currentPage) {
            $html .= '<li><span class="pagination-link current">' . $i . '</span></li>';
        } else {
            $html .= '<li><a href="' . $url . '" class="pagination-link">' . $i . '</a></li>';
        }
    }
    
    if ($end < $totalPages) {
        if ($end < $totalPages - 1) {
            $html .= '<li><span class="pagination-ellipsis">...</span></li>';
        }
        $url = $baseUrl . '?page=' . $totalPages;
        if (!empty($queryParams)) {
            $url .= '&' . http_build_query($queryParams);
        }
        $html .= '<li><a href="' . $url . '" class="pagination-link">' . $totalPages . '</a></li>';
    }
    
    // Next button
    if ($currentPage < $totalPages) {
        $nextUrl = $baseUrl . '?page=' . ($currentPage + 1);
        if (!empty($queryParams)) {
            $nextUrl .= '&' . http_build_query($queryParams);
        }
        $html .= '<li><a href="' . $nextUrl . '" class="pagination-link" aria-label="Next page">Next &raquo;</a></li>';
    }
    
    $html .= '</ul>';
    $html .= '</nav>';
    
    return $html;
}

/**
 * Format time duration
 */
function formatDuration($time) {
    if (!$time) return '';
    
    $parts = explode(':', $time);
    if (count($parts) >= 2) {
        $minutes = intval($parts[1]);
        $seconds = intval($parts[2] ?? 0);
        return sprintf('%d:%02d', $minutes, $seconds);
    }
    
    return $time;
}

/**
 * Get time ago format
 */
function timeAgo($datetime) {
    $time = time() - strtotime($datetime);
    
    if ($time < 60) return 'just now';
    if ($time < 3600) return floor($time/60) . ' minutes ago';
    if ($time < 86400) return floor($time/3600) . ' hours ago';
    if ($time < 2592000) return floor($time/86400) . ' days ago';
    if ($time < 31536000) return floor($time/2592000) . ' months ago';
    
    return floor($time/31536000) . ' years ago';
}

/**
 * Check if user is logged in as admin
 */
function isAdminLoggedIn() {
    return isset($_SESSION['admin_id']) && isset($_SESSION['admin_username']);
}

/**
 * Redirect to URL
 */
function redirect($url) {
    header('Location: ' . $url);
    exit;
}

/**
 * Display flash message
 */
function setFlashMessage($message, $type = 'info') {
    $_SESSION['flash_message'] = $message;
    $_SESSION['flash_type'] = $type;
}

/**
 * Get and clear flash message
 */
function getFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        $type = $_SESSION['flash_type'] ?? 'info';
        unset($_SESSION['flash_message'], $_SESSION['flash_type']);
        return ['message' => $message, 'type' => $type];
    }
    return null;
}

/**
 * Generate CSRF token
 */
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 */
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Log user activity (for debugging/monitoring)
 */
function logActivity($action, $details = '') {
    if (DEBUG_MODE) {
        $log = date('Y-m-d H:i:s') . " - $action";
        if ($details) {
            $log .= " - $details";
        }
        $log .= " - IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown') . "\n";
        
        file_put_contents(__DIR__ . '/../logs/activity.log', $log, FILE_APPEND | LOCK_EX);
    }
}
?>
