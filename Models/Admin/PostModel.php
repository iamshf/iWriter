<?php
declare(strict_types=1);
namespace iWriter\Models\Admin 
{
    use iWriter\Common\MyPdo;
    use iWriter\Common\Validate;
    use iWriter\Models\Admin\CategoryModel;
    class PostModel {
        private $_data;
        public function __construct(array $data = array()) {
            $this->_data = $data;
        }
        public function verifyId(): bool {
            return array_key_exists('id', $this->_data) && is_numeric($this->_data['id']) && $this->_data['id'] > 0;
        }
        public function verifyContent(): bool {
            return array_key_exists('content', $this->_data) && !empty($this->_data['content']);
        }
        public function getViews(): array {
            $views = array(
                'id' => -1,
                'title' => '', 
                'subtitle' => '', 
                'foreword' => '', 
                'content' => '', 
                'categories' => array(),
                'post_categories' => array()
            );
            !empty($categories = $this->getCategories()) && $views['categories'] = $categories;
            if($this->verifyId()) {
                !empty($post = $this->get()) && $views = array_merge($views, $post);
                if(!empty($post_categories = $this->getRelCategoryPost())) {
                    foreach($post_categories as $item) {
                        $views['post_categories'][] = $item['category_id'];
                    }
                }
            }
            return $views;
        }
        public function get(): ?array {
            $sqlWhere = $params = array();
            $count = $this->verifyCount() ? $this->_data['count'] : 20;
            if($this->verifyId()) {
                $sqlWhere[] = 'id = :id';
                $params[':id'] = array('value' => $this->_data['id'], 'dataType' => \PDO::PARAM_INT);
                $count = 1;
            }
            if($this->verifyTitle()) {
                $sqlWhere[] = 'title like :title';
                $params[':title'] = array('value' => ('%' . $this->_data['name'] . '%'), 'dataType' => \PDO::PARAM_STR);
            }
            if($this->verifySearch()) {
                $sqlWhere[] = '(title like :search or subtitle like :search or foreword like :search or content like :search)';
                $params[':search'] = array('value' => ('%' . $this->_data['search'] . '%'), 'dataType' => \PDO::PARAM_STR);
            }
            if($this->verifyCategoryId()) {
                $sqlWhere[] = 'id in (select post_id from rel_category_post where category_id = :category_id)';
                $params[':category_id'] = array('value' => $this->_data['category_id'], 'dataType' => \PDO::PARAM_INT);
            }
            if(($status = $this->verifyStatus() ? $this->_data['status'] : 1) > -1) {
                $sqlWhere[] = 'status = :status';
                $params[':status'] = array('value' => (int)$status, 'dataType' => \PDO::PARAM_INT);
            }
            if($this->verifyStartLtTime()) {
                $sqlWhere[] = 'gmt_modify < :start_lt_time';
                $params[':start_lt_time'] = array('value' => $this->_data['start_lt_time'], 'dataType' => \PDO::PARAM_STR);
            }

            $sql = 'select ' . ($this->verifyColumns() ? $this->_data['columns'] : 'id,title,subtitle,foreword,content') . ' from post ' . (!empty($sqlWhere) ? ' where ' . implode(' and ', $sqlWhere) : '') . ' order by gmt_modify desc';
            if(is_numeric($count)) {
                $sql .= ' limit :count';
                $params[':count'] = array('value' => (int)$count, 'dataType' => \PDO::PARAM_INT);
            }
            $sth = MyPdo::init('r')->query($sql, $params);
            $result = ($this->_data['count'] ?? 0) == 1 ? $sth->fetch(\PDO::FETCH_ASSOC) : $sth->fetchAll(\PDO::FETCH_ASSOC);
            return is_array($result) && !empty($result) ? $result : null;
        }
        public function save(): int {
            if($this->verifyId() && $this->isIdExists()) {
                $this->update();
                return (int)$this->_data['id'];
            }
            else {
                return $this->add();
            }
        }
        public function add(): int {
            $sql = 'insert into post (title, subtitle, foreword, content, gmt_add, gmt_modify, status) values (:title, :subtitle, :foreword, :content, from_unixtime(:gmt_add), from_unixtime(:gmt_modify), :status)';
            $params = array(
                ':title' => array('value' => ($this->verifyTitle() ? $this->_data['title'] : ''), 'dataType' => \PDO::PARAM_STR),
                ':subtitle' => array('value' => ($this->verifySubtitle() ?  $this->_data['subtitle'] : ''), 'dataType' => \PDO::PARAM_STR),
                ':foreword' => array('value' => ($this->verifyForeword() ?  $this->_data['foreword'] : ''), 'dataType' => \PDO::PARAM_STR),
                ':content' => array('value' => ($this->verifyContent() ?  $this->_data['content'] : ''), 'dataType' => \PDO::PARAM_STR),
                ':gmt_add' => array('value' => $_SERVER['REQUEST_TIME'], 'dataType' => \PDO::PARAM_STR),
                ':gmt_modify' => array('value' => $_SERVER['REQUEST_TIME'], 'dataType' => \PDO::PARAM_STR),
                ':status' => array('value' => (($this->verifyStatus() && $this->_data['status'] > -1) ? $this->_data['status'] : 1), 'dataType' => \PDO::PARAM_INT)
            );
            $myPdo = MyPdo::init();
            $myPdo->exec($sql, $params);
            $id = (int)($myPdo->lastInsertId());
            //保存分类
            $id > 0 && $this->verifyCategoryIds() && $this->saveRelCategoryPost($id);
            return $id;
        }
        public function update(): int {
            $sql = 'update post set gmt_modify = from_unixtime(:gmt_modify)';
            $params = array(
                ':id' => array('value' => $this->_data['id'], 'dataType' => \PDO::PARAM_INT),
                ':gmt_modify' => array('value' => $_SERVER['REQUEST_TIME'], 'dataType' => \PDO::PARAM_STR)
            );
            if($this->verifyTitle()) {
                $sql .= ', title = :title';
                $params[':title'] = array('value' => $this->_data['title'], 'dataType' => \PDO::PARAM_STR);
            }
            if($this->verifySubtitle()) {
                $sql .= ', subtitle = :subtitle';
                $params[':subtitle'] = array('value' => $this->_data['subtitle'], 'dataType' => \PDO::PARAM_STR);
            }
            if($this->verifyForeword()) {
                $sql .= ', foreword = :foreword';
                $params[':foreword'] = array('value' => $this->_data['foreword'], 'dataType' => \PDO::PARAM_STR);
            }
            if($this->verifyContent()) {
                $sql .= ', content = :content';
                $params[':content'] = array('value' => $this->_data['content'], 'dataType' => \PDO::PARAM_STR);
            }
            if($this->verifyStatus()) {
                $sql .= ', status = :status';
                $params[':status'] = array('value' => $this->_data['status'], 'dataType' => \PDO::PARAM_STR);
            }
            $sql .= ' where id = :id';
            $result = MyPdo::init()->exec($sql, $params);
            $this->verifyCategoryIds() && $this->saveRelCategoryPost((int)$this->_data['id']);
            return $result;
        }
        private function saveRelCategoryPost(int $post_id): bool {
            $model = new RelCategoryPostModel(array('post_id' => $post_id, 'category_ids' => $this->_data['category_ids']));
            return $model->save();
        }
        private function getCategories(): array {
            $model = new CategoryModel(array('deep' => '*'));
            $model->initReadDB();
            return $model->get();
        }
        private function getRelCategoryPost(): array {
            $model = new RelCategoryPostModel(array('post_id' => $this->_data['id'], 'columns' => 'category_id'));
            return $model->get();
        }

