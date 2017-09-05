/**
 * Created by GCAdmin on 2017/7/11.
 */
$(document).ready(function() {
    search();
    //初始化swiper插件
    var mySwiper = new Swiper('.slide', {
        autoplay: 5000,
        visibilityFullFit: true,
        loop: true,
        pagination: '.pagination'
    });

    function search(){
        /*
         * 1.颜色随着 页面的滚动  逐渐加深
         * 2.当我们超过  轮播图的  时候  颜色保持不变
         * */
        /*获取搜索盒子*/
        var searchBox = document.querySelector('.list_head');
        /*获取banner盒子*/
        var bannerBox = document.querySelector('.slide');
        /*获取高度*/
        var h = bannerBox.offsetHeight;
        /*监听window的滚动事件*/
        window.onscroll = function(){
            /*不断的获取离顶部的距离*/
            var top = document.body.scrollTop;
            var opacity = 0;
            if( top < h){
                /*1.颜色随着 页面的滚动  逐渐加深*/
                opacity = top/h * 0.85
            }else{
                /*2.当我们超过  轮播图的  时候  颜色保持不变*/
                opacity = 0.85
            }

            /*把透明度设置上去*/
            searchBox.style.background = "rgba(255,80,0,"+opacity+")";

        }
    }
});
