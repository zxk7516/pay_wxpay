<?php


class wxpay_h5_pay
{
    var $redirect_url;
    public function __construct($wxpay_config)
    {

    }

    function scripts()
    {
        $scripts = <<<H5PAYSCRIPTS
<script src="//wx.gtimg.com/wxpay_h5/fingerprint2.min.1.4.1.js"></script>
<script type="text/javascript">
  if(window)
  var fp=new Fingerprint2();
  fp.get(function(result)
  {
    $.getJSON("h5.json.php?code="+result, function(d){
        if(d.errmsg === ''){
          $('#getBrandWCPayRequest').attr("href",d.url);//+'&redirect_url=http%3a%2f%2fwxpay.    wxutil.com%2fmch%2fpay%2fh5jumppage.php
        }else{
          alert(d.errmsg);
        }
     });                                                            
  });
</script>
H5PAYSCRIPTS;
    }

    public function h5_data(){
        $data['errmsg'] = '';
        $data['url'] = 'https://wx.tenpay.com/cgi-bin/mmpayweb-bin/checkmweb?prepay_id=wx20170915160105291a70842b0794933945&package=3723631201'
        .'&redirect_url='.$this->redirect_url;
        return $data;
    }
}