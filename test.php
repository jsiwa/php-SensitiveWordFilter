<?php
echo "脚本开始执行...\n";
require 'Dfa.php';

echo "创建 TrieTree 对象...\n";
$disturbList = ['@', '#', '$', '&'];
// 创建TrieTree对象，并添加干扰因子
$trie = new Dfa($disturbList);

// Load sensitive words
function loadSensitiveWords($dir) {
    $words = [];
    foreach (glob("$dir/*.txt") as $filename) {
        echo "加载敏感词文件: $filename\n";
        $file = fopen($filename, "r");
        while (($line = fgets($file)) !== false) {
            $words[] = trim($line);
        }
        fclose($file);
    }
    return $words;
}

echo "加载敏感词...\n";
$sensitiveWords = loadSensitiveWords('sensitive_words');

// 添加敏感词
echo "添加敏感词...\n";
$trie->addWords($sensitiveWords);

// 生成大文本
echo "生成大文本...\n";
$largeText = str_repeat("敏感词文本", 1000) . "测试 测&试";

// 开始计时
echo "DFA:开始搜索敏感词...\n";
$startTime = microtime(true);

// 搜索敏感词
$foundWords = $trie->search($largeText);
print_r($foundWords);

// 结束计时
$endTime = microtime(true);
$searchDuration = $endTime - $startTime;
echo "找到的敏感词数量: " . count($foundWords) . "\n";
echo "搜索敏感词耗时: " . $searchDuration . " 秒\n";

echo "脚本执行完毕。\n";
?>
