<?php
namespace iWriter\Models\Admin {
    use iWriter\Common\MyPdo;
    class CategoryModel{
        private $_data;
        private $_myPdo;
        public function __construct($data = array()){
            $this->_data = $data;
        }
        public function __set($k, $v){
            property_exists($this, $k) ? $this->$k = $v : $this->_data[$k] = $v;
        }
        public function verifyId(){
            return array_key_exists('id', $this->_data) && is_numeric($this->_data['id']) && $this->_data['id'] > 0;
        }
        public function verifyPID() {
            if(array_key_exists('pid', $this->_data) && is_numeric($this->_data['pid'])){
                if($this->_data['pid'] > 0) {
                    $params = array(':pid' => array('value' => $this->_data['pid'], 'dataType' => \PDO::PARAM_INT));
                    return $this->_myPdo->isExists('select id from category where id = :pid limit 1', $params);
                }
                return $this->_data['pid'] == 0;
            }

            return false;
        }
        public function verifyName(){
            return array_key_exists('name', $this->_data) && !empty($this->_data['name']);
        }
        public function verifyRemark(){
            return array_key_exists('remark', $this->_data);
        }
        public function verifyBeforeId() {
            if(array_key_exists('before_id', $this->_data) && is_numeric($this->_data['before_id']) && $this->_data['before_id'] > 0){
                $sql = 'select @max_rv := rv,@pid := pid,@deep := deep from category where id =  :id limit 1';
                $params = array(':id' => array('value' => $this->_data['before_id'], 'dataType' => \PDO::PARAM_INT));
                
                return $this->_myPdo->isExists($sql, $params);
            }
            return false;
        }
        public function verifyAfterId() {
            if(array_key_exists('after_id', $this->_data) && is_numeric($this->_data['after_id']) && $this->_data['after_id'] > 0){
                $sql = 'select @min_lv := lv,@pid := pid,@deep := deep from category where id =  :id limit 1';
                $params = array(':id' => array('value' => $this->_data['after_id'], 'dataType' => \PDO::PARAM_INT));

                return $this->_myPdo->isExists($sql, $params);
            }
            return false;
        }
        public function lockTable(){
            $this->_myPdo = MyPdo::init();
            $this->_myPdo->exec('lock table category write');
            return true;
        }
        public function unlockTable(){
            $this->_myPdo->exec('unlock tables');
        }
        public function initReadDB(){
            $this->_myPdo = MyPdo::init('r');
        }
        public function isExists(){
            $sql = 'select id from category where id = :id limit 1';
            $params = array(':id' => array('value' => $this->_data['id'], 'dataType' => \PDO::PARAM_INT));
            $this->initReadDB();
            return $this->_myPdo->isExists($sql, $params);
        }

