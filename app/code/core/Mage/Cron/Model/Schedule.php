<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category    Mage
 * @package     Mage_Cron
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Crontab schedule model
 *
 * @method Mage_Cron_Model_Resource_Schedule _getResource()
 * @method Mage_Cron_Model_Resource_Schedule getResource()
 * @method Mage_Cron_Model_Resource_Schedule_Collection getCollection()
 * @method string getJobCode()
 * @method $this setJobCode(string $value)
 * @method string getStatus()
 * @method $this setStatus(string $value)
 * @method string getMessages()
 * @method $this setMessages(string $value)
 * @method string getCreatedAt()
 * @method $this setCreatedAt(string $value)
 * @method string getScheduledAt()
 * @method $this setScheduledAt(string $value)
 * @method string getExecutedAt()
 * @method $this setExecutedAt(string $value)
 * @method string getFinishedAt()
 * @method $this setFinishedAt(string $value)
 * @method $this unsScheduleId()
 * @method array[]|false|string[] getCronExprArr()
 * @method $this setCronExprArr(array[]|false|string[] $value)
 *
 * @category    Mage
 * @package     Mage_Cron
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Cron_Model_Schedule extends Mage_Core_Model_Abstract
{
    const STATUS_PENDING = 'pending';
    const STATUS_RUNNING = 'running';
    const STATUS_SUCCESS = 'success';
    const STATUS_MISSED = 'missed';
    const STATUS_ERROR = 'error';

    public function _construct()
    {
        $this->_init('cron/schedule');
    }

    /**
     * @param string $expr
     * @return $this
     * @throws Mage_Core_Exception
     */
    public function setCronExpr($expr)
    {
        $e = preg_split('#\s+#', $expr, null, PREG_SPLIT_NO_EMPTY);
        if (count($e) < 5 || count($e) > 6) {
            throw Mage::exception('Mage_Cron', 'Invalid cron expression: '.$expr);
        }

        $this->setCronExprArr($e);
        return $this;
    }

    /**
     * Checks the observer's cron expression against time
     *
     * Supports $this->setCronExpr('* 0-5,10-59/5 2-10,15-25 january-june/2 mon-fri')
     *
     * @param string|int $time
     * @return boolean
     */
    public function trySchedule($time)
    {
        $e = $this->getCronExprArr();
        if (!$e || !$time) {
            return false;
        }
        if (!is_numeric($time)) {
            $time = strtotime($time);
        }

        if ($time === false) {
            $time = null;
        }

        $d = getdate(Mage::getSingleton('core/date')->timestamp($time));

        $match = $this->matchCronExpression($e[0], $d['minutes'])
            && $this->matchCronExpression($e[1], $d['hours'])
            && $this->matchCronExpression($e[2], $d['mday'])
            && $this->matchCronExpression($e[3], $d['mon'])
            && $this->matchCronExpression($e[4], $d['wday']);

        if ($match) {
            $this->setCreatedAt(strftime('%Y-%m-%d %H:%M:%S', time()));
            $this->setScheduledAt(strftime('%Y-%m-%d %H:%M', (int)$time));
        }
        return $match;
    }

    /**
     * @param string $expr
     * @param int $num
     * @return bool
     * @throws Mage_Core_Exception
     */
    public function matchCronExpression($expr, $num)
    {
        // handle ALL match
        if ($expr==='*') {
            return true;
        }

        // handle multiple options
        if (strpos($expr, ',')!==false) {
            foreach (explode(',', $expr) as $e) {
                if ($this->matchCronExpression($e, $num)) {
                    return true;
                }
            }
            return false;
        }

        // handle modulus
        if (strpos($expr, '/')!==false) {
            $e = explode('/', $expr);
            if (count($e)!==2) {
                throw Mage::exception('Mage_Cron', "Invalid cron expression, expecting 'match/modulus': ".$expr);
            }
            if (!is_numeric($e[1])) {
                throw Mage::exception('Mage_Cron', "Invalid cron expression, expecting numeric modulus: ".$expr);
            }
            $expr = $e[0];
            $mod = $e[1];
        } else {
            $mod = 1;
        }

        // handle all match by modulus
        if ($expr==='*') {
            $from = 0;
            $to = 60;
        } // handle range
        elseif (strpos($expr, '-')!==false) {
            $e = explode('-', $expr);
            if (count($e)!==2) {
                throw Mage::exception('Mage_Cron', "Invalid cron expression, expecting 'from-to' structure: ".$expr);
            }

            $from = $this->getNumeric($e[0]);
            $to = $this->getNumeric($e[1]);
        } // handle regular token
        else {
            $from = $this->getNumeric($expr);
            $to = $from;
        }

        if ($from===false || $to===false) {
            throw Mage::exception('Mage_Cron', "Invalid cron expression: ".$expr);
        }

        return ($num>=$from) && ($num<=$to) && ($num%$mod===0);
    }

    /**
     * @param int|string $value
     * @return int|string|false
     */
    public function getNumeric($value)
    {
        static $data = [
            'jan'=>1,
            'feb'=>2,
            'mar'=>3,
            'apr'=>4,
            'may'=>5,
            'jun'=>6,
            'jul'=>7,
            'aug'=>8,
            'sep'=>9,
            'oct'=>10,
            'nov'=>11,
            'dec'=>12,

            'sun'=>0,
            'mon'=>1,
            'tue'=>2,
            'wed'=>3,
            'thu'=>4,
            'fri'=>5,
            'sat'=>6,
        ];

        if (is_numeric($value)) {
            return $value;
        }

        if (is_string($value)) {
            $value = strtolower(substr($value, 0, 3));
            if (isset($data[$value])) {
                return $data[$value];
            }
        }

        return false;
    }

    /**
     * Sets a job to STATUS_RUNNING only if it is currently in STATUS_PENDING.
     * Returns true if status was changed and false otherwise.
     *
     * @param string $oldStatus
     * This is used to implement locking for cron jobs.
     *
     * @return boolean
     */
    public function tryLockJob($oldStatus = self::STATUS_PENDING)
    {
        $result = $this->_getResource()->trySetJobStatusAtomic($this->getId(), self::STATUS_RUNNING, $oldStatus);
        if ($result) {
            $this->setStatus(self::STATUS_RUNNING);
        }
        return $result;
    }
}
