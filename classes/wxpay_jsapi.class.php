<?php

ini_set('date.timezone', 'Asia/Shanghai');
//error_reporting(E_ERROR);
require_once "lib/WxPay.Api.php";
require_once "lib/WxPay.JsApiPay.php";


class wxpay_jsapi
{
    var $wxpay_config;

    public function  __construct($wxpay_config)
    {
        $this->wxpay_config = $wxpay_config;
    }

    public function run($body, $attach, $trade_no, $total_fee)
    {

//①、获取用户openid
        $tools = new JsApiPay();
        $openId = $tools->GetOpenid();

//②、统一下单
        $input = new WxPayUnifiedOrder();
        $input->SetBody($body);
        $input->SetAttach($attach);
        $input->SetOut_trade_no($trade_no);
        $input->SetTotal_fee($total_fee);
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 600));
        $input->SetGoods_tag("test");
        $input->SetNotify_url("http://paysdk.weixin.qq.com/example/notify.php");
        $input->SetTrade_type("JSAPI");
        $input->SetOpenid($openId);
        $order = WxPayApi::unifiedOrder($input);
        echo '<span style="color: #f00;font-weight:bold"><b>统一下单支付单信息</b></span><br/>';

        $jsApiParameters = $tools->GetJsApiParameters($order);

//获取共享收货地址js函数参数
        $editAddress = $tools->GetEditAddressParameters();

        $js_pay_script = <<<JS_IN_WECHAT
<script type="text/javascript">
function jsApiCall()
{
    WeixinJSBridge.invoke(
        'getBrandWCPayRequest',
        $jsApiParameters,
        function(res){
            WeixinJSBridge.log(res.err_msg);
            alert(res.err_code+res.err_desc+res.err_msg);
        }
    );
}

function callpay()
{
    if (typeof WeixinJSBridge === "undefined"){
        if( document.addEventListener ){
            document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
        }else if (document.attachEvent){
            document.attachEvent('WeixinJSBridgeReady', jsApiCall); 
            document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
        }
    }else{
        jsApiCall();
    }
}
//获取共享地址
function editAddress()
{
    WeixinJSBridge.invoke(
        'editAddress',
        $editAddress
        function(res){
            var value1 = res.proviceFirstStageName;
            var value2 = res.addressCitySecondStageName;
            var value3 = res.addressCountiesThirdStageName;
            var value4 = res.addressDetailInfo;
            var tel = res.telNumber;
            
            alert(value1 + value2 + value3 + value4 + ":" + tel);
        }
    )
}
	
window.onload = function(){
    if (typeof WeixinJSBridge === "undefined"){
        if( document.addEventListener ){
            document.addEventListener('WeixinJSBridgeReady', editAddress, false);
        }else if (document.attachEvent){
            document.attachEvent('WeixinJSBridgeReady', editAddress); 
            document.attachEvent('onWeixinJSBridgeReady', editAddress);
        }
    }else{
        editAddress();
    }
};

</script>
JS_IN_WECHAT;
        return $js_pay_script;
    }

}