        public function add() {
            if($this->verifyAfterId()) {
                $this->_myPdo->exec('update category set lv = lv + 2 where lv >= @min_lv');
                $this->_myPdo->exec('update category set rv = rv + 2 where rv > @min_lv');

                $sql = 'insert into category (name, pid, remark, enabled, lv, rv,deep) values (:name, @pid, :remark, :enabled, @min_lv, @min_lv + 1, @deep)';
            }
            else {
                if(!$this->verifyBeforeId()) {
                    if(!$this->verifyPID() || $this->_data['pid'] == 0){
                        $this->_myPdo->exec('select @max_rv := ifnull(max(rv), 0),@pid := 0,@deep := 1 from category where pid = 0');
                    }
                    else {
                        $params = array(':pid' => array('value' => $this->_data['pid'], 'dataType' => \PDO::PARAM_INT));
                        $this->_myPdo->exec('select @max_rv := rv - 1,@pid := :pid,@deep := deep + 1 from category where id = :pid', $params);
                    }
                }

                $this->_myPdo->exec('update category set lv = lv + 2 where lv > @max_rv');
                $this->_myPdo->exec('update category set rv = rv + 2 where rv > @max_rv');

                $sql = 'insert into category (name, pid, remark, enabled, lv, rv, deep) values (:name, @pid, :remark, :enabled, @max_rv + 1, @max_rv + 2, @deep)';
            }

            $params = array(
                ':name' => array('value' => $this->_data['name'], 'dataType' => \PDO::PARAM_STR),
                ':remark' => array('value' => ($this->verifyRemark() ? $this->_data['remark'] : ''), 'dataType' => \PDO::PARAM_STR),
                ':enabled' => array('value' => ($this->verifyEnabled() && $this->_data['enabled'] > -1 ? $this->_data['enabled'] : '1'), 'dataType' => \PDO::PARAM_STR)
            );
            $this->_myPdo->exec($sql, $params);
            return $this->_myPdo->lastInsertId();
        }
        public function get() {
            $columns = $this->verifyColumns() ? $this->_data['columns'] : 'id,name,pid,lv,rv,deep';
            $sql = 'select ' . $columns . ' from category ';
            $params = array();
            $sqlWhere = array();

            if($this->verifyDeep()){
                if($this->_data['deep'] == '0') {
                    $this->getBrotherNode($sql, $sqlWhere, $params);
                }
                else if(mb_substr($this->_data['deep'],0,1, 'UTF-8') == '-'){
                    $this->getAncestorNode($sql, $sqlWhere, $params);
                }
                else {
                    if(!$this->verifyPID() && !$this->verifyId()) { $this->_data['pid'] = 0; }
                    $this->getDescendantNode($sql, $sqlWhere, $params);
                }
            }
            else {
                if($this->verifyId()){
                    $sqlWhere[] = 'id = :id';
                    $params[':id'] = array('value' => $this->_data['id'], 'dataType' => \PDO::PARAM_INT);
                    $this->_data['count'] = 1;
                }
                if($this->verifyPID()){
                    $sqlWhere[] = 'pid = :pid';
                    $params[':pid'] = array('value' => $this->_data['pid'], 'dataType' => \PDO::PARAM_INT);
                }
            }
            if(!$this->verifyEnabled()) {
                $this->_data['enabled'] = '1';
            }
            if($this->verifyEnabled() && $this->_data['enabled'] > -1) {
                $sqlWhere[] = 'enabled = :enabled';
                $params[':enabled'] = array('value' => $this->_data['enabled'], 'dataType' => \PDO::PARAM_STR);
            }
            
            if(count($sqlWhere) > 0) {
                $sql .= ' where ' . implode(' and ', $sqlWhere);
            }
            $sql .= ' order by lv asc';
            if($this->verifyCount() && $this->_data['count'] != '0'){
                $sql .= ' limit :count';
                $params[':count'] = array('value' => (int)$this->_data['count'], 'dataType' => \PDO::PARAM_INT);
            }

            $result = $this->_myPdo->query($sql, $params);
            return $this->verifyCount() && $this->_data['count'] == 1 ? array($result->fetch(\PDO::FETCH_ASSOC)) : $result->fetchAll(\PDO::FETCH_ASSOC);

        }
        public function update() {
            $result = 0;
            $values = array();
            $params = array(':id' => array('value' => $this->_data['id'], 'dataType' => \PDO::PARAM_INT));
            if($this->verifyName()){
                $values[] = 'name = :name';
                $params[':name'] = array('value' => $this->_data['name'], 'dataType' => \PDO::PARAM_STR);
            }
            if($this->verifyRemark()) {
                $values[] = 'remark = :remark';
                $params[':remark'] = array('value' => $this->_data['remark'], 'dataType' => \PDO::PARAM_STR);
            }
            if(count($values) > 0) {
                $sql = 'update category set ' . implode(',', $values) . ' where id = :id';
                $result = $this->_myPdo->exec($sql, $params);
            }
            return $result == 0 || $result == 1;
        }
        public function delete() {
            $row = $this->_myPdo->query(
                'select id,lv,rv,enabled from category where id = :id and enabled = :enabled limit 1',
                array(
                    ':id' => array('value' => $this->_data['id'], 'dataType' => \PDO::PARAM_INT),
                    ':enabled' => array('value' => '1', 'dataType' => \PDO::PARAM_STR),
                )
            )->fetch(\PDO::FETCH_ASSOC);

            if($row !== false) {
                $this->_myPdo->exec(
                    'select @count := count(id) from category where lv between :lv and :rv and enabled = :enabled',
                    array(
                        ':lv' => array('value' => $row['lv'], 'dataType' => \PDO::PARAM_INT),
                        ':rv' => array('value' => $row['rv'], 'dataType' => \PDO::PARAM_INT),
                        ':enabled' => array('value' => '1', 'dataType' => \PDO::PARAM_STR)
                    )
                );
                $result = $this->_myPdo->exec(
                    'update category set enabled = :disabled, lv = 0, rv = 0 where lv between :lv and :rv and enabled = :enabled',
                    array(
                        ':disabled' => array('value' => '0', 'dataType' => \PDO::PARAM_STR),
                        ':lv' => array('value' => $row['lv'], 'dataType' => \PDO::PARAM_INT),
                        ':rv' => array('value' => $row['rv'], 'dataType' => \PDO::PARAM_INT),
                        ':enabled' => array('value' => '1', 'dataType' => \PDO::PARAM_STR)
                    )
                );
                $this->_myPdo->exec(
                    'update category set lv = (lv - (2 * @count)) where lv > :max_rv', 
                    array(':max_rv' => array('value' => $row['rv'], 'dataType' => \PDO::PARAM_INT))
                );
                $this->_myPdo->exec(
                    'update category set rv = (rv - (2 * @count)) where rv > :max_rv',
                    array(':max_rv' => array('value' => $row['rv'], 'dataType' => \PDO::PARAM_INT))
                );
                return $result > 0;
            }
            return true;
        }
        //恢复删除的节点
        public function restore(){
            $row = $this->_myPdo->query(
                'select id,lv,rv,enabled from category where id = :id and enabled = :enabled limit 1',
                array(
                    ':id' => array('value' => $this->_data['id'], 'dataType' => \PDO::PARAM_INT),
                    ':enabled' => array('value' => '0', 'dataType' => \PDO::PARAM_STR),
                )
            )->fetch(\PDO::FETCH_ASSOC);
            if($row !== false) {
                if($this->verifyAfterId()) {
                    $this->_myPdo->exec('update category set lv = lv + 2 where lv >= @min_lv');
                    $this->_myPdo->exec('update category set rv = rv + 2 where rv > @min_lv');

                    $sql = 'update category set enabled = :enabled, pid = @pid, lv = @min_lv, rv = @min_lv + 1, deep = @deep where id = :id';
                }
                else {
                    if(!$this->verifyBeforeId()) {
                        if(!$this->verifyPID() || $this->_data['pid'] == 0){
                            $this->_myPdo->exec('select @max_rv := ifnull(max(rv), 0),@pid := 0,@deep := 1 from category where pid = 0');
                        }
                        else {
                            $params = array(':pid' => array('value' => $this->_data['pid'], 'dataType' => \PDO::PARAM_INT));
                            $this->_myPdo->exec('select @max_rv := rv - 1,@pid := :pid,@deep := deep + 1 from category where id = :pid', $params);
                        }
                    }
                    $this->_myPdo->exec('update category set lv = lv + 2 where lv > @max_rv');
                    $this->_myPdo->exec('update category set rv = rv + 2 where rv > @max_rv');

                    $sql = 'update category set enabled = :enabled, pid = @pid, lv = @max_rv + 1, rv = @max_rv + 2, deep = @deep where id = :id';
                }
                $params = array(
                    ':id' => array('value' => $this->_data['id'], 'dataType' => \PDO::PARAM_INT),
                    ':enabled' => array('value' => '1', 'dataType' => \PDO::PARAM_STR)
                );
                return $this->_myPdo->exec($sql, $params) > 0;
            }
            return true;
        }

