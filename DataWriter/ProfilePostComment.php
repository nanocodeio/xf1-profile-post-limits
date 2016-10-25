<?php
/**
 * Copyright (c) Apantic (apantic.com) 2015 to present.
 * All rights reserved.
 * @license All use is subject to the Apantic License Agreement (https://www.apantic.com/community/products/license-agreement)
 * @author: Apantic Team <support@apantic.com>
 */
class Apantic_ProfilePostLimit_DataWriter_ProfilePostComment extends XFCP_Apantic_ProfilePostLimit_DataWriter_ProfilePostComment
{
    protected function _preSave()
    {
        if ($this->isChanged('message'))
        {
            $maxLength = XenForo_Application::getOptions()->applMessageLimit;
            if (utf8_strlen($this->get('message')) > $maxLength)
            {
                $this->error(new XenForo_Phrase('please_enter_message_with_no_more_than_x_characters', array('count' => $maxLength)), 'message');
            }
        }

        // do this auto linking after length counting
        /** @var $taggingModel XenForo_Model_UserTagging */
        $taggingModel = $this->getModelFromCache('XenForo_Model_UserTagging');

        $this->_taggedUsers = $taggingModel->getTaggedUsersInMessage(
            $this->get('message'), $newMessage, 'text'
        );
        $this->set('message', $newMessage);

        try {
            parent::_preSave();
            if(!empty($this->_errors) && isset($this->_errors['message']) && $this->_errors['message'] == new XenForo_Phrase('please_enter_message_with_no_more_than_x_characters', array('count' => 420)))
            {
                unset($this->_errors['message']);
            }
        } catch (XenForo_Exception $e) {}
    }
}