<?php

namespace Ailove\VKApiHelperBundle\Controller;

use Sonata\AdminBundle\Controller\CRUDController as Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Story controller
 */
class MessageAdminController extends Controller
{
      /**
     * Send controller
     *
     * @param mixed $id
     *
     * @return RedirectResponse
     */
    public function sendAction($id = null)
    {
        $id = $this->get('request')->get($this->admin->getIdParameter());

        $message = $this->admin->getObject($id);

        if (!$message) {
            throw new NotFoundHttpException(sprintf('unable to find the object with id : %s', $id));
        }

        if (false === $this->admin->isGranted('EDIT', $message)) {
            throw new AccessDeniedException();
        }

        $this->admin->setSubject($message);


        // Entity manager
        $conn = $this->getDoctrine()->getConnection();

        $sql = "SELECT
                    u.id
                FROM users AS u
                WHERE u.vk_uid IS NOT NULL AND u.email = concat(u.vk_uid, '@vk.com') AND (u.age >= 18 OR u.age IS NULL)";
        
        $sth = $conn->prepare($sql);
        $sth->execute();

        $users = $sth->fetchAll(\PDO::FETCH_COLUMN);

        $count = 0;
        $insertArray = array();
        foreach ($users as $userId) {
            $datetime = new \DateTime();
            $insertArray[] = "(" . $message->getId() . ", " . $userId . ", '" . $datetime->format('Y-m-d H:i:s') . "', 1)";
            
            if ($count % 1000 == 0) {
                $this->insertNotices($conn, $insertArray);
                $insertArray = array();
            }
            $count++;
        }

        $this->insertNotices($conn, $insertArray);

        $this->get('session')->setFlash('sonata_flash_success', sprintf('Сообщение отправлено в очередь для %s получателя(ей)', $count));

        return new RedirectResponse($this->admin->generateObjectUrl('edit', $message));
    }

    private function insertNotices($conn, $insertArray)
    {
        $sql = 'INSERT INTO notices_queue (message_id, user_id, create_at, status) VALUES ' . join(',', $insertArray);
        try {
            $conn->query($sql);
            return true;
        } catch (\Exception $e) {
            $errors = $e->getMessage();
            var_dump($errors);die;
            return $errors;
        }
    }
}
