<?php
include_once 'request.php';
function __outFilter() {
    global $R;
    class OutFilter {
        private $R = NULL;
        public function __construct(&$R) {
            ob_start();
            $this->R = &$R;
        }
        public function __destruct() {
            global $result;
            $R = &$this->R;
            $content = ob_get_clean();
            $match = NULL;
            while(preg_match('/\{:(.+?)\}/', $content, $match)) {
                $content = str_replace($match[0], eval('return ' . $match[1] . ';'), $content);
            }
            echo $content;
        }
    }
    static $outFilter = NULL;
    if($outFilter == NULL)
        $outFilter = new OutFilter($R);
}
__outFilter();