        private function verifyTitle(): bool {
            return array_key_exists('title', $this->_data);
        }
        private function verifySubtitle(): bool {
            return array_key_exists('subtitle', $this->_data);
        }
        private function verifyForeword(): bool {
            return array_key_exists('foreword', $this->_data);
        }
        private function verifySearch(): bool {
            return array_key_exists('search', $this->_data) && !empty($this->_data['search']);
        }
        private function verifyColumns(): bool {
            return array_key_exists('columns', $this->_data) && Validate::sqlParam($this->_data['columns']);
        }
        private function verifyStatus(): bool {
            return array_key_exists('status', $this->_data) && in_array($this->_data['status'], array(-1, 0, 1, 2));
        }
        private function verifyCount(): bool {
            return array_key_exists('count', $this->_data) && (is_numeric($this->_data['count']) || $this->_data['count'] == '*');
        }
        private function verifyCategoryIds(): bool {
            return array_key_exists('category_ids', $this->_data) && is_array($this->_data['category_ids']);
        }
        private function verifyCategoryId(): bool {
            return array_key_exists('category_id', $this->_data) && is_numeric($this->_data['category_id']) && $this->_data['category_id'] > 0;
        }
        private function verifyStartLtTime(): bool {
            return array_key_exists('start_lt_time', $this->_data) && Validate::date($this->_data['start_lt_time']);
        }
        private function isIdExists(): bool {
            return MyPdo::init('r')->isExists(
                'select 1 from post where id = :id', 
                array(':id' => array('value' => $this->_data['id'], 'dataType' => \PDO::PARAM_INT))
            );
        }
    }
}
