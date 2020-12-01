<?php

use Common\Core\BaseDateTime;
use AccountService\UserProfile\UserProfile;
use AccountService\UserProfile\UserProfileCollection;
use AccountService\Account\UserType;

class User_profile_model extends Base_Model {

    private $tableName = 'user_profile';
    private $selectFields = 'user_profile.id
                ,user_profile.user_type
                ,user_profile.accountID
                ,user_profile.nick_name
                ,user_profile.name
                ,user_profile.mobile
                ,user_profile.avatar_url
                ,user_profile.status
                ,user_profile.user_group_id
                ,user_profile.remark
                ,user_profile.ip_address
                ,user_profile.channel
                ,user_profile.last_share_at
                ,user_profile.last_login_at
                ,user_profile.last_updated_at
                ,user_profile.created_at
                ,user_profile.created_by
                ,user_profile.updated_at
                ,user_profile.updated_by
                ,user_profile.deleted_at
                ,user_profile.deleted_by';

    public function map(stdClass $data) {

        $entity = new UserProfile();

        if (isset($data->id))
            $entity->setId($data->id);
        if (isset($data->user_type))
            $entity->setUserType($data->user_type);
        if (isset($data->accountID))
            $entity->setAccountID($data->accountID);
        if (isset($data->nick_name))
            $entity->setNickName($data->nick_name);
        if (isset($data->name))
            $entity->setName($data->name);
        if (isset($data->mobile))
            $entity->setMobile($data->mobile);
        if (isset($data->avatar_url))
            $entity->setAvatarUrl($data->avatar_url);
        if (isset($data->status))
            $entity->setStatus($data->status);

        if (isset($data->user_group_id))
            $entity->setUserGroupId($data->user_group_id);

        if (isset($data->remark))
            $entity->setRemark($data->remark);

        if (isset($data->ip_address))
            $entity->getIpAddress()->setIpAddressInteger($data->ip_address);

        if (isset($data->channel))
            $entity->setChannel($data->channel);

        if (isset($data->last_updated_at))
            $entity->setLastUpdatedAt(BaseDateTime::fromUnix($data->last_updated_at));

        if (isset($data->last_share_at))
            $entity->setLastShareAt(BaseDateTime::fromUnix($data->last_share_at));

        if (isset($data->last_login_at))
            $entity->setLastLoginAt(BaseDateTime::fromUnix($data->last_login_at));

        if (isset($data->created_at))
            $entity->setCreatedAt(BaseDateTime::fromUnix($data->created_at));
        if (isset($data->created_by))
            $entity->setCreatedBy($data->created_by);
        if (isset($data->updated_at))
            $entity->setUpdatedAt(BaseDateTime::fromUnix($data->updated_at));
        if (isset($data->updated_by))
            $entity->setUpdatedBy($data->updated_by);
        if (isset($data->deleted_at))
            $entity->setDeletedAt(BaseDateTime::fromUnix($data->deleted_at));
        if (isset($data->deleted_by))
            $entity->setDeletedBy($data->deleted_by);

        return $entity;
    }

    public function insert(UserProfile $entity) {

        $this->db->set('ip_address', $entity->getIpAddress()->getInteger());
        $this->db->set('user_type', $entity->getUserType());
        $this->db->set('accountID', $entity->getAccountID());
        $this->db->set('nick_name', $entity->getNickName());
        $this->db->set('name', $entity->getName());
        $this->db->set('mobile', $entity->getMobile());
        $this->db->set('avatar_url', $entity->getAvatarUrl());
        $this->db->set('status', $entity->getStatus());

        $this->db->set('user_group_id', $entity->getUserGroupId());

        $this->db->set('remark', $entity->getRemark());

        if($entity->getLastLoginAt() && !$entity->getLastLoginAt()->isNull())
            $this->db->set('last_login_at', $entity->getLastLoginAt()->getUnix());
        if($entity->getLastShareAt() && !$entity->getLastShareAt()->isNull())
            $this->db->set('last_share_at', $entity->getLastShareAt()->getUnix());
        if($entity->getLastUpdatedAt() && !$entity->getLastUpdatedAt()->isNull())
            $this->db->set('last_updated_at', $entity->getLastUpdatedAt()->getUnix());

        if($entity->getChannel()){
            $this->db->set('channel', $entity->getChannel());
        }
        
        $this->db->set('created_at', BaseDateTime::now()->getUnix());
        $this->db->set('created_by', $entity->getCreatedBy());
        if ($this->db->insert($this->tableName)) {
            $id = $this->db->insert_id();
            $entity->setId($id);
//            $sql = $this->db->last_query();
//            log_message("debug", "User_profile_model -> insert sql = $sql");
            return $entity;
        }
        return false;
    }

