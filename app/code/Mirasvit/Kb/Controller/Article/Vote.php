<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-kb
 * @version   1.0.26
 * @copyright Copyright (C) 2017 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Kb\Controller\Article;

class Vote extends \Mirasvit\Kb\Controller\Article
{
    /**
     *
     */
    public function execute()
    {
        if ($article = $this->_initArticle()) {
            if (!$this->kbVote->getVoteResult($article)) {
                $vote = $this->getRequest()->getParam('vote');
                $article->addVote($vote)
                        ->save();
                $this->kbVote->markAsVoted($article, $vote);
                $this->messageManager->addSuccess(__('Thank you for your vote!'));
            }
            $refererUrl = $this->getRequest()->getServer('HTTP_REFERER');
            $this->_redirect($refererUrl);
        } else {
            $this->_forward('no_rote');
        }
    }
}
