<?php /**
 * @file
 * Contains \Drupal\answers\EventSubscriber\ExitSubscriber.
 */

namespace Drupal\answers\EventSubscriber;

use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ExitSubscriber implements EventSubscriberInterface {

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return [KernelEvents::TERMINATE => ['onEvent', 0]];
  }

  public function onEvent() {
    $router_item = menu_get_object();
    if (isset($router_item) && $router_item->type == "answers_question") {
      $query = new EntityFieldQuery();
      $query->entityCondition('entity_type', 'node')
        ->entityCondition('bundle', 'answers_answer')
        ->fieldCondition('answers_related_question', 'target_id', $router_item->nid);
      $result = $query->execute();
      if (isset($result['node'])) {
        foreach ($result['node'] as $answer) {
          node_tag_new($answer);
        }
      }
    }
  }

}
