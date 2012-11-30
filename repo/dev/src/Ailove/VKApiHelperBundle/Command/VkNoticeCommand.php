<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ailove\VKApiHelperBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;

class VkNoticeCommand extends ContainerAwareCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('notice:vk:send')
            ->setDescription('Send notice to vk user')
//            ->setHelp(<<<EOF
//The <info>catalog:import</info> command import catalog data.
//
//<info>php app/console catalog:import --file-path=/../../../data/catalog/catalog.xls</info>
//
//EOF
//            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $doctrine = $this->getContainer()->get('doctrine');
        $em = $doctrine->getEntityManager();
        $conn = $em->getConnection();
        
        $limit = 100;

        $sql = "SELECT
                    nq.*,
                    u.vk_uid,
                    nm.text
                FROM notices_queue AS nq 
                LEFT JOIN users AS u ON nq.user_id = u.id
                LEFT JOIN notices_messages AS nm ON nq.message_id = nm.id
                WHERE nq.status = 1 AND u.vk_uid IS NOT NULL AND (u.age >= 18 OR u.age IS NULL)
                ORDER BY nq.create_at ASC
                LIMIT " . $limit;

        $result = $conn->fetchAll($sql);

        if (count($result) == 0) {
            $output->writeln("No notifications in queue\n");
            exit;
        }

        $sendData = array();
        foreach ($result as $queueItem) {
            $sendData[$queueItem['message_id']]['message_text'] = $queueItem['text'];
            $sendData[$queueItem['message_id']]['queue'][] = $queueItem;
        }
    
        // send data
        $sendCount = 0;
        $notSendCount = 0;
        foreach ($sendData as $sendPart) {
            $message = $sendPart['message_text'];
            $uids = array();
            foreach ($sendPart['queue'] as $notice) {
                $uids[] = $notice['vk_uid'];
            }

            $params = array(
                'uids'=> implode(',', $uids),
                'message' => $message
            );

            try {
                $response = $this->api('secure.sendNotification', $params);

                $recipientUids = isset($response['response']{1}) ? explode(',',$response['response']) : array();

                // set send count
                $sendCount += count($recipientUids);

                // set status send
                if (count($recipientUids) > 0) {
                    $this->updateSendStatus($conn, $recipientUids, 2);
                
                    $notRecipientsUids = array_diff($uids, $recipientUids);

                    // set status not send
                    $this->updateSendStatus($conn, $notRecipientsUids, 3);
                } else {
                    // set status not send
                    $this->updateSendStatus($conn, $uids, 3);
                    
                    $notRecipientsUids = $uids;
                }

                // set not send count
                $notSendCount += count($notRecipientsUids);
            } catch (\Exception $e) {
                continue;
            }
        }

        $output->writeln(sprintf("\nSend: <info>%s</info>\nNot send: <info>%s</info>\n", $sendCount, $notSendCount));
    }

    protected function updateSendStatus($conn, $uids, $status) {
        $sql = "UPDATE notices_queue AS nq
                SET status = " . $status . "
                FROM (
                    SELECT u1.id
                    FROM users AS u1
                    WHERE u1.vk_uid::int8 IN (" . implode(',', $uids) . ")) AS nnu
                WHERE nnu.id = nq.user_id";
        
        $conn->query($sql);
    }

    protected function api($method,$params=false) {
            if (!$params) $params = array(); 
            // PRO ID 3131497 key NsmpBdDFKUE03d1Qgvam
            // DEV ID 3108648 secret code LFRxWCrYWWDaCM1JbIYC
            $params['api_id'] = '3131497';
            $params['v'] = '3.0';
            $params['method'] = $method;
            $params['timestamp'] = time();
            $params['format'] = 'json';
            $params['random'] = rand(0,10000);
            ksort($params);
            $sig = '';
            foreach($params as $k=>$v) {
                    $sig .= $k.'='.$v;
            }
            $sig .= 'NsmpBdDFKUE03d1Qgvam';
            $params['sig'] = md5($sig);
            $query = 'http://api.vk.com/api.php?'.$this->params($params);

            $res = file_get_contents($query);

            return json_decode($res, true);
    }

    protected function params($params) {
            $pice = array();
            foreach($params as $k=>$v) {
                    $pice[] = $k.'='.urlencode($v);
            }
            return implode('&',$pice);
    }
}
