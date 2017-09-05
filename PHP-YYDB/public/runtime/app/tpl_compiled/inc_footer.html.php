<div class="clear"></div>
<div class="footer_box">
<div class="footer_inner_box">

<div class="<?php 
$k = array (
  'name' => 'load_wrap',
  't' => $this->_var['wrap_type'],
);
echo $k['name']($k['t']);
?> clearfix">
	<div class="help_row f_l">
		<?php $_from = $this->_var['deal_help']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'help');if (count($_from)):
    foreach ($_from AS $this->_var['help']):
?>
			<span>
			<dl>
				<dt><?php if ($this->_var['help']['iconfont'] != ''): ?><i class="diyfont"><?php echo $this->_var['help']['iconfont']; ?></i>&nbsp;<?php endif; ?><?php echo $this->_var['help']['title']; ?></dt>
				<dd>
					<ul>
						<?php $_from = $this->_var['help']['help_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'help_item');if (count($_from)):
    foreach ($_from AS $this->_var['help_item']):
?>
						<li><b></b><a href="<?php
echo parse_url_tag("u:index|help|"."id=".$this->_var['help_item']['id']."".""); 
?>"><?php echo $this->_var['help_item']['title']; ?></a></li>
						<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
					</ul>
				</dd>
			</dl>
			</span>
		<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
	</div>
	<div class="g-side-l f_l">
            <ul class="m-instruction-state f-clear">
                <li><i class="ico_foot ico-state-l-1"></i>100%公平公正公开</li>
                <li><i class="ico_foot ico-state-l-2"></i>100%正品保证</li>
                <li><i class="ico_foot ico-state-l-3"></i>100%权益保障</li>
            </ul>
     </div>
	<div class="foot_logo f_l">
		<a class="link" href="<?php echo $this->_var['APP_ROOT']; ?>/">
			<img src="./app/Tpl/main/fanwe/images/sccode.png"/>
		<!--<?php
			$this->_var['foot_logo_image'] = get_spec_image(app_conf("FOOTER_LOGO"),110,110,1);
		?>
		<?php 
$k = array (
  'name' => 'load_page_png',
  'v' => $this->_var['foot_logo_image'],
);
echo $k['name']($k['v']);
?>-->
		</a>
	</div>
	<div class="foot_online_time f_l">
	周一至周六&nbsp;&nbsp;&nbsp;8:30-17:30<br/>
	全国服务热线&nbsp;&nbsp;&nbsp;400-668-1058
	</div>
	<div class="blank"></div>


    <center>
    	<div class="foot_info">
			<a href="http://www.miibeian.gov.cn">Copyright© 2016-2017 江苏省谷创电子商务南通有限公司 版权所有 苏ICP备16049040号 </a>
				<p>&copy;南通谷创电子商务有限公司</p>
	    </div>
    </center>
	
</div><!--end foot_wrap-->
</div>
</div>

<a id="go_top" href="javascript:void(0);"></a>
</body>
</html>

	