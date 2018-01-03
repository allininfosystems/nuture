<?php

namespace Mirasvit\Kb\Controller\Article;

class Comment extends \Magento\Framework\App\Action\Action
{
    public function execute()
    {
		
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		
		$this->_resources = $objectManager->get('Magento\Framework\App\ResourceConnection');
		
		$connection= $this->_resources->getConnection();
			
		$customerSession = $objectManager->get('Magento\Customer\Model\Session');
		
		$name = $customerSession->getCustomer()->getName();
		
		$email = $customerSession->getCustomer()->getEmail();
		
		if($_POST['postId'] == 'like')
		{
			$tltalLike = 0;
			$aid = $_POST['aid'];
			
			$sql = "select * from mst_kb_comment_likes where comment_id=$aid and email='$email'";
			$allLikes = $connection->fetchAll($sql);
			if(count($allLikes) > 0)
			{
				$likeVal = $allLikes[0]['likes']=='0'?1:0;
				$connection->query("update mst_kb_comment_likes set likes=$likeVal where comment_id=$aid and email='$email'");
			} else {
				$connection->query("INSERT INTO mst_kb_comment_likes(comment_id, email, likes) VALUES ($aid, '$email', 1)");
			}
			
			$allLikes = $connection->fetchAll("select sum(likes) as totalLikes from mst_kb_comment_likes where comment_id=$aid");
			foreach($allLikes as $alllike)
			{
				$tltalLike = $alllike['totalLikes'];
			}
			
			echo $tltalLike;
			
		}elseif($_POST['postId'] == 'reply')
		{
			
			$aid = $_POST['parentId'];
			
			$postId = $_POST['articleId'];
			
			$comment = $_POST['comment'];
			
			$connection->query("INSERT INTO mst_kb_comment(post_id, name, email, comment, parent_id) VALUES ($postId, '$name', '$email', '$comment', $aid)");
			
			echo "reply added successfully";
			
		} else {
		
			$postId = $_POST['postId'];
			
			$comment = $_POST['comment'];
						
			$msg = '';
			
			$connection->query("INSERT INTO mst_kb_comment(post_id, name, email, comment) VALUES ($postId, '$name', '$email', '$comment')");
			
			echo "comment added successfully";
		}
    }
}
