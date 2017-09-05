<?php
/**
*
* @author hhcycj
*/
class  DuobaoItemViewModel extends ViewModel{
    public $viewFields = array(
        
        'DuobaoItem'=>array('id', 'deal_id', 'duobao_id', 'name'=>'duobaoitem_name', 'cate_id', 'description', 'is_effect', 'brief', 'icon', 'brand_id', 'deal_gallery', 'create_time', 
                            'duobao_score', 'invite_score', 'max_buy', 'min_buy', 'current_buy', 'progress', 'lottery_sn', 'has_lottery', 'success_time', 'lottery_time', 'fair_sn', 'fair_sn_local',
                            'luck_user_id', 'click_count', 'fair_type','sort', 'fair_period','robot_end_time', 'robot_is_db', '_type'=>'left'),
        
        'User'=>array('user_name', '_on'=>'User.id=DuobaoItem.luck_user_id', '_type'=>'left'),
    
        'DealCate'=>array('name'=>'cate_name', '_on'=>'DealCate.id=DuobaoItem.cate_id', '_type'=>'left'),
        
        'Brand'  => array('name'=>'brand_name', '_on'=>'Brand.id=DuobaoItem.brand_id', '_type'=>'left')
    );
}


 
 
 
 