<?php
header('Content-Type: application/json; charset=utf-8');

// 設定時區為台北
date_default_timezone_set('Asia/Taipei');

// 獲取 POST 數據
$json = file_get_contents('php://input');
$data = json_decode($json, true);

$email = isset($data['email']) ? trim($data['email']) : '';
$timestamp = isset($data['timestamp']) ? $data['timestamp'] : date('Y-m-d H:i:s');

// 驗證電子郵件格式
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['error' => '無效的電子郵件格式']);
    exit;
}

// 準備要寫入的內容
$content = "$email | $timestamp\n";

try {
    // 確保檔案存在
    if (!file_exists('text.txt')) {
        file_put_contents('text.txt', "=== 單次付費觀看申請紀錄 ===\n格式：電子郵件 | 申請時間\n============================\n");
    }
    
    // 寫入 text.txt 檔案（追加模式）
    if (file_put_contents('text.txt', $content, FILE_APPEND | LOCK_EX) !== false) {
        http_response_code(200);
        echo json_encode(['success' => true]);
    } else {
        throw new Exception('無法寫入檔案');
    }
} catch (Exception $e) {
    error_log($e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => '系統錯誤，請稍後再試']);
}