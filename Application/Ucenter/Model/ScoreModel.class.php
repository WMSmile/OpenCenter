<?php
namespace Ucenter\Model;
use Think\Model;

/**
 * Class ScoreModel   用户积分模型
 * @package Ucenter\Model
 * @author:xjw129xjt(肖骏涛) xjt@ourstu.com
 */
class ScoreModel extends Model
{
    private $typeModel =null;
    protected function _initialize()
    {
        parent::_initialize();
        $this->typeModel =  M('ucenter_score_type');
    }

    /**
     * getTypeList  获取类型列表
     * @param string $map
     * @return mixed
     * @author:xjw129xjt(肖骏涛) xjt@ourstu.com
     */
    public function getTypeList($map=''){
       $list = $this->typeModel->where($map)->select();

       return $list;
   }

    /**
     * getType  获取单个类型
     * @param string $map
     * @return mixed
     * @author:xjw129xjt(肖骏涛) xjt@ourstu.com
     */
    public function getType($map=''){
        $type = $this->typeModel->where($map)->find();
        return $type;
    }

    /**
     * addType 增加积分类型
     * @param $data
     * @return mixed
     * @author:xjw129xjt(肖骏涛) xjt@ourstu.com
     */
    public function addType($data){
       $res = $this->typeModel->add($data);
       $query = "ALTER TABLE  `ocenter_member` ADD  `score".$res."` FLOAT NOT NULL COMMENT  '".$data['title']."'";
       D()->execute($query);
       return $res;
    }

    /**
     * delType  删除分类
     * @param $ids
     * @return mixed
     * @author:xjw129xjt(肖骏涛) xjt@ourstu.com
     */
    public function delType($ids){
        $res = $this->typeModel->where(array('id'=>array('in',$ids)))->delete();
        foreach($ids as $v){
            $query = "alter table `ocenter_member` drop column score".$v;
            D()->execute($query);
      }
        return $res;
    }

    /**
     * editType  修改积分类型
     * @param $data
     * @return mixed
     * @author:xjw129xjt(肖骏涛) xjt@ourstu.com
     */
    public function editType($data){
        $res = $this->typeModel->save($data);
        $query = "alter table `ocenter_member` modify column `score".$data['id']."` FLOAT comment '".$data['title']."';";
        D()->execute($query);
        return $res;
    }


    /**
     * getUserScore  获取用户的积分
     * @param int $uid
     * @param int $type
     * @return mixed
     * @author:xjw129xjt(肖骏涛) xjt@ourstu.com
     */
    public function getUserScore($uid,$type){
        $model = D('Member');
        $score = $model->where(array('uid'=>$uid))->getField('score'.$type);
        return $score;
    }

    /**
     * setUserScore  设置用户的积分
     * @param $uid
     * @param $score
     * @param $type
     * @param string $action
     * @author:xjw129xjt(肖骏涛) xjt@ourstu.com
     */
    public function setUserScore($uid,$score,$type,$action='inc'){

        $model = D('Member');
        switch($action){
            case 'inc':
                $score = abs($score);
                $res = $model->where(array('uid'=>$uid))->setInc('score'.$type,$score);
                break;
            case 'dec':
                $score = abs($score);
                $res = $model->where(array('uid'=>$uid))->setDec('score'.$type,$score);
                break;
            case 'to':
                $res = $model->where(array('uid'=>$uid))->setField('score'.$type,$score);
                break;
            default:
                $res = false;
                break;
        }
        return $res;
    }


}