    public function delete(UserProfile $entity, $isLogic = true) {
        if ($isLogic) {
            $this->db->set('deleted_at', BaseDateTime::now()->getUnix());
            $this->db->set('deleted_by', $entity->getDeletedBy());
            $this->db->where('id', $entity->getId());
            if ($this->db->update($this->tableName)) {
                return true;
            }
        }
        else {
            $this->db->where('id', $entity->getId());
            if ($this->db->delete($this->tableName)) {
                return true;
            }
        }
        return false;
    }

    public function update(UserProfile $entity) {

        if ($entity->getIpAddress() && $entity->getIpAddress()->getInteger())
            $this->db->set('ip_address', $entity->getIpAddress()->getInteger());
        if ($entity->getUserType())
            $this->db->set('user_type', $entity->getUserType());
        if ($entity->getAccountID())
            $this->db->set('accountID', $entity->getAccountID());
        if ($entity->getNickName())
            $this->db->set('nick_name', $entity->getNickName());
        if ($entity->getName())
            $this->db->set('name', $entity->getName());
        if ($entity->getAvatarUrl())
            $this->db->set('avatar_url', $entity->getAvatarUrl());
        if ($entity->getStatus())
            $this->db->set('status', $entity->getStatus());
        if ($entity->getMobile())
            $this->db->set('mobile', $entity->getMobile());

        if ($entity->getUserGroupId())
            $this->db->set('user_group_id', $entity->getUserGroupId());

        if ($entity->getRemark())
            $this->db->set('remark', $entity->getRemark());

        if($entity->getLastLoginAt() && !$entity->getLastLoginAt()->isNull())
            $this->db->set('last_login_at', $entity->getLastLoginAt()->getUnix());
        
        if($entity->getLastShareAt() && !$entity->getLastShareAt()->isNull())
            $this->db->set('last_share_at', $entity->getLastShareAt()->getUnix());
        
        if($entity->getLastUpdatedAt() && !$entity->getLastUpdatedAt()->isNull())
            $this->db->set('last_updated_at', $entity->getLastUpdatedAt()->getUnix());

        //!== null 可以设置为空字符
        if($entity->getChannel() !== null){
            $this->db->set('channel', $entity->getChannel());
        }
        
        $this->db->set('updated_at', BaseDateTime::now()->getUnix());
        $this->db->set('updated_by', $entity->getUpdatedBy());

        $this->db->where('id', $entity->getId());

        if ($this->db->update($this->tableName)) {
//            $sql = $this->db->last_query();
//            log_message("debug", "User_profile_model -> update sql = $sql");
            return $entity;
        }
        return false;
    }

    public function updateUserGroupByIds($ids, $userGroupId){

        $this->db->set('user_group_id', $userGroupId);
        $this->db->set('updated_at', BaseDateTime::now()->getUnix());
        $this->db->where_in('id', $ids);
        
        if ($this->db->update($this->tableName)) {
            return true;
        }
        return false;
    }

    public function updateStatusByIds($ids, $status){

        $this->db->set('status', $status);
        $this->db->set('updated_at', BaseDateTime::now()->getUnix());
        $this->db->where_in('id', $ids);
        
        if ($this->db->update($this->tableName)) {
            return true;
        }
        return false;
    }

