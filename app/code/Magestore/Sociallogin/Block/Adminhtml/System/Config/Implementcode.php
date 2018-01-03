<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 *
 */
namespace Magestore\Sociallogin\Block\Adminhtml\System\Config;

class Implementcode extends \Magento\Config\Block\System\Config\Form\Field
{

    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {

        return '
<div class="entry-edit-head collapseable"><a onclick="Fieldset.toggleCollapse(\'sociallogin_template\'); return false;" href="#" id="sociallogin_template-head" class="open">Code Implementation</a></div>
<input id="sociallogin_template-state" type="hidden" value="1" name="config_state[sociallogin_template]">
<fieldset id="sociallogin_template" class="config collapseable" style="">
<h4 class="icon-head head-edit-form fieldset-legend">Code for Social Login</h4>
<div id="messages">
    <ul class="messages">
        <li class="message message-success success" style="list-style: none;">
            <ul>
                <li style="list-style: none;">' . __('You can put social login button block in any preferred position by using these following codes. Please note that social login buttons still work normally according to your settings in General Configuration tab if codes are not implemented.') . '</li>
            </ul>
        </li>
    </ul>
</div>
<div id="messages">
    <ul class="messages">
        <li class="message message-notice notice" style="list-style: none;">
            <ul>
                <li style="list-style: none;">' . __('Add code below to a template file') . '</li>
            </ul>
        </li>
    </ul>
</div>
<br>
<ul>
	<li style="list-style: none;">
		<code>
			&lt;?php echo $this->getLayout()->createBlock("Magestore\Sociallogin\Block\Buttons")->setTemplate("Magestore_Sociallogin::buttons.phtml")->setNumberButtonShow(4)->toHtml(); ?&gt;
		</code>
	</li>
</ul>
<br>
<div id="messages">
    <ul class="messages">
        <li class="message message-notice notice" style="list-style: none;">
            <ul>
                <li style="list-style: none;">' . __('You can put a social login button block on a CMS page. Here is an example that we put a login block with 4 buttons. Replace "4" in this code with the number of buttons you want to show.') . '</li>
            </ul>
        </li>
    </ul>
</div>
<br>
<ul>
	<li style="list-style: none;">
		<code>
			{{block class="Magestore\Sociallogin\Block\Buttons" name="buttons.sociallogin" template="Magestore_Sociallogin::buttons.phtml" number_button_show="4"}}
		</code>
	</li>
</ul>
<br>
<div id="messages">
    <ul class="messages">
        <li class="message message-notice notice" style="list-style: none;">
            <ul>
                <li style="list-style: none;">' . __('Please copy and paste the code below to one of xml layout files where you want to show the social button block. Replace "4" in this code with the number of buttons you want to show.') . '</li>
            </ul>
        </li>
    </ul>
</div>

<ul>
	<li style="list-style: none;">
		<code>
		 &lt;block class="Magestore\Sociallogin\Block\Buttons" name="buttons.sociallogin" template="Magestore_Sociallogin::buttons.phtml"&gt;<br>
		&nbsp;&nbsp;&nbsp;&nbsp;&lt;action method="setNumberButtonShow"&gt;&lt;number&gt;4&lt;/number&gt;&lt;/action&gt;<br>
		&lt;/block&gt;
		</code>
	</li>
</ul>
<br>
<div id="messages">
    <ul class="messages">
        <li class="message message-notice notice" style="list-style: none;">
            <ul>
                <li style="list-style: none;">' . __('Below is a code example of a block with 4 social login buttons shown on the left of the category page. Replace "4" in this code with the number of buttons you want to show.') . '</li>
            </ul>
        </li>
    </ul>
</div>
<br>
<ul>
	<li style="list-style: none;">
		<code>
&lt;?xml version="1.0"?&gt;<br>
&lt;layout version="0.1.0"&gt;<br>
&nbsp;&nbsp;&lt;catalog_category_default&gt;<br>
	&nbsp;&nbsp;&nbsp;&nbsp;&lt;reference name="left"&gt;<br>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;block class="Magento\Catalog\Block\Navigation" name="catalog.leftnav" after="currency" template="Magento_Catalog::navigation/left.phtml"/&gt;<br>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;block class="Magestore\Sociallogin\Block\Buttons" name="buttons.sociallogin" template="Magestore_Sociallogin::buttons.phtml"&gt;<br>
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;action method="setNumberButtonShow"&gt;&lt;argument="number" xsi:type="string"&gt;4&lt;/argument&gt;&lt;/action&gt;<br>
		   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;/block&gt; <br>
	&nbsp;&nbsp;&nbsp;&nbsp;&lt;/reference><br>
&nbsp;&nbsp;&lt;/catalog_category_default&gt;<br>
&lt;/layout&gt;
</code>
	</li>
</ul>
<br>

</fieldset>';
    }
}
