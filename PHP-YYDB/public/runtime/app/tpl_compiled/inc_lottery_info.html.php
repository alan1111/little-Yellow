<?php if ($this->_var['lottery_info']['luck_user_id'] > 0): ?>

	<div class="goods-record">
	<p class="owner">获得者：<a href=""><?php echo $this->_var['lottery_info']['user_name']; ?></a></p>
	<p>本期参与：<?php echo $this->_var['lottery_info']['current_buy']; ?>人次</p>
	<p>幸运号码：<?php echo $this->_var['lottery_info']['lottery_sn']; ?></p>
	</div>
<?php else: ?>

	<div class="goods-counting">
	<div class="goods-countdown">
		揭晓倒计时：
		<div class="countdown">
			<span class="countdown-nums">
				<time class="w-countdown-nums" nowtime="<?php echo $this->_var['now_time']; ?>" endtime="<?php echo $this->_var['lottery_info']['lottery_time']; ?>" id="<?php echo $this->_var['lottery_info']['id']; ?>">
				正在揭晓中
			    </time>

			</span>
		</div>
	</div>
	</div>
<?php endif; ?>