    public function select(UserProfile $entity, $orderBy = NULL, $limit = NULL, $page = NULL ) {

        $total = 0;
        $offset = NULL;
        if($limit != NULL && $page != NULL){
            $offset = ($page - 1) * $limit;
        }

        $this->db->start_cache(); //to cache active record query
        $this->db->select($this->selectFields);
        $this->db->from($this->tableName);
        $this->db->where("deleted_at", NUll);

        $id = $entity->getId();
        if (!empty($id)) {
            if(is_array($id)){
                $this->db->where_in('id', $id);
            }
            else{
                $this->db->where('id', $id);
            }
        }

        if ($entity->getIpAddress()->getInteger()) {
            $this->db->where('ip_address', $entity->getIpAddress()->getInteger());
        }
        if ($entity->getUserType()) {
            $this->db->where('user_type', $entity->getUserType());
        }
        if ($entity->getAccountID()) {
            $this->db->where('accountID', $entity->getAccountID());
        }
        if ($entity->getNickName()) {
            $this->db->where('nick_name', $entity->getNickName());
        }
        if ($entity->getName()) {
            $this->db->where('name', $entity->getName());
        }
        if ($entity->getAvatarUrl()) {
            $this->db->where('avatar_url', $entity->getAvatarUrl());
        }
        if ($entity->getStatus()) {
            $this->db->where('status', $entity->getStatus());
        }
        if ($entity->getMobile()) {
            $this->db->where('mobile', $entity->getMobile());
        }

        if ($entity->getUserGroupId()) {
            $this->db->where('user_group_id', $entity->getUserGroupId());
        }

        if ($entity->getRemark()) {
            $this->db->where('remark', $entity->getRemark());
        }
        
        $channel = $entity->getChannel();
        if(!empty($channel)){
            if(is_array($channel)){
                $this->db->where_in('channel', $channel);
            }
            else{
                $this->db->where('channel', $channel);
            }
        }

        if ($entity->getCreatedBy()) {
            $this->db->where('created_by', $entity->getCreatedBy());
        }
        if ($entity->getCreatedFrom() && !$entity->getCreatedFrom()->isNull()) {
            $this->db->where('created_at >= ', $entity->getCreatedFrom()->getUnix());
        }
        if ($entity->getCreatedTo() && !$entity->getCreatedTo()->isNull()) {
            $this->db->where('created_at <= ', $entity->getCreatedTo()->getUnix());
        }

        $this->db->stop_cache();

        $total = $this->db->count_all_results(); //to get total num of result w/o limit

        if($orderBy){
            $this->db->order_by($orderBy, null, false);
        }
        else{
            $this->db->order_by("created_at", "desc");
        }

        if($limit != NULL && $page != NULL){
            $this->db->limit($limit, $offset);
        }
        $query = $this->db->get();
        $this->db->flush_cache();

        if ($query->num_rows() > 0) {
            return $this->mapCollection($query->result(), new UserProfileCollection(), $total);
        }
        return false;
    }

