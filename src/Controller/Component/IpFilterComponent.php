<?php
namespace Tyrellsys\CakePHP3IpFilter\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\Http\Exception\ForbiddenException;
use Wikimedia\IPSet;

/**
 * IpFilter component
 */
class IpFilterComponent extends Component
{

    /**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfig = [
        'trustProxy' => true,
        'whitelist' => ''
    ];

    /**
     * @param null|string $ip ip address
     * @return bool
     */
    public function check($ip = null)
    {
        if (is_null($ip)) {
            $request = clone $this->request;
            $request->trustProxy = filter_var($this->getConfig('trustProxy', true), FILTER_VALIDATE_BOOLEAN);
            $ip = $request->clientIP();
        }

        $whitelist = $this->getConfig('whitelist');
        if (!is_array($whitelist)) {
            $whitelist = explode(",", $whitelist);
        }

        $ipset = new IPSet($whitelist);

        return $ipset->match($ip);
    }

    /**
     * @param null|string $ip ip address
     * @return void
     * @throws ForbiddenException
     */
    public function checkOrFail($ip = null)
    {
        if (!$this->check($ip)) {
            throw new ForbiddenException($ip);
        }
    }
}
