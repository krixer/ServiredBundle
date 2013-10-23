<?php 
namespace RC\ServiredBundle\Service;

use Symfony\Component\Config\Definition\Exception\Exception;

class Signature {

	protected $clave;
	protected $name;
	protected $code;
	protected $terminal;
	protected $transactiontype;
	protected $url;
	protected $order;
	protected $provider;
	protected $url_ok;
	protected $url_ko;
    protected $paymethod;
	
	public function __construct($clave, $name, $code, $terminal, $transactiontype, $provider, $paymethod){
		$this->clave = $clave;
		$this->name = $name;
		$this->code = $code;
		$this->terminal = $terminal;
		$this->transactiontype = $transactiontype;
		$this->provider = $provider;
        $this->paymethod = $paymethod;
	}
	
	public function getSignature($amount, $currency = 978){

        if(!$this->getOrder()){
            throw new \Exception('Debes establecer $order via setOrder, antes de llamar a '.__FUNCTION__);
        }

		$message = ( $amount * 100 ).$this->getOrder().$this->code.$currency.$this->transactiontype.$this->url.$this->clave;
		return strtoupper(sha1($message));
	}


    public function getSingatureResponse($amount, $orderId, $response, $currency = 978){
        $message = ( $amount * 100 ).$orderId.$this->code.$currency.$response.$this->clave;
        return strtoupper(sha1($message));
    }
	
	public function fillTPV($entity, $amount, $currency = 978){

        if( !$this->url_ok  ){
            throw new Exception('Debes establecer el parametro url_ok via setUrlOK');
        }

        if( !$this->url_ko ){
            throw new Exception('Debes establecer el parametro url_ko via setUrlKO');
        }

        if( !$this->url ){
            throw new Exception('Debes establecer el parametro url via setUr');
        }

        if(!$this->getOrder()){
            throw new \Exception('Debes establecer $order via setOrder, antes de llamar a '.__FUNCTION__);
        }

		$entity->setDsMerchantTransactionType($this->transactiontype);
		$entity->setDsMerchantMerchantURL($this->url);
		$entity->setDsMerchantTerminal($this->terminal);
		$entity->setDsMerchantMerchantCode($this->code);
		$entity->setDsMerchantOrder($this->getOrder());
		$entity->setDsMerchantCurrency($currency);
		$entity->setDsMerchantAmount(( $amount * 100 ));
		$entity->setDsMerchantMerchantSignature($this->getSignature($amount, $currency));
		$entity->setProvider($this->provider);
		$entity->setDsMerchantUrlOK($this->url_ok);
		$entity->setDsMerchantUrlKO($this->url_ko);
		$entity->setDsMerchantMerchantName($this->name);
		$entity->setDsMerchantPayMethods($this->paymethod);
		
		return $entity;
	}
	
	public function setOrder($value){
		$this->order = $value;
	}
	
	public function getOrder(){
		return $this->order;
	}

    public function setUrlKO($value){
        $this->url_ko = $value;
    }

    public function setUrlOK($value){
        $this->url_ok = $value;
    }

    /**
     * @param mixed $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

}