<?php

namespace Common\Core;

//use Common\AuditLog\AuditLogEventProducerV2;
//use Common\AuditLog\AuditLogAction;
use Common\Helper\RequestHeader;
use Common\Helper\ResponseHeader;
use Common\Helper\BaseDatabaseEvent;
use Common\Helper\BaseEventType;
use Common\Helper\BaseEventDispatcher;
use Symfony\Component\EventDispatcher\Event;

abstract class BaseService extends BasicBaseService
{

    protected $table_name = NULL;
    protected $auditLogName = NULL;
    protected $repository = NULL;
    protected $originalAuditLogEntity = NULL;

    function __construct($repository, $ipAddress = '127.0.0.1', $updatedBy = NULL)
    {
        parent::__construct($ipAddress, $updatedBy);
        
        $this->repository = $repository;
    }

    protected function getRepository()
    {
        return $this->repository;
    }

    public function startDBTransaction()
    {
        $result = $this->getRepository()->TransStart();
        BaseEventDispatcher::get()->dispatch(BaseEventType::DB_STARTED);
        return $result;
    }

    public function completeDBTransaction()
    {
        $result = $this->getRepository()->TransComplete();
        BaseEventDispatcher::get()->dispatch(BaseEventType::DB_COMPLETED);
        return $result;
    }

    public function rollbackDBTransaction()
    {
        $result = $this->getRepository()->TransRollback();
        BaseEventDispatcher::get()->dispatch(BaseEventType::DB_ROLLEDBACK);
        return $result;
    }

//    protected function setTableName($table_name)
//    {
//        $this->table_name = $table_name;
//        return $this;
//    }
//
//    protected function getTableName()
//    {
//        return $this->table_name;
//    }

//    /**
//     *
//     * @param type $auditLogName - table name most of the time
//     * @return boolean
//     */
//    public function setAuditLogName($auditLogName)
//    {
//        $this->auditLogName = $auditLogName;
//        return $this;
//    }
//
//    public function getAuditLogName()
//    {
//        return $this->auditLogName;
//    }

//    public function getProtectedValue($obj, $name)
//    {
//        $array = (array) $obj;
//        $prefix = chr(0) . '*' . chr(0);
//        return $array[$prefix . $name];
//    }

//    public function onDBPreUpdate(Event $event)
//    { //get existing
//        if (
//            $event instanceof BaseDatabaseEvent and
//            $this->originalAuditLogEntity == NULL
//        ) {
//            $this->originalAuditLogEntity = $this->getAuditLogEntity($event->getId());
//        }
//    }
//
//    public function onDBPostUpdate(Event $event)
//    { //insert audit log
//        if ($event instanceof BaseDatabaseEvent)
//            $this->sendAuditLogEvent($event->getId(), AuditLogAction::UPDATE, $this->originalAuditLogEntity);
//
//        $this->originalAuditLogEntity = NULL; //reset
//    }
//
//    public function onDBPreDelete(Event $event)
//    { //get existing
//        if (
//            $event instanceof BaseDatabaseEvent and
//            $this->originalAuditLogEntity == NULL
//        ) {
//            $this->originalAuditLogEntity = $this->getAuditLogEntity($event->getId());
//        }
//    }
//
//    public function onDBPostDelete(Event $event)
//    { //insert audit log
//        if ($event instanceof BaseDatabaseEvent)
//            $this->sendAuditLogEvent($event->getId(), AuditLogAction::DELETE, $this->originalAuditLogEntity);
//
//        $this->originalAuditLogEntity = NULL; //reset
//    }
//
//    public function onDBPostInsert(Event $event)
//    { //insert audit log
//        if ($event instanceof BaseDatabaseEvent)
//            $this->sendAuditLogEvent($event->getId(), AuditLogAction::CREATE);
//    }

//    /**
//     *
//     * rewrite this function to customize the entity for audit log
//     * @return type
//     */
//    public function getAuditLogEntity($id)
//    {
//        return $this->getRepository()->findById($id, true);
//    }
//
//    public function sendAuditLogEvent($id, $actionType, $oldValue = NULL)
//    {
//        if ($this->getAuditLogName() == NULL)
//            throw new \Exception("AuditLog: Audit log name is not defined! - " . get_class($this));
//
//        if (!$moduleCode = getenv("MODULE_CODE"))
//            throw new \Exception("AuditLog: MODULE_CODE is not defined");
//
//        if (!$newData = $this->getAuditLogEntity($id))
//            throw new \Exception("AuditLog: Failed to get new data - " . get_class($this));
//
//        $auditLog = new AuditLogEventProducerV2($this->getAuditLogName(), $this->getIpAddress());
//        $auditLog->setServiceName($moduleCode);
//        $auditLog->setUserAgent(RequestHeader::getByKey(ResponseHeader::FIELD_USER_AGENT));
//        if ($oldValue)
//            $auditLog->setOldValue($oldValue);
//        $auditLog->setNewValue($newData);
//        $auditLog->setActionType($actionType);
//        $auditLog->sendLogEvent(null);
//    }
    
}
