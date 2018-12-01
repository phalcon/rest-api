<?php

namespace Gewaer\Cli\Tasks;

use Phalcon\Cli\Task as PhTask;
use Gewaer\Models\UserLinkedSources;
use Gewaer\Models\Users;
use Phalcon\Queue\Beanstalk\Extended as BeanstalkExtended;
use Phalcon\Queue\Beanstalk\Job;
use Sly\NotificationPusher\Adapter\Gcm as GcmAdapter;
use Sly\NotificationPusher\Collection\DeviceCollection;
use Sly\NotificationPusher\Model\Device;
use Sly\NotificationPusher\Model\Message;
use Sly\NotificationPusher\Model\Push;
use Sly\NotificationPusher\PushManager;
use Throwable;

/**
 * CLI To send push ontification and pusher msg
 *
 * @package Gewaer\Cli\Tasks
 *
 * @property Config $config
 * @property \Pusher\Pusher $config
 * @property \Monolog\Logger $log
 */
class PushNotificationTask extends PhTask
{
    protected $pushGeneralNotification = 'canvas_notification';

    /**
     * Run the email queue from phalcon CLI
     * php cli/app.php Email generalQueue
     *
     * @return void
     */
    public function mainAction()
    {
        //call queue
        $queue = new BeanstalkExtended([
            'host' => $this->config->beanstalk->host,
            'prefix' => $this->config->beanstalk->prefix,
        ]);

        //have 2 different type of queues for now
        $queueName = $this->config->pusher->queue;

        //dependent variables
        $config = $this->config;

        //call que que tube
        $queue->addWorker($queueName, function (Job $job) use ($config) {
            try {
                //get the array from the queue
                $notificationInfo = $job->getBody();

                $link = !array_key_exists('link', $notificationInfo) ? $this->config->application->siteUrl . '/message/' . $notificationInfo['id'] : $notificationInfo['link'];
                $linkHtml = " <a href='{$link}'>Link</a>";

                if ($this->pusher->trigger($notificationInfo['key'], $this->pushGeneralNotification, ['message' => $notificationInfo['message'] . $linkHtml])) {
                    $this->log->info("Pusher new {$notificationInfo['message']} to {$notificationInfo['key']}");
                } else {
                    $this->log->error("Pusher failed {$notificationInfo['message']} to {$notificationInfo['key']}");
                }

                //if we are not sending it to all the Users
                if ($notificationInfo['key'] != 'gewaer_general') {
                    //find the user Informatio base on its id and try to send the push notification
                    $userData = Users::findFirst(str_replace('user_notifications_', '', $notificationInfo['key']));
                    $sourceId = '8'; //for now only android

                    if ($userDevice = UserLinkedSources::findFirst(['conditions' => 'user_id = ?0 and source_id =?1', 'bind' => [$userData->getId(), $sourceId]])) {
                        $this->log->addInfo('Pusher Sending push notification');

                        //send push notification
                        $pushManager = new PushManager($config->app->production ? PushManager::ENVIRONMENT_PROD : PushManager::ENVIRONMENT_DEV);
                        $gcmAdapter = new GcmAdapter([
                            'apiKey' => $config->pushNotifcation->android,
                        ]);

                        // Set the device(s) to push the notification to.
                        $devices = new DeviceCollection([
                            new Device($userDevice->source_users_id_text),
                        ]);

                        if (is_null($notificationInfo['id'])) {
                            $notificationInfo['id'] = 0;
                        }

                        // Then, create the push skel.
                        //$message = new Message($notificationInfo['message']);
                        $message = new Message(
                            $notificationInfo['message'],
                            [
                                'id' => time() + $userData->getId(),
                                'type' => 'BigText',
                                'title' => $notificationInfo['message'],
                                'message_id' => $notificationInfo['id']
                            ]
                        );

                        // Finally, create and add the push to the manager, and push it!
                        $push = new Push($gcmAdapter, $devices, $message);
                        $pushManager->add($push);

                        if ($pushManager->push()) { // Returns a collection of notified devices
                            $this->log->addInfo('Pusher Notifaction to Device sent', array_merge($userDevice->toArray(), $notificationInfo));
                        } else {
                            $this->log->addError('Pusher Fail to push notification to device', array_merge($userDevice->toArray(), $notificationInfo));
                        }
                    }
                }
            } catch (Throwable $e) {
                $this->log->error($e->getMessage());
            }

            // It's very important to send the right exit code!
            exit(0);
        });

        // Start processing queues
        $queue->doWork();
    }
}
