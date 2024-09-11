<?php
declare(strict_types=1);

namespace TyrellSys\CakePHP3IpFilter\Controller\Component;

use Cake\Controller\Component;
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
    protected array $_defaultConfig = [
        'trustProxy' => true,
        'whitelist' => '',
    ];

    /**
     * @param string|null $ip ip address
     * @return bool
     */
    public function check(?string $ip = null)
    {
        if (is_null($ip)) {
            $request = clone $this->getController()->getRequest();
            $request->trustProxy = filter_var($this->getConfig('trustProxy', true), FILTER_VALIDATE_BOOLEAN);
            $ip = $request->clientIP();
        }

        $whitelist = $this->getConfig('whitelist');
        if (!is_array($whitelist)) {
            $whitelist = explode(',', $whitelist);
        }

        $ipset = new IPSet($whitelist);

        return $ipset->match($ip);
    }

    /**
     * @param string|null $ip ip address
     * @return void
     * @throws \Cake\Http\Exception\ForbiddenException
     */
    public function checkOrFail(?string $ip = null)
    {
        if (!$this->check($ip)) {
            throw new ForbiddenException($ip);
        }
    }
}
