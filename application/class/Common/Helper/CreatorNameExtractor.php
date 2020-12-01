<?php

namespace Common\Helper;

use Common\Core\BaseEntityCollection;
use Common\Microservice\AccountService\AccountServiceFactory;
use Common\Microservice\AccountService\User;

class CreatorNameExtractor
{

    public static function extract(BaseEntityCollection $collection)
    {
        $e = new static();
        $ids = $e->getIds($collection);

        $users = new BaseEntityCollection();
        if (count($ids) > 0) {
            $accServ = AccountServiceFactory::build();
            if ($userCol = $accServ->getUsers($ids))
                $users = $userCol;
        }

        $e->_addDataPatchUser($users);
        return $e->mapNames($collection, $users);
    }

    protected function getIds(BaseEntityCollection $collection)
    {
        $ids = array();
        foreach ($collection as $entity) {
            if ($entity->getCreatedBy() != NULL and $entity->getCreatedBy() != "0")
                $ids[] = $entity->getCreatedBy();

            if ($entity->getUpdatedBy() != NULL and $entity->getUpdatedBy() != "0")
                $ids[] = $entity->getUpdatedBy();
        }

        return $ids;
    }

    protected function mapNames(BaseEntityCollection $collection, BaseEntityCollection $users)
    {
        $collection->joinCreatorName($users);
        $collection->joinUpdaterName($users);
        return $collection;
    }

    protected function _addDataPatchUser(BaseEntityCollection $users)
    {
        $datapatchUser = new User();
        $datapatchUser->setId("0");
        $datapatchUser->setName('System');
        $users->addData($datapatchUser);
        return $users;
    }
}