        private function verifyEnabled(){
            return array_key_exists('enabled', $this->_data) && ($this->_data['enabled'] == '0' || $this->_data['enabled'] == '1' || $this->_data['enabled'] == '-1');
        }
        private function verifyColumns(){
            return array_key_exists('columns', $this->_data) && preg_match('/^[a-z]+(,(\ ?)[a-z]+)*$/', $this->_data['columns']);
        }
        private function verifyCount(){
            return array_key_exists('count', $this->_data) && is_numeric($this->_data['count']);
        }
        private function verifyDeep(){
            return array_key_exists('deep', $this->_data) && (is_numeric($this->_data['deep']) || $this->_data['deep'] == '*' || $this->_data['deep'] == '-*');
        }
        private function verifyWidthContents(){
            return array_key_exists('with_contents', $this->_data) && $this->_data['with_contents'] == 1;
        }
        private function getBrotherNode(&$sql, &$sqlWhere, &$params) {
            if($this->verifyPID()){
                $sqlWhere[] = 'pid = :pid';
                $params[':pid'] = array('value' => $this->_data['pid'], 'dataType' => \PDO::PARAM_INT);
            }
            if($this->verifyId()){
                $sqlWhere[] = 'pid = (select pid from category where id = :id limit 1)';
                $params[':id'] = array('value' => $this->_data['id'], 'dataType' => \PDO::PARAM_INT);
            }
        }
        private function getFatherNode(&$sql, &$sqlWhere, &$params) {
            if($this->verifyId()){
                $sqlWhere[] = 'id = (select pid from category where id = :id limit 1)';
                $params[':id'] = array('value' => $this->_data['id'], 'dataType' => \PDO::PARAM_INT);
            }
            if($this->verifyPID()){
                $sqlWhere[] = 'id = :pid';
                $params[':pid'] = array('value' => $this->_data['pid'], 'dataType' => \PDO::PARAM_INT);
            }
            $this->_data['count'] = 1;
        }
        private function getAncestorNode(&$sql, &$sqlWhere, &$params){
            if($this->_data['deep'] == '-1') {
                $this->getFatherNode($sql, $sqlWhere, $params);
            }
            else {
                if($this->verifyId()) {
                    $this->_myPdo->exec(
                        'select @lv := lv, @rv := rv, @deep := deep from category where id = :id limit 1', 
                        array(':id' => array('value' => $this->_data['id'], 'dataType' => \PDO::PARAM_INT))
                    );
                }
                else {
                    $this->_myPdo->exec('select @lv := min(lv), @rv := max(rv), @deep := 1 from category');
                }
                $sqlWhere[] = 'lv < @lv and rv > @rv';
                if(is_numeric($this->_data['deep'])) {
                    $sqlWhere[] = 'cast(deep as signed) - cast(@deep as signed) >= :deep';
                    $params[':deep'] = array('value' => $this->_data['deep'], 'dataType' => \PDO::PARAM_INT);
                }
            }
        }
        private function getSonNode(&$sql, &$sqlWhere, &$params) {
            if($this->verifyId()){
                $sqlWhere[] = 'pid = :id';
                $params[':id'] = array('value' => $this->_data['id'], 'dataType' => \PDO::PARAM_INT);
            }
            if($this->verifyPID()) {
                $sqlWhere[] = 'pid = :pid';
                $params[':pid'] = array('value' => $this->_data['pid'], 'dataType' => \PDO::PARAM_INT);
            }
        }
        private function getDescendantNode(&$sql, &$sqlWhere, &$params){
            if($this->_data['deep'] == 1){
                $this->getSonNode($sql, $sqlWhere, $params);
            }
            else {
                if($this->verifyId()) {
                    $this->_myPdo->exec(
                        'select @lv := lv, @rv := rv, @deep := deep from category where id = :id limit 1', 
                        array(':id' => array('value' => $this->_data['id'], 'dataType' => \PDO::PARAM_INT))
                    );
                    $sqlWhere[] = 'lv > @lv and rv < @rv';
                }
                else {
                    $this->_myPdo->exec('select @deep := 1 from category');
                }
                if(is_numeric($this->_data['deep'])) {
                    $sqlWhere[] = 'cast(deep as signed) - cast(@deep as signed) <= :deep';
                    $params[':deep'] = array('value' => $this->_data['deep'], 'dataType' => \PDO::PARAM_INT);
                }
            }
        }
    }
}
