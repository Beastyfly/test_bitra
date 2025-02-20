<?php
return new class extends \Renins\Component\BaseTemplateClass
{
	public function execute()
	{
        $ID = $this->getContext()->arParams['ID'];
        $this['config'] = $this->getContext()->loadDetailAction($ID);
	}
};
