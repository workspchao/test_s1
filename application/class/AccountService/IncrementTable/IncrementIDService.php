<?php

namespace AccountService\IncrementTable;

class IncrementIDService extends IncrementTableService
{
    protected static $_instance = NULL;
    private $no_of_digit = 6;
    private $prefix = null;
    private $suffix = null;
    private $incrementDate = null;
    

    public static function build() {
        if (self::$_instance == NULL) {
            $_ci = &get_instance();
            $_ci->load->model('incrementtable/Increment_table_model');
            self::$_instance = new IncrementIDService($_ci->Increment_table_model);
        }
        return self::$_instance;
    }

    public function setNoOfDigit($number)
    {
        $this->no_of_digit = $number;
        return true;
    }

    public function getNoOfDigit()
    {
        return $this->no_of_digit;
    }

    public function setPrefix($prefix){
        $this->prefix = $prefix;
        return $this;
    }
    public function getPrefix(){
        return $this->prefix;
    }
    
    public function setSuffix($suffix){
        $this->suffix = $suffix;
        return $this;
    }
    public function getSuffix(){
        return $this->suffix;
    }
    
    public function getIncrementDate(){
        return $this->incrementDate;
    }

    /**
     * 
     * @param type $attribute
     * @param type $continuous_mode (false:reset by daily, true:continue always)
     * @return type (eg. [prefix][yyyy][mm][dd][suffix]000001)
     */
    public function getIncrementID($attribute, $continuous_mode = false)
    {
        return $this->_getIncrementIDString($attribute, false, $continuous_mode);
    }

    /**
     * 
     * @param type $attribute
     * @param type $continuous_mode (false:reset by daily, true:continue always)
     * @return type (eg. 000001)
     */
    public function getRawIncrementID($attribute, $continuous_mode = false)
    {
        return $this->_getIncrementIDString($attribute, true, $continuous_mode);
    }

    public function getRawIncrementIDResetAt($attribute, $reset_point)
    {
        $this->startDBTransaction();
        if (!$data = $this->_getIncrementId($attribute)) {
            $inc = IncrementTable::create($attribute);
            if (!$data  = $this->getRepository()->insert($inc))
                return false;
        }

        if ($data->getValue() >= $reset_point) {
            $incNumber = str_pad(1, $this->getNoOfDigit(), '0', STR_PAD_LEFT);
            if (!$this->_resetIncrementId($data)) {
                $this->rollbackDBTransaction();
                return false;
            }
        } else {
            $incNumber = $this->_increaseContinuous($data);
        }

        $this->completeDBTransaction();

        return $incNumber;
    }

    /*
     * This function to get increment id for the given attribute ID
     * Only when raw data is requested, If the given attribute ID does not exists, a new record will be created.
     */
    protected function _getIncrementIDString($attribute, $raw = true, $continuous_mode = false)
    {
        $this->startDBTransaction();
        if (!$data = $this->_getIncrementId($attribute)) { //insert new record
            if ($raw) {
                $inc = IncrementTable::create($attribute);
                if (!$data  = $this->getRepository()->insert($inc))
                    return false;
            } else
                return false;
        }

        if (!$continuous_mode)
            $incNumber = $this->_increaseByDailyReset($data);
        else
            $incNumber = $this->_increaseContinuous($data);

        $this->completeDBTransaction();

        $this->prefix = $data->getPrefix();
        $this->suffix = $data->getSuffix();
        $this->incrementDate = date('Y-m-d H:i:s');
        
        if ($raw)
            return $incNumber;
        else
            return $data->getPrefix() . date("Y") . date("m") . date("d") . $data->getSuffix() . $incNumber;
    }

    protected function _increaseByDailyReset(IncrementTable $data)
    {
        $toDate     = date('Y-m-d H:i:s');
        $todayDay   = date("d", strtotime($toDate));
        $todayMonth = date("m", strtotime($toDate));
        $todayYear  = date("Y", strtotime($toDate));

        $lastIncDate  = $data->getLastIncrementDate()->getString();
        $lastIncDay   = date("d", strtotime($lastIncDate));
        $lastIncMonth = date("m", strtotime($lastIncDate));
        $lastIncYear  = date("Y", strtotime($lastIncDate));

        if ($todayYear > $lastIncYear) {
            $incNumber = str_pad(1, $this->getNoOfDigit(), '0', STR_PAD_LEFT);
            if (!$this->_resetIncrementId($data)) {
                $this->rollbackDBTransaction();
                return false;
            }
        } else {
            if ($todayMonth > $lastIncMonth) {
                $incNumber = str_pad(1, $this->getNoOfDigit(), '0', STR_PAD_LEFT);
                if (!$this->_resetIncrementId($data)) {
                    $this->rollbackDBTransaction();
                    return false;
                }
            } else {
                if ($todayDay > $lastIncDay) {
                    $incNumber = str_pad(1, $this->getNoOfDigit(), '0', STR_PAD_LEFT);
                    if (!$this->_resetIncrementId($data)) {
                        $this->rollbackDBTransaction();
                        return false;
                    }
                } else {
                    $incNumber = str_pad($data->getValue(), $this->getNoOfDigit(), '0', STR_PAD_LEFT);
                    if (!$this->_setIncrementId($data, 1)) {
                        $this->rollbackDBTransaction();
                        return false;
                    }
                }
            }
        }

        return $incNumber;
    }

    protected function _increaseContinuous(IncrementTable $data)
    {
        $incNumber = str_pad($data->getValue(), $this->getNoOfDigit(), '0', STR_PAD_LEFT);
        if (!$this->_setIncrementId($data, 1)) {
            $this->rollbackDBTransaction();
            return false;
        }

        return $incNumber;
    }

    protected function _getIncrementId($attribute)
    {
        return $this->getRepository()->findByAttribute($attribute);
    }

    /**
     * reset value number to 2
     * @param \AccountService\IncrementTable\IncrementTable $data
     * @return boolean
     */
    protected function _resetIncrementId(IncrementTable $data)
    {
        $data->setValue(2);
        $data->setUpdatedBy($this->getUpdatedBy());

        if ($this->getRepository()->updateIncrementNumber($data)) {
            return true;
        }

        return false;
    }

    /**
     * add value number +value
     * @param \AccountService\IncrementTable\IncrementTable $data
     * @param type $value
     * @return boolean
     */
    protected function _setIncrementId(IncrementTable $data, $value)
    {
        $data->setUpdatedBy($this->getUpdatedBy());

        if ($this->getRepository()->addIncrementNumber($data, $value)) {
            return true;
        }

        return false;
    }
}
