<?php
namespace Luracast\Restler\Format;

use Symfony\Component\Yaml\Yaml;
use Luracast\Restler\Data\Object;

/**
 * YAML Format for Restler Framework
 *
 * @category   Framework
 * @package    Restler
 * @subpackage format
 * @author     R.Arul Kumaran <arul@luracast.com>
 * @copyright  2010 Luracast
 * @license    http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @link       http://luracast.com/products/restler/
 * @version    3.0.0rc6
 */
class YamlFormat extends DependentFormat
{
    const MIME = 'text/plain';
    const EXTENSION = 'yaml';

    const PACKAGE_NAME = 'symfony/yaml:*';
    const EXTERNAL_CLASS = 'Symfony\Component\Yaml\Yaml';

    public function encode($data, $humanReadable = false)
    {
        return @Yaml::dump(Object::toArray($data));
    }

    public function decode($data)
    {
        return Yaml::parse($data);
    }
}

