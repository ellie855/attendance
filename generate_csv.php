<?php

$filename = __DIR__ . '/sample_users_1000.csv';
$fp = fopen($filename, 'w');

// ヘッダー行
fputcsv($fp, ['name', 'email', 'password', 'role']);

// 1000行分のデータ
for ($i = 1; $i <= 1000; $i++) {
    fputcsv($fp,[
        "User{$i}",
        "user{$i}@test.com",
        'password123',
        'user',
    ]);
}

fclose($fp);
echo "生成完了: {$filename}\n";