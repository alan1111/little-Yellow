<?php
class Config{
    private $cfg = array(
        'url'=>'https://pay.swiftpass.cn/pay/gateway',
        //'mchId'=>'7551000001',
        //'key'=>'9d101c97133837e13dde2d32a5054abb',
        'version'=>'2.0'
       );
    
    public function __construct(){
        $payment_info = $GLOBALS['db']->getRow("select id,config,logo,name from ".DB_PREFIX."payment where class_name='Wft'");
        $config = unserialize($payment_info['config']);
        
        $this->cfg['mchId'] = $config['mch_id'];
        $this->cfg['key'] = $config['key'];
    }
    
    public function C($cfgName){
        return $this->cfg[$cfgName];
    }
}
?>