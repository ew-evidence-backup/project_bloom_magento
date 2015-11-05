<?php

class Unirgy_Dropship_Block_Messages extends Mage_Core_Block_Messages {
    
    /**
     * Retrieve messages in HTML format grouped by type
     *
     * @param   string $type
     * @return  string
     */
    public function getGroupedHtml()
    {
        $types = array(
            Mage_Core_Model_Message::ERROR,
            Mage_Core_Model_Message::WARNING,
            Mage_Core_Model_Message::NOTICE,
            Mage_Core_Model_Message::SUCCESS
        );
        $html = '';
        foreach ($types as $type) {
            if ( $messages = $this->getMessages($type) ) {
                if ( !$html ) {
                    $html .= '<' . $this->_messagesFirstLevelTagName . ' class="messages">';
                }
                
                if('success' == $type) {
                    $html .= '<i class="profile__tizer__ico_success"></i>';
                } else {
                    $html .= '<i class="profile__tizer__ico"></i>';
                }

                foreach ( $messages as $message ) {
                    $html.= '<' . $this->_messagesSecondLevelTagName . '>';
                    $html.= ($this->_escapeMessageFlag) ? $this->htmlEscape($message->getText()) : $message->getText();
                    $html.= '</' . $this->_messagesSecondLevelTagName . '>';
                }

            }
        }
        if ( $html) {
            $html .= '</' . $this->_messagesFirstLevelTagName . '>';
        }
        return $html;
    }    
    
}