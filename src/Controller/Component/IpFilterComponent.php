<?php
namespace TyrellSys\CakePHP3IpFilter\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\Http\Exception\ForbiddenException;
use Whitelist\Check;

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
        $checker = new Check();
        if (is_null($ip)) {
            $oTrustProxy = $this->request->trustProxy;
            $this->request->trustProxy = filter_var($this->getConfig('trustProxy', true), FILTER_VALIDATE_BOOLEAN);
            $ip = $this->request->clientIP();
            $this->request->trustProxy = $oTrustProxy;
        }

        $whitelist = $this->getConfig('whitelist');
        if (!is_array($whitelist)) {
            $whitelist = explode(",", $whitelist);
        }

        $checker->whitelist($whitelist);

        return $checker->check($ip);
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