    public function getById($id, $deleted = false ) {

        $this->db->select($this->selectFields);
        $this->db->from($this->tableName);
        if(!$deleted){
            $this->db->where('deleted_at', NULL);
        }
        $this->db->where('id', $id);

        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return $this->map($query->row());
        }
        return false;
    }

    public function findByIds(array $user_ids){
        
        $this->db->start_cache(); //to cache active record query
        $this->db->select($this->selectFields);
        $this->db->from($this->tableName);
        $this->db->where("deleted_at", NUll);
        
        //$this->db->where("user_type", UserType::USER);
        
        if (!empty($user_ids)) {
            if(is_array($user_ids)){
                $this->db->where_in('id', $user_ids);
            }
            else{
                $this->db->where('id', $user_ids);
            }
        }
        else{
            $this->db->where('1=2', null, false);
        }

        $this->db->order_by("id", "desc");

        $this->db->stop_cache();

        $total = $this->db->count_all_results(); //to get total num of result w/o limit

        $query = $this->db->get();
        $this->db->flush_cache();

        if ($query->num_rows() > 0) {
            return $this->mapCollection($query->result(), new UserProfileCollection(), $total);
        }
        return false;
    }
    
    /**
     * 查询管理员列表
     * @param UserProfile $filterUserProfile
     * @param type $role_code
     * @param type $orderBy
     * @param type $limit
     * @param type $page
     * @return boolean
     */
    public function selectAdminUserList(UserProfile $filterUserProfile, $orderBy = NULL, $limit = NULL, $page = NULL){
        
        $total = 0;
        $offset = NULL;
        if($limit != NULL && $page != NULL){
            $offset = ($page - 1) * $limit;
        }

        $this->db->start_cache(); //to cache active record query
//                ,u.mobile
//                ,u.mobile_verified_at
//                ,u.email
//                ,u.email_verified_at
//                ,u.gender
//                ,u.full_name
//                ,u.dob
//                ,u.avatar_url
//                ,u.verified_at
//                ,u.verified_by
        $this->db->select("u.id
                ,u.accountID
                ,u.user_type
                ,u.status
                ,u.nick_name
                ,u.name
                ,u.last_login_at
                ,u.channel
                ,u.created_at
                ,u.created_by
                ,u.updated_at
                ,u.updated_by
                ,u.deleted_at
                ,u.deleted_by
                ");
        $this->db->from("user_profile as u");
        $this->db->where("u.deleted_at", NUll);
        
        
        $id = $filterUserProfile->getId();
        if (!empty($id)) {
            if(is_array($id)){
                $this->db->where_in('u.id', $id);
            }
            else{
                if($id == "false"){
                    $this->db->where('1=2', null, false);
                }
                else{
                    $this->db->where('u.id', $id);
                }
            }
        }
        
        $userType = $filterUserProfile->getUserType();

        if (!empty($userType)) {
            if(is_array($userType)){
                $this->db->where_in('u.user_type', $userType);
            }
            else{
                $this->db->where('u.user_type', $userType);
            }
        }
        
        $status = $filterUserProfile->getStatus();
        if (!empty($status)) {
            if(is_array($status)){
                $this->db->where_in('u.status', $status);
            }
            else{
                $this->db->where('u.status', $status);
            }
        }
//        if ($filterUserProfile->getGender()) {
//            $this->db->where('u.gender', $filterUserProfile->getGender());
//        }
        if ($filterUserProfile->getNickName()) {
            $this->db->like('u.nick_name', $filterUserProfile->getNickName());
        }

        if ($filterUserProfile->getName()) {
            $this->db->like('u.name', $filterUserProfile->getName());
        }
        
        $channel = $filterUserProfile->getChannel();
        if(!empty($channel)){
            if(is_array($channel)){
                $this->db->where_in('channel', $channel);
            }
            else{
                $this->db->where('channel', $channel);
            }
        }

//        if ($filterUserProfile->getVerifiedBy()) {
//            $this->db->where('u.verified_by', $filterUserProfile->getVerifiedBy());
//        }
        if ($filterUserProfile->getCreatedFrom() && !$filterUserProfile->getCreatedFrom()->isNull()) {
            $this->db->where('u.created_at >= ', $filterUserProfile->getCreatedFrom()->getUnix());
        }
        if ($filterUserProfile->getCreatedTo() && !$filterUserProfile->getCreatedTo()->isNull()) {
            $this->db->where('u.created_at <= ', $filterUserProfile->getCreatedTo()->getUnix());
        }
        
        
        $this->db->stop_cache();

        $total = $this->db->count_all_results(); //to get total num of result w/o limit

        if($orderBy){
            $this->db->order_by($orderBy, null, false);
        }
        else{
            $this->db->order_by("u.created_at", "desc");
        }

        if($limit != NULL && $page != NULL){
            $this->db->limit($limit, $offset);
        }
        $query = $this->db->get();
        $this->db->flush_cache();

        if ($query->num_rows() > 0) {
            return $this->mapCollection($query->result(), new UserProfileCollection(), $total);
        }
        return false;
    }
    

    public function getAppUserListByAdmin(UserProfile $filterUserProfile, $parent = NULL, $orderBy = NULL, $limit = NULL, $page = NULL){
        $total = 0;
        $offset = NULL;
        if($limit != NULL && $page != NULL){
            $offset = ($page - 1) * $limit;
        }


        $todayDate     = date('Y-m-d');
        $yesterDayDate = date("Y-m-d",strtotime("-1 day"));


        $this->db->start_cache();
        $this->db->select(" up.id,
                            up.nick_name,
                            up.avatar_url,
                            up.mobile,
                            ifnull(e.balance, 0) as balance,
                            ifnull(today_uds.total_amount, 0) as today_amount,
                            ifnull(yesterday_uds.total_amount, 0) as yesterday_amount,
                            ifnull(us.total_amount, 0) as total_amount,
                            ifnull(us.cashout_times, 0) as cashout_times,
                            ifnull(us.cashout_amount, 0) as cashout_amount,
                            ifnull(today_uds.click_num, 0) as today_click_num,
                            ifnull(today_uds.click_amount, 0) as today_click_amount,
                            ifnull(us.click_num, 0) as total_click_num,
                            ifnull(us.click_amount, 0) as total_click_amount,
                            ifnull(us.news_share_num, 0) as total_news_share_num,
                            ifnull(today_uds.news_share_num, 0) as today_news_share_num,
                            ifnull(us.invite_num1, 0) as invite_num1,
                            ifnull(us.invite_num2, 0) as invite_num2,
                            uiup.nick_name as parent_name,
                            uiup.id as parent_id,
                            up.status,
                            up.remark,
                            ug.name as group_name,
                            up.user_group_id,
                            from_unixtime(up.created_at) as created_at,
                            up.ip_address,
                            ch.name as channel_name
                        ");

        // ll.user_agent,
        //                     ll.ip_address as login_ip,
        //                     ll.lat,
        //                     ll.long,
        //                     ll.address,
        //                     from_unixtime(ll.created_at) as last_login_at
        $this->db->from("user_profile up");
        $this->db->join("user_invite ui" , "ui.user_id = up.id", "left");
        $this->db->join("user_profile uiup" , "uiup.id = ui.parent1", "left");
        $this->db->join("ewallet e" , "(e.user_id = up.id and e.ewallet_type = 'G')", "left");
        $this->db->join("user_daily_statics today_uds" , "(today_uds.user_id = up.id and today_uds.date = '$todayDate')", "left");
        $this->db->join("user_daily_statics yesterday_uds" , "(yesterday_uds.user_id = up.id and yesterday_uds.date = '$yesterDayDate')", "left");
        $this->db->join("user_statics us" , "us.user_id = up.id", "left");
        $this->db->join("user_group ug" , "ug.id = up.user_group_id", "left");
        $this->db->join("channel ch" , "ch.id = up.channel", "left");
        // $this->db->join("(select * from (select * from login_log order by id desc limit 1000000) as v group by v.user_id order by v.id desc) as ll", "ll.user_id = up.id", "left");
        
        $this->db->where("up.deleted_at", NUll);

        if(!empty($parent)){
            $this->db->where("(uiup.nick_name like '%$parent%' or uiup.mobile like '%$parent%')");
        }

        $id = $filterUserProfile->getId();
        if (!empty($id)) {
            if(is_array($id)){
                $this->db->where_in('up.id', $id);
            }
            else{
                $this->db->where('up.id', $id);
            }
        }
        
        $userType = $filterUserProfile->getUserType();
        if (!empty($userType)) {
            $this->db->where('up.user_type', $userType);
        }
        
        $status = $filterUserProfile->getStatus();
        if (!empty($status)) {
            if(is_array($status)){
                $this->db->where_in('up.status', $status);
            }
            else{
                $this->db->where('up.status', $status);
            }
        }

        if ($filterUserProfile->getNickName()) {
            $this->db->like('up.nick_name', $filterUserProfile->getNickName());
        }

        if ($filterUserProfile->getName()) {
            $this->db->like('up.name', $filterUserProfile->getName());
        }

        if ($filterUserProfile->getMobile()) {
            $this->db->like('up.mobile', $filterUserProfile->getMobile());
        }
        
        $channel = $filterUserProfile->getChannel();
        if(!empty($channel)){
            // if(is_array($channel)){
            //     $this->db->where_in('up.channel', $channel);
            // }
            // else{
                $this->db->where('up.channel', $channel);
            // }
        }

        if ($filterUserProfile->getCreatedFrom() && !$filterUserProfile->getCreatedFrom()->isNull()) {
            $this->db->where('up.created_at >= ', $filterUserProfile->getCreatedFrom()->getUnix());
        }
        if ($filterUserProfile->getCreatedTo() && !$filterUserProfile->getCreatedTo()->isNull()) {
            $this->db->where('up.created_at <= ', $filterUserProfile->getCreatedTo()->getUnix());
        }
        
        if(!empty($filterUserProfile->getUserGroupId())){
            $this->db->where('up.user_group_id', $filterUserProfile->getUserGroupId());
        }

        $this->db->stop_cache();

        $total = $this->db->count_all_results();

        if($orderBy){
            $this->db->order_by($orderBy, null, false);
        }
        else{
            $this->db->order_by("up.created_at", "desc");
        }

        if($limit != NULL && $page != NULL){
            $this->db->limit($limit, $offset);
        }

        $query = $this->db->get();

        $this->db->flush_cache();

        if ($query->num_rows() > 0) {

            $data = array('result' => $query->result(), 'total' => $total);
            return $data;
        }
        return false;
    }  

    public function getAppUserRelationship($parentId, $level, $orderBy = NULL, $limit = NULL, $page = NULL){
        $total = 0;
        $offset = NULL;
        if($limit != NULL && $page != NULL){
            $offset = ($page - 1) * $limit;
        }


        $todayDate     = date('Y-m-d');
        $yesterDayDate = date("Y-m-d",strtotime("-1 day"));


        $this->db->start_cache();
        $this->db->select(" up.id,
                            up.nick_name,
                            up.avatar_url,
                            up.mobile,
                            ifnull(e.balance, 0) as balance,
                            ifnull(today_uds.total_amount, 0) as today_amount,
                            ifnull(yesterday_uds.total_amount, 0) as yesterday_amount,
                            ifnull(us.total_amount, 0) as total_amount,
                            ifnull(us.cashout_times, 0) as cashout_times,
                            ifnull(us.cashout_amount, 0) as cashout_amount,
                            ifnull(today_uds.click_num, 0) as today_click_num,
                            ifnull(today_uds.click_amount, 0) as today_click_amount,
                            ifnull(us.click_num, 0) as total_click_num,
                            ifnull(us.click_amount, 0) as total_click_amount,
                            ifnull(us.news_share_num, 0) as total_news_share_num,
                            ifnull(today_uds.news_share_num, 0) as today_news_share_num,
                            ifnull(us.invite_num1, 0) as invite_num1,
                            ifnull(us.invite_num2, 0) as invite_num2,
                            ifnull(us.master_contribution1, 0) as master_contribution1,
                            ifnull(us.master_contribution2, 0) as master_contribution2,
                            uiup.nick_name as parent_name,
                            uiup.id as parent_id,
                            up.status,
                            up.remark,
                            ug.name as group_name,
                            up.user_group_id,
                            from_unixtime(up.created_at) as created_at,
                            up.ip_address,
                            ll.user_agent,
                            ll.ip_address as login_ip,
                            ui.parent1,
                            ui.parent2,
                            case uiup.id
                                WHEN ui.parent1
                                    THEN '徒弟'
                                WHEN ui.parent2
                                    THEN '徒孙'
                            END relationship,

                            case uiup.id
                                WHEN ui.parent1
                                    THEN 'level_one'
                                WHEN ui.parent2
                                    THEN 'level_two'
                            END relationship_code
                        ");
        $this->db->from("user_profile up");
        $this->db->join("user_invite ui" , "ui.user_id = up.id", "left");
        $this->db->join("user_profile uiup" , "(uiup.id = ui.parent1 or uiup.id = ui.parent2)", "left");
        $this->db->join("ewallet e" , "(e.user_id = up.id and e.ewallet_type = 'G')", "left");
        $this->db->join("user_daily_statics today_uds" , "(today_uds.user_id = up.id and today_uds.date = '$todayDate')", "left");
        $this->db->join("user_daily_statics yesterday_uds" , "(yesterday_uds.user_id = up.id and yesterday_uds.date = '$yesterDayDate')", "left");
        $this->db->join("user_statics us" , "us.user_id = up.id", "left");
        $this->db->join("user_group ug" , "ug.id = up.user_group_id", "left");
        $this->db->join("(select * from (select * from login_log order by id desc limit 1000000) as v group by v.user_id order by v.id desc) as ll", "ll.user_id = up.id", "left");
        
        $this->db->where("up.deleted_at", NUll);
        $this->db->where("uiup.id", $parentId);
        $this->db->where('up.user_type', UserType::APPUSER);
        
        if(!empty($level)){
            if($level === 1){
                $this->db->where("ui.parent1 = uiup.id", null ,false);
            }
            else if($level === 2){
                $this->db->where("ui.parent2 = uiup.id", null ,false);
            }else if($level === 3){
                //有效徒弟
                $this->db->where("ui.parent1 = uiup.id", null ,false);
                $this->db->where("us.cashout_times > 0");

            }else if($level === 4){
                //有效徒孙
                $this->db->where("ui.parent2 = uiup.id", null ,false);
                $this->db->where("us.cashout_times > 0");
            }
        }
        
        $this->db->stop_cache();

        $total = $this->db->count_all_results();

        if($orderBy){
            $this->db->order_by($orderBy, null, false);
        }
        else{
            $this->db->order_by("up.created_at", "desc");
        }

        if($limit != NULL && $page != NULL){
            $this->db->limit($limit, $offset);
        }

        $query = $this->db->get();
        
        $sql = $this->db->last_query();
        log_message("debug", "getAppUserRelationship sql = $sql");

        $this->db->flush_cache();

        if ($query->num_rows() > 0) {

            $data = array('result' => $query->result(), 'total' => $total);
            return $data;
        }
        return false;
    }   

    public function getUserCountByDate($dateFrom, $dateTo){
        $this->db->select("count(*) as count");
        $this->db->from("user_profile up");

        if(!empty($dateFrom)){
            $this->db->where('up.created_at >= ', $dateFrom);
        }

        if(!empty($dateTo)){
            $this->db->where('up.created_at <= ', $dateTo);
        }

        $this->db->where("up.user_type", UserType::APPUSER);
        $query = $this->db->get();
        
        if ($query->num_rows() > 0) {
            return $query->row();    
        }
        
        return false;
    }   
}
