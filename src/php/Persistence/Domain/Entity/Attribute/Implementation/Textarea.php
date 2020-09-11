<?php


namespace WPPluginCore\Persistence\Domain\Entity\Attribute\Implementation;

use WPPluginCore\Persistence\Domain\Entity\Attribute\Abstraction\Attribute;

defined('ABSPATH') || exit;
class Textarea extends Text
{
    public function getAdminHTML(): String
    {
        return $this->createTableInput('
			<textarea name="' . $this->key . '" rows="5">'
                                            . $this->getValue() .
                                            '</textarea>
		');
    }
}
