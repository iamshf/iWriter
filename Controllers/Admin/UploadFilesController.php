<?php
/**
 * Description of Js
 *
 * @author shf
 */
declare(strict_types=1);
namespace iWriter\Controllers\Admin 
{
    use iWriter\Controllers\Resource;
    use iWriter\Models\Admin\UploadModel;
    class UploadFilesController extends Resource{
        public function exec(?string $methodName = NULL) {
            $current_path = '../Uploads/' . (empty($this->_request->_data['dir']) ? '' : $this->_request->_data['dir'] . '/') . (empty($this->_request->_data['path']) ? '' : $this->_request->_data['path']);
            $current_url = '/upload/' . (empty($this->_request->_data['dir']) ? '' : $this->_request->_data['dir'] . '/') . (empty($this->_request->_data['path']) ? '' : $this->_request->_data['path']);
            $current_dir_path = $this->_request->_data['path'];
            $moveup_dir_path = preg_replace('/(.*?)[^\/]+\/$/', '$1', $current_dir_path);
            $ext_arr = array('gif', 'jpg', 'jpeg', 'png', 'bmp');

            $expire = \Conf::CACHE_EXPIRE * 365;//缓存一天
            $this->_headers[] = 'Content-Type: application/json;';

            $file_list = array();
            if($handle = opendir($current_path)) {
                $i = 0;
                while(($filename = readdir($handle)) !== false) {
                    if($filename{0} == '.') {continue; }
                    $file = $current_path . $filename;
                    if (is_dir($file)) {
                        $file_list[$i]['is_dir'] = true; //是否文件夹
                        $file_list[$i]['has_file'] = (count(scandir($file)) > 2); //文件夹是否包含文件
                        $file_list[$i]['filesize'] = 0; //文件大小
                        $file_list[$i]['is_photo'] = false; //是否图片
                        $file_list[$i]['filetype'] = ''; //文件类别，用扩展名判断
                    } else {
                        $file_list[$i]['is_dir'] = false;
                        $file_list[$i]['has_file'] = false;
                        $file_list[$i]['filesize'] = filesize($file);
                        $file_list[$i]['dir_path'] = '';
                        $file_ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                        $file_list[$i]['is_photo'] = in_array($file_ext, $ext_arr);
                        $file_list[$i]['filetype'] = $file_ext;
                    }
                    $file_list[$i]['filename'] = $filename; //文件名，包含扩展名
                    $file_list[$i]['datetime'] = date('Y-m-d H:i:s', filemtime($file)); //文件最后修改时间
                    $i++;
                }
                closedir($handle);
            }
            usort($file_list, array($this, 'cmp_func'));
            $result = array(
                'moveup_dir_path' => $moveup_dir_path,
                'current_dir_path' => $current_dir_path,
                'current_url' => $current_url,
                'rotal_count' => count($file_list),
                'file_list' => $file_list
            );

            $this->_body = json_encode($result);
        }
        private function cmp_func($a, $b) {
            $order = $this->_request->_data['order'];
            if ($a['is_dir'] && !$b['is_dir']) {
                return -1;
            } 
            else if (!$a['is_dir'] && $b['is_dir']) {
                return 1;
            } 
            else {
                if ($order == 'size') {
                    if ($a['filesize'] > $b['filesize']) {
                        return 1;
                    } 
                    else if ($a['filesize'] < $b['filesize']) {
                        return -1;
                    } 
                    else {
                        return 0;
                    }
                } 
                else if ($order == 'type') {
                    return strcmp($a['filetype'], $b['filetype']);
                } 
                else {
                    return strcmp($a['filename'], $b['filename']);
                }
            }
        }
    }
}
