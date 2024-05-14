<?php
class Dfa
{
    /**
     * 替换码
     * @var string
     */
    private $replaceCode = '*';

    /**
     * 敏感词库集合
     * @var array
     */
    private $trieTreeMap = array();

    /**
     * 干扰因子集合
     * @var array
     */
    private $disturbList = array();

    public function __construct($disturbList = array())
    {
        $this->disturbList = $disturbList;
    }

    /**
     * 添加敏感词
     * @param array $wordsList
     */
    public function addWords(array $wordsList)
    {
        foreach ($wordsList as $word) {
            $nowWords = &$this->trieTreeMap;
            $len = mb_strlen($word);
            for ($i = 0; $i < $len; $i++) {
                $char = mb_substr($word, $i, 1);
                if (!isset($nowWords[$char])) {
                    $nowWords[$char] = [];
                }
                $nowWords = &$nowWords[$char];
            }
            // 标记敏感词的结束
            $nowWords['isEnd'] = true;
        }
    }

    /**
     * 查找对应敏感词
     * @param $txt
     * @return array
     */
    public function search($txt, $hasReplace = false, &$replaceCodeList = array())
    {
        $wordsList = array();
        $txtLength = mb_strlen($txt);

        for ($i = 0; $i < $txtLength; $i++) {
            $wordLength = $this->checkWord($txt, $i, $txtLength);
            if ($wordLength > 0) {
                $word = mb_substr($txt, $i, $wordLength);
                $wordsList[] = $word;
                if ($hasReplace) {
                    $replaceCodeList[] = str_repeat($this->replaceCode, mb_strlen($word));
                }
                $i += $wordLength - 1; // 跳过已匹配的敏感词
            }
        }

        return $wordsList;
    }

    /**
     * 过滤敏感词
     * @param $txt
     * @return mixed
     */
    public function filter($txt)
    {
        $replaceCodeList = array();
        $wordsList = $this->search($txt, true, $replaceCodeList);
        if (empty($wordsList)) {
            return $txt;
        }
        return str_replace($wordsList, $replaceCodeList, $txt);
    }

    /**
     * 敏感词检测
     * @param $txt
     * @param $beginIndex
     * @param $length
     * @return int
     */
    private function checkWord($txt, $beginIndex, $length)
    {
        $trieTree = &$this->trieTreeMap;
        $wordLength = 0;
        $skipChars = 0; // 跳过的干扰因子个数

        for ($i = $beginIndex; $i < $length; $i++) {
            $char = mb_substr($txt, $i, 1);

            // 跳过干扰字符
            if ($this->checkDisturb($char)) {
                $skipChars++;
                continue;
            }

            if (!isset($trieTree[$char])) {
                break;
            }

            $trieTree = &$trieTree[$char];
            $wordLength++;

            // 检查是否为敏感词结尾
            if (isset($trieTree['isEnd'])) {
                return $wordLength + $skipChars; // 返回包含干扰因子的敏感词长度
            }
        }

        return 0;
    }

    /**
     * 干扰因子检测
     * @param $char
     * @return bool
     */
    private function checkDisturb($char)
    {
        return in_array($char, $this->disturbList);
    }
}
?>
