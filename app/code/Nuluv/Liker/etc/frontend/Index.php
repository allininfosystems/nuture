<?php
namespace Nuluv\Liker\Controller\Liker;
class Index extends \Magento\Framework\App\Action\Action
{
    public function execute()
    {
        $return_arr = array("likes"=>3,"unlikes"=>5);

       echo json_encode($return_arr);
    }
}
