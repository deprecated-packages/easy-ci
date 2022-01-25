<?php

/**
 * This file is part of the Nette Framework (https://nette.org)
 * Copyright (c) 2004 David Grudl (https://davidgrudl.com)
 */
declare (strict_types=1);
namespace EasyCI20220125\Nette\Utils;

use EasyCI20220125\Nette;
if (\false) {
    /** @deprecated use Nette\HtmlStringable */
    interface IHtmlString extends \EasyCI20220125\Nette\HtmlStringable
    {
    }
} elseif (!\interface_exists(\EasyCI20220125\Nette\Utils\IHtmlString::class)) {
    \class_alias(\EasyCI20220125\Nette\HtmlStringable::class, \EasyCI20220125\Nette\Utils\IHtmlString::class);
}
namespace EasyCI20220125\Nette\Localization;

if (\false) {
    /** @deprecated use Nette\Localization\Translator */
    interface ITranslator extends \EasyCI20220125\Nette\Localization\Translator
    {
    }
} elseif (!\interface_exists(\EasyCI20220125\Nette\Localization\ITranslator::class)) {
    \class_alias(\EasyCI20220125\Nette\Localization\Translator::class, \EasyCI20220125\Nette\Localization\ITranslator::class);
}