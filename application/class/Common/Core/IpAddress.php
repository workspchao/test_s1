<?php

namespace Common\Core;

use Common\Helper\IpConverter;

class IpAddress
{

    protected $ipstr;
    protected $ipint;

    public static function getAddressByString($ipStr){
        //https://lbs.qq.com/dev/console/quota/applyList
        //http://lbsyun.baidu.com/apiconsole/key#/home
        // $addressData = file_get_contents("http://api.map.baidu.com/location/ip?ip=$ipStr&ak=1qeVGaBGASkxUkrOgOdstDDOfasyQtjO");

        $key = "JRGBZ-T4YCG-IX2QU-IW7VK-JKEKH-ACFYQ";
        $addressData = file_get_contents("https://apis.map.qq.com/ws/location/v1/ip?ip=$ipStr&key=$key");
        if(!empty($addressData)){
            $addressData = json_decode($addressData);

            if(isset($addressData->result)){
                $result = $addressData->result;

                if(!empty($result) && isset($result->ad_info)){
                    $result = $result->ad_info;
                    $address = "";
                    
                    if(isset($result->province) && !empty($result->province)){
                        $address .= $result->province;
                    }

                    if(isset($result->city) && !empty($result->city)){
                        $address .= $result->city;
                    }

                    if(isset($result->district) && !empty($result->district)){
                        $address .= $result->district;
                    }                    
                    
                    return $address;
                }
            }
        }

        return false;
    }

    public static function getIpIsp($ipStr){
        $token = "17ee8baa727375c83d9e14b24f1a5b03";
        $addressData = file_get_contents("http://api.ip138.com/ipv4/?ip=$ipStr&token=$token");

        if(empty($addressData)){
            return null;
        }

        $addressData = json_decode($addressData);
        if(empty($addressData)){
            return null;
        }

        $ret = $addressData->ret;

        if($ret != 'ok'){
            log_message("error", json_encode($addressData));
            return null;
        }

        $data = $addressData->data;
        $address = $data[0].$data[1].$data[2].$data[3]."-".$data[4];
        return $address;
    }

    public static function fromString($ipstr)
    {
        $a = new IpAddress();
        $a->setIpAddressString($ipstr);
        return $a;
    }

    public static function fromInt($ipint)
    {
        $a = new IpAddress();
        $a->setIpAddressInteger($ipint);
        return $a;
    }

    public function setIpAddressString($ipstr)
    {
        $this->ipstr = $ipstr;
        $this->ipint = IpConverter::toInt($ipstr);
        return true;
    }

    public function setIpAddressInteger($ipint)
    {
        $this->ipint = $ipint;
        $this->ipstr = IpConverter::toString($ipint);
        return true;
    }

    public function getString()
    {
        return $this->ipstr;
    }

    public function getInteger()
    {
        return $this->ipint;
    }

    public function jsonSerialize()
    {
        return [
            'ip' => $this->getString()
        ];
    }
}
